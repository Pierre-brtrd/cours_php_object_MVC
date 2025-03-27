<h1><?= $poste->getTitre(); ?></h1>
<em><strong>Date:</strong> <?= $poste->getCreated_at()->format('Y/m/d'); ?></em>
<p><strong>Auteur:</strong> <?= $poste->getUser()?->getFullName(); ?></p>