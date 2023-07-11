<section class="container mt-4">
    <h1 class="text-center">Modifier un utilisateur</h1>
    <? if (!empty($_SESSION['error'])) : ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $_SESSION['error'];
            unset($_SESSION['error']) ?>
        </div>
    <? endif; ?>
    <?= $form; ?>
    <a href="/admin/users" class="btn btn-primary mt-4">Retour à la liste</a>
</section>