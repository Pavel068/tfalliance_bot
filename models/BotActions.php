<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bot_actions".
 *
 * @property int $id
 * @property int $chat_id
 * @property string|null $action
 * @property string|null $data
 * @property string $created_at
 * @property string $updated_at
 */
class BotActions extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bot_actions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['chat_id'], 'required'],
            [['chat_id'], 'integer'],
            [['data'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['action'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'chat_id' => 'Chat ID',
            'action' => 'Action',
            'data' => 'Data',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
