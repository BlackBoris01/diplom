<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">

<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>

<body class="d-flex flex-column h-100">
    <?php $this->beginBody() ?>

    <header id="header" class="fixed-top">
        <div class="wide-container">
            <!-- Логотип -->
            <a href="<?= Yii::$app->homeUrl ?>" class="logo-link">
                <span class="logo-text">PSYCHO<span class="logo-highlight">BOOK</span></span>
            </a>

            <!-- Поисковая строка -->
            <form class="search-form" action="<?= \yii\helpers\Url::to(['/search/index']) ?>" method="GET">
                <div class="search-container">
                    <input type="text" class="search-input" placeholder="книги, статьи, авторы" name="q">
                    <button type="submit" class="search-button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>

            <!-- Навигационные иконки -->
            <nav class="nav-icons">
                <a href="<?= Yii::$app->homeUrl ?>" class="nav-icon-link active">
                    <i class="fas fa-book"></i>
                    <span>Главная</span>
                </a>
                <a href="<?= \yii\helpers\Url::to(['/catalog']) ?>" class="nav-icon-link">
                    <i class="fas fa-list"></i>
                    <span>Каталог</span>
                </a>
                <a href="/favorites" class="nav-icon-link">
                    <i class="fas fa-heart"></i>
                    <span>Избранное</span>
                </a>
                <?php if (Yii::$app->user->isGuest): ?>
                    <a href="<?= \yii\helpers\Url::to(['/site/login']) ?>" class="nav-icon-link">
                        <i class="fas fa-user"></i>
                        <span>Профиль</span>
                    </a>
                    <a href="<?= \yii\helpers\Url::to(['/site/register']) ?>" class="nav-icon-link">
                        <i class="fas fa-user-plus"></i>
                        <span>Регистрация</span>
                    </a>
                <?php else: ?>
                    <a href="<?= \yii\helpers\Url::to(['/site/profile']) ?>" class="nav-icon-link">
                        <i class="fas fa-user"></i>
                        <span>Профиль</span>
                    </a>
                    <?= Html::beginForm(['/site/logout'], 'post', ['class' => 'nav-icon-link logout-form']) ?>
                    <button type="submit" class="nav-icon-link logout-button">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Выход</span>
                    </button>
                    <?= Html::endForm() ?>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main id="main" class="flex-shrink-0" role="main">
        <div class="wide-container">
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
            <?= Alert::widget() ?>
            <?= $content ?>
        </div>
    </main>

    <footer id="footer" class="mt-auto py-3 bg-light">
        <div class="wide-container">
            <div class="row text-muted">
                <div class="col-md-6 text-center text-md-start">&copy; PSYCHOBOOK <?= date('Y') ?></div>
                <div class="col-md-6 text-center text-md-end"><?= Yii::powered() ?></div>
            </div>
        </div>
    </footer>

    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>

<style>
    /* Широкий контейнер */
    .wide-container {
        width: 95%;
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 15px;
    }

    /* Стили для хедера */
    #header {
        background-color: #1C1C1C;
        border-bottom: 1px solid #2c2c2c;
    }

    #header .wide-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 15px;
    }

    /* Логотип */
    .logo-link {
        text-decoration: none;
        color: #fff;
        margin-right: 40px;
    }

    .logo-link:hover {
        text-decoration: none;
        color: #fff;
    }

    .logo-text {
        font-size: 24px;
        font-weight: bold;
        letter-spacing: -0.5px;
    }

    .logo-highlight {
        color: #FFB800;
    }

    /* Поисковая строка */
    .search-form {
        flex: 1;
        max-width: 500px;
        margin: 0 20px;
    }

    .search-container {
        display: flex;
        align-items: center;
        background-color: #2c2c2c;
        border-radius: 6px;
        padding: 8px 16px;
    }

    .search-input {
        flex: 1;
        background: none;
        border: none;
        color: #fff;
        font-size: 14px;
        outline: none;
        width: 100%;
    }

    .search-input::placeholder {
        color: #888;
    }

    .search-button {
        background: none;
        border: none;
        color: #888;
        cursor: pointer;
        padding: 0;
        margin-left: 10px;
    }

    .search-button:hover {
        color: #FFB800;
    }

    /* Навигационные иконки */
    .nav-icons {
        display: flex;
        gap: 32px;
        align-items: center;
    }

    .nav-icon-link {
        display: flex;
        flex-direction: column;
        align-items: center;
        color: #888;
        text-decoration: none;
        font-size: 12px;
        gap: 4px;
    }

    .nav-icon-link i {
        font-size: 18px;
    }

    .nav-icon-link:hover,
    .nav-icon-link.active {
        color: #FFB800;
        text-decoration: none;
    }

    .logout-form {
        margin: 0;
        padding: 0;
    }

    .logout-button {
        background: none;
        border: none;
        padding: 0;
        color: inherit;
        cursor: pointer;
    }

    /* Отступ для контента под фиксированным хедером */
    #main {
        margin-top: 65px;
        padding-top: 20px;
        min-height: calc(100vh - 65px - 60px);
        /* высота экрана минус хедер и футер */
    }

    /* Стили для футера */
    #footer {
        height: 60px;
        background-color: #1C1C1C !important;
        color: #888;
    }

    #footer .text-muted {
        color: #888 !important;
    }

    /* Адаптивность */
    @media (max-width: 768px) {
        .wide-container {
            width: 100%;
            padding: 0 10px;
        }

        #header .wide-container {
            flex-wrap: wrap;
            padding: 10px;
        }

        .logo-link {
            margin-right: 0;
            margin-bottom: 10px;
            width: 100%;
            text-align: center;
        }

        .search-form {
            order: 2;
            margin: 10px 0;
            max-width: none;
            width: 100%;
        }

        .nav-icons {
            order: 3;
            width: 100%;
            justify-content: space-around;
            gap: 10px;
        }

        .nav-icon-link span {
            display: none;
        }
    }
</style>