<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\helpers\Json;
use yii\helpers\Url;
use app\modules\apply\models\ApplyPermissionList;
/* @var $this yii\web\View */
/* @var $model app\modules\apply\models\search\ApplyPermissionListSearch */
/* @var $form yii\widgets\ActiveForm */
$baseUrl = Yii::$app->request->baseUrl;
?>
<script src="<?php echo $baseUrl.'/public/js/jquery/jquery.min.js' ?>" type="text/javascript" charset="utf-8"></script>

<div class="apply-permission-list-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' =>  ['class' => 'form-horizontal search-form'],
        'fieldConfig' => [
            'template' => "<div class='search-labels text-right'>{label}</div><div class='col-xs-9 col-sm-7'>{input}</div>",
        ]
    ]); ?>

    <?= $form->field($model, 'id') ?>
    <?= $form->field($model,'spot')->dropDownList(ArrayHelper::map($spotList?$spotList:array(), 'spot', 'spot_name'),[
        'prompt' => '请选择站点'
    ])?>
    <?= $form->field($model, 'status')->dropDownList(ApplyPermissionList::$apply_status,['prompt' => '全部']) ?>
    <?= $form->field($model, 'item_name')->dropDownList(ArrayHelper::map($roleList?$roleList:array(), 'name', 'description','spot'),[
        'prompt' => '请选择角色'
    ]) ?>   
   
     <?= Html::submitButton('查询', ['class' => 'btn btn-primary btn-submit']) ?>
     <?= Html::a('重置',Url::to(['@ApplyApplyIndex']),['class' => 'btn btn-default']) ?>
   

    <?php ActiveForm::end(); ?>
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
            'id',
            'wxname',
            'item_name_description',
             
            'reason:text',
            [
                'attribute' => 'apply_persons',
                'value' => function($searchModel){
                    if($searchModel->status == 1){
                        return '结束';
                    }
                    return $searchModel->apply_persons;
                }
            ],
            [
            'attribute' => 'status',
            'format' => 'raw',
            'value' => function ($model){
                    
                    
                    return "<span class='".ApplyPermissionList::$color[$model->status]."'>".ApplyPermissionList::$apply_status[$model->status]."</span>";
                }
            ],

        ],
        ])?>
</div>
