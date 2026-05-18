<?php

    /**
     * @var View $view
     * @var string $title
     * @var string $content
     *
     * @var array $scripts
     * @var array $styles
     * @var string $root
     * */

    use App\View\View;

    $toast = $_SESSION['toast'] ?? '';
    unset($_SESSION['toast']);
?>

<?php
    $styles = array_merge($styles, ["static/css/main.css", "static/css/dark-theme.css", "static/css/search-suggestions.css"]);
    $view->render('layout/head', [
        'title' => $title,
        'styles' => $styles
    ]);
    ?>

<div class="container-fluid p-0 m-0 d-flex flex-column min-vh-100">

    <?= $content ?>

    <?php $view->render('partials/toast', [
            'toast' => $toast
    ])?>
</div>

<?php
if (isset($toast) && $toast != []) {
    $scripts = array_merge($scripts, ["static/js/main.js"]);
}

    $view->render('layout/foot', [
        'scripts' => array_merge(['static/js/search-bar.js'], $scripts)
    ]);
?>
