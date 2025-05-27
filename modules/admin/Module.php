<?php

namespace app\modules\admin;

use yii\filters\AccessControl;

class Module extends \yii\base\Module
{
    public function init()
    {
        parent::init();
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return \Yii::$app->user->identity && \Yii::$app->user->identity->roleId === 2;
                        }
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    return \Yii::$app->response->redirect(['/site/login']);
                }
            ],
        ];
    }
}