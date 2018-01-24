<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\grid\GridView;
use app\modules\user\models\User;
use yii\widgets\Pjax;
use johnitvn\ajaxcrud\CrudAsset;
CrudAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\modules\user\models\search\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = '人员管理';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
    <?php AppAsset::addCss($this, '@web/public/css/lib/search.css')?>
<?php $this->endBlock()?>
<?php $this->beginBlock('content');?>

<div class="user-index col-xs-12">
    <?php Pjax::begin(['id' => 'crud-datatable-pjax'])?>
    
   <div class = "box">
   <div class = 'row search-margin'>
      <div class = 'col-sm-2 col-md-2'>
       <?php  if(isset($this->params['permList']['role'])||in_array($this->params['requestModuleController'].'/create', $this->params['permList'])):?>
       <?= Html::a('<i class="fa fa-plus"></i>新增', ['create'], ['class' => 'btn btn-default font-body2','data-pjax' => 0]) ?>
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
        /*'filterModel' => $searchModel,*/
        'columns' => [
//             ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'attribute' => 'username',
            ],
            [
                'attribute' => 'email',
            ],
            [
                'attribute' => 'iphone',
            ],
            [
                'attribute' => 'sex',
                'value' => function ($searchModel){
                    return User::$getSex[$searchModel->sex];
                }
            ],
            [
                'attribute' => 'clinic_name',
                'value' => function ($searchModel) use($spotNameList){
                    return $spotNameList[$searchModel->id]['clinic_name'];
                }
            ],
            [
                'attribute' => 'occupation',
                'value' => function ($searchModel){
                    return User::$getOccuption[$searchModel->occupation];
                }
            ],
            [
                'attribute' => 'occupation_type',
                'value' => function ($searchModel){
                    return User::$getOccupationType[$searchModel->occupation_type];
                }
            ],
            [
                'attribute' => 'status',
                'value' => function($searchModel){
                    return User::$getStatus[$searchModel->status];
                }
            ],
            [
                'class' => 'app\common\component\ActionTextColumn',
                'template' => '{view}{update}{delete}{reply}',
                'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2'],
                'buttons' => [
                    'delete' => function($url,$model,$key){
                        if((!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'].'/delete', $this->params['permList']))||Yii::$app->user->identity->id == $key){
                            return false;
                        }
                        $options = [
                            'title'=>'删除',
                            'class' => 'op-group-a',
                            'data-confirm'=>false,
                            'data-method'=>false,
                            'data-request-method'=>'post',
                            'role'=>'modal-remote',
                            'data-confirm-title'=>'系统提示',
                            'data-delete' => false,
                            'data-confirm-message'=>Yii::t('yii', 'Are you sure you want to delete this item?'),
                        ];
                        return Html::a('删除', $url, $options);
                        
                    },
                    'reply' => function($url,$model,$key){
                        $redirect_url = Yii::getAlias('@userManageReset');
                        if(!isset($this->params['permList']['role']) && !in_array($redirect_url, $this->params['permList'])){
                            return false;
                        }
                        $options = [
                            'title' => '重置密码',
                            'class' => 'op-group-a',
                            'aria-label' => '重置密码',
                            'data-confirm' => '你确定进行重置密码吗?',
                            'data-method' => 'post',
                            'data-pjax' => '0',
                            'delete' => true,//true是反向操作，false是正向操作,按钮位置一致，区别只是视觉重量在哪边
                        ];
                         
                        return Html::a('重置密码',[$redirect_url,'id' => $model->id],$options);
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
