<?php

namespace app\controllers\api\v1;

use app\models\PasswordRecovery;
use app\models\User;
use app\models\Users;
use GuzzleHttp\Exception\GuzzleException;
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
        $behaviors['authenticator']['except'][] = 'signup';
        $behaviors['authenticator']['except'][] = 'signin';
        $behaviors['authenticator']['except'][] = 'recovery';
        return $behaviors;
    }

    /**
     * @param $data
     * @return Users|array|\yii\db\ActiveRecord|null
     * @throws BadRequestHttpException
     */
    protected function _addUser($data)
    {
        $user = new Users();

        try {
            Yii::$app->response->statusCode = 201;

            $user->load(array_merge([
                'access_token' => sha1((new Security())->generateRandomKey())
            ], $data), '');

            if ($user->validate() && $user->save()) {
                $user = Users::find()->where(['id' => $user->id])->asArray()->one();
                unset($user['password']);
            }

            return $user;
        } catch (\Exception $e) {
            Yii::error($e);
            throw new BadRequestHttpException();
        }
    }

    /**
     * @return Users|array|\yii\db\ActiveRecord|null
     * @throws BadRequestHttpException
     */
    public function actionLogin()
    {
        $user = Users::find()->where(['identifier' => Yii::$app->request->post('identifier')])->asArray()->one();
        if ($user) {
            unset($user['password']);
            return $user;
        } else {
            $user = Users::find()->where(['email' => Yii::$app->request->post('email')])->one();
            if ($user) {
                $user->identifier = Yii::$app->request->post('identifier');
                $user->save();
                $user->refresh();
                unset($user->password);

                return $user;
            } else {
                return $this->_addUser(Yii::$app->request->post());
            }
        }
    }

    /**
     * @return Users|array|\yii\db\ActiveRecord|null
     * @throws BadRequestHttpException
     */
    public function actionSignup()
    {
        return $this->_addUser(Yii::$app->request->post());
    }

    /**
     * @return array|\yii\db\ActiveRecord|null
     * @throws BadRequestHttpException
     * @throws \yii\base\Exception
     */
    public function actionSignin()
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

    /**
     * @return void
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * @throws \Throwable
     */
    public function actionDelete()
    {
        $user = Users::find()->where(['id' => Yii::$app->user->getId()])->one();

        if (!$user) {
            throw new NotFoundHttpException();
        }

        try {
            $user->delete();
            Yii::$app->response->statusCode = 204;
            return;
        } catch (\Exception $e) {
            throw new BadRequestHttpException();
        }
    }

    /**
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionRecovery()
    {
        $email = Yii::$app->request->post('email');
        $user = Users::find()->where(['email' => $email])->one();

        if (!$user) {
            throw new NotFoundHttpException();
        }

        $recovery = new PasswordRecovery();
        $recovery->load([
            'email' => $email,
            'token' => md5(uniqid('', true))
        ], '');
        $recovery->save();
        $recovery->refresh();

        // Send mail
        try {
            $recovery_link = Url::base(true) . '/site/recovery/?token=' . $recovery->token;

            Yii::$app->mailer->compose()
                ->setFrom($_ENV['SMTP_USERNAME'])
                ->setTo($recovery->email)
                ->setSubject('Password recovery')
                ->setHtmlBody(
                    "
                    <h1>Hello, {$user->name}</h1>
                    <p>To reset you password please follow this <a href='$recovery_link'>link</a>.</p>
                    <p>If your browser doesn't support hyperlinks, paste below URL in address input:</p>
                    <code>$recovery_link</code>
                    "
                )
                ->send();

            Yii::$app->response->statusCode = 204;
            return;
        } catch (\Exception $e) {
            Yii::error($e);
            throw new BadRequestHttpException();
        }
    }

    /**
     * @return array|\yii\db\ActiveRecord|null
     * @throws BadRequestHttpException
     * @throws \yii\base\Exception
     */
    public function actionChangePassword()
    {
        $user = Users::find()->where(['id' => Yii::$app->user->getId()])->one();
        $new_password = Yii::$app->request->post('new_password');

        if (!$new_password) {
            throw new BadRequestHttpException();
        }

        $new_password = Yii::$app->security->generatePasswordHash($new_password);
        $user->password = $new_password;
        $user->save();
        unset($user->password);

        return $user;
    }

    public function actionSetPushToken()
    {
        $push_token = Yii::$app->request->post('push_token');
        if (!$push_token) {
            throw new BadRequestHttpException();
        }

        $user = Users::find()->where(['id' => Yii::$app->user->getId()])->one();
        $user->push_token = $push_token;
        $user->save();

        return;
    }
}
