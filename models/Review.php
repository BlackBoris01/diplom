<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "review".
 *
 * @property int $id
 * @property int $userId
 * @property string $reviewDescription
 * @property bool $isNegative
 * @property int $bookId
 * @property string $status
 * @property string $created_at
 *
 * @property Catalog $book
 * @property User $user
 */
class Review extends ActiveRecord
{
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'review';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['userId', 'reviewDescription', 'bookId'], 'required'],
            [['userId', 'bookId'], 'integer'],
            [['reviewDescription'], 'string'],
            [['isNegative'], 'boolean'],
            [['created_at'], 'safe'],
            [['status'], 'string', 'max' => 20],
            [['bookId'], 'exist', 'skipOnError' => true, 'targetClass' => Catalog::class, 'targetAttribute' => ['bookId' => 'id']],
            [['userId'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['userId' => 'id']],
            ['status', 'default', 'value' => self::STATUS_PENDING],
            ['status', 'in', 'range' => [self::STATUS_PENDING, self::STATUS_APPROVED, self::STATUS_REJECTED]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userId' => 'Пользователь',
            'reviewDescription' => 'Текст рецензии',
            'isNegative' => 'Отрицательная',
            'bookId' => 'Книга',
            'status' => 'Статус',
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
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($insert) {
            // Создаем запись модерации после сохранения отзыва
            $moderation = new ReviewModeration([
                'reviewId' => $this->id,
                'status' => ReviewModeration::STATUS_PENDING
            ]);
            $moderation->save();
        }
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if ($insert) {
            $this->created_at = date('Y-m-d H:i:s');
            $this->status = self::STATUS_PENDING;
        }

        return true;
    }

    /**
     * Получает текст рецензии
     * @return string
     */
    public function getReviewDescription()
    {
        return $this->reviewDescription;
    }

    /**
     * Устанавливает текст рецензии
     * @param string $value
     */
    public function setReviewDescription($value)
    {
        $this->reviewDescription = $value;
    }

    /**
     * Получает флаг отрицательной рецензии
     * @return bool
     */
    public function getIsNegative()
    {
        return !$this->isNegative;
    }

    /**
     * Устанавливает флаг отрицательной рецензии
     * @param bool $value
     */
    public function setIsNegative($value)
    {
        $this->isNegative = !$value;
    }

    /**
     * Получает ID книги
     * @return int
     */
    public function getBookId()
    {
        return $this->bookId;
    }

    /**
     * Устанавливает ID книги
     * @param int $value
     */
    public function setBookId($value)
    {
        $this->bookId = $value;
    }

    /**
     * Получает ID пользователя
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Устанавливает ID пользователя
     * @param int $value
     */
    public function setUserId($value)
    {
        $this->userId = $value;
    }

    public function getModeration()
    {
        return $this->hasOne(ReviewModeration::class, ['reviewId' => 'id']);
    }
}