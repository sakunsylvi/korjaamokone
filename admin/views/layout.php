<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Hallinta') ?> &mdash; Korjaamokone.fi</title>
    <link rel="stylesheet" href="assets/admin.css">
</head>
<body>
    <div class="admin-toolbar">
        <a href="https://korjaamokoneet.fi/">korjaamokoneet.fi &rarr;</a>
    </div>
    <header class="admin-header">
        <div class="admin-header-inner">
            <a href="index.php" class="admin-logo">Korjaamokone.fi &mdash; Hallinta</a>
            <nav class="admin-nav">
                <a href="index.php">Tuotteet</a>
                <a href="index.php?action=publish">Julkaise</a>
                <a href="index.php?action=guide">Käyttöohje</a>
                <a href="index.php?action=logout">Kirjaudu ulos</a>
            </nav>
        </div>
    </header>
    <main class="admin-content">
        <?php if (!empty($_SESSION['flash_success'])): ?>
            <div class="flash-success"><?= htmlspecialchars($_SESSION['flash_success']) ?></div>
            <?php unset($_SESSION['flash_success']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['flash_error'])): ?>
            <div class="flash-error"><?= htmlspecialchars($_SESSION['flash_error']) ?></div>
            <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>

        <?= $content ?>
    </main>

    <?php if (!empty($_SESSION['flash_toast'])): ?>
        <div class="toast" id="toast" role="status" aria-live="polite" title="Sulje">
            <span><?= htmlspecialchars($_SESSION['flash_toast']) ?></span>
            <button type="button" class="toast-close" aria-label="Sulje ilmoitus">&times;</button>
        </div>
        <script>
        (function () {
            var t = document.getElementById('toast');
            var hide = function () { t.classList.add('toast-hidden'); setTimeout(function () { t.remove(); }, 300); };
            t.addEventListener('click', hide);
            setTimeout(hide, 6000);
        })();
        </script>
        <?php unset($_SESSION['flash_toast']); ?>
    <?php endif; ?>
</body>
</html>
