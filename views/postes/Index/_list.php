<div class="list-card mt-4">
    <?php foreach ($postes as $poste): ?>
        <div class="card">
            <?php if ($poste->getImage()): ?>
                <img src="/images/poste/<?= $poste->getImage(); ?>" alt="<?= $poste->getTitre(); ?>" loading="lazy" />
            <?php endif; ?>
            <div class="card-body">
                <h2 class="card-title">
                    <?= $poste->getTitre(); ?>
                </h2>
                <p class="card-text text-muted">
                    <a href="/postes/auteur/<?= $poste->getUserId() ?>">
                        <?= $poste->getUser()?->getFullName(); ?>
                    </a>
                </p>
                <p class="card-text">
                    <?= strlen($poste->getDescription()) > 150 ? substr($poste->getDescription(), 0, 150) . '...' : $poste->getDescription(); ?>
                </p>
                <a href="/postes/details/<?= $poste->getId(); ?>" class="btn btn-primary">En savoir plus</a>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php include_once '_pagination.php'; ?>