<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/data.php';

// Start session early so the cookie is sent before any HTML output
init_session();

$action = $_GET['action'] ?? 'dashboard';

// Public routes (no auth required)
if ($action === 'login') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        verify_csrf($_POST['csrf_token'] ?? '');
        if (login($_POST['username'] ?? '', $_POST['password'] ?? '')) {
            header('Location: index.php');
            exit;
        }
        $error = 'Virheelliset tunnukset';
    }
    require __DIR__ . '/views/login.php';
    exit;
}

if ($action === 'logout') {
    logout();
    header('Location: index.php?action=login');
    exit;
}

// All other routes require auth
require_auth();

switch ($action) {
    case 'dashboard':
        $products = load_products();
        $categories = load_categories();
        require __DIR__ . '/views/dashboard.php';
        break;

    case 'product':
        $id = $_GET['id'] ?? null;
        $clone_from = $_GET['clone'] ?? null;
        if ($id) {
            $product = get_product($id);
        } elseif ($clone_from) {
            $product = get_product($clone_from);
            $product['id'] = '';
            $product['modelCode'] = '';
            $product['title'] = '';
            $product['images'] = [];
            $product['diagramImages'] = [];
        } else {
            // New product from scratch
            $product = [
                'id' => '', 'title' => '', 'category' => '', 'subcategory' => '',
                'modelCode' => '', 'price' => null, 'originalPrice' => null,
                'priceNote' => null, 'availability' => 'available',
                'specs' => [], 'specTable' => [], 'description' => '',
                'metaDescription' => '', 'sortOrder' => 0,
                'images' => [], 'diagramImages' => [],
            ];
        }
        $categories = load_categories();
        require __DIR__ . '/views/product-form.php';
        break;

    case 'save-product':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }
        verify_csrf($_POST['csrf_token'] ?? '');
        require_once __DIR__ . '/upload.php';

        $product = [
            'id' => $_POST['id'] ?? '',
            'title' => trim($_POST['title'] ?? ''),
            'category' => $_POST['category'] ?? '',
            'categoryLabel' => CATEGORIES[$_POST['category'] ?? ''] ?? '',
            'subcategory' => trim($_POST['subcategory'] ?? ''),
            'modelCode' => strtolower(trim($_POST['modelCode'] ?? '')),
            'price' => trim($_POST['price'] ?? '') ?: null,
            'originalPrice' => trim($_POST['originalPrice'] ?? '') ?: null,
            'priceNote' => trim($_POST['priceNote'] ?? '') ?: null,
            'availability' => $_POST['availability'] ?? 'available',
            'specs' => array_values(array_filter(array_map('trim', explode("\n", $_POST['specs'] ?? '')))),
            'specTable' => [],
            'description' => trim($_POST['description'] ?? ''),
            'metaDescription' => trim($_POST['metaDescription'] ?? ''),
            'sortOrder' => (int)($_POST['sortOrder'] ?? 0),
        ];

        // Parse spec table rows
        $specLabels = $_POST['specLabel'] ?? [];
        $specValues = $_POST['specValue'] ?? [];
        for ($i = 0; $i < count($specLabels); $i++) {
            $label = trim($specLabels[$i] ?? '');
            $value = trim($specValues[$i] ?? '');
            if ($label && $value) {
                $product['specTable'][] = ['label' => $label, 'value' => $value];
            }
        }

        // Generate slug
        $product['slug'] = $product['category'] . '-' . $product['subcategory'] . '-' . $product['modelCode'] . '.html';

        // If no ID set, generate from modelCode
        if (!$product['id']) {
            $product['id'] = $product['modelCode'];
        }

        // Keep existing images (from hidden field), dropping entries whose file is missing
        $product['images'] = existing_images($_POST['existingImages'] ?? '[]');
        $product['diagramImages'] = existing_images($_POST['existingDiagramImages'] ?? '[]');

        // Handle new uploads
        $uploadErrors = array_merge(
            process_uploads('newImages', $product, 'product', $product['images']),
            process_uploads('newDiagrams', $product, 'diagram', $product['diagramImages'])
        );

        save_product($product);
        if ($uploadErrors) {
            $_SESSION['flash_error'] = 'Tuote tallennettu, mutta kuvan lataus epäonnistui: ' . implode('; ', $uploadErrors);
            header('Location: index.php?action=product&id=' . urlencode($product['id']));
            exit;
        }
        header('Location: index.php');
        exit;

    case 'delete-product':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }
        verify_csrf($_POST['csrf_token'] ?? '');
        $id = $_POST['id'] ?? '';
        if ($id) {
            delete_product($id);
        }
        header('Location: index.php');
        exit;

    case 'category':
        $id = $_GET['id'] ?? '';
        $category = get_category($id);
        require __DIR__ . '/views/category-form.php';
        break;

    case 'save-category':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }
        verify_csrf($_POST['csrf_token'] ?? '');
        $category = [
            'id' => $_POST['id'] ?? '',
            'label' => CATEGORIES[$_POST['id'] ?? ''] ?? '',
            'intro' => trim($_POST['intro'] ?? ''),
            'homepageDescription' => trim($_POST['homepageDescription'] ?? ''),
            'metaDescription' => trim($_POST['metaDescription'] ?? ''),
            'sortOrder' => (int)($_POST['sortOrder'] ?? 0),
        ];
        save_category($category);
        header('Location: index.php');
        exit;

    case 'guide':
        require __DIR__ . '/views/guide.php';
        break;

    case 'publish':
        require_once __DIR__ . '/markdown.php';
        require_once __DIR__ . '/templates.php';
        $products = load_products();
        $categories = load_categories();
        $results = publish_site($products, $categories);
        require __DIR__ . '/views/publish.php';
        break;

    case 'remove-image':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }
        verify_csrf($_POST['csrf_token'] ?? '');
        $productId = $_POST['productId'] ?? '';
        $filename = $_POST['filename'] ?? '';
        $imageType = $_POST['imageType'] ?? 'product';

        if ($productId && $filename) {
            $product = get_product($productId);
            if ($product) {
                $key = $imageType === 'diagram' ? 'diagramImages' : 'images';
                $product[$key] = array_values(array_filter($product[$key], function($img) use ($filename) {
                    return $img !== $filename;
                }));
                save_product($product);
            }
        }
        header('Content-Type: application/json');
        echo json_encode(['ok' => true]);
        exit;

    default:
        header('Location: index.php');
        exit;
}
