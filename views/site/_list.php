<?php
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
?>


<div class="col-sm-6 col-md-3">
    <div class="thumbnail">
        <?php echo Html::img('@web/images/news/'.$model->user_id.'/'.$model->image_path, [
            'alt' => Html::encode($model->title),
        ]) ?>
        <div class="caption">
            <h3><?= Html::encode($model->title) ?></h3>
            <p><?= wordwrap(HtmlPurifier::process($model->short_text), '30', '<br \>',true) ?></p>
            <p>
                <a href="/index.php?r=news%2Fdetails&id=<?=$model->id?>" class="btn btn-primary" role="button">Details...</a>
            </p>
        </div>
    </div>
</div>
