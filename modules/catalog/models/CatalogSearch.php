<?php

namespace app\modules\catalog\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Catalog;
use app\models\Category;

/**
 * CatalogSearch represents the model behind the search form of `app\models\Catalog`.
 */
class CatalogSearch extends Catalog
{
    public $minYear;
    public $maxYear;

    public function rules()
    {
        return [
            [['id', 'categoryId'], 'integer'],
            [['title', 'author'], 'safe'],
            [['minYear', 'maxYear'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Catalog::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,

            'pagination' => [
                'pageSize' => 12
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],

        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'categoryId' => $this->categoryId,
        ]);

        if ($this->minYear) {
            $query->andFilterWhere(['>=', 'releaseYear', $this->minYear]);
        }
        if ($this->maxYear) {
            $query->andFilterWhere(['<=', 'releaseYear', $this->maxYear]);
        }

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'author', $this->author]);

        return $dataProvider;
    }
}