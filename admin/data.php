<?php
/**
 * JSON-based CRUD with atomic writes
 */

require_once __DIR__ . '/config.php';

// --- Low-level helpers ---

/**
 * Read a JSON file and return its decoded contents as an array.
 * Returns an empty array if the file does not exist or is invalid.
 */
function read_json(string $path): array
{
    if (!is_file($path)) {
        return [];
    }
    $json = file_get_contents($path);
    if ($json === false) {
        return [];
    }
    $data = json_decode($json, true);
    return is_array($data) ? $data : [];
}

/**
 * Write data to a JSON file atomically.
 * Writes to a temp file in the same directory, then renames.
 * Returns true on success, false on failure.
 */
function write_json(string $path, array $data): bool
{
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $tmp = tempnam($dir, 'tmp_');
    if ($tmp === false) {
        return false;
    }

    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if (file_put_contents($tmp, $json, LOCK_EX) === false) {
        unlink($tmp);
        return false;
    }

    chmod($tmp, 0644);
    if (!rename($tmp, $path)) {
        unlink($tmp);
        return false;
    }

    return true;
}

// --- Products ---

/**
 * Load all products from data/products.json.
 */
function load_products(): array
{
    return read_json(DATA_DIR . '/products.json');
}

/**
 * Save the entire products array to data/products.json.
 */
function save_products(array $products): bool
{
    return write_json(DATA_DIR . '/products.json', $products);
}

/**
 * Find a single product by its id.
 * Returns the product array or null if not found.
 */
function get_product(string $id): ?array
{
    $products = load_products();
    foreach ($products as $product) {
        if (($product['id'] ?? '') === $id) {
            return $product;
        }
    }
    return null;
}

/**
 * Save (insert or update) a single product.
 * If a product with the same id exists, it is replaced.
 * Otherwise the product is appended.
 */
function save_product(array $product): bool
{
    $products = load_products();
    $found = false;

    foreach ($products as $i => $existing) {
        if (($existing['id'] ?? '') === ($product['id'] ?? '')) {
            $products[$i] = $product;
            $found = true;
            break;
        }
    }

    if (!$found) {
        $products[] = $product;
    }

    return save_products($products);
}

/**
 * Delete a product by id.
 * Returns true if the product was found and removed.
 */
function delete_product(string $id): bool
{
    $products = load_products();
    $filtered = array_values(array_filter($products, function ($p) use ($id) {
        return ($p['id'] ?? '') !== $id;
    }));

    if (count($filtered) === count($products)) {
        return false; // Not found
    }

    return save_products($filtered);
}

// --- Categories ---

/**
 * Load all categories from data/categories.json.
 */
function load_categories(): array
{
    return read_json(DATA_DIR . '/categories.json');
}

/**
 * Save the entire categories array to data/categories.json.
 */
function save_categories(array $categories): bool
{
    return write_json(DATA_DIR . '/categories.json', $categories);
}

/**
 * Find a single category by its id.
 * Returns the category array or null if not found.
 */
function get_category(string $id): ?array
{
    $categories = load_categories();
    foreach ($categories as $category) {
        if (($category['id'] ?? '') === $id) {
            return $category;
        }
    }
    return null;
}

/**
 * Save (insert or update) a single category.
 * If a category with the same id exists, it is replaced.
 * Otherwise the category is appended.
 */
function save_category(array $category): bool
{
    $categories = load_categories();
    $found = false;

    foreach ($categories as $i => $existing) {
        if (($existing['id'] ?? '') === ($category['id'] ?? '')) {
            $categories[$i] = $category;
            $found = true;
            break;
        }
    }

    if (!$found) {
        $categories[] = $category;
    }

    return save_categories($categories);
}
