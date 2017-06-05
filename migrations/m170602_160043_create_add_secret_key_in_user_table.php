<?php

use yii\db\Migration;

/**
 * Handles the creation of table `add_secret_key_in_user`.
 */
class m170602_160043_create_add_secret_key_in_user_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('user', 'secret_key', $this->string());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('user', 'secret_key');
    }
}
