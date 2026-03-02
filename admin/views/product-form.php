<?php
$isEdit = !empty($product['id']);
$isClone = isset($_GET['clone']) && !$isEdit;
$pageTitle = $isEdit ? 'Muokkaa tuotetta' : 'Uusi tuote';

// Extract product fields with defaults
$id           = $product['id'] ?? '';
$title        = $product['title'] ?? '';
$category     = $product['category'] ?? '';
$subcategory  = $product['subcategory'] ?? '';
$modelCode    = $product['modelCode'] ?? '';
$price        = $product['price'] ?? '';
$originalPrice = $product['originalPrice'] ?? '';
$priceNote    = $product['priceNote'] ?? '';
$availability = $product['availability'] ?? 'available';
$specs        = $product['specs'] ?? [];
$specTable    = $product['specTable'] ?? [];
$description  = $product['description'] ?? '';
$metaDescription = $product['metaDescription'] ?? '';
$sortOrder    = $product['sortOrder'] ?? 0;
$images       = $product['images'] ?? [];
$diagrams     = $product['diagrams'] ?? [];

// When cloning, clear identity fields
if ($isClone) {
    $id = '';
    $modelCode = '';
    $title = '';
    $images = [];
    $diagrams = [];
}

ob_start();
?>

<div class="page-header">
    <h1><?= htmlspecialchars($pageTitle) ?></h1>
</div>

<form method="post" action="index.php?action=save-product" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
    <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
    <input type="hidden" name="existing_images" id="existing-images" value="<?= htmlspecialchars(json_encode($images)) ?>">
    <input type="hidden" name="existing_diagrams" id="existing-diagrams" value="<?= htmlspecialchars(json_encode($diagrams)) ?>">

    <div class="card">
        <h2>Perustiedot</h2>

        <div class="form-group">
            <label class="form-label" for="title">Tuotteen nimi</label>
            <input class="form-input" type="text" id="title" name="title" value="<?= htmlspecialchars($title) ?>" required>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="category">Kategoria</label>
                <select class="form-select" id="category" name="category" required>
                    <option value="">-- Valitse --</option>
                    <?php foreach (CATEGORIES as $catId => $catLabel): ?>
                        <option value="<?= htmlspecialchars($catId) ?>" <?= $category === $catId ? 'selected' : '' ?>>
                            <?= htmlspecialchars($catLabel) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="subcategory">Alatunniste</label>
                <input class="form-input" type="text" id="subcategory" name="subcategory" value="<?= htmlspecialchars($subcategory) ?>" placeholder="esim. kaksipilarinostin">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="modelCode">Mallikoodi</label>
            <input class="form-input" type="text" id="modelCode" name="modelCode" value="<?= htmlspecialchars($modelCode) ?>" placeholder="esim. htl-3940d" required>
            <div class="slug-preview" id="slug-preview">
                <?php if ($category && $subcategory && $modelCode): ?>
                    <?= htmlspecialchars($category . '-' . $subcategory . '-' . $modelCode . '.html') ?>
                <?php else: ?>
                    {kategoria}-{alatunniste}-{mallikoodi}.html
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="card">
        <h2>Hinnoittelu ja saatavuus</h2>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="price">Hinta</label>
                <input class="form-input" type="text" id="price" name="price" value="<?= htmlspecialchars($price) ?>" placeholder="esim. 2 790 &euro; sis. alv">
            </div>
            <div class="form-group">
                <label class="form-label" for="originalPrice">Alkuperainen hinta</label>
                <input class="form-input" type="text" id="originalPrice" name="originalPrice" value="<?= htmlspecialchars($originalPrice) ?>" placeholder="valinnainen, yliviivattu hinta">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="priceNote">Hintahuomio</label>
            <input class="form-input" type="text" id="priceNote" name="priceNote" value="<?= htmlspecialchars($priceNote) ?>" placeholder="esim. Hinta pyydettaessa">
        </div>

        <div class="form-group">
            <label class="form-label" for="availability">Saatavuus</label>
            <select class="form-select" id="availability" name="availability">
                <option value="available" <?= $availability === 'available' ? 'selected' : '' ?>>Saatavilla</option>
                <option value="unavailable" <?= $availability === 'unavailable' ? 'selected' : '' ?>>Ei saatavilla</option>
                <option value="order" <?= $availability === 'order' ? 'selected' : '' ?>>Tilaustuote</option>
            </select>
        </div>
    </div>

    <div class="card">
        <h2>Ominaisuudet listaukseen</h2>
        <div class="form-group">
            <label class="form-label" for="specs">Yksi ominaisuus per rivi (nakyy kategoriasivulla)</label>
            <textarea class="form-textarea" id="specs" name="specs" rows="6"><?= htmlspecialchars(implode("\n", $specs)) ?></textarea>
        </div>
    </div>

    <div class="card">
        <h2>Tekniset tiedot</h2>
        <p class="form-hint" style="margin-bottom:12px;">Ominaisuus-arvo -parit tuotesivun taulukkoon</p>

        <div class="spec-rows" id="spec-rows">
            <?php foreach ($specTable as $i => $row): ?>
                <div class="spec-row">
                    <input class="form-input" type="text" name="spec_labels[]" value="<?= htmlspecialchars($row['label'] ?? '') ?>" placeholder="Ominaisuus">
                    <input class="form-input" type="text" name="spec_values[]" value="<?= htmlspecialchars($row['value'] ?? '') ?>" placeholder="Arvo">
                    <button type="button" class="btn-remove" onclick="this.parentElement.remove()" title="Poista">X</button>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="btn btn-small" onclick="addSpecRow()">Lisaa rivi</button>
    </div>

    <div class="card">
        <h2>Kuvaus</h2>
        <div class="form-group">
            <label class="form-label" for="description">Kuvaus (Markdown)</label>
            <textarea class="form-textarea" id="description" name="description" rows="8"><?= htmlspecialchars($description) ?></textarea>
        </div>
    </div>

    <div class="card">
        <h2>Tuotekuvat</h2>

        <div class="image-previews" id="image-previews">
            <?php foreach ($images as $img): ?>
                <div class="image-preview" data-filename="<?= htmlspecialchars($img) ?>">
                    <img src="../images/<?= htmlspecialchars($img) ?>" alt="">
                    <button type="button" class="btn-delete-image" onclick="removeImage(this, 'images')" title="Poista">X</button>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="form-group">
            <label class="form-label">Lisaa uusia kuvia</label>
            <input class="form-input" type="file" name="new_images[]" multiple accept="image/*">
        </div>
    </div>

    <div class="card">
        <h2>Mittakuvat</h2>

        <div class="image-previews" id="diagram-previews">
            <?php foreach ($diagrams as $img): ?>
                <div class="image-preview" data-filename="<?= htmlspecialchars($img) ?>">
                    <img src="../images/<?= htmlspecialchars($img) ?>" alt="">
                    <button type="button" class="btn-delete-image" onclick="removeImage(this, 'diagrams')" title="Poista">X</button>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="form-group">
            <label class="form-label">Lisaa uusia mittakuvia</label>
            <input class="form-input" type="file" name="new_diagrams[]" multiple accept="image/*">
        </div>
    </div>

    <div class="card">
        <h2>SEO ja jarjestys</h2>

        <div class="form-group">
            <label class="form-label" for="metaDescription">Meta-kuvaus</label>
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
        <?php if ($isEdit): ?>
            <button type="button" class="btn btn-danger" onclick="confirmDelete()">Poista tuote</button>
        <?php endif; ?>
    </div>
</form>

<?php if ($isEdit): ?>
<form id="delete-form" method="post" action="index.php?action=delete-product" style="display:none;">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
    <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
</form>
<?php endif; ?>

<script>
// --- Slug preview ---
function updateSlugPreview() {
    var cat = document.getElementById('category').value || '{kategoria}';
    var sub = document.getElementById('subcategory').value || '{alatunniste}';
    var model = document.getElementById('modelCode').value || '{mallikoodi}';
    document.getElementById('slug-preview').textContent = cat + '-' + sub + '-' + model + '.html';
}

document.getElementById('category').addEventListener('change', updateSlugPreview);
document.getElementById('subcategory').addEventListener('input', updateSlugPreview);
document.getElementById('modelCode').addEventListener('input', updateSlugPreview);

// --- Dynamic spec table rows ---
function addSpecRow() {
    var container = document.getElementById('spec-rows');
    var row = document.createElement('div');
    row.className = 'spec-row';
    row.innerHTML =
        '<input class="form-input" type="text" name="spec_labels[]" placeholder="Ominaisuus">' +
        '<input class="form-input" type="text" name="spec_values[]" placeholder="Arvo">' +
        '<button type="button" class="btn-remove" onclick="this.parentElement.remove()" title="Poista">X</button>';
    container.appendChild(row);
}

// --- Image removal ---
function removeImage(btn, type) {
    var preview = btn.closest('.image-preview');
    var filename = preview.getAttribute('data-filename');
    preview.remove();

    // Update the hidden field
    var fieldId = type === 'diagrams' ? 'existing-diagrams' : 'existing-images';
    var field = document.getElementById(fieldId);
    var current = JSON.parse(field.value || '[]');
    current = current.filter(function(f) { return f !== filename; });
    field.value = JSON.stringify(current);
}

// --- Delete confirmation ---
function confirmDelete() {
    if (confirm('Haluatko varmasti poistaa taman tuotteen? Tata toimintoa ei voi peruuttaa.')) {
        document.getElementById('delete-form').submit();
    }
}
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
