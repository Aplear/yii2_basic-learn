<?php

use yii\helpers\Html;
use yii\grid\GridView;
use jino5577\daterangepicker\DateRangePicker;

/* @var $this yii\web\View */
/* @var $searchModel app\models\NewsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'News';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="news-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create News', ['create'], [ 'id'=>'modal-btn-create', 'class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => [
            'class' => 'table table-striped table-bordered'
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'title',
            [
                'attribute'=>'short_text',
                'format'=>'raw',
                'headerOptions' => [ 'class' => 'col-md-2' ],
                'content'=>function($dataProvider){
                    return wordwrap($dataProvider->short_text, 50, "\n", true);
                },
            ],
            [
                'attribute'=>'status',
                'value' => function ($model) {
                    return HTML::activeDropDownList($model, 'status', $model->status_array, [
                        'class' => 'form-control status_change',
                        'data-url' => '/index.php?r=news%2Fchange-status&id='.$model->id.'&status='

                    ]);
                },
                'format' => 'raw',
                'filter'=> ["1"=>"deactivate","2"=>"active"],
            ],
            [
                'attribute' => 'created_at',
                'value' => function ($model) {
                    if (extension_loaded('intl')) {
                        return Yii::t('app', '{0, date, MMMM dd, YYYY HH:mm}', [$model->created_at]);
                    } else {
                        return date('Y-m-d G:i:s', $model->created_at);
                    }
                },
                'headerOptions' => [ 'class' => 'col-md-2' ],
                'filter' => DateRangePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'created_at_range',
                    'pluginOptions' => [
                        'format' => 'd-m-Y',
                        'autoUpdateInput' => false
                    ]
                ])
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'update' => function ($url,$model) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-edit"></span>',
                            $url,
                            [ 'id'=>'modal-btn-update', 'title' => 'Update', 'aria-label'=>'Update', 'data-pjax' => 0]
                        );
                    },
                ],
            ],
        ],
    ]); ?>
</div>


<?php
yii\bootstrap\Modal::begin([
    'header' => 'Create News',
    'id' => 'modal',
    'size' => 'modal-md',
]);
?>
    <div id='modal-content'>Loading...</div>
<?php yii\bootstrap\Modal::end(); ?>

<script>
    //create handler && update handler
    $('#modal-btn-create, #modal-btn-update').click(function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        var modal_body = $('.modal-body');
        $.get(url, function(data) {
            // apply news model window
            $('#modal').modal('show')
                .find('#modal-content')
                .load($(this).attr('data-target'));
            //insert form into modal body
            modal_body.html(data);
        });
        return false;
    });

    //change news status ajax
    $(".status_change").on("change", function () {
        var url = $(this).attr('data-url')+this.value;
        $.get(url, {})
            .done(function() {

        });
        return false;
    })

</script>

