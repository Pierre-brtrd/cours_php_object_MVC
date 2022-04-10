<section>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-6 m-auto">
                <h1 class="text-center">Ajouter un poste</h1>
                <? if (!empty($_SESSION['error'])) : ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $_SESSION['error'];
                        unset($_SESSION['error']) ?>
                    </div>
                <? endif; ?>
                <?= $form; ?>
            </div>
        </div>
    </div>
</section>