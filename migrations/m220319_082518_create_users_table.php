<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%users}}`.
 */
class m220319_082518_create_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%users}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->null(),
            'email' => $this->string(255)->notNull()->unique(),
            'password' => $this->string(255)->null(),
            'access_token' => $this->string(255)->null(),
            'is_admin' => $this->boolean()->defaultValue(false),
            'created_at' => "TIMESTAMP NOT NULL default CURRENT_TIMESTAMP",
            'updated_at' => "TIMESTAMP NOT NULL default CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP",
        ], 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB');

        $this->insert('users', [
           'name' => 'Super Admin',
           'email' => 'admin@test.com',
           'password' => Yii::$app->security->generatePasswordHash('123456')
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%users}}');
    }
}
