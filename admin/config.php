<?php
/**
 * Admin panel configuration
 */

// Directory paths
define('ADMIN_DIR', __DIR__);
define('DATA_DIR', ADMIN_DIR . '/data');
define('SITE_DIR', dirname(ADMIN_DIR) . '/2026');
define('IMAGES_DIR', SITE_DIR . '/images');

// Auth credentials - single user
define('AUTH_USER', 'petri_jussila');
define('AUTH_PASS_HASH', '$2y$10$Z1nCHadt6jpCoTYelxQ4J.pYZKHPEll/iJtZCnxuczNZXccAFPGfy');

// Toast shown after saving content that still needs to be published
define('PUBLISH_REMINDER', 'Muista painaa julkaise lopuksi');

// Session config
define('SESSION_LIFETIME', 86400); // 24 hours

// Categories (ordered)
define('CATEGORIES', [
    'keventimet'     => 'Keventimet',
    'pilarinostimet' => 'Pilarinostimet',
    'rengaskoneet'   => 'Rengaskoneet',
    'saksinostimet'  => 'Saksinostimet',
]);
