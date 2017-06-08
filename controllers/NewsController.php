<?php

namespace app\controllers;

use Yii;

use app\helper\notification\SendNotification;
use app\helper\notification\EmailNotification;
use app\helper\notification\BrowserNotification;
use app\models\UserNotifyGraber;

use app\models\News;
use app\models\NewsSearch;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\imagine\Image as Imagine;

/**
 * NewsController implements the CRUD actions for News model.
 */
class NewsController extends Controller
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

    /**
     * @param \yii\base\Action $action
     * @return bool
     * @throws ForbiddenHttpException
     */
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            if (!\Yii::$app->user->can($action->id)) {
                throw new ForbiddenHttpException('Access denied');
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Lists all News models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new NewsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single News model.
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
     * Displays a single News full story.
     * @param integer $id
     * @return mixed
     */
    public function actionDetails($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new News model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new News();
        //ajax came
        if(Yii::$app->request->isAjax) {
            //prepare response formate
            Yii::$app->response->format = Response::FORMAT_JSON;
            //check if post
            if (Yii::$app->request->post() && $model->load(Yii::$app->request->post())) {
                return ActiveForm::validate($model);
            }
            return [
                $this->renderAjax('create', [ 'model'=>$model])
            ];

        } else {
            //check if post
            if (Yii::$app->request->post() && $model->load(Yii::$app->request->post())) {
                $model->user_id = \Yii::$app->user->id;
                //upload image ad save news
                if($model->upload() && $model->save()) {
                    //send notification start
                    $this->sendNotification(Yii::$app->urlManager->createAbsoluteUrl(['news/details', 'id'=>$model->id]));
                    return $this->redirect(['index', 'id' => $model]);
                }

                throw new \Exception("System error, please contact to admin");
            }
        }
    }

    /**
     * Updates an existing News model.
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
            //check if post
            if (Yii::$app->request->post() && $model->load(Yii::$app->request->post())) {
                if ($model->upload() && $model->validate()) {
                    $model->save();
                    return $this->redirect(['index', 'id' => $model]);
                }

                throw new \Exception("System error, please contact to admin");
            }
        }
    }

    /**
     * @param $link
     */
    public function sendNotification($link)
    {
        //prepare data for sending
        $result = UserNotifyGraber::prepareData($link);
        //set notifications objects
        $notifications =  [
            new EmailNotification($result['email']),
            new BrowserNotification($result['browser']),
        ];

        $sendNotification = new SendNotification($notifications);
        $sendNotification->sendNotification();
    }

    public function actionChangeStatus($id, $status)
    {
        //ajax came
        if(Yii::$app->request->isAjax) {
            //check in come data
            if(is_numeric($id) && is_numeric($status)) {
                //find data for update
                $model = News::findOne([
                    'id'=>$id,
                    'user_id' => Yii::$app->user->id
                ]);
                if(!is_null($model)) {
                    $model->status = $status;
                    $model->save();
                } else {
                    return false;
                }
                //prepare response formate
                \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return [
                    'status' => $model->status
                ];
            }
            throw new \Exception("Data is not valide!");
        }
        throw new \Exception("System error, please contact to admin");
    }
    /**
     * Deletes an existing News model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (!\Yii::$app->user->can('deleteOwnNews', ['news_id' => \Yii::$app->user->id])) {
            throw new ForbiddenHttpException('Access denied');
        }
        $model = $this->findModel($id);
        $model->unlinkNewsImage();
        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the News model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return News the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = News::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
