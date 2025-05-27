<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Category;

/** @var yii\web\View $this */
/** @var app\models\Catalog $model */
/** @var yii\widgets\ActiveForm $form */

$this->title = 'Добавить книгу';
$this->params['breadcrumbs'][] = ['label' => 'Управление книгами', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerCss("
    .catalog-form {
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
    .preview-image {
        max-width: 200px;
        margin-top: 10px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
");
?>

<div class="catalog-form">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'title')->textInput(['maxlength' => true, 'placeholder' => 'Введите название книги']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'author')->textInput(['maxlength' => true, 'placeholder' => 'Введите автора']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'categoryId')->dropDownList(
                ArrayHelper::map(Category::find()->all(), 'id', 'title'),
                ['prompt' => 'Выберите категорию']
            ) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'releaseYear')->dropDownList(
                array_combine(range(date('Y'), 1750), range(date('Y'), 1750)),
                ['prompt' => 'Выберите год издания']
            ) ?>
        </div>
    </div>

    <?= $form->field($model, 'description')->textarea(['rows' => 4, 'placeholder' => 'Введите описание книги']) ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'pages')->textInput(['type' => 'number', 'placeholder' => 'Количество страниц']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'weight')->textInput(['type' => 'number', 'placeholder' => 'Вес книги в граммах']) ?>
        </div>
    </div>

    <?php
    if (!$model->isNewRecord && $model->photo) {
        echo '<div class="form-group">';
        echo '<label class="control-label">Текущее фото</label>';
        echo Html::img('@web/uploads/' . $model->photo, ['class' => 'preview-image']);
        echo '</div>';
    }
    ?>

    <?= $form->field($model, 'photo')->fileInput([
        'accept' => 'image/*',
        'class' => 'form-control',
        'onchange' => 'previewImage(this)'
    ])->hint('Загрузите фото книги (JPG, PNG)') ?>

    <div id="imagePreview"></div>

    <div class="form-group text-center">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success btn-lg']) ?>
        <?= Html::a('Отмена', ['index'], ['class' => 'btn btn-default btn-lg']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?php
$js = <<<JS
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        
        reader.onload = function(e) {
            var preview = document.getElementById('imagePreview');
            preview.innerHTML = '<img src="' + e.target.result + '" class="preview-image">';
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}
JS;
$this->registerJs($js, \yii\web\View::POS_END);
?>