<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\widgets\Pjax;
use johnitvn\ajaxcrud\CrudAsset;
use yii\grid\GridView;
use app\modules\charge\models\ChargeRecordLog;
use yii\helpers\Url;
use app\modules\user\models\User;
use app\modules\patient\models\Patient;
use app\modules\charge\models\ChargeRecord;
 AppAsset::addCss($this, '@web/public/css/lib/search.css');
?>
<?php Pjax::begin(['id' => 'charge-crud-datatable-pjax','timeout' => 5000,'enablePushState' => false]) ?>
<div class='row no-gap'>
    <div class='col-sm-12 col-md-12'>
        <?php
            echo $this->render('_patientChargeInfoSearch', ['model' => $patientChargeSearch]);
        ?>
    </div>
</div>

<?=
GridView::widget([
    'dataProvider' => $chargeDataProvider,
    'options' => ['class' => 'grid-view table-responsive add-table-padding'],
    'tableOptions' => ['class' => 'table table-hover table-border charge-info-table'],
    'layout' => '{items}<div class="text-right">{summary}{pager}</div>',
    'summary' =>'<div class="table-summary">( {totalCount} 结果，共 {pageCount} 页 )</div>',
    'pager' => [
        //'options'=>['class'=>'hidden']//关闭自带分页
        'hideOnSinglePage' => false,//在只有一页时也显示分页
        'firstPageLabel' => Yii::getAlias('@firstPageLabel'),
        'prevPageLabel' => Yii::getAlias('@prevPageLabel'),
        'nextPageLabel' => Yii::getAlias('@nextPageLabel'),
        'lastPageLabel' => Yii::getAlias('@lastPageLabel'),
    ],
    'columns' => [
    	[
            'attribute' => 'create_time',
            'value' => function ($model) {
                return date('Y-m-d H:i', $model->create_time);
            }
        ],
        [
            'attribute' => 'case_id',
            'value' => function ($model) {
                return $model->case_id?$model->case_id : '--';
            }
        ],
        [
            'attribute' => 'spot_name',
            'value' => function ($model) {
                return $model->spot_name?$model->spot_name : '--';
            }
        ],
        [
            'attribute' => 'diagnosis_time',
            'value' => function ($model) {
                return $model->diagnosis_time?date('Y-m-d H:i', $model->diagnosis_time):'--';
            }
        ],
        [
            'attribute' => 'doctor_name',
            'value' => function ($model) {
                return $model->doctor_name?$model->doctor_name : '--';
            }
        ],
        [
            'attribute' => 'type_description',
            'value' => function ($model) {
                return $model->type_description?$model->type_description : '--';
            }
        ],
        [
            'attribute' => 'type',
            'value' => function ($model) {
                return ChargeRecordLog::$getType[$model->type];
            }
        ],
        [
            'attribute' => 'pay_type',
            'value' => function($model){
                return ChargeRecord::$getType[$model->pay_type];
            }
        ],
        [
            'attribute' => 'price',
            'value'=>function($model){
                if($model->price != '0.00'){
                    $price = $model->type == 1?'+'.$model->price:'<span style="color:red;">-'.$model->price.'</span>';
                }else{
                    $price = $model->price;
                }
                return $price;
            },
            'format'=>'raw'
        ],
        [
            'class' => 'app\common\component\ActionColumn',
            'template' => "{view}",
            'headerOptions' => ['class' => 'col-sm-1 col-md-1'],
            'buttons' => [
                'view' => function ($url, $model, $key)use($spotId) {
                    if ((!isset($this->params['permList']['role']) && !in_array(Yii::getAlias('@chargeIndexTradeLog'), $this->params['permList'])) || $spotId != $model->spot_id) {
                        return false;
                    }

                    $options = array_merge([
                        'data-pjax' => '0',
                        'target' => '_blank',
                        'class' => ' op-group-a',
                    ]);
                    return Html::a('查看', ['@chargeIndexTradeLog', 'id' => $model->id], $options);
                },
            ]
        ],
    ],
]);
?>
<?php Pjax::end(); ?>
