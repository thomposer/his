<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use yii\base\ActionEvent;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\helpers\Json;
use dosamigos\datetimepicker\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model app\modules\outpatient\models\CureRecord */
/* @var $form ActiveForm  */
$attributes = $model->attributeLabels();
?>
<?php
Pjax::begin([
    'id' => 'makeupCurePjax'
])
?>

<div class="cure-record-index col-xs-12"> 
    <?php
    $form = ActiveForm::begin([
                'action' => Url::to(
                        [
                            'save-cure',
                            'id' => $id,
                            'patientId' => $patientId,
                            'doctorId' => $doctorId
                        ]
                ),
                'id' => 'cure-record',
            ])
    ?>
    <div class="row">
        <div class="col-md-6">
            <?php
            echo $form->field($model, 'billingTime')->widget(
                    DateTimePicker::className(), [
                'inline' => false,
                'language' => 'zh-CN',
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd hh:ii',
                    'minuteStep' => 1,
                ]
                    ]
            )->label($attributes['billingTime'] . '<span class = "label-required">*</span>')
            ?>
            <?php // echo  $form->field($model, 'diagnosis_time')->textInput(['maxlength' => 30])->label($labels['diagnosis_time'] . '<span class = "label-required">*</span>')  ?>
        </div>
    </div>
    <div class = 'cure-record-form row'>
        <div class="col-md-8">
            <?= $form->field($model, 'cureName')->dropDownList(ArrayHelper::map($cureList, 'id', 'name'), ['prompt' => '请选择', 'class' => 'form-control select2', 'style' => 'width:100%']) ?>
        </div>
        <div class="col-md-3">
            <?php echo Html::a('治疗医嘱配置', Yii::$app->request->getHostInfo() . Url::to(['@spotCureListCreate']), ['class' => 'btn btn-default btn-mt25 blank', 'target' => '_blank']) ?>
        </div>
    </div>
    <div class = 'box'>
        <?=
        GridView::widget([
            'dataProvider' => $cureRecordDataProvider,
            'options' => ['class' => 'grid-view table-responsive'],
            'tableOptions' => ['class' => 'table table-hover cure-form'],
            'headerRowOptions' => ['class' => 'header'],
            'layout' => '{items}',
            'columns' => [
                [
                    'attribute' => 'name',
                    'headerOptions' => ['class' => 'col-sm-3'],
                ],
                [
                    'attribute' => 'unit',
                    'headerOptions' => ['class' => 'col-sm-2'],
                ],
                [
                    'attribute' => 'time',
                    'headerOptions' => ['class' => 'col-sm-2'],
                    'format' => 'raw',
                    'value' => function ($model) {
                $list = [
                    'id' => $model->id,
                    'name' => $model->name,
                    'unit' => $model->unit,
                    'price' => $model->price
                ];
                $html = Html::input('text', 'CureRecord[time][]', $model->time, ['class' => 'form-control']);
                $html .= Html::input('hidden', 'CureRecord[cure_id][]', Json::encode(array_merge($list, ['isNewRecord' => 0]), JSON_ERROR_NONE));
                $html .=Html::input('hidden', 'CureRecord[isNewRecord][]', 0);
                return $html;
            }
                ],
                [
                    'attribute' => 'description',
                    'headerOptions' => ['class' => 'col-sm-3'],
                    'format' => 'raw',
                    'value' => function ($model) {

//                $html = Html::tag('span', Html::encode($model->description));
                $html = Html::input('text', 'CureRecord[description][]', $model->description, ['class' => 'form-control']);
                return $html;
            }
                ],
                [
                    'attribute' => 'remark',
                    'headerOptions' => ['class' => 'col-sm-3'],
                    'format' => 'raw',
                    'value' => function ($model) {

//                $html = Html::tag('span', Html::encode($model->remark));
                $html = Html::input('text', 'CureRecord[remark][]', $model->remark, ['class' => 'form-control']);
                return $html;
            }
                ],
                [
                    'class' => 'app\common\component\ActionColumn',
                    'template' => '{delete}',
                    'headerOptions' => ['class' => 'col-sm-1 action-column'],
                    'buttons' => [
                        'delete' => function($url, $model, $key) {
                            $html = Html::hiddenInput('CureRecord[deleted][]') . Html::img(Yii::$app->request->baseUrl . '/public/img/common/delete.png');
                            return $html;
                        }
                    ]
                ],
            ],
        ]);
        ?> 
    </div>
    <div class = 'row makeup-outpatient'>
        <div class="button-center form-group">
            <?= Html::button('取消', ['class' => 'btn btn-cancel btn-form ', 'type' => 'button', 'data-dismiss' => 'modal']) ?>
            <?= Html::button('保存', ['class' => 'btn btn-default btn-form ', 'type' => 'submit']) ?>
        </div>

    </div>
    <?php ActiveForm::end() ?>    
</div>

<div id ="cure_print" class = "tab-pane hide">
</div>

<?php
$cureCount = $cureRecordDataProvider->query->count();
$this->registerJs("
    var cureCount = $cureCount;
    $('.empty').parents('tr').remove();
    $('#cure-record').yiiAjaxForm({
	   beforeSend: function() {
						
	   },
	   complete: function() {
					   
	   },
	   success: function(data) {
            
	   if(data.errorCode == 0){
		    showInfo(data.msg,'180px');
			$.pjax.reload({container:'#makeupCurePjax',cache : false,timeout : 5000});  //Reload
                        $.pjax.reload({container:'#ump_reception'+$id,cache : false,timeout : 5000});  //Reload
                            $('.cureIsNewRecord[value=1]').each(function(key){
                                 $(this).val(0);
                                 if(data.data!=''){
                                    var ret=JSON.parse(data.data);
                                    console.log(key);
                                    var recordData=JSON.parse($(this).siblings('#cure_id').val());
                                     recordData.id=ret[key]
                                     $(this).siblings('#cure_id').val(JSON.stringify(recordData));
                                 }
                             });
                             $('[name=\'CureRecord[deleted][]\'][value=1]').each(function(){
                                $(this).parent().parent().remove();
                             });                               
                        $('.ump-reception>ul').find('li').eq(3).find('a').click();
	   }else{
		    showInfo(data.msg,'180px',2);
        }
	},
});
         setTimeout(function () {
                $('.field-curerecord-curename .select2').select2();
                $('#cure button').attr({'type': 'submit'});
            }, 500);
")
?>
<?php Pjax::end() ?>
