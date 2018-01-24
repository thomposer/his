<?php
use yii\helpers\Url;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\triage\models\search\NursingRecordSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<div id="ptab3" class="tab-pane nursing-record-index col-xs-12" data-type="3">
    <?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>
    <div id="ajaxCrudDatatable" class='box'>
        <div class='row search-margin change-padding'>
            <div class='consume-padding col-sm-2 col-md-2'>
                <?php if (isset($this->params['permList']['role']) || in_array(Yii::getAlias('@triageNursingRecordCreate'), $this->params['permList'])): ?>
                    <?= Html::a("<i class='fa fa-plus'></i>新增", [Yii::getAlias('@nursingRecordCreate'),'recordId'=>$recordId], ['class' => 'btn btn-default font-body2', 'role' => 'modal-remote', 'data-toggle' => 'tooltip']) ?>
                <?php endif ?>
            </div>
        </div>
        <?= GridView::widget([
            'id' => 'crud-datatable',
            'dataProvider' => $dataProvider,
            //'filterModel' => $searchModel,
            'options' => ['class' => 'grid-view table-responsive add-table-padding consume-padding'],
            'tableOptions' => ['class' => 'table-border header'],
            'layout' => '{items}<div class="text-right">{pager}</div>',
            'pager' => [
                //'options'=>['class'=>'hidden']//关闭自带分页
                
                'firstPageLabel' => Yii::getAlias('@firstPageLabel'),
                'prevPageLabel' => Yii::getAlias('@prevPageLabel'),
                'nextPageLabel' => Yii::getAlias('@nextPageLabel'),
                'lastPageLabel' => Yii::getAlias('@lastPageLabel'),
            ],
            'columns' => [
                [
                    'attribute' => 'name',
                    'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2'],
                ],
                [
                    'attribute' => 'executor',
                    'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2'],
                ],
                [
                    'attribute' => 'create_time',
                    'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2'],
                    'value' => function($model){
                        if ($model->create_time) {
                            return date('Y-m-d H:i', $model->create_time);
                        }
                        return '';
                    }
                ],
                [
                    'class' => 'app\common\component\ActionColumn',
                    'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2'],
                    'template' => '{view}{update}{delete}',
                    'buttons' => [
                        'view' => function ($url, $model, $key)use($recordId) {

                            if (!isset($this->params['permList']['role']) && !in_array(Yii::getAlias('@triageNursingRecordCareModal'), $this->params['permList'])) {
                                return false;
                            }
                            $options = [
                                'class' => 'icon_button_view fa fa-eye',
                                'id' => $model->id,
                                'title' => '查看',
                                'data-toggle' => 'tooltip',
                                'role' => 'modal-remote',
                                'data-modal-size' => 'large',
                                'data-url' => Url::to(['@nursingRecordCareModal', 'id' => $model->id,'recordId' => $recordId])
                            ];
                            /* 完善信息按钮 */
                            return Html::a('', '#', $options);
                        },
                        'update' => function ($url, $model, $key)use($recordId) {
                            if (isset($this->params['permList']['role']) || in_array(Yii::getAlias('@triageNursingRecordUpdate'), $this->params['permList'])) {
                                return Html::a('', [Yii::getAlias('@nursingRecordUpdate'), 'id' => $model->id,'recordId' => $recordId], ['class' => 'icon_button_view fa fa-pencil-square-o', 'title' => '修改', 'data-toggle' => 'tooltip','role'=>'modal-remote']);
                            }
                        },
                        'delete' => function ($url, $model, $key)use($recordId) {
                            if (isset($this->params['permList']['role']) || in_array(Yii::getAlias('@triageNursingRecordDelete'), $this->params['permList'])) {
                                return Html::a('', [Yii::getAlias('@nursingRecordDelete'), 'id' => $model->id,'recordId' => $recordId], ['class' => 'icon_button_view fa fa-trash-o', 'title' => '删除', 'data-toggle' => 'tooltip','role'=>"modal-remote",'data-request-method'=>"post"]);
                            }
                        }
                    ]
                ],

            ],
            'striped' => false,
            'condensed' => false,
            'hover' => true,
            'bordered' => false,

        ]) ?>
    </div>
    <?php Pjax::end() ?>
</div>

