<div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-indicators">
        <?php foreach ($postes as $key => $poste): ?>
            <?php if ($key === array_key_first($postes)): ?>
                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="<?= $key; ?>" class="active"
                    aria-current="true" aria-label=" <?= $key; ?>"></button>
            <?php else: ?>
                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="<?= $key; ?>"
                    aria-label="Slide <?= $key; ?>"></button>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
    <div class="carousel-inner">
        <?php foreach ($postes as $key => $poste): ?>
            <div class="carousel-item <?= $key === array_key_first($postes) ? 'active' : '' ?>">
                <?php if ($poste->getImage()): ?>
                    <img src="/images/poste/<?= $poste->getImage(); ?>" class="img-carousel d-block"
                        alt="<?= $poste->getTitre(); ?>" loading="lazy" />
                <?php else: ?>
                    <img src="https://fakeimg.pl/1200x600" class="img-carousel d-block w-100">
                <?php endif; ?>
                <div class="carousel-caption d-none d-md-block">
                    <h2><?= $poste->getTitre(); ?></h2>
                    <p><?= strlen($poste->getDescription()) > 150 ? substr($poste->getDescription(), 0, 150) . '...' : $poste->getDescription(); ?>
                    </p>
                    <p class="card-text text-muted">
                        <?= $poste->getUser()?->getFullName(); ?>
                    </p>
                    <a href="/postes/details/<?= $poste->getId() ?>" class="btn btn-primary">En savoir plus</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators"
        data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators"
        data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>