<?php

namespace app\controllers\api\v1;

use app\models\Ideas;
use Yii;
use yii\data\ActiveDataProvider;

class IdeasController extends RestController
{
    public $modelClass = 'app\models\Idea';

    /**
     * @return ActiveDataProvider
     */
    public function actionIndex(): ActiveDataProvider
    {
        $query = Ideas::find()->with(['crypto'])->asArray();

        if (Yii::$app->request->get('crypto_name')) {
            $query->andFilterWhere(['crypto_name' => Yii::$app->request->get('crypto_name')]);
        }

        $query->orderBy('created_at DESC');

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->request->get('per_page') ? Yii::$app->request->get('per_page') : 20,
                'page' => Yii::$app->request->get('page') ? (int)Yii::$app->request->get('page') - 1 : 0
            ]
        ]);
    }

    public function actionView()
    {

    }
}