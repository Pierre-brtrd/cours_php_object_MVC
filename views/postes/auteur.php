<section>
    <div class="container mt-4">
        <h1>Page des articles de <?= $auteur->nom ?></h1>
        <div class="d-flex flex-sm-wrap justify-content-between align-items-stretch">
            <?php foreach ($postes as $poste) : ?>
                <div class="col-md-4 p-2">
                    <div class="card">
                        <?php if ($poste->image) : ?>
                            <img src="/uploads/postes/<?= $poste->image; ?>" alt="<?= $poste->titre; ?>" loading="lazy" />
                        <?php endif; ?>
                        <div class="card-body">
                            <h2 class="card-title">
                                <?= $poste->titre ?>
                            </h2>
                            <p class="card-text"><?= $poste->description; ?></p>
                            <a href="/postes/details/<?= $poste->id ?>" class="btn btn-primary">En savoir plus</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>