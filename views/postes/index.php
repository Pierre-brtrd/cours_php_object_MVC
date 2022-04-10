<section>
    <div class="container mt-4">
        <h1>Liste des annonces</h1>

        <div class="d-flex flex-sm-wrap justify-content-between align-items-stretch">
            <?php foreach ($postes as $poste) : ?>
                <div class="col-md-4 p-2">
                    <div class="card">
                        <h2 class="card-header">
                            <?= $poste->titre; ?>
                        </h2>
                        <div class="card-body">
                            <p class="card-text"><?= $poste->description; ?></p>
                            <a href="/postes/details/<?= $poste->id; ?>" class="btn btn-primary">En savoir plus</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>