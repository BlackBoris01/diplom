<?php

use yii\helpers\Html;
use yii\widgets\ListView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var string $query */

$this->title = 'Поиск: ' . Html::encode($query);
?>

<div class="search-results">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php if ($dataProvider->getTotalCount() > 0): ?>
        <p class="results-count">Найдено результатов: <?= $dataProvider->getTotalCount() ?></p>

        <?= ListView::widget([
            'dataProvider' => $dataProvider,
            'itemView' => '_book',
            'layout' => "{items}\n{pager}",
            'options' => ['class' => 'books-grid'],
            'itemOptions' => ['class' => 'book-item'],
        ]) ?>
    <?php else: ?>
        <div class="no-results">
            <p>По вашему запросу ничего не найдено.</p>
            <p>Попробуйте изменить поисковый запрос или просмотреть наш <a href="/catalog">каталог</a>.</p>
        </div>
    <?php endif; ?>
</div>

<style>
    .search-results {
        padding: 20px 0;
        width: 100%;
    }

    .search-results h1 {
        font-size: 28px;
        margin-bottom: 20px;
        color: #333;
    }

    .results-count {
        color: #666;
        margin-bottom: 30px;
        font-size: 16px;
    }

    .books-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 30px;
        padding: 0;
        list-style: none;
        margin: 0;
    }

    .book-item {
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .book-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .no-results {
        text-align: center;
        padding: 60px 20px;
        background: #f8f9fa;
        border-radius: 8px;
        margin: 20px 0;
    }

    .no-results p {
        margin: 10px 0;
        color: #666;
        font-size: 16px;
    }

    .no-results a {
        color: #FFB800;
        text-decoration: none;
        font-weight: 500;
    }

    .no-results a:hover {
        text-decoration: underline;
    }

    @media (max-width: 1200px) {
        .books-grid {
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }
    }

    @media (max-width: 768px) {
        .search-results h1 {
            font-size: 24px;
        }

        .books-grid {
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 15px;
        }

        .results-count {
            font-size: 14px;
            margin-bottom: 20px;
        }
    }
</style>