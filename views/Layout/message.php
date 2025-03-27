<?php foreach ($_SESSION['message'] ?? [] as $type => $message): ?>
    <div class="alert alert-<?= $type ?>"><?= $message ?></div>
    <?php unset($_SESSION['message'][$type]) ?>
<?php endforeach; ?>