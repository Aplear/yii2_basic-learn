<?php

use yii\db\Migration;

/**
 * Handles the creation of table `news`.
 */
class m170602_164138_create_news_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('news', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(10)->notNull(),
            'title' => $this->string()->notNull(),
            'short_text' => $this->text(),
            'full_text' => $this->text(),
            'image' => $this->string(),
            'status' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),

        ]);
        // creates index for column `user_id`
        $this->createIndex(
            'idx-news-user_id',
            'news',
            'user_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-news-user_id',
            'news',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );

    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        // drops foreign key for table `user`
        $this->dropForeignKey(
            'fk-news-user_id',
            'news'
        );

        // drops index for column `news`
        $this->dropIndex(
            'idx-news-user_id',
            'news'
        );

        $this->dropTable('news');
    }
}
