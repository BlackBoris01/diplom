<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Rating;
use app\modules\catalog\assets\CatalogViewAsset;

/** @var yii\web\View $this */
/** @var app\models\Catalog $model */

$this->title = $model->title;
\yii\web\YiiAsset::register($this);
CatalogViewAsset::register($this);

$this->registerJs("window.bookId = " . $model->id . ";");
?>
<div class="catalog-view-card">
    <div class="catalog-view-back">
        <?= Html::a('← Вернуться назад', ['index'], ['class' => 'btn btn-outline-secondary catalog-back-btn']) ?>
    </div>
    <div class="catalog-view-header">
        <div class="catalog-view-cover">
            <?= Html::img('@web/uploads/' . $model->photo, ['alt' => $model->title, 'class' => 'catalog-view-img']) ?>
        </div>
        <div class="catalog-view-info">
            <h1 class="catalog-view-title"><?= Html::encode($model->title) ?></h1>
            <div class="catalog-view-meta">
                <div><b>Автор</b> <span><?= Html::encode($model->author) ?></span></div>
                <div><b>Дата выхода</b> <span><?= Html::encode($model->releaseYear) ?></span></div>
                <div><b>Категория</b> <span><?= Html::encode($model->category->title ?? '-') ?></span></div>
            </div>
            <div class="catalog-view-desc-block">
                <b>Краткое описание</b>
                <div class="catalog-view-desc">
                    <?= Html::encode($model->description) ?>
                </div>
            </div>
        </div>
        <div class="catalog-view-fav">
            <span class="catalog-view-fav-icon">&#9825;</span>
        </div>
    </div>
    <div class="catalog-view-ratings">
        <?php
        $userStats = Rating::getRatingStatsByRole($model->id, 'user');
        $specialistStats = Rating::getRatingStatsByRole($model->id, 'specialist');
        ?>
        <div class="catalog-view-rating-col">
            <div class="catalog-view-rating-title">Рейтинг пользователей</div>
            <div class="catalog-view-rating-badge"><?= number_format($userStats['avgRating'], 2) ?></div>
            <div class="catalog-view-rating-count">Всего оценок: <?= $userStats['totalRatings'] ?></div>
            <div class="catalog-view-rating-bar">
                <?php if ($userStats['totalRatings'] > 0): ?>
                <div class="catalog-view-rating-bar-user" style="width:<?= $userStats['negativePercent'] ?>%"
                    title="Оценки 1-5">
                    <?= $userStats['negativeCount'] ?>
                </div>
                <div class="catalog-view-rating-bar-user catalog-view-rating-bar-user-green"
                    style="width:<?= $userStats['positivePercent'] ?>%" title="Оценки 6-10">
                    <?= $userStats['positiveCount'] ?>
                </div>
                <?php else: ?>
                <div class="catalog-view-rating-bar-user" style="width:50%">0</div>
                <div class="catalog-view-rating-bar-user catalog-view-rating-bar-user-green" style="width:50%">0</div>
                <?php endif; ?>
            </div>
        </div>
        <div class="catalog-view-rating-col">
            <div class="catalog-view-rating-title">Рейтинг Специалистов</div>
            <div class="catalog-view-rating-badge"><?= number_format($specialistStats['avgRating'], 2) ?></div>
            <div class="catalog-view-rating-count">Всего оценок: <?= $specialistStats['totalRatings'] ?></div>
            <div class="catalog-view-rating-bar">
                <?php if ($specialistStats['totalRatings'] > 0): ?>
                <div class="catalog-view-rating-bar-user" style="width:<?= $specialistStats['negativePercent'] ?>%"
                    title="Оценки 1-5">
                    <?= $specialistStats['negativeCount'] ?>
                </div>
                <div class="catalog-view-rating-bar-user catalog-view-rating-bar-user-green"
                    style="width:<?= $specialistStats['positivePercent'] ?>%" title="Оценки 6-10">
                    <?= $specialistStats['positiveCount'] ?>
                </div>
                <?php else: ?>
                <div class="catalog-view-rating-bar-user" style="width:50%">0</div>
                <div class="catalog-view-rating-bar-user catalog-view-rating-bar-user-green" style="width:50%">0</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if (!Yii::$app->user->isGuest): ?>
    <div class="catalog-view-rating-block">
        <div class="catalog-view-rating-label">Оценить книгу</div>
        <div class="catalog-view-stars" data-book-id="<?= $model->id ?>">
            <?php for ($i = 1; $i <= 10; $i++): ?>
            <span class="catalog-view-star<?= $i <= $model->getCurrentRating() ? ' active' : '' ?>">&#9733;</span>
            <?php endfor; ?>
            <span class="catalog-view-rating-value"><?= $model->getCurrentRating() ?>/10</span>
        </div>
    </div>
    <?php endif; ?>

    <div class="catalog-view-section">
        <a href="#review-form" class="review-toggle">
            Оставить рецензию
        </a>
        <?php if (!Yii::$app->user->isGuest): ?>
        <div id="review-form" class="review-form-container" style="display: none;">
            <form class="review-form" method="post" action="#">
                <input type="hidden" name="bookId" value="<?= $model->id ?>">
                <div class="review-form-row">
                    <label class="review-form-label">Тип рецензии:</label>
                    <label class="rew-type-label">
                        <input type="radio" name="review_type" value="positive">
                        <span class="review-type-icon review-type-icon-positive">&#10003;</span> Положительная
                    </label>
                    <label class="review-type-label">
                        <input type="radio" name="review_type" value="negative">
                        <span class="review-type-icon review-type-icon-negative">&#10007;</span> Отрицательная
                    </label>
                </div>
                <div class="review-form-row">
                    <textarea class="review-form-textarea" name="review_text" rows="4" placeholder="Содержание рецензии"
                        required></textarea>
                </div>
                <button type="submit" class="review-form-submit">Отправить</button>
            </form>
        </div>
        <?php else: ?>
        <div class="catalog-view-login-prompt">
            <a href="<?= \yii\helpers\Url::to(['/site/login']) ?>">Войдите</a>, чтобы оставить рецензию и оценить книгу
        </div>
        <?php endif; ?>
    </div>

    <div class="catalog-view-section">
        <h3 style="margin-bottom: 24px;">Рецензии пользователей</h3>
        <div class="reviews-list" id="user-reviews">
            <?php
            $reviewRole = 'user';
            $userReviews = $model->getReviewsByRole(3, 0, $reviewRole);
            $userReviewsCount = $model->getReviewsCountByRole($reviewRole);

            if (!empty($userReviews)): ?>
            <?php foreach ($userReviews as $review): ?>
            <div class="review-card <?= $review->isNegative ? 'review-card-red' : 'review-card-green' ?>">
                <div class="review-card-header">
                    <span
                        class="review-card-author"><?= Html::encode($review->user ? $review->user->nickname : 'Аноним') ?></span>
                    <span class="review-card-date"><?= Yii::$app->formatter->asDate($review->created_at) ?></span>
                </div>
                <div class="review-card-text">
                    <?= Html::encode($review->reviewDescription) ?>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if ($userReviewsCount > 3): ?>
            <div class="text-center">
                <a href="#" class="show-more-link" data-role="<?= $reviewRole ?>" data-offset="3" data-limit="5">
                    Показать еще
                </a>
            </div>
            <?php endif; ?>
            <?php else: ?>
            <div class="no-reviews-message">
                Пока нет ни одной рецензии. Будьте первым!
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="catalog-view-section">
        <h3 style="margin-bottom: 24px;">Рецензии специалистов</h3>
        <div class="reviews-list" id="specialist-reviews">
            <?php
            $reviewRole = 'specialist';
            $specialistReviews = $model->getReviewsByRole(3, 0, $reviewRole);
            $specialistReviewsCount = $model->getReviewsCountByRole($reviewRole);

            if (!empty($specialistReviews)): ?>
            <?php foreach ($specialistReviews as $review): ?>
            <div class="review-card <?= $review->isNegative ? 'review-card-red' : 'review-card-green' ?>">
                <div class="review-card-header">
                    <span
                        class="review-card-author"><?= Html::encode($review->user ? $review->user->nickname : 'Аноним') ?></span>
                    <span class="review-card-date"><?= Yii::$app->formatter->asDate($review->created_at) ?></span>
                </div>
                <div class="review-card-text">
                    <?= Html::encode($review->reviewDescription) ?>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if ($specialistReviewsCount > 3): ?>
            <div class="text-center">
                <a href="#" class="show-more-link" data-role="<?= $reviewRole ?>" data-offset="3" data-limit="5">
                    Показать еще
                </a>
            </div>
            <?php endif; ?>
            <?php else: ?>
            <div class="no-reviews-message">
                Пока нет ни одной рецензии от специалистов.
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>