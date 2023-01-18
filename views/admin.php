<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($meta['title']) ? $meta['title'] : null; ?> | My app PHP Object</title>
    <?php if (isset($meta)) : ?>
        <?php foreach ($meta as $name => $content) : ?>
            <meta name="<?= $name; ?>" content="<?= $content; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    <meta name="og:site_name" content="My app PHP Object">
    <link rel="stylesheet" href="/styles/main.css">
    <link rel="shortcut icon" href="/favicon/favicon.ico" type="image/x-icon">
</head>

<body>
    <?php require_once 'Layout/header.php'; ?>
    <main>
        <?php require_once 'Layout/message.php' ?>
        <?= $contenu ?>
    </main>
    <script src="/js/actif-postes.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
</body>

</html>