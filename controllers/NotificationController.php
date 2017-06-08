<?php
namespace app\controllers;

use app\models\News;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\models\Notification;

/**
 * Site controller
 */
class NotificationController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['check', 'change-status'],
                'rules' => [
                    [
                        'allow' => true,
                        'controllers' => ['notification'],
                        'actions' => ['index', 'check', 'change-status'],
                        'verbs' => ['GET', 'POST'],
                        'roles' => ['@']
                    ]
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function actionIndex()
    {
        //prepare response formate
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $status = false;

        if(isset(Yii::$app->user->id)) {
            $status = true;
        } else {
            $status = false;
        }
        return ['status' => $status];

    }

    /**
     * @return array
     */
    public function actionCheck()
    {
        $notifications = Notification::find()
            ->where(["user_id"=>Yii::$app->user->id])
            ->where(["status"=>0])
            ->all();

        //prepare response formate
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return [
            'notifications' => $notifications
        ];
    }

    /**
     * @param $id
     */
    public function actionChangeStatus($id)
    {
        $notifications = Notification::findOne(['id'=>$id]);
        $notifications->status = 1;
        $notifications->save();

    }
}