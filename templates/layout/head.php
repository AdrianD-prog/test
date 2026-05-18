<?php
    /**
     * @var string $root
     * @var string $title
     * @var array $styles
     */
?>

<!doctype html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/x-icon" href="<?= htmlspecialchars($root . "/static/images/logo/logo.png", ENT_QUOTES, 'UTF-8') ?>">
    <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
            rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
            crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
<?php foreach ($styles as $href) : ?>
    <link rel="stylesheet" href="<?= htmlspecialchars($root . "/" . $href, ENT_QUOTES, 'UTF-8') ?>">
<?php endforeach; ?>
    <title>Amazonix - <?= htmlspecialchars(($title ?? ''), ENT_QUOTES, 'UTF-8') ?></title>
</head>
<body>
