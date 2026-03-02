<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kirjaudu — Korjaamokone.fi</title>
    <link rel="stylesheet" href="assets/admin.css">
</head>
<body class="login-page">
    <div class="login-card">
        <div class="login-title">Korjaamokone.fi &mdash; Hallinta</div>

        <?php if (!empty($error)): ?>
            <div class="flash-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" action="index.php?action=login">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">

            <div class="form-group">
                <label class="form-label" for="username">Kayttajanimi</label>
                <input class="form-input" type="text" id="username" name="username" autocomplete="username" required autofocus>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Salasana</label>
                <input class="form-input" type="password" id="password" name="password" autocomplete="current-password" required>
            </div>

            <button type="submit" class="btn btn-primary">Kirjaudu</button>
        </form>
    </div>
</body>
</html>
