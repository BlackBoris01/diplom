<?php
use app\models\User;
use app\models\Role;
use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\RoleRequest;

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\helpers\VarDumper;

/** @var yii\web\View $this */
/** @var app\models\User $model */

$this->title = 'Профиль пользователя';
$this->params['breadcrumbs'][] = $this->title;

// Получаем заявку на роль пользователя, если она есть
$roleRequest = RoleRequest::find()
    ->where(['userId' => Yii::$app->user->id])
    ->andWhere(['status' => [RoleRequest::STATUS_PENDING, RoleRequest::STATUS_REVISION]])
    ->one();

$this->registerCss("
    .profile-card {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .profile-header {
        display: flex;
        align-items: center;
        margin-bottom: 30px;
    }
    .profile-photo {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 30px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .profile-info {
        flex: 1;
    }
    .profile-name {
        font-size: 24px;
        margin: 0 0 10px 0;
        color: #333;
    }
    .profile-role {
        font-size: 16px;
        color: #666;
        margin-bottom: 15px;
    }
    .profile-stats {
        display: flex;
        gap: 20px;
        margin-top: 10px;
    }
    .stat-item {
        text-align: center;
    }
    .stat-value {
        font-size: 20px;
        font-weight: bold;
        color: #333;
    }
    .stat-label {
        font-size: 14px;
        color: #666;
    }
    .role-request-status {
        margin-top: 20px;
        padding: 15px;
        border-radius: 8px;
        background: #f8f9fa;
    }
    .role-request-status.pending {
        background: #fff3cd;
        color: #856404;
    }
    .role-request-status.revision {
        background: #cce5ff;
        color: #004085;
    }
    .role-request-status.rejected {
        background: #f8d7da;
        color: #721c24;
    }
    .role-request-status.approved {
        background: #d4edda;
        color: #155724;
    }
    .role-request-toggle {
        cursor: pointer;
        color: #007bff;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        margin-bottom: 15px;
    }
    .role-request-toggle:hover {
        color: #0056b3;
        text-decoration: underline;
    }
    .role-request-toggle::after {
        content: '▼';
        margin-left: 5px;
        font-size: 12px;
        transition: transform 0.3s ease;
    }
    .role-request-toggle.collapsed::after {
        transform: rotate(-90deg);
    }
    .role-request-content {
        display: none;
        margin-top: 15px;
    }
    .role-request-content.show {
        display: block;
    }
    .status__value.clickable {
        cursor: pointer;
        color: #007bff;
        text-decoration: underline;
        display: inline-block;
    }
    .status__value.clickable:hover {
        color: #0056b3;
    }
    .role-request-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
    }
    .role-request-modal.show {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .role-request-modal-content {
        background: white;
        padding: 20px;
        border-radius: 8px;
        width: 90%;
        max-width: 500px;
        position: relative;
    }
    .role-request-modal-close {
        position: absolute;
        right: 15px;
        top: 15px;
        font-size: 20px;
        cursor: pointer;
        color: #666;
    }
    .role-request-modal-close:hover {
        color: #333;
    }
");

$this->registerJs("
    $(document).ready(function() {
        $('.role-request-toggle').on('click', function(e) {
            e.preventDefault();
            $(this).toggleClass('collapsed');
            $('.role-request-content').toggleClass('show');
        });

        // Обработчик клика по статусу
        $('.status__value.clickable').on('click', function() {
            $('.role-request-modal').addClass('show');
        });

        // Закрытие модального окна
        $('.role-request-modal-close').on('click', function() {
            $('.role-request-modal').removeClass('show');
        });

        // Закрытие по клику вне окна
        $('.role-request-modal').on('click', function(e) {
            if ($(e.target).hasClass('role-request-modal')) {
                $(this).removeClass('show');
            }
        });

        // Обработка отправки формы
        $('#role-request-form').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    if (response.success) {
                        showMessage('Заявка успешно отправлена', 'success');
                        $('.role-request-modal').removeClass('show');
                        location.reload();
                    } else {
                        showMessage(response.message || 'Ошибка при отправке заявки', 'error');
                    }
                },
                error: function() {
                    showMessage('Произошла ошибка при отправке заявки', 'error');
                }
            });
        });
    });

    function showMessage(text, type) {
        var messageDiv = $('<div>')
            .addClass('alert alert-' + (type === 'success' ? 'success' : 'danger'))
            .css({
                position: 'fixed',
                top: '20px',
                right: '20px',
                zIndex: 1001
            })
            .text(text);
        
        $('body').append(messageDiv);
        
        setTimeout(function() {
            messageDiv.remove();
        }, 3000);
    }
");
?>

<?
// // var_dump(Yii::$app->user)
// echo '<pre>'; 
// //var_dump(Yii::$app->user);
//  print_r(Yii::$app->user->identity); 
// echo '</pre>';

?>
<div class="site-profile">
    <div class="profile">
        <!-- Основной блок профиля -->
        <div class="profile__card__conainer">
            <div class="profile__card">
                <!-- Левая часть с фото -->
                <div class="card__left">
                    <div class="profile__photo-container">

                        <img class="profile__photo" src="<?= Yii::getAlias('@web/uploads/users/' . $model->photo) ?>"
                            alt="Фото профиля">

                        <?= Html::button('Сменить фото', ['class' => 'photo__change-btn']) ?>
                    </div>
                </div>

                <!-- Правая часть с информацией -->
                <div class="card__right">
                    <div class="profile__nickname"><?= Html::encode($model->nickname) ?></div>
                    <div class="profile__info">
                        <!-- Статус профиля -->
                        <div class="profile__status info__item">
                            <div class="status__title info__title">Статус профиля:</div>
                            <div class="status__value info__value <?= $model->roleId === 0 ? 'clickable' : '' ?>">
                                <?php
                                $roles = [
                                    0 => 'Пользователь',
                                    1 => 'Специалист',
                                    2 => 'Администратор'
                                ];
                                echo $roles[$model->roleId] ?? 'Неизвестная роль';
                                ?>
                            </div>
                        </div>

                        <!-- Дата регистрации -->
                        <div class="profile__registration info__item">
                            <div class="registration__title info__title">Дата регистрации:</div>
                            <div class="registration__value info__value">
                                <?= Yii::$app->formatter->asDate($model->registered) ?>
                            </div>
                        </div>

                        <!-- Количество отзывов -->
                        <div class="profile__reviews__count info__item">
                            <div class="review__title info__title">Рецензий:</div>
                            <div class="review__count info__value"><?= $model->getReviewsCount($model->id) ?></div>
                        </div>

                        <!-- Рейтинг пользователя -->
                        <div class="profile__rating info__item">
                            <div class="rating__title info__title">Рейтинг:</div>
                            <div class="rating__value info__value">
                                <?= number_format($model->getUserRating($model->id), 1) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php if ($roleRequest): ?>
            <div class="d-flex justify-content-center" style="margin-top: 30px;">
                <div class="card" style="min-width:350px;max-width:500px;width:100%;">
                    <div class="card-body">
                        <a href="#" class="role-request-toggle" style="display:block;text-align:center;">
                            Заявка на статус специалиста
                        </a>
                        <div class="role-request-content">
                            <div class="role-request-status <?= $roleRequest->status ?>">
                                <?php
                                $statusMessages = [
                                    RoleRequest::STATUS_PENDING => 'Ваша заявка на получение роли специалиста находится на рассмотрении.',
                                    RoleRequest::STATUS_REVISION => 'Ваша заявка требует доработки. Комментарий администратора: ' . $roleRequest->adminComment,
                                    RoleRequest::STATUS_REJECTED => 'Ваша заявка была отклонена. Причина: ' . $roleRequest->adminComment,
                                    RoleRequest::STATUS_APPROVED => 'Ваша заявка была одобрена!'
                                ];
                                echo $statusMessages[$roleRequest->status] ?? 'Неизвестный статус заявки';
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .profile-index {
        padding: 20px;
    }

    .card {
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .card-title {
        color: #333;
        font-weight: 600;
        margin-bottom: 20px;
    }

    .role-request-status {
        margin-top: 15px;
    }

    .role-request-text,
    .admin-comment {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 4px;
        margin: 10px 0;
    }

    .badge {
        font-size: 0.9em;
        padding: 5px 10px;
    }

    .bg-warning {
        background-color: #ffc107 !important;
        color: #000;
    }

    .bg-success {
        background-color: #28a745 !important;
    }

    .bg-danger {
        background-color: #dc3545 !important;
    }

    .bg-info {
        background-color: #17a2b8 !important;
        color: #fff;
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
<!-- Блок с отзывами -->
<div class="profile__reviews-section">
    <h2 class="reviews-title">Мои рецензии</h2>

    <?php if ($reviews = User::getUserReviews($model->id)): ?>
        <?php foreach ($reviews as $review): ?>
            <div class="profile__review__conainer">
                <div class="profile__review">
                    <div class="review__top">
                        <!-- Обложка книги -->
                        <div class="review__book-cover">
                            <img class="review__photo" src="<?= $review->book && $review->book->photo
                                ? Html::encode(Yii::getAlias('@web/uploads/' . $review->book->photo))
                                : Yii::getAlias('@web/files/book1.png') ?>"
                                alt="<?= $review->book ? 'Обложка книги' : 'Книга удалена' ?>">
                        </div>

                        <!-- Основная информация об отзыве -->
                        <div class="review__main">
                            <div class="book__title">
                                <?= $review->book ? Html::encode($review->book->title) : 'Книга удалена' ?>
                            </div>
                            <div class="author__name">
                                <?= $review->book ? Html::encode($review->book->author) : 'Автор неизвестен' ?>
                            </div>
                            <div class="review__container <?= $review->isNegative ? 'review-positive' : 'review-negative' ?>">
                                <div class="review">
                                    <div class="review__top">
                                        <div class="review__date">
                                            <?= Yii::$app->formatter->asDate($review->created_at) ?>
                                        </div>
                                        <div class="review__rating">
                                            <span class="stars"><?= str_repeat('★', $review->rating) ?></span>
                                            <span class="rating-value"><?= $review->rating ?>/10</span>
                                        </div>
                                    </div>
                                    <div class="review__value"><?= Html::encode($review->reviewDescription) ?></div>
                                </div>
                            </div>

                            <!-- Статус отзыва -->
                            <div class="review__status">
                                <div class="status__title">Статус:</div>
                                <div class="status__value <?= $review->status ?>">
                                    <?= User::getReviewStatusLabel($review->status) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="no-reviews">
            <p>У вас пока нет рецензий. Поделитесь своим мнением о прочитанных книгах!</p>
        </div>
    <?php endif; ?>
</div>

<!-- Модальное окно для заявки -->
<div class="role-request-modal">
    <div class="role-request-modal-content">
        <span class="role-request-modal-close">&times;</span>
        <h3>Заявка на получение статуса специалиста</h3>
        <form id="role-request-form" action="<?= \yii\helpers\Url::to(['/site/ajax-request-role']) ?>" method="post">
            <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>"
                value="<?= Yii::$app->request->csrfToken ?>">
            <div class="form-group">
                <label for="requestText">Почему вы хотите стать специалистом?</label>
                <textarea class="form-control" id="requestText" name="requestText" rows="6" required
                    placeholder="Расскажите о своем опыте и мотивации"></textarea>
            </div>
            <button type="submit" class="btn btn-primary" style="margin-top: 15px;">Отправить заявку</button>
        </form>
    </div>
</div>
</div>
</div>