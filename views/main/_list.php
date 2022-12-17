<div class="list-card mt-4">
    <?php foreach ($postes as $poste) : ?>
        <div class="card">
            <?php if ($poste->image) : ?>
                <img src="/uploads/postes/<?= $poste->image; ?>" alt="<?= $poste->titre; ?>" loading="lazy" />
            <?php endif; ?>
            <div class="card-body">
                <h2 class="card-title">
                    <?= $poste->titre; ?>
                </h2>
                <p class="card-text"><?= $poste->description; ?></p>
                <a href="/postes/details/<?= $poste->id; ?>" class="btn btn-primary">En savoir plus</a>
            </div>
        </div>
    <?php endforeach; ?>
</div>