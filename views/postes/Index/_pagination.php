<nav class="mt-4 d-flex justify-content-center <?= $totalPage == 1 ? 'd-none' : null; ?>" aria-label="Page navigation">
    <ul class="pagination">
        <li class="page-item <?= $page == 1 ? 'disabled' : null; ?>">
            <a class="page-link" href="<?= isset($admin) ? '/admin/postes' : '/postes'; ?>?page=<?= $page > 1 ? $page - 1 : '1'; ?>" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>
        <?php for ($i = 1; $i <= $totalPage; $i++) : ?>
            <li class="page-item <?= $page === $i ? 'active' : null; ?>">
                <a class="page-link" href="<?= isset($admin) ? '/admin/postes' : '/postes'; ?>?page=<?= $i; ?>"><?= $i; ?></a>
            </li>
        <?php endfor; ?>
        <li class="page-item <?= $page == $totalPage ? 'disabled' : null; ?>">
            <a class="page-link" href="<?= isset($admin) ? '/admin/postes' : '/postes'; ?>?page=<?= $page < $totalPage ? $page + 1 : '2'; ?>" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
    </ul>
</nav>