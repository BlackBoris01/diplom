<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = 'Управление пользователями';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="admin-users">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'login',
            'email',
            'nickname',
            [
                'attribute' => 'roleId',
                'value' => function ($model) {
                        $roles = [
                            0 => 'Пользователь',
                            1 => 'Специалист',
                            2 => 'Администратор'
                        ];
                        return $roles[$model->roleId] ?? 'Неизвестно';
                    },
            ],
            'registered:datetime',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update}',
                'buttons' => [
                    'update' => function ($url, $model) {
                            return Html::a('<i class="fas fa-edit"></i>', ['update-user', 'id' => $model->id], [
                                'class' => 'btn btn-sm btn-primary',
                                'title' => 'Редактировать',
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
.admin-users {
    padding: 20px;
}

.table {
    background-color: #fff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
}

.btn-primary:hover {
    background-color: #0056b3;
    border-color: #0056b3;
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