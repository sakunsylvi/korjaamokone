<?php
/**
 * Static HTML generator for korjaamokone.fi
 *
 * Generates all static HTML pages from the admin data.
 * Output must match the exact structure of the current live pages.
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/data.php';
require_once __DIR__ . '/markdown.php';

// ---------------------------------------------------------------------------
// Atomic file write
// ---------------------------------------------------------------------------

/**
 * Write content to a file atomically: write to .tmp, then rename.
 */
function write_atomic(string $path, string $content): bool
{
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $tmp = $path . '.tmp';
    if (file_put_contents($tmp, $content, LOCK_EX) === false) {
        return false;
    }
    chmod($tmp, 0644);
    if (!rename($tmp, $path)) {
        @unlink($tmp);
        return false;
    }
    return true;
}

// ---------------------------------------------------------------------------
// Shared page fragments
// ---------------------------------------------------------------------------

/**
 * Generate the common header HTML.
 * $activeCategory: category id to mark active, or null for homepage.
 */
function generate_header(?string $activeCategory = null): string
{
    $navItems = [
        'keventimet'     => 'Keventimet',
        'pilarinostimet' => 'Pilarinostimet',
        'rengaskoneet'   => 'Rengaskoneet',
        'saksinostimet'  => 'Saksinostimet',
    ];

    $navLinks = '';
    foreach ($navItems as $id => $label) {
        $active = ($id === $activeCategory) ? ' class="active"' : '';
        $navLinks .= "\n        <a href=\"{$id}.html\"{$active}>{$label}</a>";
    }

    $mobileLinks = '';
    foreach ($navItems as $id => $label) {
        $mobileLinks .= "\n    <a href=\"{$id}.html\">{$label}</a>";
    }

    return <<<HTML

  <div class="topstripe"></div>

  <header class="site-header">
    <div class="header-inner">
      <a href="index.html" class="site-logo">
        <span class="logo-main">Korjaamokone.fi</span>
        <span class="logo-sub">KM-Machinen verkkosivusto</span>
      </a>
      <nav class="site-nav" aria-label="Päävalikko">{$navLinks}
      </nav>
      <div class="search-wrap">
        <input type="search" id="search-input" placeholder="Hae tuotteita..." autocomplete="off">
        <div id="search-results"></div>
      </div>
      <button class="nav-toggle" aria-label="Avaa valikko" aria-expanded="false" aria-controls="mobile-nav">
        <span></span><span></span><span></span>
      </button>
    </div>
  </header>

  <nav class="mobile-nav" id="mobile-nav" aria-label="Mobiilivalikko">
    <button class="close-nav" aria-label="Sulje valikko">&#x2715;</button>{$mobileLinks}
  </nav>
HTML;
}

/**
 * Generate the common footer HTML.
 */
function generate_footer(): string
{
    return <<<'HTML'

  <footer class="site-footer">
    <div class="footer-inner">
      <div class="footer-col">
        <strong>Korjaamokone.fi</strong>
        <p>KM-Machinen virallinen verkkokauppa</p>
      </div>
      <div class="footer-col">
        <strong>Osoite</strong>
        <p>KM-Machine<br>Savikontie 41<br>82500 Kitee</p>
      </div>
      <div class="footer-col">
        <strong>Verkkokauppa</strong>
        <p>KM-Machine verkkokauppa<br>Kaikki hinnat alv 25,5%</p>
      </div>
      <div class="footer-col">
        <strong>Yhteystiedot</strong>
        <p>kmmachinefinland(ät)gmail.com<br>040&nbsp;563&nbsp;7605</p>
      </div>
    </div>
    <div class="footer-bottom">&copy; KM-Machine — Korjaamokone.fi</div>
  </footer>
HTML;
}

/**
 * Generate the common JS block (search + mobile nav toggle).
 */
function generate_js(): string
{
    return <<<'HTML'

  <script src="search.js"></script>
  <script>
    const toggle = document.querySelector('.nav-toggle');
    const mobileNav = document.querySelector('.mobile-nav');
    const closeBtn = document.querySelector('.close-nav');
    toggle.addEventListener('click', () => {
      const open = mobileNav.classList.toggle('open');
      toggle.classList.toggle('open', open);
      toggle.setAttribute('aria-expanded', open);
      document.body.style.overflow = open ? 'hidden' : '';
    });
    closeBtn.addEventListener('click', () => {
      mobileNav.classList.remove('open');
      toggle.classList.remove('open');
      toggle.setAttribute('aria-expanded', false);
      document.body.style.overflow = '';
    });
  </script>
HTML;
}

// ---------------------------------------------------------------------------
// Availability badge helper
// ---------------------------------------------------------------------------

/**
 * Return the badge HTML for a given availability value.
 * Used on both category listing and product detail pages.
 *
 * $availability: "available", "unavailable", "order"
 * $badgeText: optional custom text override (e.g. "Ei saatavilla toistaiseksi")
 * $style: optional inline style (e.g. "margin-bottom:12px;" for detail pages)
 */
function render_badge(string $availability, ?string $badgeText = null, string $style = ''): string
{
    $styleAttr = $style !== '' ? ' style="' . $style . '"' : '';

    switch ($availability) {
        case 'available':
            $text = $badgeText ?? 'Saatavilla';
            return '<span class="badge badge-green"' . $styleAttr . '>' . esc($text) . '</span>';
        case 'unavailable':
            $text = $badgeText ?? 'Ei saatavilla';
            return '<span class="badge badge-red"' . $styleAttr . '>' . esc($text) . '</span>';
        case 'order':
            $text = $badgeText ?? 'Tilaustuote';
            return '<span class="badge badge-orange"' . $styleAttr . '>' . esc($text) . '</span>';
        default:
            return '';
    }
}

/**
 * Escape a string for HTML output.
 */
function esc(string $text): string
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

// ---------------------------------------------------------------------------
// Homepage
// ---------------------------------------------------------------------------

/**
 * Generate the index.html homepage.
 */
function generate_homepage(array $categories): string
{
    $header = generate_header(null);
    $footer = generate_footer();
    $js = generate_js();

    // Category cards sorted by sortOrder
    usort($categories, fn($a, $b) => ($a['sortOrder'] ?? 0) - ($b['sortOrder'] ?? 0));

    $cards = '';
    foreach ($categories as $cat) {
        $id = esc($cat['id']);
        $label = esc($cat['label']);
        $desc = $cat['homepageDescription'] ?? '';
        $cards .= <<<HTML

        <a href="{$id}.html" class="category-card" style="text-decoration:none;color:inherit;">
          <h2>{$label}</h2>
          <p>{$desc}</p>
          <span class="card-link">Katso tuotteet</span>
        </a>
HTML;
    }

    return <<<HTML
<!DOCTYPE html>
<html lang="fi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>KORJAAMOKONE.FI – KM-MACHINEN VERKKOSIVUSTO</title>
  <meta name="description" content="KM-Machine – Laadukkaat korjaamokoneet: pilarinostimet, saksinostimet, rengaskoneet ja keventimet. Kitee, Suomi.">
  <link rel="stylesheet" href="style.css">
</head>
<body>
{$header}

  <main id="tp_content">

    <section class="hero" aria-label="Etusivun hero-kuva">
      <img src="images/homepage-hero.gif" alt="KM-Machine korjaamokoneet" style="width:100%;height:100%;object-fit:cover;">
    </section>

    <section aria-label="Tuotekategoriat">
      <div class="category-grid">
{$cards}

      </div>
    </section>

    <div class="index-thanks">
      <p><em>Kiitos, että olet asiakkaamme. Saat asiakaspalvelumme kiinni numerosta <strong>040&nbsp;563&nbsp;7605</strong>, mikäli sinulla on kysyttävää!</em></p>
    </div>

  </main>
{$footer}
{$js}

</body>
</html>
HTML;
}

// ---------------------------------------------------------------------------
// Category page
// ---------------------------------------------------------------------------

/**
 * Generate a category listing page.
 *
 * $category: category data array
 * $products: array of products in this category, already sorted by sortOrder
 */
function generate_category_page(array $category, array $products): string
{
    $catId = $category['id'];
    $catLabel = esc($category['label']);
    $metaDesc = esc($category['metaDescription'] ?? '');
    $intro = $category['intro'] ?? '';

    $header = generate_header($catId);
    $footer = generate_footer();
    $js = generate_js();

    // Sort products by sortOrder
    usort($products, fn($a, $b) => ($a['sortOrder'] ?? 0) - ($b['sortOrder'] ?? 0));

    $productCards = '';
    foreach ($products as $i => $p) {
        $productCards .= "\n" . render_product_card($p, $i + 1);
    }

    return <<<HTML
<!DOCTYPE html>
<html lang="fi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{$catLabel} – KORJAAMOKONE.FI</title>
  <meta name="description" content="{$metaDesc}">
  <link rel="stylesheet" href="style.css">
</head>
<body>
{$header}

  <main id="tp_content">

    <div class="page-header">
      <h1>Kategoria: {$catLabel}</h1>
      <p class="page-intro">{$intro}</p>
    </div>

    <div class="product-list">
{$productCards}

    </div>
  </main>
{$footer}
{$js}

</body>
</html>
HTML;
}

/**
 * Render a single product card for a category listing page.
 */
function render_product_card(array $p, int $num): string
{
    $title = esc($p['title']);
    $slug = esc($p['slug']);
    $images = $p['images'] ?? [];
    $firstImage = !empty($images) ? esc($images[0]) : '';
    $availability = $p['availability'] ?? 'available';
    $badgeText = $p['badgeText'] ?? null;
    $titleHtml = $p['titleHtml'] ?? null;

    // Title in the h3 link: use titleHtml if set (allows inline HTML like <span>), else escaped title
    $displayTitle = $titleHtml ?? $title;

    $imgTag = $firstImage
        ? '<img src="images/' . $firstImage . '" alt="' . $title . '" style="width:200px;height:160px;object-fit:contain;background:#e0e0e0;border-radius:2px;">'
        : '';

    // Badge
    $badge = render_badge($availability, $badgeText);

    // Price block
    $priceBlock = '';
    $originalPrice = $p['originalPrice'] ?? null;
    $price = $p['price'] ?? null;

    if ($originalPrice && $price) {
        // Sale price: show original strikethrough + tarjous label + sale price
        $priceBlock = <<<HTML

          <div class="price-block">
            <span class="price-original">{$originalPrice}</span>
            <span class="tarjous-label">Tarjous</span>
            <span class="price-sale">{$price}</span>
          </div>
HTML;
    } elseif (!$originalPrice && $price && isset($p['isSale']) && $p['isSale']) {
        // Tarjous without original price
        $priceBlock = <<<HTML

          <div class="price-block">
            <span class="tarjous-label">Tarjous</span>
            <span class="price-sale">{$price}</span>
          </div>
HTML;
    } elseif ($price) {
        // Regular price
        $priceExtra = '';
        if (!empty($p['priceExtra'])) {
            $priceExtra = "\n" . '            <span style="font-size:.8rem;color:var(--grey-500);">' . esc($p['priceExtra']) . '</span>';
        }
        $priceBlock = <<<HTML

          <div class="price-block">
            <span class="price">{$price}</span>{$priceExtra}
          </div>
HTML;
    }

    // Price note (e.g. "Hinta pyydettaessa")
    $priceNote = '';
    if (!empty($p['priceNote'])) {
        $priceNote = "\n" . '          <p style="font-size:.875rem;color:var(--grey-700);margin-top:4px;">' . esc($p['priceNote']) . '</p>';
    }

    // Specs list
    $specs = $p['specs'] ?? [];
    $specItems = '';
    foreach ($specs as $spec) {
        $specItems .= "\n            <li>" . esc($spec) . '</li>';
    }
    $specList = $specItems !== '' ? "\n" . '          <ul class="spec-list">' . $specItems . "\n          </ul>" : '';

    return <<<HTML
      <!-- {$num} -->
      <article class="product-card">
        {$imgTag}
        <div class="product-info">
          <h3><a href="{$slug}">{$displayTitle}</a></h3>
          {$badge}{$priceBlock}{$priceNote}{$specList}
          <a href="{$slug}" class="read-more">Lue lisää</a>
        </div>
      </article>
HTML;
}

// ---------------------------------------------------------------------------
// Product detail page
// ---------------------------------------------------------------------------

/**
 * Generate a product detail page.
 *
 * $product: product data array
 * $prevProduct: previous product in the category (or null)
 * $nextProduct: next product in the category (or null)
 */
function generate_product_page(array $product, ?array $prevProduct, ?array $nextProduct): string
{
    $title = esc($product['title']);
    $metaDesc = esc($product['metaDescription'] ?? '');
    $catId = $product['category'];
    $catLabel = esc($product['categoryLabel']);
    $slug = $product['slug'];
    $images = $product['images'] ?? [];
    $availability = $product['availability'] ?? 'available';
    $price = $product['price'] ?? null;
    $priceNote = $product['priceNote'] ?? null;
    $specTable = $product['specTable'] ?? [];
    $description = $product['description'] ?? '';
    $diagramImages = $product['diagramImages'] ?? [];

    $header = generate_header($catId);
    $footer = generate_footer();
    $js = generate_js();

    // --- Images ---
    $imageHtml = '';
    foreach ($images as $idx => $img) {
        $alt = $title;
        $style = 'width:100%;max-height:480px;object-fit:contain;background:#e0e0e0;border-radius:2px;';
        if ($idx > 0) {
            $alt .= ' — lisäkuva';
            $style .= 'margin-top:12px;';
        }
        $imageHtml .= "\n          <img src=\"images/" . esc($img) . "\" alt=\"" . $alt . "\" style=\"" . $style . "\">";
    }

    // --- Spec table rows ---
    $specRows = '';

    // Price row first (if price exists)
    if ($price) {
        $specRows .= <<<HTML

              <tr class="price-row">
                <td>Hinta</td>
                <td>{$price}</td>
              </tr>
HTML;
    } elseif ($priceNote) {
        // Show priceNote in the price row if no numeric price
        $specRows .= <<<HTML

              <tr class="price-row">
                <td>Hinta</td>
                <td>{$priceNote}</td>
              </tr>
HTML;
    }

    foreach ($specTable as $row) {
        $label = esc($row['label'] ?? '');
        $value = esc($row['value'] ?? '');
        $specRows .= <<<HTML

              <tr>
                <td>{$label}</td>
                <td>{$value}</td>
              </tr>
HTML;
    }

    // --- Availability badge ---
    $badgeText = $product['badgeText'] ?? null;
    $badge = render_badge($availability, $badgeText, 'margin-bottom:12px;');

    // --- Description section ---
    $descriptionSection = '';
    if (!empty($description)) {
        $descHtml = markdown_to_html($description);
        $descriptionSection = <<<HTML


    <section class="product-description" style="max-width:var(--max-w);margin:32px auto 0;padding:0 24px;">
      {$descHtml}
    </section>
HTML;
    }

    // --- Diagram images section ---
    $diagramSection = '';
    if (!empty($diagramImages)) {
        $diagImgs = '';
        foreach ($diagramImages as $idx => $img) {
            $style = 'width:100%;border-radius:2px;display:block;';
            if ($idx > 0) {
                $style .= 'margin-top:12px;';
            }
            $diagImgs .= "\n        <img src=\"images/" . esc($img) . "\" alt=\"" . $title . " mittakuva\" style=\"" . $style . "\">";
        }
        $diagramSection = <<<HTML


    <section class="product-diagrams" aria-label="Mittakuvat">
      <h2>Mittakuvat</h2>
      <div>{$diagImgs}
      </div>
    </section>
HTML;
    }

    // --- Prev/Next navigation ---
    $prevLink = '<span></span>';
    if ($prevProduct) {
        $prevSlug = esc($prevProduct['slug']);
        $prevShort = esc(extract_short_name($prevProduct));
        $prevLink = '<a href="' . $prevSlug . '" class="nav-prev">' . $prevShort . '</a>';
    }

    $nextLink = '';
    if ($nextProduct) {
        $nextSlug = esc($nextProduct['slug']);
        $nextShort = esc(extract_short_name($nextProduct));
        $nextLink = "\n      " . '<a href="' . $nextSlug . '" class="nav-next">' . $nextShort . '</a>';
    } else {
        $nextLink = "\n      " . '<span></span>';
    }

    return <<<HTML
<!DOCTYPE html>
<html lang="fi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{$title} – KORJAAMOKONE.FI</title>
  <meta name="description" content="{$metaDesc}">
  <link rel="stylesheet" href="style.css">
  <style>
    .product-detail-grid { margin-top: 28px; }
    .spec-table tr td:first-child { font-size:.8rem;color:var(--grey-500);font-weight:600;text-transform:uppercase;letter-spacing:.04em; }
    .spec-table tr td:last-child { font-size:.9375rem;font-weight:500;color:var(--black); }
    .spec-table .price-row td:last-child { font-family:var(--font-display);font-weight:800;font-size:1.4rem;color:var(--green-dark); }
  </style>
</head>
<body>
{$header}

  <main id="tp_content">

    <nav class="breadcrumb" aria-label="Sivupolku">
      <a href="index.html">Etusivu</a>
      <span aria-hidden="true">&#x203A;</span>
      <a href="{$catId}.html">{$catLabel}</a>
      <span aria-hidden="true">&#x203A;</span>
      <span aria-current="page">{$title}</span>
    </nav>

    <section class="product-detail" aria-label="Tuotetiedot">
      <h1>{$title}</h1>

      <div class="product-detail-grid">

        <div>{$imageHtml}
        </div>

        <aside class="product-specs-panel" aria-label="Tekniset tiedot">
          <h2>Tekniset tiedot</h2>
          <table class="spec-table">
            <tbody>{$specRows}
            </tbody>
          </table>

          <div style="margin-top:20px;">
            {$badge}
            <p style="font-size:.8125rem;color:var(--grey-700);line-height:1.55;">
              Kysy lisätietoja tai tee tilaus puhelimitse tai sähköpostilla.
            </p>
          </div>
        </aside>

      </div>
    </section>
{$descriptionSection}{$diagramSection}

    <section class="product-cta" aria-label="Ota yhteyttä">
      <div class="product-cta-inner">
        <p>Kysy lisää tai tilaa:<br>
          <strong>040 563 7605</strong>&ensp;|&ensp;
          <strong>kmmachinefinland(ät)gmail.com</strong>
        </p>
        <a href="#" onclick="this.href='mai'+'lto:'+'kmmachinefinland'+'@'+'gmail.com';return true;" aria-label="Lähetä sähköposti KM-Machinelle">Lähetä sähköposti</a>
      </div>
    </section>

    <div class="back-link">
      <a href="{$catId}.html">Takaisin {$catLabel}-listaan</a>
    </div>

    <nav class="product-nav" aria-label="Tuotenavigaatio">
      {$prevLink}{$nextLink}
    </nav>

  </main>
{$footer}
{$js}

</body>
</html>
HTML;
}

/**
 * Extract a short model name for prev/next navigation links.
 *
 * Uses the modelCode field (uppercased) if available, otherwise extracts
 * the last uppercase word(s) from the title.
 * E.g. "Kaksipilarinostin HTL-3940D" -> "HTL-3940D"
 *      "Saksinostin CR-6106"         -> "CR-6106"
 *      "Nelipilarinostin Hauvrex HPL-4036HL" -> "HPL-4036HL"
 */
function extract_short_name(array $product): string
{
    // Prefer explicit modelCode field
    if (!empty($product['modelCode'])) {
        return strtoupper($product['modelCode']);
    }

    $title = $product['title'] ?? '';

    // Try to find a model code pattern: letters+dash+digits (optionally with suffix letters)
    // e.g. HTL-3940D, CR-6106, CRL-6112, HPL-4036HL, HXL-6230, RX20Q, TR-3813
    if (preg_match('/([A-Z]{2,}-?\d{3,}[A-Z]*\d*)(?:\s|$)/i', $title, $m)) {
        return strtoupper($m[1]);
    }

    // Fallback: everything after the last space that starts with uppercase
    $parts = explode(' ', $title);
    $short = end($parts);
    return strtoupper($short);
}

// ---------------------------------------------------------------------------
// Search JSON (products.json)
// ---------------------------------------------------------------------------

/**
 * Generate the public products.json search data.
 */
function generate_search_json(array $products): string
{
    $entries = [];
    foreach ($products as $p) {
        $available = ($p['availability'] ?? 'available') === 'available'
                  || ($p['availability'] ?? 'available') === 'order';

        $entries[] = [
            'title'         => $p['title'],
            'category'      => $p['category'],
            'categoryLabel' => $p['categoryLabel'],
            'slug'          => $p['slug'],
            'image'         => 'images/' . ($p['images'][0] ?? ''),
            'specs'         => $p['specs'] ?? [],
            'price'         => $p['price'] ?? null,
            'available'     => $available,
        ];
    }

    return json_encode($entries, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

// ---------------------------------------------------------------------------
// Main publish orchestrator
// ---------------------------------------------------------------------------

/**
 * Publish the entire static site.
 *
 * Reads products and categories from the admin data store, generates all
 * HTML pages and products.json, removes orphaned product pages, and writes
 * a publish log.
 *
 * @return array{success: bool, generated: string[], deleted: string[], errors: string[]}
 */
function publish_site(array $products, array $categories): array
{
    $results = [
        'success'   => true,
        'generated' => [],
        'deleted'   => [],
        'errors'    => [],
    ];

    $siteDir = SITE_DIR;

    // Build a lookup: category id -> category data
    $catById = [];
    foreach ($categories as $cat) {
        $catById[$cat['id']] = $cat;
    }

    // Group products by category and sort each group by sortOrder
    $productsByCategory = [];
    foreach ($products as $p) {
        $catId = $p['category'];
        $productsByCategory[$catId][] = $p;
    }
    foreach ($productsByCategory as &$group) {
        usort($group, fn($a, $b) => ($a['sortOrder'] ?? 0) - ($b['sortOrder'] ?? 0));
    }
    unset($group);

    // Track all expected HTML filenames (to detect orphans)
    $expectedFiles = ['index.html'];

    // --- 1. Generate homepage ---
    try {
        $html = generate_homepage($categories);
        if (!write_atomic($siteDir . '/index.html', $html)) {
            $results['errors'][] = 'Failed to write index.html';
            $results['success'] = false;
        } else {
            $results['generated'][] = 'index.html';
        }
    } catch (Exception $e) {
        $results['errors'][] = 'Homepage: ' . $e->getMessage();
        $results['success'] = false;
    }

    // --- 2. Generate category pages ---
    foreach ($categories as $cat) {
        $catId = $cat['id'];
        $filename = $catId . '.html';
        $expectedFiles[] = $filename;

        try {
            $catProducts = $productsByCategory[$catId] ?? [];
            $html = generate_category_page($cat, $catProducts);
            if (!write_atomic($siteDir . '/' . $filename, $html)) {
                $results['errors'][] = "Failed to write {$filename}";
                $results['success'] = false;
            } else {
                $results['generated'][] = $filename;
            }
        } catch (Exception $e) {
            $results['errors'][] = "{$filename}: " . $e->getMessage();
            $results['success'] = false;
        }
    }

    // --- 3. Generate product detail pages ---
    foreach ($productsByCategory as $catId => $catProducts) {
        $count = count($catProducts);
        for ($i = 0; $i < $count; $i++) {
            $product = $catProducts[$i];
            $prev = $i > 0 ? $catProducts[$i - 1] : null;
            $next = $i < $count - 1 ? $catProducts[$i + 1] : null;
            $filename = $product['slug'];
            $expectedFiles[] = $filename;

            try {
                $html = generate_product_page($product, $prev, $next);
                if (!write_atomic($siteDir . '/' . $filename, $html)) {
                    $results['errors'][] = "Failed to write {$filename}";
                    $results['success'] = false;
                } else {
                    $results['generated'][] = $filename;
                }
            } catch (Exception $e) {
                $results['errors'][] = "{$filename}: " . $e->getMessage();
                $results['success'] = false;
            }
        }
    }

    // --- 4. Generate products.json ---
    try {
        // Sort all products for the search JSON: by category sortOrder, then by product sortOrder
        $allSorted = [];
        usort($categories, fn($a, $b) => ($a['sortOrder'] ?? 0) - ($b['sortOrder'] ?? 0));
        foreach ($categories as $cat) {
            $catProducts = $productsByCategory[$cat['id']] ?? [];
            foreach ($catProducts as $p) {
                $allSorted[] = $p;
            }
        }

        $json = generate_search_json($allSorted);
        if (!write_atomic($siteDir . '/products.json', $json)) {
            $results['errors'][] = 'Failed to write products.json';
            $results['success'] = false;
        } else {
            $results['generated'][] = 'products.json';
        }
    } catch (Exception $e) {
        $results['errors'][] = 'products.json: ' . $e->getMessage();
        $results['success'] = false;
    }

    // --- 5. Delete orphaned HTML files ---
    // Orphans are HTML files that match the product slug pattern
    // (category-subcategory-model.html) but aren't in the expected list.
    // Category pages (single-word.html) and index.html are never orphans.
    $categoryIds = array_keys($catById);
    $expectedSet = array_flip($expectedFiles);

    $existingFiles = glob($siteDir . '/*.html');
    if ($existingFiles) {
        foreach ($existingFiles as $filepath) {
            $filename = basename($filepath);

            // Skip non-product files: index.html, category pages (single-word.html)
            if ($filename === 'index.html') {
                continue;
            }
            // Check if it's a category page (matches a known category id)
            $nameWithoutExt = pathinfo($filename, PATHINFO_FILENAME);
            if (isset($catById[$nameWithoutExt])) {
                continue;
            }

            // This looks like a product page — check if it's expected
            if (!isset($expectedSet[$filename])) {
                if (@unlink($filepath)) {
                    $results['deleted'][] = $filename;
                } else {
                    $results['errors'][] = "Failed to delete orphan: {$filename}";
                }
            }
        }
    }

    // --- 6. Write publish log ---
    $pageCount = 1 + count($categories) + count($products); // index + categories + products
    $log = [
        'lastPublish'  => date('c'),
        'productCount' => count($products),
        'pageCount'    => $pageCount,
    ];

    $logDir = ADMIN_DIR . '/data';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    write_json($logDir . '/publish-log.json', $log);

    return $results;
}
