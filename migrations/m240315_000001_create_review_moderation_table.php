<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%review_moderation}}`.
 */
class m240315_000001_create_review_moderation_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%review_moderation}}', [
            'id' => $this->primaryKey(),
            'reviewId' => $this->integer()->notNull(),
            'moderatorId' => $this->integer()->null(),
            'status' => $this->string(20)->notNull()->defaultValue('pending'),
            'adminComment' => $this->text()->null(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updatedAt' => $this->timestamp()->null(),
        ]);

        // Добавляем внешние ключи
        $this->addForeignKey(
            'fk-review_moderation-reviewId',
            '{{%review_moderation}}',
            'reviewId',
            '{{%review}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-review_moderation-moderatorId',
            '{{%review_moderation}}',
            'moderatorId',
            '{{%user}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        // Создаем индексы
        $this->createIndex(
            'idx-review_moderation-status',
            '{{%review_moderation}}',
            'status'
        );

        $this->createIndex(
            'idx-review_moderation-reviewId',
            '{{%review_moderation}}',
            'reviewId'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-review_moderation-moderatorId', '{{%review_moderation}}');
        $this->dropForeignKey('fk-review_moderation-reviewId', '{{%review_moderation}}');
        $this->dropTable('{{%review_moderation}}');
    }
}