<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "psychobook_rating".
 *
 * @property int $id
 * @property int $bookId
 * @property int $userId
 * @property int $value
 * @property string $created_at
 *
 * @property Catalog $book
 * @property User $user
 */
class Rating extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rating';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bookId', 'userId', 'value'], 'required'],
            [['bookId', 'userId', 'value'], 'integer'],
            [['value'], 'integer', 'min' => 1, 'max' => 10],
            [['created_at'], 'safe'],
            [['bookId'], 'exist', 'skipOnError' => true, 'targetClass' => Catalog::class, 'targetAttribute' => ['bookId' => 'id']],
            [['userId'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['userId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'bookId' => 'Книга',
            'userId' => 'Пользователь',
            'value' => 'Оценка',
            'created_at' => 'Дата создания',
        ];
    }

    /**
     * Gets query for [[Book]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBook()
    {
        return $this->hasOne(Catalog::class, ['id' => 'bookId']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'userId']);
    }

    /**
     * Get user role
     * @return string
     */
    public function getUserRole()
    {
        if (!$this->user) {
            return 0; // По умолчанию обычный пользователь
        }
        return $this->user->roleId;
    }

    /**
     * Get ratings statistics by user role
     * @param int $bookId
     * @param string $role
     * @return array
     */
    public static function getRatingStatsByRole($bookId, $role = 'user')
    {
        $stats = [
            'avgRating' => 0,
            'totalRatings' => 0,
            'positiveCount' => 0,
            'negativeCount' => 0,
            'positivePercent' => 0,
            'negativePercent' => 0
        ];

        $ratings = self::find()
            ->alias('r')
            ->innerJoin('user u', 'r.userId = u.id')
            ->where(['r.bookId' => $bookId])
            ->andWhere(['u.roleId' => $role === 'specialist' ? 1 : 0])
            ->all();

        if (!empty($ratings)) {
            $stats['totalRatings'] = count($ratings);
            $sum = 0;

            foreach ($ratings as $rating) {
                $sum += $rating->value;
                if ($rating->value > 5) {
                    $stats['positiveCount']++;
                } else {
                    $stats['negativeCount']++;
                }
            }

            $stats['avgRating'] = round($sum / $stats['totalRatings'], 2);

            if ($stats['totalRatings'] > 0) {
                $stats['positivePercent'] = round(($stats['positiveCount'] / $stats['totalRatings']) * 100);
                $stats['negativePercent'] = 100 - $stats['positivePercent'];
            }
        }

        return $stats;
    }
}