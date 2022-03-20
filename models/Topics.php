<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "topics".
 *
 * @property int $id
 * @property int $user_id
 * @property int $bot_message_id
 * @property string $name
 * @property string|null $message
 * @property string|null $keywords
 * @property string $created_at
 * @property string $updated_at
 *
 * @property TopicReplies[] $topicReplies
 * @property Users $user
 */
class Topics extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'topics';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'bot_message_id', 'name'], 'required'],
            [['user_id', 'bot_message_id'], 'integer'],
            [['message'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'keywords'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'bot_message_id' => 'Bot Message ID',
            'name' => 'Name',
            'message' => 'Message',
            'keywords' => 'Keywords',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[TopicReplies]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTopicReplies()
    {
        return $this->hasMany(TopicReplies::className(), ['topic_id' => 'id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }
}
