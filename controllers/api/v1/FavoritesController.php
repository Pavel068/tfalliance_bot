<?php

namespace app\controllers\api\v1;

use app\models\Favorites;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class FavoritesController extends RestController
{
    public $modelClass = 'app\models\Favorites';

    /**
     * @return ActiveDataProvider
     */
    public function actionIndex(): ActiveDataProvider
    {
        $query = Favorites::find()
            ->with('crypto')
            ->where(['user_id' => Yii::$app->user->getId()])
            ->orderBy('id DESC')
            ->asArray();

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->request->get('per_page') ? Yii::$app->request->get('per_page') : 10,
                'page' => Yii::$app->request->get('page') ? (int)Yii::$app->request->get('page') - 1 : 0
            ]
        ]);
    }

    /**
     * @return Favorites
     */
    public function actionCreate(): Favorites
    {
        $favorite = new Favorites();
        $favorite->load(array_merge([
            'user_id' => Yii::$app->user->getId()
        ], Yii::$app->request->post()), '');
        $favorite->save();
        $favorite->refresh();

        Yii::$app->response->statusCode = 201;

        return $favorite;
    }

    /**
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     * @throws NotFoundHttpException
     * @throws ForbiddenHttpException
     */
    public function actionDelete(int $id)
    {
        $review = Favorites::find()->where(['id' => $id])->one();

        if (!$review) {
            throw new NotFoundHttpException();
        }

        if ($review->user_id != Yii::$app->user->getId()) {
            throw new ForbiddenHttpException();
        }

        $review->delete();

        Yii::$app->response->statusCode = 204;
        return;
    }
}