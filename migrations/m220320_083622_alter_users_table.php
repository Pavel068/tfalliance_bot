<?php

use yii\db\Migration;

/**
 * Class m220320_083622_alter_users_table
 */
class m220320_083622_alter_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('users', 'email', $this->string(255)->null()->unique());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220320_083622_alter_users_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220320_083622_alter_users_table cannot be reverted.\n";

        return false;
    }
    */
}
