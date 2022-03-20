<?php

use yii\db\Migration;

/**
 * Class m220320_071648_alter_users_table
 */
class m220320_071648_alter_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('users', 'is_manager', $this->boolean()->defaultValue(0)->after('is_admin'));
        $this->addColumn('users', 'tg_username', $this->string(255)->null()->after('password'));
        $this->addColumn('users', 'bot_chat_id', $this->bigInteger()->null()->after('password'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220320_071648_alter_users_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220320_071648_alter_users_table cannot be reverted.\n";

        return false;
    }
    */
}
