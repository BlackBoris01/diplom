<?php

namespace app\models;

use Yii;
use yii\bootstrap5\Html;

/**
 * This is the model class for table "category".
 *
 * @property int $id
 * @property string $title
 */
class Category extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
        ];
    }

    public static function getCategoryById($id)
    {
        return Html::encode(self::findOne($id)->title);
    }
    public static function getService()
    {
        return (new \yii\db\Query())
            ->select(['title'])
            ->from('category')
            ->indexBy('id')
            ->column();
    }
}