<?php
/**
 * Migration script: Parse existing static HTML pages and create admin JSON data store.
 *
 * Reads products.json (search data) and each product/category HTML page to extract
 * all structured data needed by the CMS admin.
 *
 * Usage: php admin/migrate.php   (from project root)
 *    or: visit korjaamokoneet.fi/admin/migrate.php in browser
 */

// Determine base path (works from CLI or browser)
$baseDir = dirname(__DIR__);

// Output helper — works in both CLI and browser
$isCli = php_sapi_name() === 'cli';
function out(string $msg): void {
    global $isCli;
    if ($isCli) {
        echo $msg . "\n";
    } else {
        echo htmlspecialchars($msg) . "<br>\n";
    }
}

if (!$isCli) {
    echo "<pre style='font-family:monospace;font-size:14px;'>\n";
}

out("=== Korjaamokone CMS Migration ===");
out("");

// ---------------------------------------------------------------------------
// 1. Load existing search data
// ---------------------------------------------------------------------------

$searchJsonPath = $baseDir . '/products.json';
if (!file_exists($searchJsonPath)) {
    out("ERROR: products.json not found at $searchJsonPath");
    exit(1);
}

$searchProducts = json_decode(file_get_contents($searchJsonPath), true);
if (!is_array($searchProducts)) {
    out("ERROR: Failed to parse products.json");
    exit(1);
}

out("Loaded " . count($searchProducts) . " products from products.json");

// ---------------------------------------------------------------------------
// 2. Known subcategory prefixes per category
// ---------------------------------------------------------------------------

$subcategoryPrefixes = [
    'keventimet'     => ['kevennin', 'paineilmatunkki'],
    'pilarinostimet' => ['kaksipilarinostin', 'nelipilarinostin'],
    'rengaskoneet'   => ['raskaan-kaluston-rengaskone', 'rengaskone', 'tasapainotuskone'],
    'saksinostimet'  => ['saksinostin'],
];

// ---------------------------------------------------------------------------
// 3. Parse category pages — build per-slug lookup for availability, originalPrice, priceNote
// ---------------------------------------------------------------------------

$categories = ['keventimet', 'pilarinostimet', 'rengaskoneet', 'saksinostimet'];
$categoryData = [];
$categoryCardLookup = []; // slug -> {availability, originalPrice, priceNote}

foreach ($categories as $catId) {
    $catFile = $baseDir . '/' . $catId . '.html';
    if (!file_exists($catFile)) {
        out("WARNING: Category file not found: $catFile");
        continue;
    }

    $html = file_get_contents($catFile);
    $doc = new DOMDocument();
    libxml_use_internal_errors(true);
    $doc->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();
    $xpath = new DOMXPath($doc);

    // Category meta description
    $metaNodes = $xpath->query('//meta[@name="description"]/@content');
    $catMeta = $metaNodes->length > 0 ? $metaNodes->item(0)->nodeValue : '';

    // Category intro text
    $introNodes = $xpath->query('//*[contains(@class,"page-intro")]');
    $catIntro = '';
    if ($introNodes->length > 0) {
        $catIntro = trim($introNodes->item(0)->textContent);
    }

    $categoryData[$catId] = [
        'id'          => $catId,
        'label'       => ucfirst($catId),
        'intro'       => $catIntro,
        'metaDescription' => $catMeta,
        'homepageDescription' => '', // filled below from index.html
    ];

    // Parse product cards for availability, originalPrice, priceNote
    $cards = $xpath->query('//*[contains(@class,"product-card")]');
    foreach ($cards as $card) {
        $cardXpath = new DOMXPath($doc);

        // Find the product link to get slug
        $links = $xpath->query('.//h3/a/@href', $card);
        if ($links->length === 0) continue;
        $slug = $links->item(0)->nodeValue;

        // Availability from badge class
        $badges = $xpath->query('.//*[contains(@class,"badge")]', $card);
        $availability = 'available';
        if ($badges->length > 0) {
            $badgeClass = $badges->item(0)->getAttribute('class');
            if (strpos($badgeClass, 'badge-red') !== false) {
                $availability = 'unavailable';
            } elseif (strpos($badgeClass, 'badge-orange') !== false) {
                $availability = 'order';
            }
        }

        // Original price
        $origPriceNodes = $xpath->query('.//*[contains(@class,"price-original")]', $card);
        $originalPrice = null;
        if ($origPriceNodes->length > 0) {
            $originalPrice = trim($origPriceNodes->item(0)->textContent);
        }

        // Price note — inline <p> after badge (like CRL-6112's "Hinta pyydettaessa")
        $priceNote = null;
        $productInfoNodes = $xpath->query('.//*[contains(@class,"product-info")]', $card);
        if ($productInfoNodes->length > 0) {
            $productInfo = $productInfoNodes->item(0);
            foreach ($productInfo->childNodes as $child) {
                if ($child->nodeType === XML_ELEMENT_NODE && $child->tagName === 'p') {
                    $style = $child->getAttribute('style');
                    // The priceNote paragraphs have inline font-size styling
                    if (strpos($style, 'font-size') !== false) {
                        $priceNote = trim($child->textContent);
                    }
                }
            }
        }

        $categoryCardLookup[$slug] = [
            'availability'  => $availability,
            'originalPrice' => $originalPrice,
            'priceNote'     => $priceNote,
        ];
    }

    out("Parsed category: $catId (" . $cards->length . " product cards)");
}

// ---------------------------------------------------------------------------
// 4. Parse homepage for category card descriptions
// ---------------------------------------------------------------------------

$indexFile = $baseDir . '/index.html';
if (file_exists($indexFile)) {
    $html = file_get_contents($indexFile);
    $doc = new DOMDocument();
    libxml_use_internal_errors(true);
    $doc->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();
    $xpath = new DOMXPath($doc);

    $catCards = $xpath->query('//*[contains(@class,"category-card")]');
    foreach ($catCards as $catCard) {
        $href = $catCard->getAttribute('href');
        // href is like "keventimet.html"
        $catId = str_replace('.html', '', $href);
        $pNodes = $xpath->query('.//p', $catCard);
        if ($pNodes->length > 0 && isset($categoryData[$catId])) {
            $categoryData[$catId]['homepageDescription'] = trim($pNodes->item(0)->textContent);
        }
    }
    out("Parsed homepage category descriptions");
}

// ---------------------------------------------------------------------------
// 5. Helper: derive subcategory and modelCode from slug + category
// ---------------------------------------------------------------------------

function deriveSubcategoryAndModel(string $slug, string $category): array {
    global $subcategoryPrefixes;

    // Strip .html and category prefix
    $remainder = preg_replace('/\.html$/', '', $slug);
    $remainder = preg_replace('/^' . preg_quote($category, '/') . '-/', '', $remainder);

    // Match against known subcategory prefixes (longest first to handle multi-word)
    $prefixes = $subcategoryPrefixes[$category] ?? [];
    usort($prefixes, function ($a, $b) {
        return strlen($b) - strlen($a); // longest first
    });

    foreach ($prefixes as $prefix) {
        if (strpos($remainder, $prefix . '-') === 0) {
            $modelCode = substr($remainder, strlen($prefix) + 1);
            return [$prefix, $modelCode];
        }
        // Edge case: remainder IS the prefix (no model code — shouldn't happen)
        if ($remainder === $prefix) {
            return [$prefix, ''];
        }
    }

    // Fallback: could not determine subcategory
    return ['', $remainder];
}

// ---------------------------------------------------------------------------
// 6. Parse each product HTML page
// ---------------------------------------------------------------------------

$products = [];
$sortOrder = [];
// Track sort order per category
foreach ($categories as $cat) {
    $sortOrder[$cat] = 0;
}

foreach ($searchProducts as $sp) {
    $slug = $sp['slug'];
    $category = $sp['category'];
    $productFile = $baseDir . '/' . $slug;

    [$subcategory, $modelCode] = deriveSubcategoryAndModel($slug, $category);

    // Use modelCode as ID
    $id = $modelCode ?: preg_replace('/\.html$/', '', $slug);

    $product = [
        'id'                => $id,
        'title'             => $sp['title'],
        'category'          => $category,
        'categoryLabel'     => $sp['categoryLabel'],
        'subcategory'       => $subcategory,
        'modelCode'         => $modelCode,
        'slug'              => $slug,
        'price'             => $sp['price'],
        'originalPrice'     => null,
        'priceNote'         => null,
        'availability'      => 'available',
        'images'            => [],
        'diagramImages'     => [],
        'specs'             => $sp['specs'],
        'specTable'         => [],
        'description'       => '',
        'metaDescription'   => '',
        'sortOrder'         => $sortOrder[$category]++,
    ];

    // Merge data from category card lookup
    if (isset($categoryCardLookup[$slug])) {
        $cardData = $categoryCardLookup[$slug];
        $product['availability']  = $cardData['availability'];
        $product['originalPrice'] = $cardData['originalPrice'];
        $product['priceNote']     = $cardData['priceNote'];
    } else {
        // Fallback: derive from search JSON's "available" field
        $product['availability'] = $sp['available'] ? 'available' : 'unavailable';
    }

    // Parse product detail HTML page
    if (!file_exists($productFile)) {
        out("WARNING: Product file not found: $slug");
        $products[] = $product;
        continue;
    }

    $html = file_get_contents($productFile);
    $doc = new DOMDocument();
    libxml_use_internal_errors(true);
    $doc->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();
    $xpath = new DOMXPath($doc);

    // Meta description
    $metaNodes = $xpath->query('//meta[@name="description"]/@content');
    if ($metaNodes->length > 0) {
        $product['metaDescription'] = $metaNodes->item(0)->nodeValue;
    }

    // Product images from .product-detail-grid > div:first-child
    $detailGridNodes = $xpath->query('//*[contains(@class,"product-detail-grid")]');
    if ($detailGridNodes->length > 0) {
        $grid = $detailGridNodes->item(0);
        // First child div contains images
        foreach ($grid->childNodes as $child) {
            if ($child->nodeType === XML_ELEMENT_NODE && $child->tagName === 'div') {
                $imgs = $xpath->query('.//img', $child);
                foreach ($imgs as $img) {
                    $src = $img->getAttribute('src');
                    // Strip "images/" prefix
                    $filename = preg_replace('#^images/#', '', $src);
                    $product['images'][] = $filename;
                }
                break; // only first div
            }
        }
    }

    // Diagram images from .product-diagrams
    $diagramNodes = $xpath->query('//*[contains(@class,"product-diagrams")]//img');
    foreach ($diagramNodes as $img) {
        $src = $img->getAttribute('src');
        $filename = preg_replace('#^images/#', '', $src);
        $product['diagramImages'][] = $filename;
    }

    // Spec table — skip price-row
    $specRows = $xpath->query('//*[contains(@class,"spec-table")]//tr');
    foreach ($specRows as $row) {
        $rowClass = $row->getAttribute('class');
        if (strpos($rowClass, 'price-row') !== false) {
            continue; // skip price row
        }
        $cells = $xpath->query('.//td', $row);
        if ($cells->length >= 2) {
            $product['specTable'][] = [
                'label' => trim($cells->item(0)->textContent),
                'value' => trim($cells->item(1)->textContent),
            ];
        }
    }

    // If category card didn't have availability, try product page badge
    if (!isset($categoryCardLookup[$slug])) {
        $pageBadges = $xpath->query('//*[contains(@class,"product-specs-panel")]//*[contains(@class,"badge")]');
        if ($pageBadges->length > 0) {
            $badgeClass = $pageBadges->item(0)->getAttribute('class');
            if (strpos($badgeClass, 'badge-red') !== false) {
                $product['availability'] = 'unavailable';
            } elseif (strpos($badgeClass, 'badge-orange') !== false) {
                $product['availability'] = 'order';
            } else {
                $product['availability'] = 'available';
            }
        }
    }

    $products[] = $product;
    out("  Parsed: $slug (images=" . count($product['images']) . ", diagrams=" . count($product['diagramImages']) . ", specs=" . count($product['specTable']) . ")");
}

// ---------------------------------------------------------------------------
// 7. Build final categories array with sort order
// ---------------------------------------------------------------------------

$categoriesOut = [];
$catSort = 0;
foreach ($categories as $catId) {
    if (isset($categoryData[$catId])) {
        $categoryData[$catId]['sortOrder'] = $catSort++;
        $categoriesOut[] = $categoryData[$catId];
    }
}

// ---------------------------------------------------------------------------
// 8. Write JSON files atomically
// ---------------------------------------------------------------------------

$dataDir = __DIR__ . '/data';
if (!is_dir($dataDir)) {
    mkdir($dataDir, 0755, true);
}

$productsJsonPath   = $dataDir . '/products.json';
$categoriesJsonPath = $dataDir . '/categories.json';

$jsonFlags = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

// Write products.json atomically
$tmpProducts = $productsJsonPath . '.tmp';
file_put_contents($tmpProducts, json_encode($products, $jsonFlags));
rename($tmpProducts, $productsJsonPath);

// Write categories.json atomically
$tmpCategories = $categoriesJsonPath . '.tmp';
file_put_contents($tmpCategories, json_encode($categoriesOut, $jsonFlags));
rename($tmpCategories, $categoriesJsonPath);

// ---------------------------------------------------------------------------
// 9. Summary
// ---------------------------------------------------------------------------

out("");
out("=== Migration Complete ===");
out(count($products) . " products migrated to admin/data/products.json");
out(count($categoriesOut) . " categories migrated to admin/data/categories.json");

// Quick stats
$withImages = count(array_filter($products, fn($p) => count($p['images']) > 0));
$withDiagrams = count(array_filter($products, fn($p) => count($p['diagramImages']) > 0));
$withSpecTable = count(array_filter($products, fn($p) => count($p['specTable']) > 0));
$withPrice = count(array_filter($products, fn($p) => $p['price'] !== null));
$withOrigPrice = count(array_filter($products, fn($p) => $p['originalPrice'] !== null));
$available = count(array_filter($products, fn($p) => $p['availability'] === 'available'));
$unavailable = count(array_filter($products, fn($p) => $p['availability'] === 'unavailable'));
$order = count(array_filter($products, fn($p) => $p['availability'] === 'order'));

out("");
out("Stats:");
out("  With images:       $withImages");
out("  With diagrams:     $withDiagrams");
out("  With spec table:   $withSpecTable");
out("  With price:        $withPrice");
out("  With orig. price:  $withOrigPrice");
out("  Available:         $available");
out("  Unavailable:       $unavailable");
out("  Order:             $order");

if (!$isCli) {
    echo "</pre>\n";
}
