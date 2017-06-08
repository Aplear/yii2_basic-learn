<?php

use yii\db\Migration;

/**
 * Handles the creation of table `notification`.
 */
class m170607_230442_create_notification_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('notification', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(10)->notNull(),
            'notification' => $this->string()->notNull(),
            'status' => $this->integer()->defaultValue(0),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('notification');
    }
}
