<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Подать заявку на статус специалиста';
$this->params['breadcrumbs'][] = ['label' => 'Мой профиль', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="profile-request-role">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="card">
        <div class="card-body">
            <p class="text-muted">
                Пожалуйста, опишите, почему вы хотите получить статус специалиста.
                Расскажите о своем опыте, знаниях и мотивации.
            </p>

            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'requestText')->textarea([
                'rows' => 6,
                'placeholder' => 'Введите текст вашей заявки...'
            ])->label('Текст заявки') ?>

            <div class="form-group">
                <?= Html::submitButton('Отправить заявку', ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Отмена', ['index'], ['class' => 'btn btn-secondary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<style>
.profile-request-role {
    padding: 20px;
}

.card {
    margin-top: 20px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.card-body {
    padding: 20px;
}

.text-muted {
    color: #6c757d;
    margin-bottom: 20px;
}

.form-group {
    margin-top: 20px;
}

.btn {
    margin-right: 10px;
}

.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
}

.btn-primary:hover {
    background-color: #0056b3;
    border-color: #0056b3;
}

.btn-secondary {
    background-color: #6c757d;
    border-color: #6c757d;
}

.btn-secondary:hover {
    background-color: #5a6268;
    border-color: #545b62;
}

textarea.form-control {
    min-height: 150px;
    resize: vertical;
}
</style>