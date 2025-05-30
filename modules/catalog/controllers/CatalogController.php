<?php

namespace app\modules\catalog\controllers;

use app\models\Catalog;
use app\models\Rating;
use app\models\Review;
use app\modules\catalog\models\CatalogSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use Yii;
use app\models\ReviewModeration;

/**
 * CatalogController implements the CRUD actions for Catalog model.
 */
class CatalogController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                        'rate' => ['POST'],
                        'review' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Catalog models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new CatalogSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Catalog model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Catalog model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
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
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Catalog model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
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
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Catalog model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Rate a book
     * @return array
     */
    public function actionRate()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (Yii::$app->user->isGuest) {
            return ['success' => false, 'message' => 'Необходимо авторизоваться'];
        }

        $data = json_decode(Yii::$app->request->getRawBody(), true);
        $bookId = $data['bookId'] ?? null;
        $value = $data['rating'] ?? null;

        if (!$bookId || !$value) {
            return ['success' => false, 'message' => 'Неверные параметры'];
        }

        // Проверяем существующую оценку
        $rating = Rating::find()
            ->where([
                'bookId' => $bookId,
                'userId' => Yii::$app->user->id
            ])
            ->one();

        if (!$rating) {
            $rating = new Rating([
                'bookId' => $bookId,
                'userId' => Yii::$app->user->id,
                'value' => $value,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            $rating->value = $value;
        }

        if ($rating->save()) {
            // Получаем статистику в зависимости от роли пользователя
            $isSpecialist = Yii::$app->user->identity && Yii::$app->user->identity->roleId == 1; // 1 - специалист
            $stats = Rating::getRatingStatsByRole($bookId, $isSpecialist ? 'specialist' : 'user');

            return [
                'success' => true,
                'message' => 'Рейтинг сохранен',
                'stats' => $stats,
                'isSpecialist' => $isSpecialist
            ];
        }

        return ['success' => false, 'message' => 'Ошибка сохранения рейтинга'];
    }

    /**
     * Add a review
     * @return array
     */
    public function actionReview()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (Yii::$app->user->isGuest) {
            return ['success' => false, 'message' => 'Необходимо авторизоваться'];
        }

        $bookId = Yii::$app->request->post('bookId');
        $reviewText = Yii::$app->request->post('review_text');
        $isNegative = Yii::$app->request->post('review_type') === 'negative' ? '1' : '0';

        if (!$bookId || !$reviewText) {
            return ['success' => false, 'message' => 'Неверные параметры'];
        }

        $review = new Review([
            'bookId' => $bookId,
            'userId' => Yii::$app->user->id,
            'reviewDescription' => $reviewText,
            'isNegative' => $isNegative
        ]);

        if ($review->save()) {
            return [
                'success' => true,
                'message' => 'Отзыв сохранен',
                'review' => [
                    'author' => Yii::$app->user->identity->nickname,
                    'date' => date('d.m.Y'),
                    'text' => $reviewText,
                    'isNegative' => $isNegative
                ]
            ];
        }

        return ['success' => false, 'message' => 'Ошибка сохранения отзыва'];
    }

    /**
     * Load more reviews
     * @return array
     */
    public function actionLoadMoreReviews()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $bookId = Yii::$app->request->get('bookId');
        $offset = Yii::$app->request->get('offset', 0);
        $limit = Yii::$app->request->get('limit', 5);
        $role = Yii::$app->request->get('role', 'user');

        $model = $this->findModel($bookId);
        $reviews = $model->getReviewsByRole($limit, $offset, $role);
        $totalCount = $model->getReviewsCountByRole($role);

        $html = '';
        foreach ($reviews as $review) {
            $html .= $this->renderPartial('_review_item', [
                'review' => $review
            ]);
        }

        return [
            'success' => true,
            'html' => $html,
            'hasMore' => ($offset + $limit) < $totalCount
        ];
    }

    /**
     * Finds the Catalog model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Catalog the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Catalog::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function getReviewsByRole($limit = 3, $offset = 0, $role = 'user')
    {
        return Review::find()
            ->alias('r')
            ->innerJoin('user u', 'r.userId = u.id')
            ->innerJoin('review_moderation rm', 'r.id = rm.reviewId')
            ->where(['r.bookId' => $this->id])
            ->andWhere(['u.roleId' => $role === 'specialist' ? 1 : 0])
            ->andWhere(['rm.status' => ReviewModeration::STATUS_APPROVED])
            ->orderBy(['r.created_at' => SORT_DESC])
            ->limit($limit)
            ->offset($offset)
            ->all();
    }

    public function getReviewsCountByRole($role = 'user')
    {
        return Review::find()
            ->alias('r')
            ->innerJoin('user u', 'r.userId = u.id')
            ->innerJoin('review_moderation rm', 'r.id = rm.reviewId')
            ->where(['r.bookId' => $this->id])
            ->andWhere(['u.roleId' => $role === 'specialist' ? 1 : 0])
            ->andWhere(['rm.status' => ReviewModeration::STATUS_APPROVED])
            ->count();
    }

    public function init()
    {
        parent::init();

        // Создаем директорию для изображений каталога, если она не существует
        $uploadPath = Yii::getAlias('@webroot/uploads/catalog');
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }
    }
}