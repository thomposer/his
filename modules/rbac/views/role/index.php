<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\grid\GridView;
use yii\widgets\Pjax;
use johnitvn\ajaxcrud\CrudAsset;
CrudAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\modules\rbac\models\search\RoleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = '角色管理';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$parentSpotCode = Yii::$app->cache->get(Yii::getAlias('@parentSpotCode').$_COOKIE['spotId'].Yii::$app->user->identity->id);

?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
    <?php AppAsset::addCss($this, '@web/public/css/lib/search.css')?>
<?php $this->endBlock()?>
<?php $this->beginBlock('content');?>

<div class="item-index col-xs-12">
    <?php Pjax::begin(['id' => 'crud-datatable-pjax'])?>
    
   <div class = "box">
    <div class = 'row search-margin'>
      <div class = 'col-sm-2 col-md-2'>
       <?php  if(isset($this->params['permList']['role'])||in_array($this->params['requestModuleController'].'/create', $this->params['permList'])):?>
       <?= Html::a("<i class='fa fa-plus'></i>新增", ['create'], ['class' => 'btn btn-default font-body2','data-pjax' => 0]) ?>
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
            'firstPageLabel' => Yii::getAlias('@firstPageLabel'),
            'prevPageLabel' => Yii::getAlias('@prevPageLabel'),
            'nextPageLabel' => Yii::getAlias('@nextPageLabel'),
            'lastPageLabel' => Yii::getAlias('@lastPageLabel'),
        ],

        'columns' => [
            ['class' => 'yii\grid\SerialColumn','header' => '序号'],
            [
                'attribute' => 'description',
                'format' => 'text',
            ],
            [
                'attribute' => 'data',
                'format' => 'text',
                'value' => function($searchModel){
                       return $searchModel->data?$searchModel->data:null;
                },
            ],
            [
                'label' => '权限',
                'format' => 'raw',
                'value' => function ($searchModel)use($parentSpotCode){
                    $parentRolePrefix = $parentSpotCode.'_roles_';
                    $systemRole = $parentRolePrefix.'system';
                    if ((!isset($this->params['permList']['role'])||!in_array($this->params['requestModuleController'].'/apply', $this->params['permList'])) && ($searchModel->name == $systemRole)){
                        return false;
                    }
                    return Html::a('权限设置',['apply','id' => $searchModel->name],['class' => 'btn btn-reset','data-pjax' => 0]);
                }
            ],
            [
                'class' => 'app\common\component\ActionTextColumn',
                'template' => '{update}{delete}',
                'headerOptions' => ['class' => 'col-sm-2 col-md-2'],
                'buttons' => [
                    
                    'delete' => function($url,$model,$key)use($parentSpotCode){
                        $parentRolePrefix = $parentSpotCode.'_roles_';
                        $defaultRoleList = include(Yii::getAlias('@initDefaultRoleUrl'));
                        $systemRole = $parentRolePrefix.'system';
                       if ((!isset($this->params['permList']['role'])||!in_array($this->params['requestModuleController'].'/delete', $this->params['permList'])) && ($model->name == $systemRole)){
                           return false;
                       }
                        $options = array_merge([
                            'data-confirm'=>false,
                            'data-method'=>false,
                            'data-request-method'=>'post',
                            'role'=>'modal-remote',
                            'data-confirm-title'=>'系统提示',
                            'data-confirm-message'=>Yii::t('yii', 'Are you sure you want to delete this item?'),
                            'title' => Yii::t('yii', 'Delete'),
                            'aria-label' => Yii::t('yii', 'Delete'),
                            'data-delete' => false,
                            'data-pjax' => '1',
                        ]);
                        
                        return Html::a('删除', $url, $options);
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
