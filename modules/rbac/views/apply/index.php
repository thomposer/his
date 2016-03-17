<?php

use yii\helpers\Html;

use app\common\AutoLayout;
use app\assets\AppAsset;
use app\modules\apply\models\ApplyPermissionList;
use yii\grid\GridView;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\apply\models\search\ApplyPermissionListSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '用户管理';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>

<?php $this->beginBlock('renderCss')?>
    <?php AppAsset::addCss($this,'@web/public/css/search.css')?>
<?php $this->endBlock()?>
<?php $this->beginBlock('content')?>

<div class="apply-permission-list-index col-xs-12">   
   <p>
      <?php if(isset($this->params['permList']['role'])||in_array($this->params['requestModuleController'].'/create', $this->params['permList'])):?>            
        <?= Html::a('添加用户', ['create'], ['class' => 'btn btn-success']) ?>
      <?php endif;?>
      <?php if(isset($this->params['permList']['role'])||in_array($this->params['requestModuleController'].'/register', $this->params['permList'])):?>            
        <?= Html::a('注册用户',['register'],['class' => 'btn btn-success']) ?>
      <?php endif;?>
  </p>
  <div class = "box">
    <div class = "box-body">
        <?php echo $this->render('_search', ['model' => $searchModel,'systemsRole'=>$systemsRole,'spotList' => $spotList,'roleList' =>$roleList,'status'=>$status]); ?>
        <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layout'=> '{items}<div class="text-right tooltip-demo">{pager}</div>',
        'tableOptions' => ['class' => 'table table-bordered table-hover'],
        'pager'=>[
             //'options'=>['class'=>'hidden']//关闭自带分页
             'firstPageLabel'=>"首页",
             'prevPageLabel'=>'上一页',
             'nextPageLabel'=>'下一页',
             'lastPageLabel'=>'尾页',
         ],
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            'username',
            [
              'attribute' => 'spot_name',
              'visible' => $systemsRole ? true : false,
            
            ],
            'item_name_description',
            
            'reason:ntext',
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function ($model){
                    return "<span class='".ApplyPermissionList::$color[$model->status]."'>".ApplyPermissionList::$apply_status[$model->status]."</span>";
                }
            ],
            [
                'class' => 'app\common\component\ActionColumn',                
                'template' => '{update} {delete}{reply}',  
                'buttons' => [
                    'reply' => function($url,$model,$key){
                        $redirect_url = Yii::getAlias('@userIndexReset');
                        if(!isset($this->params['permList']['role']) && !in_array($redirect_url, $this->params['permList'])){
                            return false;
                        }
                        $options = [
                            'title' => '重置密码',
                            'aria-label' => '重置密码',
                            'data-confirm' => '你确定进行重置密码吗?',
                            'data-method' => 'post',
                            'data-pjax' => '0',
                        ];  
                       
                        return Html::a('<span class = "glyphicon glyphicon-wrench "></span>',[$redirect_url,'id' => $model->id],$options);
                    }   
                ]
            ],
        ],
    ]); ?>
        </div>
    </div>
</div>
<?php $this->endBlock()?>
<?php $this->beginBlock('renderJs')?>
	
<?php $this->endBlock()?>
<?php AutoLayout::end()?>