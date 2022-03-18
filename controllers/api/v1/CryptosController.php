<?php

namespace app\controllers\api\v1;

use app\models\Cryptos;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\web\NotFoundHttpException;

class CryptosController extends RestController
{
    public $modelClass = 'app\models\Crypto';

    /**
     * @return ActiveDataProvider
     */
    public function actionIndex(): ActiveDataProvider
    {
        $query = Cryptos::find()
            ->andFilterWhere(['>=', 'updated_at', new Expression('NOW() - INTERVAL 1 DAY')])
            ->asArray();
        $query->orderBy('created_at DESC');

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->request->get('per_page') ? Yii::$app->request->get('per_page') : 20,
                'page' => Yii::$app->request->get('page') ? (int)Yii::$app->request->get('page') - 1 : 0
            ]
        ]);
    }

    public function actionView($id)
    {
        $crypto = Cryptos::find()
            ->with('ideas')
            ->where(['id' => $id])
            ->asArray()
            ->one();

        if (!$crypto) {
            throw new NotFoundHttpException();
        }

        return $crypto;
    }
}