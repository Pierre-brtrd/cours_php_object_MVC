<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $meta['title'] ?? null; ?> | My app PHP Object</title>
    <?php if (isset($meta)): ?>
        <?php foreach ($meta as $name => $content): ?>
            <meta name="<?= $name; ?>" content="<?= $content; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    <meta name="og:site_name" content="My app PHP Object">
    <link rel="stylesheet" href="/styles/main.css">
    <link rel="shortcut icon" href="/favicon/favicon.ico" type="image/x-icon">
</head>

<body>
    <?php require_once 'Layout/header.php' ?>
    <main>
        <?php require_once 'Layout/message.php' ?>
        <?= $contenu ?>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz"
        crossorigin="anonymous"></script>
    <script src="/js/actif-postes.js" defer></script>
    <script src="/js/inputImage.js" defer></script>
</body>

</html>