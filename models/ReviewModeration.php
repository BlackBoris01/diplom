<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "review_moderation".
 *
 * @property int $id
 * @property int $reviewId
 * @property int|null $moderatorId
 * @property string $status
 * @property string|null $adminComment
 * @property string $createdAt
 * @property string|null $updatedAt
 *
 * @property User $moderator
 * @property Review $review
 */
class ReviewModeration extends ActiveRecord
{
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_REVISION = 'revision';

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
            [['reviewId'], 'required'],
            [['reviewId', 'moderatorId'], 'integer'],
            [['adminComment'], 'string'],
            [['createdAt', 'updatedAt'], 'safe'],
            [['status'], 'string', 'max' => 20],
            [['status'], 'default', 'value' => self::STATUS_PENDING],
            [
                ['status'],
                'in',
                'range' => [
                    self::STATUS_PENDING,
                    self::STATUS_APPROVED,
                    self::STATUS_REJECTED,
                    self::STATUS_REVISION
                ]
            ],
            [['moderatorId'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['moderatorId' => 'id']],
            [['reviewId'], 'exist', 'skipOnError' => true, 'targetClass' => Review::class, 'targetAttribute' => ['reviewId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'reviewId' => 'Отзыв',
            'moderatorId' => 'Модератор',
            'status' => 'Статус',
            'adminComment' => 'Комментарий',
            'createdAt' => 'Дата создания',
            'updatedAt' => 'Дата обновления',
        ];
    }

    /**
     * Gets query for [[Moderator]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getModerator()
    {
        return $this->hasOne(User::class, ['id' => 'moderatorId']);
    }

    /**
     * Gets query for [[Review]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReview()
    {
        return $this->hasOne(Review::class, ['id' => 'reviewId']);
    }

    /**
     * Gets status label
     *
     * @return string
     */
    public function getStatusLabel()
    {
        $labels = [
            self::STATUS_PENDING => 'На проверке',
            self::STATUS_APPROVED => 'Одобрен',
            self::STATUS_REJECTED => 'Отклонен',
            self::STATUS_REVISION => 'На доработке'
        ];

        return $labels[$this->status] ?? 'Неизвестно';
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