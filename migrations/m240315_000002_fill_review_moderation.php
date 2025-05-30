<?php

use yii\db\Migration;

/**
 * Class m240315_000002_fill_review_moderation
 */
class m240315_000002_fill_review_moderation extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $reviews = $this->db->createCommand('SELECT id FROM review')->queryAll();

        foreach ($reviews as $review) {
            $this->insert('review_moderation', [
                'reviewId' => $review['id'],
                'status' => 'pending',
                'createdAt' => date('Y-m-d H:i:s')
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('review_moderation');
    }
}