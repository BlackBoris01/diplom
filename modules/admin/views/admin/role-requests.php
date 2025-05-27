<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap5\Modal;

$this->title = 'Заявки на смену роли';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="admin-role-requests">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'user.nickname',
                'label' => 'Пользователь',
            ],
            'requestText:ntext',
            [
                'attribute' => 'status',
                'value' => function ($model) {
                        $statuses = [
                            'pending' => 'На рассмотрении',
                            'approved' => 'Одобрено',
                            'rejected' => 'Отклонено',
                            'revision' => 'На доработке'
                        ];
                        return $statuses[$model->status] ?? $model->status;
                    },
                'contentOptions' => function ($model) {
                        $colors = [
                            'pending' => 'warning',
                            'approved' => 'success',
                            'rejected' => 'danger',
                            'revision' => 'info'
                        ];
                        return ['class' => 'text-' . ($colors[$model->status] ?? 'secondary')];
                    }
            ],
            'createdAt:datetime',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{approve} {reject} {revision}',
                'buttons' => [
                    'approve' => function ($url, $model) {
                            if ($model->status === 'pending') {
                                return Html::button('<i class="fas fa-check"></i>', [
                                    'class' => 'btn btn-sm btn-success approve-btn',
                                    'data-id' => $model->id,
                                    'title' => 'Одобрить',
                                ]);
                            }
                            return '';
                        },
                    'reject' => function ($url, $model) {
                            if ($model->status === 'pending') {
                                return Html::button('<i class="fas fa-times"></i>', [
                                    'class' => 'btn btn-sm btn-danger reject-btn',
                                    'data-id' => $model->id,
                                    'title' => 'Отклонить',
                                ]);
                            }
                            return '';
                        },
                    'revision' => function ($url, $model) {
                            if ($model->status === 'pending') {
                                return Html::button('<i class="fas fa-edit"></i>', [
                                    'class' => 'btn btn-sm btn-info revision-btn',
                                    'data-id' => $model->id,
                                    'title' => 'Отправить на доработку',
                                ]);
                            }
                            return '';
                        },
                ],
            ],
        ],
        'tableOptions' => ['class' => 'table table-striped table-bordered'],
    ]); ?>
    <?php Pjax::end(); ?>
</div>

<?php
Modal::begin([
    'id' => 'modal',
    'title' => 'Комментарий',
    'size' => Modal::SIZE_LARGE,
]);

echo Html::beginForm(['#'], 'post', ['id' => 'modal-form']);
echo Html::textarea('adminComment', '', ['class' => 'form-control', 'rows' => 5, 'required' => true]);
echo Html::hiddenInput('requestId', '', ['id' => 'request-id']);
echo Html::hiddenInput('action', '', ['id' => 'action-type']);
echo '<div class="mt-3">';
echo Html::submitButton('Подтвердить', ['class' => 'btn btn-primary']);
echo '</div>';
echo Html::endForm();

Modal::end();
?>

<?php
$js = <<<JS
$('.approve-btn, .reject-btn, .revision-btn').on('click', function() {
    var id = $(this).data('id');
    var action = $(this).hasClass('approve-btn') ? 'approve' : 
                $(this).hasClass('reject-btn') ? 'reject' : 'revision';
    
    $('#request-id').val(id);
    $('#action-type').val(action);
    $('#modal').modal('show');
});

$('#modal-form').on('submit', function(e) {
    e.preventDefault();
    var id = $('#request-id').val();
    var action = $('#action-type').val();
    var comment = $('textarea[name="adminComment"]').val();
    
    $.post('/admin/admin/' + action + '-role-request', {
        id: id,
        adminComment: comment,
        _csrf: yii.getCsrfToken()
    }).done(function() {
        $('#modal').modal('hide');
        $.pjax.reload({container: '#pjax-grid'});
    });
});
JS;

$this->registerJs($js);
?>

<style>
.admin-role-requests {
    padding: 20px;
}

.table {
    background-color: #fff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    line-height: 1.5;
    border-radius: 0.2rem;
    margin: 0 2px;
}

.btn-success {
    background-color: #28a745;
    border-color: #28a745;
}

.btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
}

.btn-info {
    background-color: #17a2b8;
    border-color: #17a2b8;
    color: #fff;
}

.fas {
    margin-right: 0;
}

.modal-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.modal-footer {
    background-color: #f8f9fa;
    border-top: 1px solid #dee2e6;
}
</style>