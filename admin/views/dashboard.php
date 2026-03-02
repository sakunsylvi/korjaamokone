<?php
$pageTitle = 'Tuotteet';
ob_start();
?>

<div class="page-header">
    <h1>Tuotteet</h1>
    <div class="page-actions">
        <button type="button" class="btn" onclick="document.getElementById('clone-modal').classList.add('active')">Luo uusi tuote</button>
        <a href="index.php?action=publish" class="btn btn-primary">Julkaise sivusto</a>
    </div>
</div>

<?php
// Group products by category
$grouped = [];
foreach (CATEGORIES as $catId => $catLabel) {
    $grouped[$catId] = [];
}
foreach ($products as $product) {
    $cat = $product['category'] ?? '';
    if (isset($grouped[$cat])) {
        $grouped[$cat][] = $product;
    }
}
// Sort each group by sortOrder
foreach ($grouped as $catId => &$catProducts) {
    usort($catProducts, function ($a, $b) {
        return ($a['sortOrder'] ?? 999) - ($b['sortOrder'] ?? 999);
    });
}
unset($catProducts);
?>

<table class="product-table">
    <thead>
        <tr>
            <th style="width:70px">Kuva</th>
            <th>Tuote</th>
            <th>Hinta</th>
            <th>Tila</th>
            <th>Toiminnot</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($grouped as $catId => $catProducts): ?>
        <tr>
            <td colspan="5" class="category-heading">
                <?= htmlspecialchars(CATEGORIES[$catId] ?? $catId) ?>
                <a href="index.php?action=category&id=<?= htmlspecialchars(urlencode($catId)) ?>">Muokkaa kategoriaa</a>
            </td>
        </tr>
        <?php if (empty($catProducts)): ?>
            <tr>
                <td colspan="5" style="color:#888;font-size:13px;padding:14px;">Ei tuotteita</td>
            </tr>
        <?php endif; ?>
        <?php foreach ($catProducts as $product): ?>
            <?php
            $images = $product['images'] ?? [];
            $firstImage = !empty($images) ? '../images/' . htmlspecialchars($images[0]) : '';
            $availability = $product['availability'] ?? 'available';
            ?>
            <tr>
                <td>
                    <?php if ($firstImage): ?>
                        <img class="thumb" src="<?= $firstImage ?>" alt="">
                    <?php else: ?>
                        <span style="display:inline-block;width:60px;height:45px;background:#eee;border-radius:3px;"></span>
                    <?php endif; ?>
                </td>
                <td>
                    <strong><?= htmlspecialchars($product['title'] ?? '') ?></strong>
                    <br><span style="font-size:12px;color:#888;"><?= htmlspecialchars($product['modelCode'] ?? '') ?></span>
                </td>
                <td><?= htmlspecialchars($product['price'] ?? '-') ?></td>
                <td>
                    <?php if ($availability === 'available'): ?>
                        <span class="badge badge-available">Saatavilla</span>
                    <?php elseif ($availability === 'unavailable'): ?>
                        <span class="badge badge-unavailable">Ei saatavilla</span>
                    <?php elseif ($availability === 'order'): ?>
                        <span class="badge badge-order">Tilaustuote</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="index.php?action=product&id=<?= htmlspecialchars(urlencode($product['id'] ?? '')) ?>" class="btn btn-small">Muokkaa</a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endforeach; ?>
    </tbody>
</table>

<!-- Clone Modal -->
<div id="clone-modal" class="clone-modal-overlay" onclick="if(event.target===this)this.classList.remove('active')">
    <div class="clone-modal">
        <h2>Luo uusi tuote</h2>
        <form method="get" action="index.php">
            <input type="hidden" name="action" value="product">
            <div class="form-group">
                <label class="form-label" for="clone-select">Kloonaa olemassa olevasta (valinnainen)</label>
                <select class="form-select" id="clone-select" name="clone">
                    <option value="">-- Tyhjasta --</option>
                    <?php foreach ($grouped as $catId => $catProducts): ?>
                        <?php if (!empty($catProducts)): ?>
                            <optgroup label="<?= htmlspecialchars(CATEGORIES[$catId] ?? $catId) ?>">
                                <?php foreach ($catProducts as $p): ?>
                                    <option value="<?= htmlspecialchars($p['id'] ?? '') ?>">
                                        <?= htmlspecialchars($p['title'] ?? $p['id'] ?? '') ?>
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="clone-modal-actions">
                <button type="button" class="btn" onclick="document.getElementById('clone-modal').classList.remove('active')">Peruuta</button>
                <button type="submit" class="btn btn-primary">Luo tuote</button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
