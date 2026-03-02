<?php
/**
 * Image upload and management
 */

require_once __DIR__ . '/config.php';

// Allowed MIME types and their file extensions
define('ALLOWED_TYPES', [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/gif'  => 'gif',
    'image/webp' => 'webp',
]);

// Maximum upload size: 5 MB
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024);

/**
 * Handle an uploaded file.
 *
 * @param array  $file        The $_FILES entry (e.g. $_FILES['image'])
 * @param string $category    Category slug (e.g. 'pilarinostimet')
 * @param string $subcategory Subcategory slug (e.g. 'kaksipilarinostin')
 * @param string $modelCode   Model code (e.g. 'htl-3940d')
 * @param string $type        'product' for product images, 'diagram' for dimension diagrams
 *
 * @return string Filename on success, or an error message prefixed with "ERROR: "
 */
function handle_upload(array $file, string $category, string $subcategory, string $modelCode, string $type = 'product'): string
{
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $messages = [
            UPLOAD_ERR_INI_SIZE   => 'Tiedosto ylittaa palvelimen kokorajan',
            UPLOAD_ERR_FORM_SIZE  => 'Tiedosto ylittaa lomakkeen kokorajan',
            UPLOAD_ERR_PARTIAL    => 'Tiedosto ladattiin vain osittain',
            UPLOAD_ERR_NO_FILE    => 'Tiedostoa ei ladattu',
            UPLOAD_ERR_NO_TMP_DIR => 'Vailiaikaiskansio puuttuu',
            UPLOAD_ERR_CANT_WRITE => 'Tiedoston kirjoitus epaonnistui',
        ];
        return 'ERROR: ' . ($messages[$file['error']] ?? 'Tuntematon latausvirhe');
    }

    // Check file size
    if ($file['size'] > MAX_UPLOAD_SIZE) {
        return 'ERROR: Tiedosto on liian suuri (max 5 MB)';
    }

    // Validate MIME type using finfo (not relying on browser-reported type)
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!isset(ALLOWED_TYPES[$mime])) {
        return 'ERROR: Tiedostotyyppi ei ole sallittu (sallitut: JPG, PNG, GIF, WebP)';
    }

    $ext = ALLOWED_TYPES[$mime];

    // Build filename
    $prefix = $category . '-' . $subcategory . '-' . $modelCode;

    if ($type === 'diagram') {
        // Count existing diagram images to determine N
        $existing = get_diagram_images($category, $subcategory, $modelCode);
        $n = count($existing) + 1;
        $filename = $prefix . '-dims-' . $n . '.' . $ext;
    } else {
        // Count existing product images to determine N
        $existing = get_product_images($category, $subcategory, $modelCode);
        $n = count($existing) + 1;
        $filename = $prefix . '-' . $n . '.' . $ext;
    }

    // Move uploaded file
    $destination = IMAGES_DIR . '/' . $filename;
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        return 'ERROR: Tiedoston siirto epaonnistui';
    }

    chmod($destination, 0644);
    return $filename;
}

/**
 * Delete an image file from the images directory.
 * Returns true if deleted, false if file not found.
 */
function delete_image(string $filename): bool
{
    // Sanitize: only allow simple filenames (no path traversal)
    $filename = basename($filename);
    $path = IMAGES_DIR . '/' . $filename;

    if (is_file($path)) {
        return unlink($path);
    }

    return false;
}

/**
 * List product images for a given model.
 * Returns an array of filenames sorted alphabetically.
 */
function get_product_images(string $category, string $subcategory, string $modelCode): array
{
    $prefix = $category . '-' . $subcategory . '-' . $modelCode;
    return find_images($prefix, false);
}

/**
 * List diagram/dimension images for a given model.
 * Returns an array of filenames sorted alphabetically.
 */
function get_diagram_images(string $category, string $subcategory, string $modelCode): array
{
    $prefix = $category . '-' . $subcategory . '-' . $modelCode;
    return find_images($prefix, true);
}

/**
 * Internal: find images matching a prefix pattern.
 *
 * @param string $prefix  Filename prefix (e.g. 'pilarinostimet-kaksipilarinostin-htl-3940d')
 * @param bool   $diagram If true, match '-dims-N.ext'; if false, match '-N.ext' but NOT '-dims-'
 */
function find_images(string $prefix, bool $diagram): array
{
    if (!is_dir(IMAGES_DIR)) {
        return [];
    }

    $results = [];
    $dir = opendir(IMAGES_DIR);
    if ($dir === false) {
        return [];
    }

    while (($entry = readdir($dir)) !== false) {
        if ($entry[0] === '.') {
            continue;
        }

        // Must start with our prefix
        if (strpos($entry, $prefix) !== 0) {
            continue;
        }

        // The part after the prefix
        $suffix = substr($entry, strlen($prefix));

        if ($diagram) {
            // Match: -dims-N.ext
            if (preg_match('/^-dims-\d+\.\w+$/', $suffix)) {
                $results[] = $entry;
            }
        } else {
            // Match: -N.ext but NOT -dims-
            if (preg_match('/^-\d+\.\w+$/', $suffix)) {
                $results[] = $entry;
            }
        }
    }

    closedir($dir);
    sort($results);
    return $results;
}
