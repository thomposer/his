<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\Pjax;
use app\modules\spot\models\RecipeList;
use yii\helpers\Json;
use app\modules\outpatient\models\RecipeTemplateInfo;
use app\modules\spot\models\CureList;
use app\modules\outpatient\models\CureRecord;
use rkit\yii2\plugins\ajaxform\Asset;
use johnitvn\ajaxcrud\CrudAsset;
use app\modules\outpatient\models\RecipeRecord;
use app\modules\spot_set\models\ClinicCure;

Asset::register($this);
CrudAsset::register($this);
/* @var $this yii\web\View */
/* @var $model app\modules\outpatient\models\CureRecord */
/* @var $form ActiveForm  */
$attributeLabels = $model->getModel('recipeTemplate')->attributeLabels();
$recipeTemplateInfoLabels = $model->getModel('recipeTemplateInfo')->attributeLabels();
$skinList = array_column(ClinicCure::getCureList(null,['b.type' => 1]), 'name', 'id');

?>


<div class="cure-record-index col-xs-12">
  <?php $form = ActiveForm::begin(['id' => 'recipeTemplate'])?>
  <div class = 'row'>
        
        <div class = 'col-md-6'>
            <?= $form->field($model->getModel('recipeTemplate'), 'name')->textInput()->label($attributeLabels['name'] . '<span class = "label-required">*</span>') ?>
        </div>
        <div class = 'col-md-6'>
            <?= $form->field($model->getModel('recipeTemplate'), 'recipe_type_template_id')->dropDownList(ArrayHelper::map($type, 'id', 'name'), ['prompt' => '请选择']) ?>
        </div>
    </div>
	<div class = 'box'>
        <?php
        echo $form->field($model->getModel('recipeTemplateInfo'), 'recipeName')->dropDownList([], [
            'class' => 'form-control select2',
            'style' => 'width:100%'
        ])->label($recipeTemplateInfoLabels['recipeName'] . '<span class = "label-required">*</span>');
        ?>
    </div>
	<div class='box'>
		<div id="w3" class="grid-view table-responsive">
			<table class="table table-hover recipe-form">
				<thead>
					<tr class="header">
						<th class="col-md-2"><?= $recipeTemplateInfoLabels['name'] ?></th>
						<th class="col-md-1"><?= $recipeTemplateInfoLabels['dosage_form'] ?></th>
						<th class="col-md-2"><?= $recipeTemplateInfoLabels['dose'] ?></th>
						<th><?= $recipeTemplateInfoLabels['used'] ?></th>
						<th><?= $recipeTemplateInfoLabels['frequency'] ?></th>
						<th class="col-md-2" style="width: 13%"><?= $recipeTemplateInfoLabels['day'] ?></th>
						<th class="col-md-1" style="width: 17%"><?= $recipeTemplateInfoLabels['num'] ?></th>
					</tr>
				</thead>
				<tbody>
				<?php if($recipeTemplateInfoDataProvider):?>
				<?php foreach ($recipeTemplateInfoDataProvider as $v): ?>
					
    					<tr class="recipeNameTd">
						<td class="recipeName" data-type="recipeName">
    						  <?php
            $specification = '';
            if (! empty($v['specification'])) { // 判断是否有规格
                $specification = "(" . Html::encode($v['specification']) . ")";
            }
            $showValue = '';
            $v['manufactor'] && $showValue = $showValue . '生产商：' . Html::encode(Html::encode($v['manufactor'])) . '<br/>';
            $showValue = $showValue . '零售价：' . $v['price'] . '元';
            
            $html = '<span data-toggle="tooltip" data-html="true" data-placement="bottom" data-original-title="' . $showValue . '">';
            $html .= Html::encode($v['name']) . $specification;
            $html .= '</span>';
            $html .= Html::input('hidden', 'RecipeTemplateInfo[recipe_id][]', Json::encode(array_merge($v, [
                'isNewRecord' => 0
            ]), JSON_ERROR_NONE));
            if ($v['medicine_description_id'] != 0) {
                $html .= Html::a(Html::tag('i', '', [
                    'class' => 'fa fa-question-circle recipe-question'
                ]), [
                    '@apiMedicineDescriptionItem',
                    'id' => $v['medicine_description_id']
                ], [
                    'role' => 'modal-remote',
                    'data-toggle' => 'tooltip',
                    'data-modal-size' => 'large',
                    'data-request-method' => 'post'
                ]);
            }
            echo $html;
            ?>
    						</td>
						<td><?= RecipeList::$getType[$v['recipeType']] ?></td>
						<td>
    						  <?= Html::input('text','RecipeTemplateInfo[dose][]',$v['dose'],['class'=>'form-control','style'=>'width: 45%;display: initial;'])?>
    						  <?= Html::dropDownList('RecipeTemplateInfo[dose_unit][]',$v['dose_unit'],$v['recipe_dose_unit'],['class' => 'form-control','style' => 'width: 50%;float: right;']) ?></td>
						<td><?= Html::dropDownList('RecipeTemplateInfo[used][]',$v['used'],RecipeList::$getDefaultUsed,['class' => 'form-control','prompt' => '请选择'])  ?></td>
						<td><?= Html::dropDownList('RecipeTemplateInfo[frequency][]',$v['frequency'],RecipeList::$getDefaultConsumption,['class' => 'form-control','prompt' => '请选择']) ?></td>
						<td><?= Html::input('text','RecipeTemplateInfo[day][]',$v['day'],['class'=>'form-control']) ?></td>
						<td id="unit"><?= Html::input('text','RecipeTemplateInfo[num][]',$v['num'],['class'=>'form-control recipeNum']).RecipeList::$getUnit[$v['unit']] ?></td>

					</tr>
					<tr>
						<td></td>
						<td colspan="4"><?= Html::input('text','RecipeTemplateInfo[description][]',$v['description'],['class'=>'form-control','placeholder' => '请输入说明/描述,不超过35个字']) ?></td>
						<td><?= Html::dropDownList('RecipeTemplateInfo[type][]',$v['type'],RecipeList::$getAddress,['class' => 'form-control recipeOut','data-id' => $v['recipe_id']]) ?></td>
						<td class='recipe-delete op-group'>
    						  <?php
            echo Html::hiddenInput('RecipeTemplateInfo[deleted][]') . Html::img(Yii::$app->request->baseUrl . '/public/img/common/delete.png');
            ?>
    						</td>
					</tr>
					<?php if($v['skin_test_status'] != 0): ?>
					<tr class="skinTestTr">
                       <?php
                        $skinHtml = '<td colspan = "2">';
                        $skinHtml .= '<label class="skin-test-content" style="">皮试：</label>';
                        
                     
                        $skinHtml .= Html::dropDownList('RecipeTemplateInfo[skin_test_status][]', $v['skin_test_status'], RecipeRecord::$getSkinTestStatus, [
                                'class' => 'skinTestStatus cure-skin-select form-control','prompt' => ['text' => '请选择', 'options' => ['value' => 0]],
                            ]);
                        $skinHtml .= '</td>';
                        
                        $skinHtml .= '<td colspan = "3">';
                        $skinHtml .= '<label class="skin-test-content skin-test-status " '.($v['skin_test_status'] == 2?'style="display:none;"':'').' >' . ($v['skin_test'] ? ('皮试内容：' . Html::encode($v['skin_test'])) : '') . '</label>';
                        $skinHtml .= '</td>';
                        
                        $skinHtml .= '<td colspan = "2">';
                        $skinHtml .= '<label class="skin-test-content skin-test-status"'.($v['skin_test_status'] == 2?'style="display:none;"':'').' >皮试类型：</label>' . Html::dropDownList('RecipeTemplateInfo[curelist_id][]', $v['curelist_id'],$skinList,['class' => 'form-control curelistId skin-test-status','style' => $v['skin_test_status'] == 2?'display:none;':'']);
                        $skinHtml .= '</td>';
                     
                        
                        echo $skinHtml;
                        ?>
    					</tr>
    				<?php else:?>	
    				<?= Html::hiddenInput('RecipeTemplateInfo[skin_test_status][]','');//若没有配置皮试，则默认为没，即是0 ?>
                    <?= Html::hiddenInput('RecipeTemplateInfo[curelist_id][]',0);//若没有配置皮试，则默认皮试配置id为0 ?>	
    				<?php endif;?>		
			     <?php endforeach;?>
			     <?php endif;?>
				</tbody>
			</table>
		</div>
	</div>
	<div class="form-group">
      	<?= Html::a('取消', Yii::$app->request->referrer, ['class' => 'btn btn-cancel btn-form second-cancel']) ?>
        <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form ajaxform-btn']) ?>
        
    </div>
    <?php ActiveForm::end()?>
</div>

