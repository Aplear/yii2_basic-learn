<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\jui\DatePicker;
/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create User', ['create'], [ 'id'=>'modal-btn-create', 'class' => 'btn btn-success']) ?>
    </p>
    <?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'username',
            'email:email',
            //'created_at',
            [
                'attribute' => 'created_at',
                'value' => 'created_at',
                'format' => ['date', 'php:Y-m-d H:i:s'],
                'filter' => DatePicker::widget([
                    'model'=>$searchModel,
                    'attribute'=>'created_at',
                    'dateFormat' => 'php:Y-m-d',
                ]),
            ],


            [
                'attribute' => 'logined_at',
                'value' => 'logined_at',
                'format' => ['date', 'php:Y-m-d H:i:s'],
                'filter' => DatePicker::widget([
                    'model'=>$searchModel,
                    'attribute'=>'logined_at',
                    'dateFormat' => 'php:Y-m-d',
                ]),
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
    <?php Pjax::end(); ?></div>

<?php
yii\bootstrap\Modal::begin([
    'header' => 'Create User',
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
            // apply user model window
            $('#modal').modal('show')
                .find('#modal-content')
                .load($(this).attr('data-target'));
            //insert form into modal body
            modal_body.html(data);
        });
        return false;
    });

</script>
