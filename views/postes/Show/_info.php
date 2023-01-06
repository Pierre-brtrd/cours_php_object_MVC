<h1><?= $poste->titre; ?></h1>
<em><strong>Date:</strong> <?= date('Y/m/d', strtotime($poste->created_at)); ?></em>
<p><strong>Auteur:</strong> <?= "$poste->prenom $poste->nom"; ?></p>