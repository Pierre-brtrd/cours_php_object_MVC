<section>
    <div class="container mt-4">
        <h1>Page des articles de <?= $auteur->getNom() ?></h1>
        <div class="d-flex flex-sm-wrap justify-content-between align-items-stretch">
            <?php foreach ($postes as $poste) : ?>
                <?php if ($poste->getActif()) : ?>
                    <div class="col-md-4 p-2">
                        <div class="card">
                            <?php if ($poste->getImage()) : ?>
                                <img src="/images/poste/<?= $poste->getImage(); ?>" alt="<?= $poste->getTitre(); ?>" loading="lazy" />
                            <?php endif; ?>
                            <div class="card-body">
                                <h2 class="card-title">
                                    <?= $poste->getTitre() ?>
                                </h2>
                                <p class="card-text"><?= strlen($poste->getDescription()) > 150 ? substr($poste->getDescription(), 0, 150) . '...' : $poste->getDescription(); ?></p>
                                <a href="/postes/details/<?= $poste->getId() ?>" class="btn btn-primary">En savoir plus</a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>