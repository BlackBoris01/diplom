<?php

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;
/** @var yii\web\View $this */
/** @var app\models\User $model */
/** @var ActiveForm $form */
?>
<div class="site-register">

    <?php $form = ActiveForm::begin(['id' => 'register-form']); ?>

    <?= $form->field($model, 'login') ?>
    <?= $form->field($model, 'nickname') ?>
    <?= $form->field($model, 'email') ?>
    <?= $form->field($model, 'fullName') ?>
    <?= $form->field($model, 'password')->passwordInput() ?>
    <div class="form-group">
        <?= Html::submitButton('Зарегестрироваться', ['class' => 'btn btn-primary']) ?>
        <div>Уже зарегстрированы? <?= Html::a('Вход', $url = '../site/login') ?></div>

    </div>
    <?php ActiveForm::end(); ?>

</div><!-- site-register -->