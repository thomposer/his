<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use johnitvn\ajaxcrud\CrudAsset;
use app\modules\patient\models\Patient;
CrudAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\modules\report\models\search\AppointmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '报到';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$public_img_path = $baseUrl.'/public/img/';
$tabData = [
    'titleData' => [
        ['title' => '今日预约待报到', 'url' => Url::to(['@reportRecordAppointment']), 'icon_img' => $public_img_path . '/tab/tab_paiban.png'],
        ['title' => '报到记录', 'url' => Url::to(['@reportRecordIndex']), 'icon_img' => $public_img_path . '/tab/tab_paiban.png']
    ],
];
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
    <?php AppAsset::addCss($this, '@web/public/css/lib/search.css')?>
    <?php AppAsset::addCss($this, '@web/public/css/report/index.css')?>
    <?php AppAsset::addCss($this, '@web/public/css/check/print.css') ?>
<?php $this->endBlock()?>
<?php $this->beginBlock('content');?>

<div class="appointment-index col-xs-12">
    <?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>

   <?php echo $this->render(Yii::getAlias('@contentTopTab'), ['data' => $tabData]); ?>
   <div class = "box delete_gap">
    <div class = 'row no-gap'>
      <div class = 'col-sm-2 col-md-2'>
       <?php  if(isset($this->params['permList']['role'])||in_array(Yii::getAlias('@reportRecordCreate'), $this->params['permList'])):?>
       <?= Html::a("<i class='fa fa-plus'></i>新增", ['@reportRecordCreate','url'=>'appointment'], ['class' => 'btn btn-default font-body2','data-pjax' => 0]) ?>
       <?php endif?>
    </div>
    <div class = 'col-sm-10 col-md-10'>
                <?php echo $this->render('_searchAppointment', ['model' => $searchModel]); ?>
        </div>
   </div>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['class' => 'grid-view table-responsive add-table-padding'],
        'tableOptions' => ['class' => 'table table-hover table-border'],
        'layout'=> '{items}<div class="text-right">{pager}</div>',
        'pager'=>[
            //'options'=>['class'=>'hidden']//关闭自带分页
            
            'firstPageLabel' => Yii::getAlias('@firstPageLabel'),
            'prevPageLabel' => Yii::getAlias('@prevPageLabel'),
            'nextPageLabel' => Yii::getAlias('@nextPageLabel'),
            'lastPageLabel' => Yii::getAlias('@lastPageLabel'),
        ],
        'columns' => [
            [
                'attribute' => 'username',
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-xs-3 col-sm-3 col-md-3'],
                'value' => function ($searchModel)use($cardInfo) {
                    $user_sex = Patient::$getSex[$searchModel->sex];
                    $dateDiffage = Patient::dateDiffage($searchModel->birthday, time());
                    $firstRecord = Patient::getFirstRecord($searchModel->firstRecord);
                    return Html::encode($searchModel->username) . '(' . $user_sex . ' ' . $dateDiffage . ')'.Patient::getUserVipInfo($cardInfo[$searchModel->iphone]) . $firstRecord;
                }
            ],
            [
                'attribute' => 'iphone',
                'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2 col-phone']
            ],
            [
                'attribute' => 'birthday',
                'value' => function ($searchModel) {
                    if ($searchModel->birthday) {
                        return date('Y-m-d H:i', $searchModel->birthday);
                    }
                    return '';
                },
                'headerOptions' => ['class' => 'col-sm-2 col-md-2 col-time'],
            ],
            [
                'attribute' => 'doctorName',
                'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2 col-doctor']
            ],
            'type_description',
                [
                'attribute' => 'time',
                'value' => function($searchModel){
                    return date('H:i',$searchModel->time);
                },
                'headerOptions' => ['class' => 'col-time-sm']
            ],
            [
                'class' => 'app\common\component\ActionTextColumn',
//                'headerOptions' => ['class' => 'col-option'],
                'template' => '{record}{delete}',
                'buttons' => [
                    'record' => function ($url,$model,$key){
                        if(!isset($this->params['permList']['role']) && !in_array(Yii::getAlias('@reportRecordUpdate'), $this->params['permList'])){
                            return false;
                        }
                        return Html::a('报到',['@reportRecordUpdate','id' => $model->record_id],['data-pjax' => 0]);
                    },
                    'delete' => function($url,$model,$key){
                        if(!isset($this->params['permList']['role']) && !in_array(Yii::getAlias('@make_appointmentAppointmentDelete'), $this->params['permList'])){
                            return false;
                        }
                        //修改取消预约二次确认为弹窗
                        $options = [
                            'data-pjax' => '1',
                            'data-request-method'=>'post',
                            'role'=>'modal-remote',
                            'data-toggle'=>'tooltip',
                        ];
                        return Html::a('关闭',['@make_appointmentAppointmentDelete','id' => $model->record_id],$options);
                    }
                ],
            ],
        ],
    ]); ?>
    </div>
    <?php Pjax::end();?>
</div>
<?php $this->endBlock();?>
<?php $this->beginBlock('renderJs');?>
<?php AppAsset::addScript($this, '@web/public/js/lib/common.js')?>
<?php $this->endBlock();?>
<?php AutoLayout::end();?>
