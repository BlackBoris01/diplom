<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */

/** @var app\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Авторизация';
// $this->params['breadcrumbs'][] = $this->title;
?>

<div class="auth-page">
    <div class="auth-left">
        <div class="auth-logo">
            <?= Html::a(
                '<span class="logo-text">PSYCHO<span class="logo-highlight">BOOK</span></span>',
                ['/'],
                ['class' => 'logo-link']
            ) ?>
        </div>
    </div>

    <div class="auth-right">
        <div class="auth-form-container">
            <div class="floating-label">Вход в систему</div>

            <?php if (Yii::$app->session->hasFlash('success')): ?>
                <div class="alert alert-success">
                    <?= Yii::$app->session->getFlash('success') ?>
                </div>
            <?php endif; ?>

            <?php $form = ActiveForm::begin([
                'id' => 'login-form',
                'options' => ['class' => 'auth-form'],
                'fieldConfig' => [
                    'template' => "{input}\n{error}",
                    'inputOptions' => ['class' => 'form-control auth-input'],
                    'errorOptions' => ['class' => 'invalid-feedback'],
                ],
            ]); ?>

            <?= $form->field($model, 'login')->textInput(['placeholder' => 'Логин']) ?>
            <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Пароль']) ?>

            <div class="form-group">
                <?= Html::submitButton('войти', ['class' => 'btn btn-primary auth-submit']) ?>
            </div>

            <div class="auth-links">
                <span>Еще нет аккаунта?</span>
                <?= Html::a('Регистрация', ['/site/register'], ['class' => 'auth-link']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<style>
    .auth-page {
        display: flex;
        min-height: 100vh;
    }

    .auth-left {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #fff;
    }

    .auth-right {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
    }

    .auth-logo {
        text-align: center;
    }

    .logo-link {
        text-decoration: none;
        color: #000;
        transition: opacity 0.3s;
        display: inline-block;
    }

    .logo-link:hover {
        opacity: 0.8;
        color: #000;
    }

    .logo-text {
        font-size: 48px;
        font-weight: bold;
        letter-spacing: -1px;
    }

    .logo-highlight {
        color: #FFB800;
    }

    .auth-form-container {
        width: 100%;
        max-width: 400px;
        padding: 40px;
        position: relative;
    }

    .floating-label {
        position: absolute;
        top: -10px;
        left: 40px;
        background-color: #f8f9fa;
        padding: 0 10px;
        font-size: 24px;
        color: #333;
        animation: floatIn 0.5s ease-out;
    }

    @keyframes floatIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .auth-form {
        display: flex;
        flex-direction: column;
        gap: 20px;
        margin-top: 20px;
    }

    .auth-input {
        padding: 12px 15px;
        border: none;
        border-bottom: 1px solid #ddd;
        font-size: 16px;
        transition: border-color 0.3s;
        background-color: transparent;
    }

    .auth-input:focus {
        outline: none;
        border-color: #FFB800;
        box-shadow: none;
    }

    .auth-submit {
        width: 100%;
        padding: 12px;
        background-color: #FFB800 !important;
        border: none;
        border-radius: 6px;
        color: #000;
        font-size: 16px;
        cursor: pointer;
        margin-top: 10px;
        text-transform: lowercase;
    }

    .auth-submit:hover {
        background-color: #e6a600 !important;
        color: #000;
    }

    .auth-links {
        margin-top: 20px;
        font-size: 14px;
        text-align: center;
    }

    .auth-link {
        color: #FFB800;
        text-decoration: none;
        margin-left: 5px;
    }

    .auth-link:hover {
        color: #e6a600;
    }

    .invalid-feedback {
        display: block;
        margin-top: 5px;
        color: #dc3545;
        font-size: 14px;
        text-align: left;
    }

    .alert {
        margin-bottom: 20px;
        padding: 12px 15px;
        border-radius: 6px;
        text-align: left;
    }

    .alert-success {
        background-color: #f0fff4;
        border: 1px solid #28a745;
        color: #28a745;
    }

    @media (max-width: 768px) {
        .auth-page {
            flex-direction: column;
        }

        .auth-left {
            padding: 40px 20px;
        }

        .auth-right {
            padding: 0 20px 40px;
        }

        .auth-form-container {
            padding: 40px 0;
        }

        .floating-label {
            left: 0;
        }
    }
</style>