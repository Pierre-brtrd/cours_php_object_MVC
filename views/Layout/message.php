<?php if (!empty($_SESSION['message'])) : ?>
    <div class="container mt-4">
        <div class="alert alert-success" role="alert">
            <?php echo $_SESSION['message'];
            unset($_SESSION['message']) ?>
        </div>
    </div>
<?php endif; ?>
<?php if (!empty($_SESSION['error'])) : ?>
    <div class="container mt-4">
        <div class="alert alert-danger" role="alert">
            <?php echo $_SESSION['error'];
            unset($_SESSION['error']) ?>
        </div>
    </div>
<?php endif; ?>