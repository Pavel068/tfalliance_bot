<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string|null $name
 * @property string $email
 * @property string|null $password
 * @property int|null $bot_chat_id
 * @property string|null $tg_username
 * @property string|null $access_token
 * @property int|null $is_admin
 * @property int|null $is_manager
 * @property string $created_at
 * @property string $updated_at
 *
 * @property TopicReplies[] $topicReplies
 * @property Topics[] $topics
 */
class Users extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email'], 'required'],
            [['bot_chat_id', 'is_admin', 'is_manager'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'email', 'password', 'tg_username', 'access_token'], 'string', 'max' => 255],
            [['email'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'email' => 'Email',
            'password' => 'Password',
            'bot_chat_id' => 'Bot Chat ID',
            'tg_username' => 'Tg Username',
            'access_token' => 'Access Token',
            'is_admin' => 'Is Admin',
            'is_manager' => 'Is Manager',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @param $insert
     * @return bool
     * @throws \yii\base\Exception
     */
    public function beforeSave($insert): bool
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord && $this->password) {
                $this->password = Yii::$app->security->generatePasswordHash($this->password);
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Gets query for [[TopicReplies]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTopicReplies()
    {
        return $this->hasMany(TopicReplies::className(), ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Topics]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTopics()
    {
        return $this->hasMany(Topics::className(), ['user_id' => 'id']);
    }
}
