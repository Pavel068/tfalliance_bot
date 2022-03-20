<?php

use yii\db\Migration;

/**
 * Class m220319_145501_alter_bot_actions_add_data
 */
class m220319_145501_alter_bot_actions_add_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('bot_actions', 'data', $this->text()->null()->after('action'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220319_145501_alter_bot_actions_add_data cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220319_145501_alter_bot_actions_add_data cannot be reverted.\n";

        return false;
    }
    */
}
