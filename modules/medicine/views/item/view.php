<?php

use yii\widgets\DetailView;
use yii\bootstrap\Tabs;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use app\assets\AppAsset;
use yii\helpers\Url;
$baseUrl = Yii::$app->request->baseUrl;
/* @var $this yii\web\View */
/* @var $model app\modules\medicine\models\MedicineItem */
/* @var $form yii\widgets\ActiveForm */
AppAsset::addCss($this, '@web/public/css/lib/tab.css');
AppAsset::addCss($this, '@web/public/css/medicine/form.css')

?>
<?=
    Tabs::widget([
        'renderTabContent' => false,
        'navType' => ' nav-tabs second-tabs',
        'items' => [
               [
                'label' => '美国儿童',
                'options' => ['id' => 'americanChildren']
               ]
        ]
    ]);
?>
<!-- <div class="medicine-item-view col-xs-12"> -->
    <div class = 'tab-content' style = 'padding:20px;word-break:break-all'>
      <div id = 'americanChildren' class="tab-pane active">
      <?php if(empty($medicineItemList)):?>
          <p class = 'no-medicine-view'>暂无药品用药指南，请联系管理员配置。</p>
      <?php else :?>
      <?php $form = ActiveForm::begin(); ?>
      <div class="row">
        <div class="col-md-12">
            <?= $form->field($medicineModel, 'indication')->dropDownList(ArrayHelper::map($medicineItemList, 'id', 'indication')) ?>
        </div>
      </div>
    <?php ActiveForm::end(); ?>
    <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                'chinese_name',
                'english_name',
//                 'used:ntext',
                [
                    'attribute' => 'used',
                    'format' => 'raw',
                    'value' => Html::tag('span',Html::encode($model->used),['class' => 'used']),
                ],
                [
                    'attribute' => 'renal_description',
                    'format' => 'raw',
                    'value' => Html::tag('span',Html::encode($model->renal_description),['class' => 'renal_description']),
                ],
                [
                    'attribute' => 'liver_description',
                    'format' => 'raw',
                    'value' => Html::tag('span',Html::encode($model->liver_description),['class' => 'liver_description']),
                ],
                [
                    'attribute' => 'contraindication',
                    'format' => 'raw',
                    'value' => Html::tag('span',Html::encode($model->contraindication),['class' => 'contraindication']),
                ],
                [
                    'attribute' => 'side_effect',
                    'format' => 'raw',
                    'value' => Html::tag('span',Html::encode($model->side_effect),['class' => 'side_effect']),
                ],
                [
                    'attribute' => 'pregnant_woman',
                    'format' => 'raw',
                    'value' => Html::tag('span',Html::encode($model->pregnant_woman),['class' => 'pregnant_woman']),
                ],
                [
                    'attribute' => 'breast',
                    'format' => 'raw',
                    'value' => Html::tag('span',Html::encode($model->breast),['class' => 'breast']),
                ],
                [
                    'attribute' => 'careful',
                    'format' => 'raw',
                    'value' => Html::tag('span',Html::encode($model->careful),['class' => 'careful']),
                ],
            ],
        ]) ?>
        <?php endif;?>
      </div>
   </div>
<?php 
    $getItemUrl = Url::to(['@apiMedicineDescriptionView']);
    $this->registerCss('
        #ajaxCrudModal .modal-body {
            padding: 0px;
        };
        .detail-view > tbody > tr > th{
            width : 100px;
        }
        .detail-view > tbody > tr > th{
            width : 100px;
        }
        table.detail-view{
            table-layout : auto;
        }
    ');
    $this->registerJs("
        var getItemUrl = '$getItemUrl';
        $('body').on('change','#medicineitem-indication',function(){
					var medicineItemId = $(this).val();
					$.ajax({

					     type: 'post',

					     url: getItemUrl ,

					     data: {
					     	'id' : medicineItemId,
					     } ,

					     success: function(json){
					     	if(json.errorCode == 0){
					     		var data = json.list;
					     		$('.used').text(data.used);
					     		$('.side_effect').text(data.side_effect);
					     		$('.renal_description').text(data.renal_description);
					     		$('.pregnant_woman').text(data.pregnant_woman);
					     		$('.liver_description').text(data.liver_description);
					     		$('.contraindication').text(data.contraindication);
					     		$('.careful').text(data.careful);
					     		$('.breast').text(data.breast);
					     	}else{
					     		showInfo(json.msg,'200px',2);
					     	}
					     },
					     error : function(){

					     },
					     dataType: 'json'

					});
        });    
    ");
?>    
<!-- </div> -->
