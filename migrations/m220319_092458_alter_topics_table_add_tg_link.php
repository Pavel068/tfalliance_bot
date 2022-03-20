<?php

use yii\db\Migration;

/**
 * Class m220319_092458_alter_topics_table_add_tg_link
 */
class m220319_092458_alter_topics_table_add_tg_link extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('topics', 'tg_link', $this->string(255)->null()->after('keywords'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220319_092458_alter_topics_table_add_tg_link cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220319_092458_alter_topics_table_add_tg_link cannot be reverted.\n";

        return false;
    }
    */
}
