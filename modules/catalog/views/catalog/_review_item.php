<?php
use yii\helpers\Html;

/** @var app\models\Review $review */
?>
<div class="review-card <?= $review->isNegative ? 'review-card-red' : 'review-card-green' ?>">
    <div class="review-card-header">
        <span class="review-card-author"><?= Html::encode($review->user->nickname) ?></span>
        <span class="review-card-date"><?= Yii::$app->formatter->asDate($review->created_at) ?></span>
    </div>
    <div class="review-card-text">
        <?= Html::encode($review->reviewDescription) ?>
    </div>
</div>