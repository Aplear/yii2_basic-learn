<?php
namespace app\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\models\LoginForm;
use app\models\PasswordResetRequestForm;
use app\models\ResetPasswordForm;
use app\models\SignupForm;

use app\common\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\AccountActivation;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'allow' => true,
                        'controllers' => ['site'],
                        'actions' => ['signup', 'login', 'activate-account'],
                        'verbs' => ['GET', 'POST'],
                        'roles' => ['?']
                    ],
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }


    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        $emailActivation = Yii::$app->params['emailActivation'];
        $model = $emailActivation ? new SignupForm(['scenario' => 'emailActivation']) : new SignupForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($user = $model->signup()) {
                if ($user->status === User::STATUS_ACTIVE) {
                    if (Yii::$app->getUser()->login($user)) {
                        return $this->goHome();
                    }
                } else {
                    if ($model->sendActivationEmail($user)) {
                        Yii::$app->session->setFlash('success', 'Check your mail for active account <strong>' . Html::encode($user->email) . '</strong> (check the spam folder).');
                    } else {
                        Yii::$app->session->setFlash('error', 'Exception. did not send.');
                        Yii::error('Error send mail.');
                    }
                    return $this->refresh();
                }
            }else{
                Yii::$app->session->setFlash('error', 'There was an error registering.');
                Yii::error('Registration error.');
                return $this->refresh();
            }
        }

        return $this->render('signup', ['model' => $model]);
    }

    public function actionActivateAccount($key,$set_password=false)
    {

        try {
            $user = new AccountActivation($key);
            //if password set
            if($set_password === '1') {
                //if post set for password
                if(Yii::$app->request->post()) {
                    if($user->load(Yii::$app->request->post())) {
                        $user->setNewPassword();
                    } else {
                        return $this->render('setPassword', ['model' => $user]);
                    }
                } else {
                    return $this->render('setPassword', ['model' => $user]);
                }

            }
        }
        catch(InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if($user->activateAccount()) {
            Yii::$app->session->setFlash('success', 'Activation was success, <strong>' . Html::encode($user->username) . '</strong> welcome');
        } else {
            Yii::$app->session->setFlash('error', 'Activation error.');
            Yii::error('Activation error.');
        }
        return $this->redirect(Url::to(['/site/login']));
    }


    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }
}
