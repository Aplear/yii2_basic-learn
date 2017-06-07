<?php

use yii\db\Migration;

/**
 * Handles the creation of table `add_column_group_in_user`.
 */
class m170605_152548_create_add_column_group_in_user_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('user', 'group', $this->string()->defaultValue('guest'));
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('user', 'group');
    }
}
