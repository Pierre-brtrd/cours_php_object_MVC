<section>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-6 m-auto">
                <h1 class="text-center">Création d'un poste</h1>
                <? if (!empty($_SESSION['error'])) : ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $_SESSION['error'];
                        unset($_SESSION['error']) ?>
                    </div>
                <? endif; ?>
                <?= $form; ?>
            </div>
        </div>
        <a href="/admin/postes" class="btn btn-primary mb-4">Retour à la liste</a>
    </div>
</section>