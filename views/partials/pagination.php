<?php
/**
 * Pagination partial. Expects: $pages (int), $page (int), $baseUrl (string with
 * existing query params already encoded, {page} is replaced).
 */
if (!isset($pages) || $pages <= 1) {
    return;
}
$page = max(1, (int) ($page ?? 1));
?>
<nav aria-label="Page navigation" class="mt-4">
    <ul class="pagination pagination-sm justify-content-center mb-0">
        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= e(str_replace('{page}', (string) ($page - 1), $baseUrl)) ?>" aria-label="Previous">
                <i class="fa-solid fa-chevron-left"></i>
            </a>
        </li>
        <?php for ($i = 1; $i <= $pages; $i++): ?>
            <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                <a class="page-link" href="<?= e(str_replace('{page}', (string) $i, $baseUrl)) ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>
        <li class="page-item <?= $page >= $pages ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= e(str_replace('{page}', (string) ($page + 1), $baseUrl)) ?>" aria-label="Next">
                <i class="fa-solid fa-chevron-right"></i>
            </a>
        </li>
    </ul>
</nav>
