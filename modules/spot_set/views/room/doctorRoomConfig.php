<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $model app\modules\spot_set\models\Room */

$this->title = '医生常用诊室配置';
$this->params['breadcrumbs'][] = ['label' => '诊所设置', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '诊室管理';
$baseUrl = Yii::$app->request->baseUrl;
//$returnUrl = Yii::$app->request->referrer;
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>
<?php
$css = <<<CSS
   .doctor-room-config .custom-label{
        margin-top : 2.5px;
        margin-bottom : 2.5px;
    }
    .doctor-room-config .doctor-room-config-td{
        padding-top: 5.5px;
        padding-bottom: 5.5px;
   }
CSS;
$this->registerCss($css);
?>
<?php $this->endBlock(); ?>
<?php $this->beginBlock('content') ?>
<div class="doctor-room-config col-xs-12">
    <div class = "box">
        <div class="box-header with-border">
            <span class = 'left-title'><?= Html::encode($this->title) ?></span>
            <?= Html::a(Html::img($baseUrl . '/public/img/common/icon_back.png') . '返回', Url::to(['index']), ['class' => 'right-cancel']) ?>      
        </div>
        <div class = "box-body">
            <?php
                $form = ActiveForm::begin([
                            'options' => [
                                'class' => 'form-horizontal',
                                'method' => 'post',
                            ],
                            'fieldConfig' => [
                                'template' => "<div class='col-xs-3 col-sm-2 text-right'>{label}</div><div class='col-xs-9 col-sm-7'>{input}</div><div class='col-xs-12 col-xs-offset-3 col-sm-3 col-sm-offset-0'>{error}</div>"
                            ]
                ]);
            ?>
            <?=
                GridView::widget([
                    'dataProvider' => $dataProvider,
                    'options' => ['class' => 'grid-view table-responsive add-table-padding'],
                    'tableOptions' => ['class' => 'table table-hover table-border header table-bordered'],
                    'layout' => '{items}<div class="text-right tooltip-demo">{pager}</div>',
                    'pager' => [
                        //'options'=>['class'=>'hidden']//关闭自带分页

                        'firstPageLabel' => Yii::getAlias('@firstPageLabel'),
                        'prevPageLabel' => Yii::getAlias('@prevPageLabel'),
                        'nextPageLabel' => Yii::getAlias('@nextPageLabel'),
                        'lastPageLabel' => Yii::getAlias('@lastPageLabel'),
                    ],
                    'columns' => [
                        [
                            'contentOptions' => ['class' => 'text-center'],
                            'headerOptions' => ['class' => 'col-xs-1 col-sm-1 col-md-1 text-center'],
                            'label' => '医生',
                            'value' => 'doctorName'
                        ],
                        [
                            'label' => '选择常用诊室',
                            'contentOptions' => ['class' => 'doctor-room-config-td'],
                            'headerOptions' => ['class' => 'text-center'],
                            'format' => 'raw',
                            'value' => function ($searchModel) use ($roomList) {
                                if(!$roomList){
                                    return Html::tag('span','没有找到数据。',['class' => 'col-xs-12 col-sm-12 col-md-12 text-center']);
                                }
                                $selection = $searchModel->unionId ? explode(',', $searchModel->unionId) : null;
                                $items = ArrayHelper::map($roomList, 'room_id', 'room_name');
                                $name = 'doctorRoomUnionId[' . $searchModel->doctorId .']';
                                return Html::checkboxList($name, $selection, $items,['itemOptions' => ['labelOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2 custom-label']]]);
                            }
                        ],
                    ],
                ]);
            ?>
            <div class="form-group" style="margin-left: 20px;margin-bottom: 0px;">
                <?php if($dataProvider->count && $roomList): ?>
                    <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form']) ?>
                <?php endif ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<?php  $this->endBlock()?>
<?php  $this->beginBlock('renderJs')?>

<?php  $this->endBlock()?>
<?php  AutoLayout::end()?>
