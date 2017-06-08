<?php

use yii\db\Migration;

/**
 * Handles the creation of table `profile`.
 */
class m170606_204113_create_profile_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('profile', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(10)->notNull(),
            'rss_email' => $this->integer(2)->defaultValue(0),
            'rss_browser' => $this->integer(2)->defaultValue(0),
        ]);
        // creates index for column `user_id`
        $this->createIndex(
            'idx-profile-user_id',
            'profile',
            'user_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-profile-user_id',
            'profile',
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
            'fk-profile-user_id',
            'profile'
        );

        // drops index for column `profile`
        $this->dropIndex(
            'idx-profile-user_id',
            'profile'
        );

        $this->dropTable('profile');
    }
}
