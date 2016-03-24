<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\make_appointment\models\search\PatientSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Patients';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
    <?php AppAsset::addCss($this, '@web/public/css/lib/search.css')?>
<?php $this->endBlock()?>
<?php $this->beginBlock('content');?>

<div class="patient-index col-xs-12">
    <p>
       <?php  if(isset($this->params['permList']['role'])||in_array($this->params['requestModuleController'].'/create', $this->params['permList'])):?>
       <?= Html::a('添加 Patient', ['create'], ['class' => 'btn btn-success']) ?>
       <?php endif?>
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
            'user_name',
            'sex',
            'birthday',
            'nation',
            // 'marriage',
            // 'occupation',
            // 'province',
            // 'city',
            // 'area',
            // 'detail_address',

            [
                'class' => 'app\common\component\ActionColumn'
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
