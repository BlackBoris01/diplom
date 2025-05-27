<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "roleRequest".
 *
 * @property int $id
 * @property int $userId
 * @property string $requestText
 * @property string $status pending|approved|rejected|revision
 * @property string|null $adminComment
 * @property string $createdAt
 * @property string|null $updatedAt
 *
 * @property User $user
 */
class RoleRequest extends \yii\db\ActiveRecord
{
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_REVISION = 'revision';

    public static function tableName()
    {
        return 'roleRequest';
    }

    public function rules()
    {
        return [
            [['userId', 'requestText'], 'required'],
            [['userId'], 'integer'],
            [['requestText', 'adminComment'], 'string'],
            [['status'], 'string', 'max' => 20],
            [['status'], 'in', 'range' => [self::STATUS_PENDING, self::STATUS_APPROVED, self::STATUS_REJECTED, self::STATUS_REVISION]],
            [['createdAt', 'updatedAt'], 'safe'],
            [['userId'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['userId' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userId' => 'Пользователь',
            'requestText' => 'Текст заявки',
            'status' => 'Статус',
            'adminComment' => 'Комментарий администратора',
            'createdAt' => 'Дата создания',
            'updatedAt' => 'Дата обновления',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'userId']);
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->createdAt = date('Y-m-d H:i:s');
            $this->status = self::STATUS_PENDING;
        } else {
            $this->updatedAt = date('Y-m-d H:i:s');
        }
        return parent::beforeSave($insert);
    }
}