<div class="row gy-4 mt-4">
    <?php foreach ($postes as $poste): ?>
        <div class="col-md-4">
            <div class="card <?= $poste->getActif() ? 'border-success' : 'border-danger'; ?>">
                <?php if ($poste->getImage()): ?>
                    <img class="card-img-top" src="/images/poste/<?= $poste->getImage(); ?>" loading="lazy">
                <?php endif; ?>
                <div class="card-body">
                    <h2 class="card-title"><?= $poste->getTitre(); ?></h2>
                    <em class="card-text">Id du poste : <?= $poste->getId(); ?></em>
                    <p class="text-muted"><?= $poste->getCreated_at()->format('Y/m/d'); ?></p>
                    <p class="card-text">
                        <?= strlen($poste->getDescription()) > 150 ? substr($poste->getDescription(), 0, 150) . '...' : $poste->getDescription(); ?>
                    </p>
                    <p class="text-actif-article <?= $poste->getActif() ? 'text-success' : 'text-danger'; ?> ">
                        <?= $poste->getActif() ? 'Actif' : 'Inactif'; ?>
                    </p>
                    <div class="form-check form-switch">
                        <input class="form-check-input enabled" type="checkbox"
                            id="switch-visibility-<?= $poste->getId(); ?>" <?= $poste->getActif() ? 'checked' : null; ?>
                            data-id="<?= $poste->getId(); ?>">
                        <label class="form-check-label" for="switch-visibility-<?= $poste->getId(); ?>">Visibilté</label>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="/admin/poste/edit/<?= $poste->getId(); ?>" class="btn btn-warning">Modifier</a>
                        <form action="/admin/deletePoste" method="POST"
                            onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?')">
                            <input type="hidden" name="id" value="<?= $poste->getId(); ?>">
                            <input type="hidden" name="token" value="<?= $token; ?>">
                            <button type="submit" class="btn btn-danger">Supprimer</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php include_once ROOT . '/Views/postes/index/_pagination.php'; ?>