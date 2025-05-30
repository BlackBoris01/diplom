<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Catalog;
use yii\data\ActiveDataProvider;

class SearchController extends Controller
{
    public function actionIndex()
    {
        $query = Yii::$app->request->get('q', '');

        if ($query) {
            $searchQuery = Catalog::find()
                ->where([
                    'or',
                    ['like', 'title', $query],
                    ['like', 'author', $query],
                    ['like', 'description', $query]
                ]);

            $dataProvider = new ActiveDataProvider([
                'query' => $searchQuery,
                'pagination' => [
                    'pageSize' => 12,
                ],
                'sort' => [
                    'defaultOrder' => ['id' => SORT_DESC]
                ],
            ]);

            return $this->render('index', [
                'dataProvider' => $dataProvider,
                'query' => $query,
            ]);
        }

        return $this->redirect(['/']);
    }
}