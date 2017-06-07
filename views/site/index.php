<?php

/* @var $this yii\web\View */

use yii\helpers\Url;
use yii\widgets\ListView;
use yii\helpers\Html;

$this->title = 'Basic Test';
?>
<div class="site-index">

    <div class="header">

        <h2>News...</h2>

    </div>

    <div class="body-content">
        <?= Html::dropDownList('pagination', \app\models\News::getPerPage(), [
                '1' => '1 per page',
                '5' => '5 per page',
                '10' => '10 per page',
            ], [
                'class' => "form-control input-sm pagination",
                'data-change'=> Url::toRoute('index')
        ])
        ?>
        <div class="row">
            <?php
            echo ListView::widget([
                'dataProvider' => $dataProvider,
                'itemView' => '_list',
                'layout' => "{summary}\n{items}\n{pager}",
                'emptyText' => 'No posts yet...',
            ]);
            ?>
        </div>

    </div>
</div>

<script>
    $('select.pagination').on('change', function() {
        document.location.href = $(this).attr('data-change') + '&page=1&per-page=' + $(this).val();
    });
</script>