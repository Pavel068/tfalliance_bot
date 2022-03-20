<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "topic_replies".
 *
 * @property int $id
 * @property int $user_id
 * @property int $topic_id
 * @property int $bot_message_id
 * @property string|null $message
 *
 * @property Topics $topic
 * @property Users $user
 */
class TopicReplies extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'topic_replies';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'topic_id', 'bot_message_id'], 'required'],
            [['user_id', 'topic_id', 'bot_message_id'], 'integer'],
            [['message'], 'string'],
            [['topic_id'], 'exist', 'skipOnError' => true, 'targetClass' => Topics::className(), 'targetAttribute' => ['topic_id' => 'id']],
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
            'topic_id' => 'Topic ID',
            'bot_message_id' => 'Bot Message ID',
            'message' => 'Message',
        ];
    }

    /**
     * Gets query for [[Topic]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTopic()
    {
        return $this->hasOne(Topics::className(), ['id' => 'topic_id']);
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
