<?php

use yii\helpers\Html;

$this->title = 'Панель администратора';
?>

<div class="admin-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Управление книгами</h5>
                    <p class="card-text">Добавление, редактирование и удаление книг в каталоге.</p>
                    <?= Html::a('Перейти', ['books'], ['class' => 'btn btn-primary']) ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Управление пользователями</h5>
                    <p class="card-text">Просмотр и редактирование пользователей системы.</p>
                    <?= Html::a('Перейти', ['users'], ['class' => 'btn btn-primary']) ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Заявки на смену роли</h5>
                    <p class="card-text">Модерация заявок на получение статуса специалиста.</p>
                    <?= Html::a('Перейти', ['role-requests'], ['class' => 'btn btn-primary']) ?>
                </div>
            </div>
        </div>

        <div class="col-md-4 mt-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Модерация отзывов</h5>
                    <p class="card-text">Проверка и публикация отзывов пользователей.</p>
                    <?= Html::a('Перейти', ['reviews'], ['class' => 'btn btn-primary']) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .admin-index {
        padding: 20px;
    }

    .card {
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s;
    }

    .card:hover {
        transform: translateY(-5px);
    }

    .card-title {
        color: #333;
        font-weight: 600;
    }

    .card-text {
        color: #666;
        margin-bottom: 20px;
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #0056b3;
    }
</style>