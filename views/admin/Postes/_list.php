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
                    <div class="row">
                        <div class="col-md-6">
                            <a href="/postes/modifier/<?= $poste->id; ?>" class="btn btn-warning">Modifier</a>
                        </div>
                        <div class="col-md-6">
                            <form action="/admin/deletePoste" method="POST" onsubmit="return confirm('Êtes-vous vraiment sûr de vouloir supprimer ce poste ?')">
                                <input type="hidden" name="token" value="<?= $token; ?>">
                                <input type="hidden" name="id" value="<?= $poste->id; ?>">
                                <button type="submit" class="btn btn-danger">Supprimer</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>