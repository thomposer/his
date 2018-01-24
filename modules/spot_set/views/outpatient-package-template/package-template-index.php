<?php
use app\common\AutoLayout;
use app\assets\AppAsset;
use johnitvn\ajaxcrud\CrudAsset;
use yii\widgets\Pjax;
use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\spot_set\models\OutpatientPackageTemplate;
CrudAsset::register($this);

$this->title = '医嘱模板/套餐';
$this->params['breadcrumbs'][]=$this->title;
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
<?php AppAsset::addCss($this, '@web/public/css/lib/search.css')?>
<?php $this->endBlock()?>
<?php $this->beginBlock('content');?>
<?php echo $this->render('_templateSidebar')?>
<?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>
<div class="once-department-index col-xs-10">
    <div class = "box">
        <div class="box-header with-border">

            <span class = 'left-title'> <?= Html::encode($this->title) ?></span>
        </div>
        <div class = 'row search-margin'>
            <div class = 'col-sm-2 col-md-2'>
                <?php  if(isset($this->params['permList']['role'])||in_array($this->params['requestModuleController'].'/package-template-create', $this->params['permList'])):?>
                    <?= Html::a("<i class='fa fa-plus'></i>新增", ['package-template-create'], ['class' => 'btn btn-default font-body2','data-pjax' => 0]) ?>
                <?php endif?>
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
            'columns' => [
                'name',
                'price',
                [
                    'attribute'=>'type',
                    'value'=>function($model){
                        return OutpatientPackageTemplate::$getType[$model->type];
                    }
                ],
                'attribute'=>'userName',
                [
                    'attribute'=>'create_time',
                    'value'=>function($model){
                        return date("Y-m-d H:i:s",$model->create_time);
                    }
                ],
                [
                    'class' => 'app\common\component\ActionTextColumn',
                    'template' => '{package-template-update}{package-template-delete}',
                    'buttons' => [
                        
                        'package-template-update' => function($url,$model,$key){
                            if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/package-template-update', $this->params['permList'])) {
                                return false;
                            }
                            return Html::a('修改',$url,['data-pjax' => 0,'class'=>'op-group-a']);
                        
                        },
                        'package-template-delete' => function($url,$model,$key){
                            if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/package-template-delete', $this->params['permList'])) {
                                return false;
                            }
                            $options = [
                                'data-confirm' => false,
                                'data-method' => false,
                                'data-request-method' => 'post',
                                'role' => 'modal-remote',
                                'data-confirm-title' => '系统提示',
                                'data-delete' => false,
                                'data-confirm-message' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                            ];
                            return Html::a('删除',$url,$options);
                        
                        }
                    ]
                ],

            ],
        ]); ?>
    </div>
    <?php  Pjax::end()?>
</div>
<?php $this->endBlock();?>
<?php AutoLayout::end();?>
