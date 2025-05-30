<?php

namespace app\controllers;

use Symfony\Component\VarDumper\VarDumper;
use Yii;
use yii\bootstrap5\ActiveForm;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\RoleRequest;
use app\models\User;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        $this->layout = 'auth';
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            Yii::$app->session->setFlash('success', 'Вы успешно авторизовались! ❤', );
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */


    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }


    public function actionRegister()
    {
        $this->layout = 'auth';
        $model = new \app\models\User();
        $model->scenario = 'create';

        if ($model->load(Yii::$app->request->post())) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }

            if ($model->save()) {
                if (Yii::$app->user->login($model)) {
                    Yii::$app->session->setFlash('success', 'Пользователь успешно зарегистрирован');
                    return $this->redirect(['/']);
                }
            } else {
                // Добавляем ошибки в сессию для отображения пользователю
                foreach ($model->getErrors() as $attribute => $errors) {
                    foreach ($errors as $error) {
                        Yii::$app->session->addFlash('error', $error);
                    }
                }
            }
        }

        return $this->render('register', [
            'model' => $model,
        ]);
    }

    public function actionProfile()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }

        $model = Yii::$app->user->identity;
        return $this->render('profile', [
            'model' => $model
        ]);
    }

    public function actionAjaxRequestRole()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (Yii::$app->user->isGuest) {
            return ['success' => false, 'message' => 'Необходимо авторизоваться'];
        }

        if (Yii::$app->user->identity->roleId !== 0) {
            return ['success' => false, 'message' => 'Вы уже имеете статус специалиста или администратора'];
        }

        $requestText = Yii::$app->request->post('requestText');
        if (!$requestText) {
            return ['success' => false, 'message' => 'Текст заявки обязателен'];
        }

        // Проверка на существующую заявку
        $exists = RoleRequest::find()
            ->where(['userId' => Yii::$app->user->id])
            ->andWhere(['status' => [RoleRequest::STATUS_PENDING, RoleRequest::STATUS_REVISION]])
            ->exists();

        if ($exists) {
            return ['success' => false, 'message' => 'У вас уже есть активная заявка'];
        }

        $roleRequest = new RoleRequest();
        $roleRequest->userId = Yii::$app->user->id;
        $roleRequest->requestText = $requestText;
        $roleRequest->status = 'pending';
        $roleRequest->createdAt = date('Y-m-d H:i:s');

        if ($roleRequest->save()) {
            return ['success' => true, 'message' => 'Заявка успешно отправлена'];
        }

        return ['success' => false, 'message' => 'Ошибка при сохранении заявки'];
    }

    public function actionUpdatePhoto()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (Yii::$app->user->isGuest) {
            return ['success' => false, 'message' => 'Необходимо авторизоваться'];
        }

        $user = User::findOne(Yii::$app->user->id);
        if (!$user) {
            return ['success' => false, 'message' => 'Пользователь не найден'];
        }

        $photo = \yii\web\UploadedFile::getInstanceByName('photo');
        if (!$photo) {
            return ['success' => false, 'message' => 'Файл не загружен'];
        }

        // Проверяем тип файла
        if (!in_array($photo->type, ['image/jpeg', 'image/png', 'image/gif'])) {
            return ['success' => false, 'message' => 'Допустимые форматы: JPG, PNG, GIF'];
        }

        // Проверяем размер файла (максимум 5MB)
        if ($photo->size > 5 * 1024 * 1024) {
            return ['success' => false, 'message' => 'Максимальный размер файла: 5MB'];
        }

        // Генерируем уникальное имя файла
        $fileName = uniqid('user_') . '.' . $photo->extension;
        $filePath = Yii::getAlias('@webroot/uploads/users/') . $fileName;

        // Создаем директорию, если её нет
        if (!is_dir(Yii::getAlias('@webroot/uploads/users/'))) {
            mkdir(Yii::getAlias('@webroot/uploads/users/'), 0777, true);
        }

        // Удаляем старое фото, если оно существует
        if ($user->photo && file_exists(Yii::getAlias('@webroot/uploads/users/') . $user->photo)) {
            unlink(Yii::getAlias('@webroot/uploads/users/') . $user->photo);
        }

        if ($photo->saveAs($filePath)) {
            $user->photo = $fileName;
            if ($user->save()) {
                return [
                    'success' => true,
                    'photoUrl' => Yii::getAlias('@web/uploads/users/') . $fileName
                ];
            }
        }

        return ['success' => false, 'message' => 'Ошибка при сохранении файла'];
    }

}