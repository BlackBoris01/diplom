<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\ArrayHelper;
use app\models\Category;
use app\models\Catalog;

/** @var yii\web\View $this */
/** @var app\modules\catalog\models\CatalogSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="catalog-search">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1,
            'class' => 'filter-form'
        ],
    ]); ?>

    <div class="filter-column">
        <div class="filter-group">
            <?= $form->field($model, 'title')->dropDownList(
                ArrayHelper::map(Catalog::find()->select('title')->distinct()->all(), 'title', 'title'),
                ['prompt' => 'Выберите название']
            ) ?>
        </div>

        <div class="filter-group">
            <?= $form->field($model, 'author')->dropDownList(
                ArrayHelper::map(Catalog::find()->select('author')->distinct()->all(), 'author', 'author'),
                ['prompt' => 'Выберите автора']
            ) ?>
        </div>

        <div class="filter-group">
            <?= $form->field($model, 'categoryId')->dropDownList(
                ArrayHelper::map(Category::find()->all(), 'id', 'title'),
                ['prompt' => 'Выберите категорию']
            ) ?>
        </div>

        <div class="filter-group">
            <?= $form->field($model, 'minYear')->dropDownList(
                array_combine(range(date('Y'), 1900), range(date('Y'), 1900)),
                ['prompt' => 'От года']
            ) ?>
        </div>

        <div class="filter-group">
            <?= $form->field($model, 'maxYear')->dropDownList(
                array_combine(range(date('Y'), 1900), range(date('Y'), 1900)),
                ['prompt' => 'До года']
            ) ?>
        </div>

        <div class="filter-group">
            <div class="form-group">
                <?= Html::submitButton('Применить', ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Сбросить', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>