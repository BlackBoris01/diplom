<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Catalog;
use app\models\User;
use app\models\RoleRequest;
use app\models\Review;
use app\models\ReviewModeration;
use yii\data\ActiveDataProvider;
use yii\web\UploadedFile;

class AdminController extends Controller
{
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                        'approve-role-request' => ['POST'],
                        'reject-role-request' => ['POST'],
                        'revision-role-request' => ['POST'],
                        'approve-review' => ['POST'],
                        'reject-review' => ['POST'],
                        'revision-review' => ['POST'],
                    ],
                ],
            ]
        );
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    // Управление книгами
    public function actionBooks()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Catalog::find(),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('books', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreateBook()
    {
        $model = new Catalog();

        if ($this->request->isPost) {
            $model->load($this->request->post());

            // Загрузка файла
            $file = UploadedFile::getInstance($model, 'photo');
            if ($file) {
                $fileName = 'book_' . time() . '.' . $file->extension;
                $filePath = Yii::getAlias('@webroot/uploads/') . $fileName;

                if ($file->saveAs($filePath)) {
                    $model->photo = $fileName;
                }
            }

            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Книга успешно создана');
                return $this->redirect(['books']);
            }
        }

        return $this->render('create-book', [
            'model' => $model,
        ]);
    }

    public function actionUpdateBook($id)
    {
        $model = $this->findBook($id);
        $oldPhoto = $model->photo;

        if ($this->request->isPost && $model->load($this->request->post())) {
            // Загрузка файла
            $file = UploadedFile::getInstance($model, 'photo');
            if ($file) {
                $fileName = 'book_' . time() . '.' . $file->extension;
                $filePath = Yii::getAlias('@webroot/uploads/') . $fileName;

                if ($file->saveAs($filePath)) {
                    // Удаляем старое фото
                    if ($oldPhoto && file_exists(Yii::getAlias('@webroot/uploads/') . $oldPhoto)) {
                        unlink(Yii::getAlias('@webroot/uploads/') . $oldPhoto);
                    }
                    $model->photo = $fileName;
                }
            } else {
                $model->photo = $oldPhoto;
            }

            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Книга успешно обновлена');
                return $this->redirect(['books']);
            }
        }

        return $this->render('update-book', [
            'model' => $model,
        ]);
    }

    public function actionDeleteBook($id)
    {
        $this->findBook($id)->delete();
        return $this->redirect(['books']);
    }

    // Управление пользователями
    public function actionUsers()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => User::find(),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('users', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreateUser()
    {
        $model = new User();
        $model->scenario = 'create';

        if ($this->request->isPost) {
            $model->load($this->request->post());
            $model->photoFile = UploadedFile::getInstance($model, 'photoFile');

            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Пользователь успешно создан');
                return $this->redirect(['users']);
            }
        }

        return $this->render('update-user', [
            'model' => $model,
        ]);
    }

    public function actionUpdateUser($id)
    {
        $model = $this->findUser($id);

        if ($this->request->isPost && $model->load($this->request->post())) {
            $model->photoFile = UploadedFile::getInstance($model, 'photoFile');

            // Если пароль не указан, удаляем его из модели
            if (empty($model->password)) {
                $model->password = null;
            }

            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Пользователь успешно обновлен');
                return $this->redirect(['users']);
            }
        }

        return $this->render('update-user', [
            'model' => $model,
        ]);
    }

    // Управление заявками на смену роли
    public function actionRoleRequests()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => RoleRequest::find()->with('user'),
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => ['createdAt' => SORT_DESC],
            ],
        ]);

        return $this->render('role-requests', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionApproveRoleRequest($id)
    {
        $request = $this->findRoleRequest($id);
        $request->status = RoleRequest::STATUS_APPROVED;
        $request->adminComment = Yii::$app->request->post('adminComment');

        if ($request->save()) {
            $user = $request->user;
            $user->roleId = 1; // Меняем роль на специалиста
            $user->save();
            Yii::$app->session->setFlash('success', 'Заявка одобрена');
        }

        return $this->redirect(['role-requests']);
    }

    public function actionRejectRoleRequest($id)
    {
        $request = $this->findRoleRequest($id);
        $request->status = RoleRequest::STATUS_REJECTED;
        $request->adminComment = Yii::$app->request->post('adminComment');

        if ($request->save()) {
            Yii::$app->session->setFlash('success', 'Заявка отклонена');
        }

        return $this->redirect(['role-requests']);
    }

    public function actionRevisionRoleRequest($id)
    {
        $request = $this->findRoleRequest($id);
        $request->status = RoleRequest::STATUS_REVISION;
        $request->adminComment = Yii::$app->request->post('adminComment');

        if ($request->save()) {
            Yii::$app->session->setFlash('success', 'Заявка отправлена на доработку');
        }

        return $this->redirect(['role-requests']);
    }

    // Управление отзывами
    public function actionReviews()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => ReviewModeration::find()
                ->with(['review.user', 'review.book'])
                ->where(['status' => ReviewModeration::STATUS_PENDING]),
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => ['createdAt' => SORT_DESC],
            ],
        ]);

        return $this->render('reviews', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionApproveReview($id)
    {
        $moderation = $this->findReviewModeration($id);
        $moderation->status = ReviewModeration::STATUS_APPROVED;
        $moderation->adminComment = Yii::$app->request->post('adminComment');

        if ($moderation->save()) {
            Yii::$app->session->setFlash('success', 'Отзыв одобрен');
        }

        return $this->redirect(['reviews']);
    }

    public function actionRejectReview($id)
    {
        $moderation = $this->findReviewModeration($id);
        $moderation->status = ReviewModeration::STATUS_REJECTED;
        $moderation->adminComment = Yii::$app->request->post('adminComment');

        if ($moderation->save()) {
            Yii::$app->session->setFlash('success', 'Отзыв отклонен');
        }

        return $this->redirect(['reviews']);
    }

    public function actionRevisionReview($id)
    {
        $moderation = $this->findReviewModeration($id);
        $moderation->status = ReviewModeration::STATUS_REVISION;
        $moderation->adminComment = Yii::$app->request->post('adminComment');

        if ($moderation->save()) {
            Yii::$app->session->setFlash('success', 'Отзыв отправлен на доработку');
        }

        return $this->redirect(['reviews']);
    }

    // Вспомогательные методы
    protected function findBook($id)
    {
        if (($model = Catalog::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Книга не найдена.');
    }

    protected function findUser($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Пользователь не найден.');
    }

    protected function findRoleRequest($id)
    {
        if (($model = RoleRequest::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Заявка не найдена.');
    }

    protected function findReviewModeration($id)
    {
        if (($model = ReviewModeration::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Модерация отзыва не найдена.');
    }
}