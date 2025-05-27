<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\RoleRequest;
use app\models\User;

class ProfileController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $user = Yii::$app->user->identity;
        $roleRequest = RoleRequest::find()
            ->where(['userId' => $user->id])
            ->orderBy(['createdAt' => SORT_DESC])
            ->one();

        return $this->render('/site/profile', [
            'user' => $user,
            'roleRequest' => $roleRequest,
        ]);
    }

    public function actionRequestRole()
    {
        if (Yii::$app->user->identity->roleId !== 0) {
            Yii::$app->session->setFlash('error', 'Вы уже имеете статус специалиста или администратора.');
            return $this->redirect(['index']);
        }

        $model = new RoleRequest();
        $model->userId = Yii::$app->user->id;

        if ($this->request->isPost) {
            $model->load($this->request->post());

            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Ваша заявка успешно отправлена на рассмотрение.');
                return $this->redirect(['index']);
            }
        }

        return $this->render('request-role', [
            'model' => $model,
        ]);
    }

    public function actionUpdateRoleRequest($id)
    {
        $model = $this->findRoleRequest($id);

        if ($model->userId !== Yii::$app->user->id || $model->status !== RoleRequest::STATUS_REVISION) {
            throw new NotFoundHttpException('Заявка не найдена или недоступна для редактирования.');
        }

        if ($this->request->isPost) {
            $model->load($this->request->post());
            $model->status = RoleRequest::STATUS_PENDING;

            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Ваша заявка успешно обновлена и отправлена на повторное рассмотрение.');
                return $this->redirect(['index']);
            }
        }

        return $this->render('update-role-request', [
            'model' => $model,
        ]);
    }

    protected function findRoleRequest($id)
    {
        if (($model = RoleRequest::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Заявка не найдена.');
    }
}