<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\UserSearch;
use yii\bootstrap\ActiveForm;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            if (!Yii::$app->user->can('userModuleCrud')) {
                throw new ForbiddenHttpException('Access denied');
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $emailActivation = Yii::$app->params['emailActivation'];
        $model = $emailActivation ? new User(['scenario' => 'emailActivation']) : new User();

        //ajax came
        if(Yii::$app->request->isAjax) {
            //prepare response formate
            Yii::$app->response->format = Response::FORMAT_JSON;
            if (Yii::$app->request->post() && $model->load(Yii::$app->request->post())) {
                return ActiveForm::validate($model);
            }
            return [
                $this->renderAjax('create', [ 'model'=>$model])
            ];
        } else {
            if (Yii::$app->request->post() && $model->load(Yii::$app->request->post())) {
                if($model->validate()) {
                    //save user if valide
                    if($user = $model->createUser()) {
                        //check sending activation email
                        if (!$model->sendActivationEmail($user, true)) {
                            Yii::$app->session->setFlash('error', 'Exception. did not send.');
                            Yii::error('Error send mail.');
                        }
                        return $this->redirect(Yii::$app->urlManager->createAbsoluteUrl("user/index"));
                    } else {
                        Yii::$app->session->setFlash('error', 'There was an error registering.');
                        Yii::error('Registration error.');
                        return $this->refresh();
                    }

                }

                throw new \Exception("System error, please contact to admin");
            }

        }
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        //ajax came
        if(Yii::$app->request->isAjax) {
            //prepare response formate
            Yii::$app->response->format = Response::FORMAT_JSON;
            //check if post
            if (Yii::$app->request->post() && $model->load(Yii::$app->request->post())) {
                return ActiveForm::validate($model);
            }
            return [
                $this->renderAjax('update', [ 'model'=>$model])
            ];
        } else {
            if (Yii::$app->request->post() && $model->load(Yii::$app->request->post())) {
                if($model->validate() && $model->save()) {
                    return $this->redirect(['index', 'id' => $model]);
                }
                throw new \Exception("System error, please contact to admin");
            }

        }
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
