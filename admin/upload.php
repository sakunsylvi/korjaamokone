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
            UPLOAD_ERR_INI_SIZE   => 'Tiedosto ylittää palvelimen kokorajan (' . ini_get('upload_max_filesize') . ')',
            UPLOAD_ERR_FORM_SIZE  => 'Tiedosto ylittää lomakkeen kokorajan',
            UPLOAD_ERR_PARTIAL    => 'Tiedosto ladattiin vain osittain',
            UPLOAD_ERR_NO_FILE    => 'Tiedostoa ei ladattu',
            UPLOAD_ERR_NO_TMP_DIR => 'Väliaikaiskansio puuttuu',
            UPLOAD_ERR_CANT_WRITE => 'Tiedoston kirjoitus epäonnistui',
            UPLOAD_ERR_EXTENSION  => 'PHP-laajennus esti latauksen',
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

    // Extension comes from the detected MIME type (lowercase, allowed, jpeg -> jpg)
    $ext = ALLOWED_TYPES[$mime];

    // Keep the uploaded file's own name (SEO), sanitised; fall back to <slug>-<N>
    $base = seo_basename($file['name'] ?? '');
    if ($base === '') {
        $prefix = $category . '-' . $subcategory . '-' . $modelCode;
        $base = $type === 'diagram'
            ? $prefix . '-dims-' . next_image_number(get_diagram_images($category, $subcategory, $modelCode))
            : $prefix . '-' . next_image_number(get_product_images($category, $subcategory, $modelCode));
    }
    $filename = unique_filename($base, $ext);

    // Move uploaded file
    $destination = IMAGES_DIR . '/' . $filename;
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        return 'ERROR: Tiedoston siirto epäonnistui';
    }

    chmod($destination, 0644);
    return $filename;
}

// Maximum length of the sanitised base name (without "-N" and extension)
define('MAX_BASENAME_LENGTH', 120);

/**
 * Sanitise an uploaded file's name into an SEO/URL-safe base name (no extension):
 * lowercase, ä/ö/å -> a/o/a, other diacritics stripped, anything outside
 * [a-z0-9-] -> '-', repeated '-' collapsed, '-' trimmed from the ends.
 * Returns '' when nothing usable is left.
 */
function seo_basename(string $original): string
{
    $name = pathinfo(basename(str_replace('\\', '/', $original)), PATHINFO_FILENAME);
    $name = strtr($name, ['ä' => 'a', 'Ä' => 'a', 'ö' => 'o', 'Ö' => 'o', 'å' => 'a', 'Å' => 'a']);

    // Strip remaining diacritics (é -> e, ü -> u ...)
    if (class_exists('Normalizer') && ($nfd = Normalizer::normalize($name, Normalizer::FORM_D)) !== false) {
        $name = preg_replace('/\p{Mn}+/u', '', $nfd) ?? $name;
    } elseif (function_exists('iconv')) {
        $name = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $name) ?: $name;
    }

    $name = strtolower($name);
    $name = trim(preg_replace('/[^a-z0-9]+/', '-', $name), '-');
    return rtrim(substr($name, 0, MAX_BASENAME_LENGTH), '-');
}

/**
 * Never overwrite: returns "<base>.<ext>", or "<base>-2.<ext>", "-3" ... if taken.
 */
function unique_filename(string $base, string $ext): string
{
    $filename = $base . '.' . $ext;
    for ($n = 2; file_exists(IMAGES_DIR . '/' . $filename); $n++) {
        $filename = $base . '-' . $n . '.' . $ext;
    }
    return $filename;
}

/**
 * Next free image number for the <slug>-<N> fallback name: highest existing -N suffix + 1.
 * (count + 1 would overwrite an existing file when the numbering has gaps.)
 */
function next_image_number(array $existing): int
{
    $max = 0;
    foreach ($existing as $name) {
        if (preg_match('/-(\d+)\.\w+$/', $name, $m)) {
            $max = max($max, (int)$m[1]);
        }
    }
    return $max + 1;
}

/**
 * Process a multi-file input (e.g. name="newImages[]").
 * Saved filenames are appended to $target; returns error messages ("name: reason").
 */
function process_uploads(string $field, array $product, string $type, array &$target): array
{
    $errors = [];
    $files = $_FILES[$field] ?? null;
    if (!$files || !is_array($files['name'])) {
        return $errors;
    }

    foreach ($files['name'] as $i => $name) {
        if ($files['error'][$i] === UPLOAD_ERR_NO_FILE) {
            continue; // empty file input
        }
        $result = handle_upload([
            'name'     => $name,
            'tmp_name' => $files['tmp_name'][$i],
            'size'     => $files['size'][$i],
            'error'    => $files['error'][$i],
        ], $product['category'], $product['subcategory'], $product['modelCode'], $type);

        if (strpos($result, 'ERROR: ') === 0) {
            $errors[] = $name . ': ' . substr($result, strlen('ERROR: '));
        } else {
            $target[] = $result;
        }
    }
    return $errors;
}

/**
 * Decode the existingImages JSON from the form and keep only plain filenames
 * that exist in IMAGES_DIR (drops stale or bogus entries).
 */
function existing_images(string $json): array
{
    $list = json_decode($json, true);
    if (!is_array($list)) {
        return [];
    }
    return array_values(array_filter($list, function ($img) {
        return is_string($img) && $img === basename($img) && is_file(IMAGES_DIR . '/' . $img);
    }));
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
 * Internal: find images matching a prefix pattern. Only used to number the
 * <slug>-<N> fallback name; a product's images are whatever its 'images' /
 * 'diagramImages' arrays list (any filename, in that order).
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
