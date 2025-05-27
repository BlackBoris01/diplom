<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "review_moderation".
 *
 * @property int $id
 * @property int $reviewId
 * @property int $moderatorId
 * @property string $status
 * @property string|null $comment
 * @property string $createdAt
 * @property string $updatedAt
 */
class ReviewModeration extends ActiveRecord
{
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'review_moderation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['reviewId', 'moderatorId', 'status'], 'required'],
            [['reviewId', 'moderatorId'], 'integer'],
            [['status'], 'string'],
            [['comment'], 'string'],
            [['createdAt', 'updatedAt'], 'safe'],
            [['status'], 'in', 'range' => [self::STATUS_PENDING, self::STATUS_APPROVED, self::STATUS_REJECTED]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'reviewId' => 'ID отзыва',
            'moderatorId' => 'ID модератора',
            'status' => 'Статус',
            'comment' => 'Комментарий',
            'createdAt' => 'Дата создания',
            'updatedAt' => 'Дата обновления',
        ];
    }

    /**
     * Gets query for [[Review]].
     */
    public function getReview()
    {
        return $this->hasOne(Review::class, ['id' => 'reviewId']);
    }

    /**
     * Gets query for [[Moderator]].
     */
    public function getModerator()
    {
        return $this->hasOne(User::class, ['id' => 'moderatorId']);
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if ($this->isNewRecord) {
            $this->createdAt = date('Y-m-d H:i:s');
        }
        $this->updatedAt = date('Y-m-d H:i:s');

        return true;
    }
}