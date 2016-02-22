<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\helpers\Url;
use app\modules\apply\models\ApplyPermissionList;
/* @var $this yii\web\View */
/* @var $model app\modules\apply\models\search\ApplyPermissionListSearch */
/* @var $form yii\widgets\ActiveForm */
$baseUrl = Yii::$app->request->baseUrl;
?>
<div class="apply-permission-list-search">
    <p>
        <?= Html::a('添加用户', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <div class = "box">
    <div class = "box-body">
    
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
       'options' =>  ['class' => 'form-horizontal search-form'],
        'fieldConfig' => [
            'template' => "<div class='search-labels text-right'>{label}</div><div class='col-xs-9 col-sm-7'>{input}</div>",
        ]
    ]); ?>

    <?= $form->field($model, 'user_id'); ?>
    <?php if($systemsRole):?>
        <?= $form->field($model,'spot')->dropDownList(ArrayHelper::map($spotList, 'spot', 'spot_name'),[
            'prompt' => '请选择站点'
        ])?>
    <?php endif;?>
    <?= $form->field($model, 'status')->dropDownList($status) ?>
    <?= $form->field($model, 'item_name')->dropDownList(ArrayHelper::map($roleList, 'name', 'description','spot'),[
        'prompt' => '请选择角色'
    ]) ?>   
   <div class="form-group search_button">
    <?= Html::submitButton('查询', ['class' => 'btn btn-primary btn-submit']) ?>
    <?= Html::a('重置',Url::to(['@rbacApplyIndex']), ['class' => 'btn btn-default']) ?>
   </div>

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
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'contentOptions' => ['class' => 'op-group'],
                'headerOptions'=>['class'=>'op-header'],
                'template' => '{update} {delete}',
                               
            ],
        ],
    ]); ?>
</div>
</div>
</div>
