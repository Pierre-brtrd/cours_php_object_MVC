<section class="container mt-4">
    <h1 class="text-center">Création d'un poste</h1>
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $_SESSION['error'];
            unset($_SESSION['error']) ?>
        </div>
    <?php endif; ?>
    <?= $form; ?>
    <a href="/admin/postes" class="btn btn-primary mt-4">Retour à la liste</a>
</section>