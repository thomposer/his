<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\widgets\Pjax;
use johnitvn\ajaxcrud\CrudAsset;
use yii\grid\GridView;
use app\modules\spot_set\models\CheckListClinic;
use yii\helpers\Url;
CrudAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\modules\spot_set\models\search\CheckListClinicSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '影像学检查医嘱';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
    <?php AppAsset::addCss($this, '@web/public/css/lib/search.css')?>
<?php $this->endBlock()?>
<?php $this->beginBlock('content');?>
<?php echo $this->render(Yii::getAlias('@spotChargeItemNav')) ?>
<div class="check-list-clinic-index col-xs-10">
<?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>

   <div class = "box">
       <div class = 'row search-margin'>
         <div class = 'col-sm-2 col-md-2'>
           <?php  if(isset($this->params['permList']['role'])||in_array($this->params['requestModuleController'].'/check-list-clinic-create', $this->params['permList'])):?>
           		<?= Html::a("<i class='fa fa-plus'></i>新增",Url::to(['@spot_setChargeManageCheckListClinicCreate']), ['class' => 'btn btn-default font-body2','data-pjax' => 0,'role'=>'modal-remote','data-modal-size'=>'large']) ?>
           <?php endif?>
        </div>
        <div class = 'col-sm-10 col-md-10'>
            <?php echo $this->render('_search', ['model' => $searchModel]); ?>
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
            'firstPageLabel'=> Yii::getAlias('@firstPageLabel'),
            'prevPageLabel'=> Yii::getAlias('@prevPageLabel'),
            'nextPageLabel'=> Yii::getAlias('@nextPageLabel'),
            'lastPageLabel'=> Yii::getAlias('@lastPageLabel'),
        ],
        /*'filterModel' => $searchModel,*/
        'columns' => [
            'id',
            'name',
            'unit',
            'price',
            'meta',
            'remark',
            [
                'attribute' => 'status',
                'value' => function($model){
                    return CheckListClinic::$status[$model->status];
                }
            ],
            [
                'class' => 'app\common\component\ActionTextColumn',
                'template' => '{check-list-clinic-view}{check-list-clinic-update}{check-list-clinic-delete}',
                'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2'],
                'ajaxList'=>['delete' => true],
                'buttons' => [
                    'check-list-clinic-view' => function($url,$model,$key){
                        if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/check-list-clinic-view', $this->params['permList'])) {
                            return false;
                        }
                        $options = array_merge([
                            'data-pjax' => '0',
                            'class' => 'op-group-a'
                        ]);
                        return Html::a('查看', $url, $options);
                    },
                    'check-list-clinic-update' => function($url,$model,$key){
                        if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/check-list-clinic-update', $this->params['permList'])) {
                            return false;
                        }
                        $options = array_merge([
                            'role' => 'modal-remote',
                            'class' => 'op-group-a',
                            'data-modal-size'=>'large'
                        ]);
                        return Html::a('修改', $url, $options);
                    },
                    'check-list-clinic-delete' => function($url,$model,$key){
                        if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/check-list-clinic-delete', $this->params['permList'])) {
                               return false;
                        }
                        $message ="确认删除吗？<br><span style='font-size: 12px;color:#97A3B6;'>确认删除后，医嘱套餐里该医嘱项也会被删除。</span>";
                        $options = [
                            'data-confirm' => false,
                            'data-method' => false,
                            'data-request-method' => 'post',
                            'role' => 'modal-remote',
                            'data-confirm-title' => '系统提示',
                            'data-delete' => false,
                            'data-confirm-message' => $message,
                            'class' => 'op-group-a'
                        ];
                        return Html::a('删除', $url, $options); 
                    }
                ],
            ],
        ],
    ]); ?>
    </div>
    <?php  Pjax::end()?>
</div>
<?php $this->endBlock();?>
<?php $this->beginBlock('renderJs');?>
<?php AppAsset::addScript($this, '@web/public/plugins/select2/select2.full.min.js') ?>
<?php AppAsset::addScript($this, '@web/public/plugins/select2/i18n/zh-CN.js') ?>
<?php $this->endBlock();?>
<?php AutoLayout::end();?>
