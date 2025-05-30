<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var app\models\Catalog $model */
?>

<div class="book-card">
    <div class="book-cover">
        <?php if ($model->photo): ?>
            <img src="<?= Yii::getAlias('@web') ?>/uploads/<?= Html::encode($model->photo) ?>"
                alt="<?= Html::encode($model->title) ?>">
        <?php else: ?>
            <div class="no-cover">Нет обложки</div>
        <?php endif; ?>
    </div>

    <div class="book-info">
        <h3 class="book-title">
            <?= Html::a(Html::encode($model->title), ['/catalog/catalog/view', 'id' => $model->id]) ?>
        </h3>
        <p class="book-author"><?= Html::encode($model->author) ?></p>
        <div class="book-meta">
            <span class="year"><?= Html::encode($model->releaseYear) ?></span>
            <span class="pages"><?= $model->pages ?> стр.</span>
        </div>
        <div class="book-rating">
            <span class="rating-value"><?= number_format($model->getCurrentRating(), 1) ?></span>
            <div class="rating-stars">
                <?php
                $rating = $model->getCurrentRating();
                for ($i = 1; $i <= 5; $i++) {
                    if ($i <= $rating) {
                        echo '<i class="fas fa-star"></i>';
                    } elseif ($i - 0.5 <= $rating) {
                        echo '<i class="fas fa-star-half-alt"></i>';
                    } else {
                        echo '<i class="far fa-star"></i>';
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>

<style>
    .book-card {
        height: 100%;
        display: flex;
        flex-direction: column;
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .book-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .book-cover {
        position: relative;
        padding-top: 140%;
        background: #f0f0f0;
    }

    .book-cover img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .no-cover {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #999;
        font-size: 14px;
        text-align: center;
        padding: 20px;
    }

    .book-info {
        padding: 20px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .book-title {
        font-size: 18px;
        margin: 0 0 10px;
        line-height: 1.3;
    }

    .book-title a {
        color: #333;
        text-decoration: none;
        transition: color 0.2s;
    }

    .book-title a:hover {
        color: #FFB800;
    }

    .book-author {
        color: #666;
        font-size: 14px;
        margin: 0 0 15px;
    }

    .book-meta {
        font-size: 13px;
        color: #999;
        margin-bottom: 15px;
    }

    .book-meta span:not(:last-child)::after {
        content: "•";
        margin: 0 8px;
    }

    .book-rating {
        margin-top: auto;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .rating-value {
        font-size: 20px;
        font-weight: bold;
        color: #FFB800;
    }

    .rating-stars {
        color: #FFB800;
        font-size: 16px;
    }

    .rating-stars i {
        margin-right: 3px;
    }

    @media (max-width: 1200px) {
        .book-info {
            padding: 15px;
        }

        .book-title {
            font-size: 16px;
        }
    }

    @media (max-width: 768px) {
        .book-cover {
            padding-top: 130%;
        }

        .book-info {
            padding: 12px;
        }

        .book-title {
            font-size: 14px;
            margin-bottom: 8px;
        }

        .book-author {
            font-size: 12px;
            margin-bottom: 10px;
        }

        .book-meta {
            font-size: 11px;
            margin-bottom: 10px;
        }

        .rating-value {
            font-size: 16px;
        }

        .rating-stars {
            font-size: 14px;
        }
    }
</style>