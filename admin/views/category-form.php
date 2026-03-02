<?php
$catId = $category['id'] ?? '';
$catLabel = CATEGORIES[$catId] ?? $catId;
$pageTitle = 'Muokkaa: ' . $catLabel;

$intro = $category['intro'] ?? '';
$homepageDescription = $category['homepageDescription'] ?? '';
$metaDescription = $category['metaDescription'] ?? '';
$sortOrder = $category['sortOrder'] ?? 0;

ob_start();
?>

<div class="page-header">
    <h1><?= htmlspecialchars($catLabel) ?></h1>
</div>

<form method="post" action="index.php?action=save-category">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
    <input type="hidden" name="id" value="<?= htmlspecialchars($catId) ?>">

    <div class="card">
        <div class="form-group">
            <label class="form-label" for="intro">Esittelyteksti</label>
            <span class="form-hint">Nakyy kategoriasivun ylaosassa</span>
            <textarea class="form-textarea" id="intro" name="intro" rows="5"><?= htmlspecialchars($intro) ?></textarea>
        </div>

        <div class="form-group">
            <label class="form-label" for="homepageDescription">Etusivun kuvaus</label>
            <span class="form-hint">Nakyy etusivun kategoriakortissa</span>
            <textarea class="form-textarea" id="homepageDescription" name="homepageDescription" rows="4"><?= htmlspecialchars($homepageDescription) ?></textarea>
        </div>

        <div class="form-group">
            <label class="form-label" for="metaDescription">Meta-kuvaus</label>
            <span class="form-hint">Hakukoneoptimointi</span>
            <textarea class="form-textarea" id="metaDescription" name="metaDescription" rows="3"><?= htmlspecialchars($metaDescription) ?></textarea>
        </div>

        <div class="form-group">
            <label class="form-label" for="sortOrder">Jarjestys</label>
            <input class="form-input" type="number" id="sortOrder" name="sortOrder" value="<?= htmlspecialchars((string)$sortOrder) ?>" style="width:120px;">
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Tallenna</button>
        <a href="index.php" class="btn">Peruuta</a>
    </div>
</form>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
