<section>
    <div class="container mt-4">
        <h1>Liste des annonces</h1>

        <div class="d-flex flex-sm-wrap justify-content-between align-items-stretch">
            <?php foreach ($postes as $poste) : ?>
                <div class="col-md-4 p-2">
                    <div class="card <?= $poste->actif ? 'border-success' : 'border-danger' ?>">
                        <h2 class="card-header">
                            <?= $poste->titre; ?>
                        </h2>
                        <div class="card-body">
                            <em class="card-text">Id du poste : <?= $poste->id; ?></em>
                            <p class="card-text"><?= $poste->description; ?></p>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input enabled" type="checkbox" role="switch" id="flexSwitchCheckDefault<?= $poste->id; ?>" <?= $poste->actif ? 'checked' : '' ?> data-id="<?= $poste->id; ?>">
                                <label class="form-check-label" for="flexSwitchCheckDefault<?= $poste->id; ?>">Actif</label>
                            </div>
                            <a href="/postes/modifier/<?= $poste->id; ?>" class="btn btn-warning">Modifier</a>
                            <a href="/admin/deletePoste/<?= $poste->id; ?>" class="btn btn-danger">Supprimer</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <a href="/postes/ajouter" class="btn btn-primary mt-5">Créer un poste</a>
    </div>
</section>