<?php

use app\models\Category;
use yii\bootstrap5\Html;
?>

<?= Html::a(
    Html::img('@web/uploads/' . $model->photo, ['class' => 'book-image']) .
    Html::tag(
        'div',
        Html::tag('h5', Html::encode($model->title), ['class' => 'book-title']) .
        Html::tag('p', Html::encode($model->author), ['class' => 'book-author']) .
        Html::tag('p', Html::encode($model->releaseYear), ['class' => 'book-year']),
        ['class' => 'book-info']
    ),
    ['view', 'id' => $model->id],
    ['class' => 'book-card d-block text-decoration-none text-dark']
) ?>