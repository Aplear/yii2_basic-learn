<?php

use yii\helpers\Html;
use yii\helpers\HtmlPurifier;

/* @var $this yii\web\View */
/* @var $model app\models\News */

$this->title = $model->title;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="news-view">


    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="media">
        <a class="pull-left" href="#">
            <?php echo Html::img('@web/images/news/'.$model->user_id.'/'.$model->image_path, ['alt' => Html::encode($model->title),]) ?>
        </a>
        <div class="media-body">
            <h4 class="media-heading">Media heading</h4>
            <p class="lead">
                <?=HtmlPurifier::process($model->full_text)?>
            </p>

        </div>
    </div>

</div>
