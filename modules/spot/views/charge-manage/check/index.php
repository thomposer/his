<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\grid\GridView;
use app\modules\spot\models\CheckList;
use yii\widgets\Pjax;
use johnitvn\ajaxcrud\CrudAsset;
use yii\helpers\Url;
CrudAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\modules\spot\models\search\CheckListSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '影像学检查管理';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>

    <?php AppAsset::addCss($this, '@web/public/css/lib/search.css')?>
    <?php AppAsset::addCss($this, '@web/public/css/spot/recipeList.css')?>

<?php $this->endBlock()?>
<?php $this->beginBlock('content');?>
<?php echo $this->render(Yii::getAlias('@spotChargeItemNav')) ?>
<div class="check-list-index col-xs-10">
    <?php Pjax::begin(['id' => 'crud-datatable-pjax']);?>
    
   <div class = "box">
   <div class = 'row search-margin'>
      <div class = 'col-sm-2 col-md-2'>
       <?php  if(isset($this->params['permList']['role'])||in_array($this->params['requestModuleController'].'/check-create', $this->params['permList'])):?>
       <?= Html::a("<i class='fa fa-plus'></i>新增",  Url::to(['@spotChargeManageCheckCreate']), ['class' => 'btn btn-default font-body2','data-pjax' => 0]) ?>
       <?php endif?>
    </div>
    <div class = 'col-sm-10 col-md-10'>
                <?php echo $this->render('_search', ['model' => $searchModel,'spotList' => $spotList]); ?>
        </div>
   </div>
     <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['class' => 'grid-view table-responsive add-table-padding'],
        'tableOptions' => ['class' => 'table table-hover table-border header'],
         'layout' => '{items}<div class="text-right">{summary}{pager}</div>',
         'summary' =>'<div class="table-summary">( {totalCount} 结果，共 {pageCount} 页 )</div>',
        'pager'=>[
            //'options'=>['class'=>'hidden']//关闭自带分页
            'hideOnSinglePage' => false,//在只有一页时也显示分页
            'firstPageLabel' => Yii::getAlias('@firstPageLabel'),
            'prevPageLabel' => Yii::getAlias('@prevPageLabel'),
            'nextPageLabel' => Yii::getAlias('@nextPageLabel'),
            'lastPageLabel' => Yii::getAlias('@lastPageLabel'),
        ],
        /*'filterModel' => $searchModel,*/
        'columns' => [
            [
                'attribute' => 'id',
                'headerOptions' => ['class' => 'col-sm-1 col-md-1'],
            ],
            [
                'attribute' => 'name',
            ],
            [
                'attribute' => 'unit',
            ],
//            [
//                'attribute' => 'price',
//            ],
            [
                'attribute' => 'meta',
            ],
            [
                'attribute' => 'unionSpotId',
                'contentOptions' => ['class' => 'configure-clinic-name'],
                'value' => function($searchModel) use($spotNameList){
                    return $spotNameList[$searchModel->id]['spotName'];
                }
            ],
            [
                'attribute' => 'remark',
            ],
            // 'international_code',
            [
                'attribute' => 'status',
                'value' => function($searchModel){
                    return CheckList::$getStatus[$searchModel->status];
                }
            ],
            [
                'class' => 'app\common\component\ActionTextColumn',
                'template' => '{check-view}{check-update}{check-delete}',
                'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2'],
                'buttons' => [
                    'check-view'=>function($url,$model){
                        if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/check-view', $this->params['permList'])) {
                            return false;
                        }
                        return Html::a('查看', $url,['class'=>'op-group-a']);
                    },
                    'check-update'=>function($url,$model){
                        if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/check-update', $this->params['permList'])) {
                            return false;
                        }
                        return Html::a('修改', $url,['class'=>'op-group-a']);
                    },
                    'check-delete' => function ($url, $model, $key) {
                        if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/check-delete', $this->params['permList'])) {
                            return false;
                        }

                        if($model->status == 1){
                            $title = '停用';
                            $deleteColor = false;
                            $message = "确认停用吗？<br><span style='font-size: 12px;color:#97A3B6;'>确认停用后，该医嘱项在诊所下也会被停用并且医嘱套餐里该医嘱项也会被删除。</span>";
                        }else{
                            $title = '启用';
                            $deleteColor = true;
                            $message = '确认启用吗？';
                        }

                        $options = [
                            'data-method' => false,
                            'data-request-method' => 'post',
                            'role' => 'modal-remote',
                            'data-confirm-title' => '系统提示',
                            'data-delete' => $deleteColor,
                            'data-confirm-message' => $message,
                            'class'=>'op-group-a'
                        ];

                        return Html::a($title, Url::to(['check-delete', 'id' => $model->id]), $options);
                    }
                ]
            ],
        ],
    ]); ?>
    </div>
    <?php Pjax::end();?>
</div>
<?php $this->endBlock();?>
<?php $this->beginBlock('renderJs');?>

<?php $this->endBlock();?>
<?php AutoLayout::end();?>
