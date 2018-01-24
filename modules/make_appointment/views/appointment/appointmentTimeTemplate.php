<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\widgets\Pjax;
use johnitvn\ajaxcrud\CrudAsset;
use yii\grid\GridView;
use yii\helpers\Url;
CrudAsset::register($this);
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '预约时间模板';
$this->params['breadcrumbs'][] = ['label' => '预约时间设置', 'url' => URL::to(['@make_appointmentAppointmentTimeConfig'])];

$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
<?php AppAsset::addCss($this, '@web/public/css/lib/search.css')?>
<?php $this->endBlock()?>
<?php $this->beginBlock('content');?>

    <div class="appointment-time-template-index col-xs-12">
        <?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>

        <div class = "box">
            <div class = 'row search-margin'>
                <div class = 'col-sm-2 col-md-2'>

                    <?php  if(isset($this->params['permList']['role'])||in_array(Yii::getAlias('@createAppointmentTimeTemplate'), $this->params['permList'])):?>
                        <?= Html::a("<i class='fa fa-plus'></i>新增", ['@createAppointmentTimeTemplate'], ['class' => 'btn btn-default font-body2','data-pjax' => 0]) ?>
                    <?php endif?>
                </div>
                <div class = 'col-sm-10 col-md-10'>
                </div>
            </div>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'options' => ['class' => 'grid-view table-responsive add-table-padding'],
                'tableOptions' => ['class' => 'table table-hover table-border header'],
                'layout'=> '{items}<div class="text-right">{pager}</div>',
                'pager'=>[
                    //'options'=>['class'=>'hidden']//关闭自带分页
                    'firstPageLabel'=> Yii::getAlias('@firstPageLabel'),
                    'prevPageLabel'=> Yii::getAlias('@prevPageLabel'),
                    'nextPageLabel'=> Yii::getAlias('@nextPageLabel'),
                    'lastPageLabel'=> Yii::getAlias('@lastPageLabel'),
                ],
                'columns' => [
                    'id',
                    'name',
                    [
                        'attribute' => 'appointment_times',
                        'value'=>  function ($model){
                            return str_replace(',','，',$model->appointment_times);
                        },
                        'headerOptions' => ['class' => 'col-xs-3 col-sm-3 col-md-3']
                    ],
                    [
                        'class' => 'app\common\component\ActionColumn',
                        'template' => '{update-time-template}{delete-time-template}',
                        'buttons' => [
                            'update-time-template' => function ($url, $model, $key){
                                    if ((!isset($this->params['permList']['role']) && !in_array(Yii::getAlias('@updateAppointmentTimeTemplate'), $this->params['permList'])) ) {
                                        return false;
                                    }
                                    $options = array_merge([
                                        'title' => Yii::t('yii', 'Update'),
                                        'aria-label' => Yii::t('yii', 'Update'),
                                        'data-pjax' => '0',
                                    ]);
                                    return Html::a('<span class="icon_button_view fa fa-pencil-square-o" title="编辑", data-toggle="tooltip"></span>', $url, $options);

                            },

                            'delete-time-template' => function($url, $model, $key) {
                                if ((!isset($this->params['permList']['role']) && !in_array(Yii::getAlias('@deleteAppointmentTimeTemplate'), $this->params['permList']))) {
                                    return false;
                                }
                                $options = [
                                    'data-confirm' => false,
                                    'data-method' => false,
                                    'data-request-method' => 'post',
                                    'role' => 'modal-remote',
                                    'data-toggle' => 'tooltip',
                                    'data-confirm-title' => '系统提示',
                                    'data-delete' => false,
                                    'data-confirm-message' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                ];
                                return Html::a('<span class="icon_button_view fa fa-trash-o" title="删除", data-toggle="tooltip"></span>', ['@deleteAppointmentTimeTemplate', 'id' => $model->id], $options);
                            },
                        ],
                    ],
                ],
            ]); ?>
        </div>
        <?php  Pjax::end()?>
    </div>

<?php $this->endBlock();?>
<?php $this->beginBlock('renderJs');?>

<?php $this->endBlock();?>
<?php AutoLayout::end();?>