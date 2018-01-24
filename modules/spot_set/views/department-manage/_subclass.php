<?php

use yii\grid\GridView;
use app\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use app\modules\spot\models\SecondDepartment;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
$baseUrl = Yii::$app->request->baseUrl;
?>

<?=

GridView::widget([
    'dataProvider' => $dataProvider,
    'tableOptions' => ['class' => 'table table-hover second-department-subclass'],
    'layout' => '{items}',
    'showHeader' => false,
    'columns' => [
        [
            'attribute' => 'id',
            'contentOptions' => ['class' => 'col-sm-4 col-md-4 col-xs-4'],
            'format' => 'raw',
            'value' => function ($searchModel) use ($secondDepartmentId){
                $roomList[] = ['id' => $searchModel->id,'room_id' => $searchModel->id];
                $selection = in_array($searchModel->id,$secondDepartmentId)? $searchModel->id : null;
                $items = ArrayHelper::map($roomList, 'id', 'room_id');
//                $name = 'secondDepartmentUnionId';
                $name = 'secondDepartmentUnionId[' . $searchModel->onceDepartmentId .']';
                $hiddenInput = Html::input('hidden','onceDepartmentId[]',$searchModel->onceDepartmentId,['class'=>'form-control']);
                return $hiddenInput.Html::checkboxList($name,$selection,$items,['itemOptions' => ['labelOptions' => ['class' => 'col-xs-8 col-sm-8 col-md-8 custom-label','style' => 'padding:0px;']]]);
            }
        ],
        [
            'attribute' => 'name',
            'contentOptions' => ['class' => 'col-sm-4 col-md-4 col-xs-4','style' => 'padding-left:4.5%']
        ],
        [
            'attribute' => 'status',
            'contentOptions' => ['class' => 'col-sm-3 col-md-3 col-xs-3'],
            'value' => function($model){
                return SecondDepartment::$getStatus[$model->status];
            }
        ],
        [
            'attribute' => 'room_type',
            'contentOptions' => ['class' => 'col-sm-3 col-md-3 col-xs-3'],
            'value' => function($model){
                return SecondDepartment::$getRoomType[$model->room_type];
            }
        ]
    ],
]);
?>

