<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = isset($model->id) ? 'Редактирование пользователя' : 'Создание пользователя';
$this->params['breadcrumbs'][] = ['label' => 'Управление пользователями', 'url' => ['users']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerCss("
    .user-form {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .form-group {
        margin-bottom: 20px;
    }
    .help-block {
        font-size: 0.9em;
        color: #666;
        margin-top: 5px;
    }
    .user-photo {
        max-width: 200px;
        max-height: 200px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 10px;
    }
    .photo-preview {
        margin-top: 10px;
        display: none;
    }
    .photo-preview img {
        max-width: 200px;
        max-height: 200px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
");
?>

<div class="user-form">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'login')->textInput(['maxlength' => true, 'placeholder' => 'Введите логин']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'placeholder' => 'Введите email']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'nickname')->textInput(['maxlength' => true, 'placeholder' => 'Введите псевдоним']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'fullName')->textInput(['maxlength' => true, 'placeholder' => 'Введите ФИО']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'password')->passwordInput(['placeholder' => isset($model->id) ? 'Оставьте пустым, чтобы не менять' : 'Введите пароль'])->hint(isset($model->id) ? 'Оставьте поле пустым, если не хотите менять пароль' : 'Минимум 6 символов') ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'roleId')->dropDownList([
                0 => 'Пользователь',
                1 => 'Специалист',
                2 => 'Администратор'
            ], ['prompt' => 'Выберите роль']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?php if (isset($model->id) && $model->photoFile): ?>
            <div class="current-photo">
                <label class="control-label">Текущее фото</label>
                <?= Html::img('@web/uploads/users/' . $model->photoFile, ['class' => 'user-photo']) ?>
            </div>
            <?php endif; ?>

            <?= $form->field($model, 'photoFile')->fileInput([
                'accept' => 'image/*',
                'class' => 'form-control',
                'onchange' => 'previewImage(this)'
            ])->hint('Загрузите фото пользователя (JPG, PNG)') ?>

            <div id="photoPreview" class="photo-preview"></div>
        </div>
    </div>

    <div class="form-group text-center">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success btn-lg']) ?>
        <?= Html::a('Отмена', ['users'], ['class' => 'btn btn-default btn-lg']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
<?php
$js = <<<JS
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        
        reader.onload = function(e) {
            var preview = document.getElementById('photoPreview');
            preview.innerHTML = '<img src="' + e.target.result + '">';
            preview.style.display = 'block';
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}
JS;
$this->registerJs($js, \yii\web\View::POS_END);
?>