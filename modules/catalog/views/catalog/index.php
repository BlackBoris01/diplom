<?php

use app\models\Catalog;
use yii\bootstrap5\LinkPager;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\widgets\ListView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var app\modules\catalog\models\CatalogSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Каталог';
$this->registerJsFile('@web/js/catalog.js', ['depends' => [\yii\web\JqueryAsset::class]]);
// $this->params['breadcrumbs'][] = $this->title;
?>
<div class="catalog-index">

    <div class="catalog-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <p>
            <?= Html::a('Добавить книгу', ['create'], ['class' => 'btn btn-success']) ?>
        </p>
    </div>

    <?php Pjax::begin(); ?>

    <div class="catalog-layout">
        <div class="catalog-filters">
            <?php
            echo $this->render('_search', ['model' => $searchModel]);
            ?>
        </div>

        <div class="catalog-content">
            <?= ListView::widget([
                'pager' => [
                    'class' => LinkPager::class,
                ],
                'dataProvider' => $dataProvider,
                'options' => [
                    'class' => 'catalog-items-container',

                ],
                'itemOptions' => [
                    'class' => 'book-card',

                ],
                'itemView' => '_item',

                'layout' => "
                
                <div class='catalog-wrapper'>
                    <div class='catalog-summary'>{summary}</div>
                    <div class='catalog-items-grid'>
                        {items}
                    </div>
                    <div class='catalog-pager'>{pager}</div>
                </div>",
            ]) ?>
        </div>
    </div>

    <?php Pjax::end(); ?>
</div>