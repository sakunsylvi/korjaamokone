<?php
$pageTitle = 'Julkaise sivusto';
$success = $results['success'] ?? false;
$generated = $results['generated'] ?? [];
$deleted = $results['deleted'] ?? [];
$errors = $results['errors'] ?? [];

ob_start();
?>

<div class="page-header">
    <h1>Julkaisun tulokset</h1>
</div>

<?php if ($success): ?>
    <div class="flash-success">Sivusto julkaistu onnistuneesti.</div>
<?php else: ?>
    <div class="flash-error">Julkaisussa tapahtui virheitä.</div>
<?php endif; ?>

<div class="publish-results">
    <?php if (!empty($errors)): ?>
        <div class="card">
            <h2>Virheet</h2>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li style="color:#d32f2f;"><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (!empty($generated)): ?>
        <div class="card">
            <h2>Luodut tiedostot (<?= count($generated) ?>)</h2>
            <ul>
                <?php foreach ($generated as $file): ?>
                    <li><?= htmlspecialchars($file) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (!empty($deleted)): ?>
        <div class="card">
            <h2>Poistetut tiedostot (<?= count($deleted) ?>)</h2>
            <ul>
                <?php foreach ($deleted as $file): ?>
                    <li><?= htmlspecialchars($file) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (empty($generated) && empty($deleted) && empty($errors)): ?>
        <div class="card">
            <p style="color:#888;">Ei muutoksia julkaistavaksi.</p>
        </div>
    <?php endif; ?>
</div>

<div style="margin-top:20px;">
    <a href="index.php" class="btn">Takaisin</a>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
