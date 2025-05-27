<?php

namespace app\models;

use Yii;
use yii\web\UploadedFile;

/**
 * This is the model class for table "catalog".
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $releaseYear
 * @property string $author
 * @property string $photo
 * @property int $categoryId
 * @property string $pages
 * @property string $weight
 * @property int|null $reviewId
 *
 * @property Category $category
 * @property Review $review
 */
class Catalog extends \yii\db\ActiveRecord
{
    /**
     * @var UploadedFile
     */
    public $imageFile;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'catalog';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'description', 'releaseYear', 'author', 'categoryId', 'pages', 'weight'], 'required'],
            [['releaseYear'], 'safe'],
            [['categoryId'], 'integer'],
            [['pages', 'weight'], 'integer', 'min' => 1],
            [['title', 'author', 'photo'], 'string', 'max' => 255],
            ['description', 'string', 'max' => 4000],
            [['categoryId'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['categoryId' => 'id']],
            [['photo'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg', 'maxSize' => 1024 * 1024 * 2],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Название',
            'description' => 'Описание',
            'releaseYear' => 'Год издания',
            'author' => 'Автор',
            'photo' => 'Фото',
            'categoryId' => 'Категория',
            'pages' => 'Количество страниц',
            'weight' => 'Вес (г)',
        ];
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'categoryId']);
    }

    /**
     * Gets query for [[Ratings]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRatings()
    {
        return $this->hasMany(Rating::class, ['bookId' => 'id']);
    }

    /**
     * Gets query for [[Reviews]].
     *
     * @return array
     */
    public function getReviews()
    {
        return Review::find()
            ->where(['bookId' => $this->id])
            ->orderBy(['created_at' => SORT_DESC])
            ->all() ?: [];
    }

    /**
     * Get current rating for the book
     *
     * @return float
     */
    public function getCurrentRating()
    {
        $rating = Rating::find()
            ->where(['bookId' => $this->id])
            ->average('value');

        return round($rating ?: 0, 1);
    }

    /**
     * Get user's rating for the book
     *
     * @param int $userId
     * @return int|null
     */
    public function getUserRating($userId)
    {
        $rating = Rating::find()
            ->where(['bookId' => $this->id, 'userId' => $userId])
            ->one();

        return $rating ? $rating->value : null;
    }

    /**
     * Get positive reviews count
     *
     * @return int
     */
    public function getPositiveReviewsCount()
    {
        return Review::find()
            ->where(['bookId' => $this->id, 'isNegative' => '0'])
            ->count();
    }

    /**
     * Get negative reviews count
     *
     * @return int
     */
    public function getNegativeReviewsCount()
    {
        return Review::find()
            ->where(['bookId' => $this->id, 'isNegative' => '1'])
            ->count();
    }

    /**
     * Get rating statistics
     * @return array
     */
    public function getRatingStats()
    {
        $stats = [
            'avgRating' => 0,
            'totalRatings' => 0,
            'positiveCount' => 0, // Оценки 6-10
            'negativeCount' => 0, // Оценки 1-5
            'positivePercent' => 0,
            'negativePercent' => 0
        ];

        $ratings = Rating::find()
            ->where(['bookId' => $this->id])
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

            // Вычисляем проценты
            if ($stats['totalRatings'] > 0) {
                $stats['positivePercent'] = round(($stats['positiveCount'] / $stats['totalRatings']) * 100);
                $stats['negativePercent'] = 100 - $stats['positivePercent'];
            }
        }

        return $stats;
    }

    /**
     * Get review statistics
     * @return array
     */
    public function getReviewStats()
    {
        $stats = [
            'totalReviews' => 0,
            'positiveCount' => 0,
            'negativeCount' => 0,
            'positivePercent' => 0,
            'negativePercent' => 0
        ];

        $reviews = $this->getReviews();
        $stats['totalReviews'] = count($reviews);

        foreach ($reviews as $review) {
            if ($review->isNegative) {
                $stats['negativeCount']++;
            } else {
                $stats['positiveCount']++;
            }
        }

        if ($stats['totalReviews'] > 0) {
            $stats['positivePercent'] = round(($stats['positiveCount'] / $stats['totalReviews']) * 100);
            $stats['negativePercent'] = 100 - $stats['positivePercent'];
        }

        return $stats;
    }

    /**
     * Gets reviews with pagination
     * @param int $limit
     * @param int $offset
     * @param string $role user|specialist
     * @return array
     */
    public function getReviewsByRole($limit = 3, $offset = 0, $role = 'user')
    {
        return Review::find()
            ->alias('r')
            ->innerJoin('user u', 'r.userId = u.id')
            ->where(['r.bookId' => $this->id])
            ->andWhere(['u.roleId' => $role === 'specialist' ? 1 : 0])
            ->orderBy(['r.created_at' => SORT_DESC])
            ->limit($limit)
            ->offset($offset)
            ->all();
    }

    /**
     * Get total reviews count by role
     * @param string $role user|specialist
     * @return int
     */
    public function getReviewsCountByRole($role = 'user')
    {
        return Review::find()
            ->alias('r')
            ->innerJoin('user u', 'r.userId = u.id')
            ->where(['r.bookId' => $this->id])
            ->andWhere(['u.roleId' => $role === 'specialist' ? 1 : 0])
            ->count();
    }
}