<?php

namespace app\controllers\api\v1;

use app\models\User;
use app\models\Users;
use Yii;
use yii\base\Security;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UnprocessableEntityHttpException;
use yii\web\UploadedFile;

class AuthController extends RestController
{
    public $modelClass = 'app\models\Users';

    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['except'][] = 'login';
        return $behaviors;
    }

    /**
     * @return array|\yii\db\ActiveRecord|null
     * @throws BadRequestHttpException
     * @throws \yii\base\Exception
     */
    public function actionLogin()
    {
        $email = Yii::$app->request->post('email');
        $password = Yii::$app->request->post('password');

        $user = Users::findOne(['email' => $email]);

        if (!$user || !$user->password) {
            throw new \yii\web\BadRequestHttpException('Wrong email or password');
        } else {
            if (\Yii::$app->security->validatePassword($password, $user->password)) {
                $security = new Security();
                $randomKey = md5($security->generateRandomKey());

                $user->access_token = $randomKey;
                $user->password = Yii::$app->security->generatePasswordHash($password);
                $user->save();
                $user->refresh();

                $user = Users::find()->where(['id' => $user->id])->asArray()->one();
                unset($user['password']);

                return $user;
            } else {
                throw new \yii\web\BadRequestHttpException('Wrong email or password');
            }
        }
    }

    /**
     * @return array|\yii\db\ActiveRecord|null
     */
    public function actionMe()
    {
        $user = Users::find()->where(['id' => Yii::$app->user->getId()])->asArray()->one();
        unset($user['password']);

        return $user;
    }
}
