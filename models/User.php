<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;
use yii\helpers\Html;
use yii\web\UploadedFile;
/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $login
 * @property string $nickname
 * @property string $passwordHash
 * @property string $authKey
 * @property int $roleId
 * @property string $fullName
 * @property string $email
 * @property string $registered
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    public $password;
    public $photoFile;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email', 'nickname', 'password'], 'required', 'on' => 'create'],
            [['roleId'], 'integer'],
            [['login', 'nickname', 'authKey', 'fullName', 'email', 'photo'], 'string', 'max' => 255],
            [['registered'], 'safe'],
            ['password', 'string', 'min' => 6],
            ['email', 'email'],
            ['email', 'unique', 'message' => 'Этот email уже используется'],
            ['nickname', 'unique', 'message' => 'Этот псевдоним уже занят'],
            [['photoFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg', 'maxSize' => 1024 * 1024 * 2],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'login' => 'Логин',
            'nickname' => 'Псевдоним',
            'password' => 'Пароль',
            'authKey' => 'Auth Key',
            'roleId' => 'Роль',
            'fullName' => 'ФИО',
            'email' => 'Email',
            'registered' => 'Дата регистрации',
            'photo' => 'Фото',
            'photoFile' => 'Фото'
        ];
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->authKey;
    }

    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    public static function findByLogin($login)
    {
        return static::findOne(['login' => $login]);

    }


    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->passwordHash);
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if ($insert) {
            $this->roleId = 0;
            $this->authKey = Yii::$app->security->generateRandomString();
            if (!empty($this->password)) {
                $this->passwordHash = Yii::$app->security->generatePasswordHash($this->password);
            }
            $this->registered = date('Y-m-d H:i:s');
            // Генерируем логин из email
            $this->login = explode('@', $this->email)[0];
            // Устанавливаем полное имя равным псевдониму, если не указано
            $this->fullName = $this->nickname;
        } else {
            // Обновляем пароль только если он был указан и не пустой
            if (!empty($this->password)) {
                $this->passwordHash = Yii::$app->security->generatePasswordHash($this->password);
            }
        }

        // Обработка загрузки фото
        if ($this->photoFile) {
            $fileName = 'user_' . time() . '.' . $this->photoFile->extension;
            $filePath = Yii::getAlias('@webroot/uploads/users/') . $fileName;

            // Создаем директорию, если она не существует
            if (!file_exists(Yii::getAlias('@webroot/uploads/users/'))) {
                mkdir(Yii::getAlias('@webroot/uploads/users/'), 0777, true);
            }

            if ($this->photoFile->saveAs($filePath)) {
                // Удаляем старое фото, если оно есть
                if ($this->photo && file_exists(Yii::getAlias('@webroot/uploads/users/') . $this->photo)) {
                    unlink(Yii::getAlias('@webroot/uploads/users/') . $this->photo);
                }
                $this->photo = $fileName;
            }
        }

        return true;
    }

    public static function getNicknameById($id)
    {
        return Html::encode(self::findOne(['id' => $id])->nickname);
    }
    public static function getRoleById($id)
    {
        $user = self::findOne(['id' => $id]);
        return $user ? Html::encode($user->roleId) : 0;
    }

    public static function getInfoById($id)
    {

        $user = User::find()
            ->where(['id' => $id])
            ->one();
        return $user;
    }

    /**
     * Получает количество отзывов пользователя
     * @param int $userId ID пользователя
     * @return int
     */
    public static function getReviewsCount($userId)
    {
        return Review::find()
            ->where(['userId' => $userId])
            ->count();
    }

    /**
     * Получает все отзывы пользователя
     * @param int $userId ID пользователя
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getUserReviews($userId)
    {
        return Review::find()
            ->where(['userId' => $userId])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();
    }

    /**
     * Получает рейтинг пользователя
     * @param int $userId ID пользователя
     * @return float
     */
    public static function getUserRating($userId)
    {
        $reviews = Review::find()
            ->where(['userId' => $userId])
            ->all();

        if (empty($reviews)) {
            return 0;
        }

        $totalRating = 0;
        foreach ($reviews as $review) {
            $totalRating += $review->rating;
        }

        return round($totalRating / count($reviews), 1);
    }

    /**
     * Получает метку статуса отзыва
     * @param string $status Статус отзыва
     * @return string
     */
    public static function getReviewStatusLabel($status)
    {
        $statuses = [
            'pending' => 'На проверке',
            'approved' => 'Одобрен',
            'rejected' => 'Отклонен'
        ];

        return isset($statuses[$status]) ? $statuses[$status] : 'Неизвестно';
    }

    /**
     * Получает отношение к отзывам
     */
    public function getReviews()
    {
        return $this->hasMany(Review::class, ['userId' => 'id']);
    }
}