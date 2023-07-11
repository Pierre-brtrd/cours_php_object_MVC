<div class="row gy-4 mt-4">
    <?php foreach ($postes as $poste) : ?>
        <div class="col-md-4">
            <div class="card <?= $poste->actif ? 'border-success' : 'border-danger'; ?>">
                <?php if ($poste->image) : ?>
                    <img class="card-img-top" src="/images/poste/<?= $poste->image; ?>" loading="lazy">
                <?php endif; ?>
                <div class="card-body">
                    <h2 class="card-title"><?= $poste->titre; ?></h2>
                    <em class="card-text">Id du poste : <?= $poste->id; ?></em>
                    <p class="text-muted"><?= date_format(new \DateTime($poste->created_at), 'Y/m/d'); ?></p>
                    <p class="card-text"><?= strlen($poste->description) > 150 ? substr($poste->description, 0, 150) . '...' : $poste->description; ?></p>
                    <p class="text-actif-article <?= $poste->actif ? 'text-success' : 'text-danger'; ?> "><?= $poste->actif ? 'Actif' : 'Inactif'; ?></p>
                    <div class="form-check form-switch">
                        <input class="form-check-input enabled" type="checkbox" id="switch-visibility-<?= $poste->id; ?>" <?= $poste->actif ? 'checked' : null; ?> data-id="<?= $poste->id; ?>">
                        <label class="form-check-label" for="switch-visibility-<?= $poste->id; ?>">Visibilté</label>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="/admin/poste/edit/<?= $poste->id; ?>" class="btn btn-warning">Modifier</a>
                        <form action="/admin/deletePoste" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?')">
                            <input type="hidden" name="id" value="<?= $poste->id; ?>">
                            <input type="hidden" name="token" value="<?= $token; ?>">
                            <button type="submit" class="btn btn-danger">Supprimer</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>