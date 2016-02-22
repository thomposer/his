<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\menu\models\search\MenuSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Menus';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
    <?php AppAsset::addCss($this, '@web/public/css/search.css')?>
<?php $this->endBlock()?>
<?php $this->beginBlock('content');?>

<div class="menu-index col-xs-12">

    <p>
        <?= Html::a('添加 Menu', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
   <div class = "box">
       <div class = "box-body"> 
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-bordered table-hover'],
        'layout'=> '{items}<div class="text-right tooltip-demo">{pager}</div>',
        'pager'=>[
            //'options'=>['class'=>'hidden']//关闭自带分页
            'firstPageLabel'=> '首页',
            'prevPageLabel'=> '上一页',
            'nextPageLabel'=> '下一页',
            'lastPageLabel'=> '尾页',
        ],
        /*'filterModel' => $searchModel,*/
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'menu_url:url',
            'type',
            'description',
            'parent_id',
            // 'status',
            // 'role_type',

            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'contentOptions' => ['class' => 'op-group'],
                'headerOptions'=>['class'=>'op-header'],
            ],
        ],
    ]); ?>
        </div>
    </div>
</div>
<?php $this->endBlock();?>
<?php $this->beginBlock('renderJs');?>

<?php $this->endBlock();?>
<?php AutoLayout::end();?>
