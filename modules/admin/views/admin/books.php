<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = 'Управление книгами';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="admin-books">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a('Добавить книгу', ['create-book'], ['class' => 'btn btn-success']) ?>
    </div>

    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'photo',
                'format' => 'raw',
                'value' => function ($model) {
                        return Html::img('@web/uploads/' . $model->photo, ['style' => 'max-width: 100px;']);
                    },
            ],
            'title',
            'author',
            'releaseYear',
            [
                'attribute' => 'categoryId',
                'value' => function ($model) {
                        return $model->category ? $model->category->title : '-';
                    },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
                'buttons' => [
                    'update' => function ($url, $model) {
                            return Html::a('<i class="fas fa-edit"></i>', ['update-book', 'id' => $model->id], [
                                'class' => 'btn btn-sm btn-primary',
                                'title' => 'Редактировать',
                            ]);
                        },
                    'delete' => function ($url, $model) {
                            return Html::a('<i class="fas fa-trash"></i>', ['delete-book', 'id' => $model->id], [
                                'class' => 'btn btn-sm btn-danger',
                                'data' => [
                                    'confirm' => 'Вы уверены, что хотите удалить эту книгу?',
                                    'method' => 'post',
                                ],
                                'title' => 'Удалить',
                            ]);
                        },
                ],
            ],
        ],
        'tableOptions' => ['class' => 'table table-striped table-bordered'],
    ]); ?>
    <?php Pjax::end(); ?>
</div>

<style>
    .admin-books {
        padding: 20px;
    }

    .table {
        background-color: #fff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
    }

    .btn-success:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }

    .btn-primary {
        margin-right: 5px;
    }

    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        line-height: 1.5;
        border-radius: 0.2rem;
    }

    .fas {
        margin-right: 0;
    }
</style>