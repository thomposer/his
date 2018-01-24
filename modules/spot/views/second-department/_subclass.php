<?php

use yii\grid\GridView;
use app\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use app\modules\spot\models\SecondDepartment;
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
        ],
        [
            'attribute' => 'name',
            'contentOptions' => ['style' => 'padding-left:3%']
        ],
        [
            'attribute' => 'status',
            'contentOptions' => ['class' => 'col-sm-2 col-md-2 col-xs-2'],
            'value' => function($model){
                    return SecondDepartment::$getStatus[$model->status];
            }
        ],
        [
            'attribute' => 'room_type',
            'contentOptions' => ['class' => 'col-sm-2 col-md-2 col-xs-2', 'style' => 'padding-left:3%'],
            'value' => function($model){
                    return SecondDepartment::$getRoomType[$model->room_type];
            }
        ],
        [
            'class' => 'app\common\component\ActionTextColumn',
            'template' => '{update}{delete}',
            'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2'],
            'contentOptions' => ['style' => 'padding-left:4.5%'],
            'buttons' => [
                'update' => function ($url, $model, $key) {
//                        if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'].'/second-department-update', $this->params['permList'])) {
//                                return false;
//                        }
                        $options = array_merge([
                            'role'=>'modal-remote',
                            'data-modal-size'=>'middle'
                        ]);
                        return Html::a('修改', Url::to(['@spotDepartmentManageSecondDepartmentUpdate', 'id' => $key]),$options);
                },
                'delete' => function ($url, $model, $key) {
//                        if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/second-department-delete', $this->params['permList'])) {
//                                return false;
//                        }
                        $deleteStr = $model->status == 1?'确认禁用吗？禁用后该科室在诊所下也会被禁用？':'确认启用吗？';
                        $options = [
                            'data-method' => false,
                            'data-request-method' => 'post',
                            'role' => 'modal-remote',
                            'data-confirm-title' => '系统提示',
                            'data-delete' => false,
                            'data-modal-size'=>'middle',
                            'data-confirm-message' => $deleteStr,
                        ];
                        return Html::a($model->status==1?'禁用':'启用', Url::to(['@spotDepartmentManageSecondDepartmentDelete', 'id' => $model->id]), $options);
                }
            ],
        ],
    ],
]);
?>
