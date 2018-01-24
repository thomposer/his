<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\modules\spot\models\RecipeList;
use yii\helpers\Json;
use app\modules\outpatient\models\RecipeRecord;
use app\modules\spot_set\models\ClinicCure;

/* @var $this yii\web\View */
/* @var $model app\modules\outpatient\models\CureRecord */
/* @var $form ActiveForm  */
$attributeLabels = $recipeModel->attributeLabels();
$recipeLabels = $recipeModel->attributeLabels();
$skinList = array_column(ClinicCure::getCureList(null,['b.type' => 1]), 'name', 'id');
?>


<div class="package-recipe-index">
	<div class = 'box'>
        <?php
        echo $form->field($recipeModel, 'recipeName')->dropDownList([], [
            'class' => 'form-control select2',
            'style' => 'width:100%'
        ])->label($recipeLabels['recipeName'] . '<span class = "label-required">*</span>');
        ?>
    </div>
	<div class='box'>
		<div id="w3" class="grid-view table-responsive">
			<table class="table table-hover recipe-form table-border">
				<thead>
					<tr class="header">
						<th class="col-md-2"><?= $recipeLabels['name'] ?></th>
						<th class="col-md-1"><?= $recipeLabels['dosage_form'] ?></th>
						<th class="col-md-2"><?= $recipeLabels['dose'] ?></th>
						<th><?= $recipeLabels['used'] ?></th>
						<th><?= $recipeLabels['frequency'] ?></th>
						<th class="col-md-2" style="width: 13%"><?= $recipeLabels['day'] ?></th>
						<th class="col-md-1" style="width: 17%"><?= $recipeLabels['num'] ?></th>
					</tr>
				</thead>
				<tbody>
				<?php if($recipeDataProvider):?>
				<?php foreach ($recipeDataProvider as $v): ?>
					
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
                                $html .= Html::input('hidden', 'OutpatientPackageRecipe[clinic_recipe_id][]',$v['clinic_recipe_id']);
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
    						  <?= Html::input('text','OutpatientPackageRecipe[dose][]',$v['dose'],['class'=>'form-control','style'=>'width: 45%;display: initial;'])?>
    						  <?= Html::dropDownList('OutpatientPackageRecipe[dose_unit][]',$v['dose_unit'],$v['recipe_dose_unit'],['class' => 'form-control','style' => 'width: 50%;float: right;']) ?></td>
						<td><?= Html::dropDownList('OutpatientPackageRecipe[used][]',$v['used'],RecipeList::$getDefaultUsed,['class' => 'form-control','prompt' => '请选择'])  ?></td>
						<td><?= Html::dropDownList('OutpatientPackageRecipe[frequency][]',$v['frequency'],RecipeList::$getDefaultConsumption,['class' => 'form-control','prompt' => '请选择']) ?></td>
						<td><?= Html::input('text','OutpatientPackageRecipe[day][]',$v['day'],['class'=>'form-control']) ?></td>
						<td id="unit"><?= Html::input('text','OutpatientPackageRecipe[num][]',$v['num'],['class'=>'form-control recipeNum']).RecipeList::$getUnit[$v['unit']] ?></td>

					</tr>
					<tr>
						<td></td>
						<td colspan="4"><?= Html::input('text','OutpatientPackageRecipe[description][]',$v['description'],['class'=>'form-control','placeholder' => '请输入说明/描述,不超过35个字']) ?></td>
						<td><?= Html::dropDownList('OutpatientPackageRecipe[type][]',$v['type'],RecipeList::$getAddress,['class' => 'form-control recipeOut','data-id' => $v['recipe_id']]) ?></td>
						<td class='recipe-delete op-group'>
    						  <?php
                                echo Html::hiddenInput('OutpatientPackageRecipe[deleted][]') . Html::img(Yii::$app->request->baseUrl . '/public/img/common/delete.png');
                              ?>
    						</td>
					</tr>
					<?php if($v['skin_test_status'] != 0): ?>
					<tr class="skinTestTr">
                       <?php
                        $skinHtml = '<td colspan = "2">';
                        $skinHtml .= '<label class="skin-test-content" style="">皮试：</label>';
                        
                     
                        $skinHtml .= Html::dropDownList('OutpatientPackageRecipe[skin_test_status][]', $v['skin_test_status'], RecipeRecord::$getSkinTestStatus, [
                                'class' => 'skinTestStatus cure-skin-select form-control',
                            ]);
                        $skinHtml .= '</td>';
                        
                        $skinHtml .= '<td colspan = "3">';
                        $skinHtml .= '<label class="skin-test-content skin-test-status " '.($v['skin_test_status'] == 2?'style="display:none;"':'').' >皮试内容：' . Html::encode($v['skin_test']) . '</label>';
                        $skinHtml .= '</td>';
                        
                        $skinHtml .= '<td colspan = "2">';
                        $skinHtml .= '<label class="skin-test-content skin-test-status"'.($v['skin_test_status'] == 2?'style="display:none;"':'').' >皮试类型：</label>' . Html::dropDownList('OutpatientPackageRecipe[curelist_id][]', $v['curelist_id'],$skinList,['class' => 'form-control curelistId skin-test-status','style' => $v['skin_test_status'] == 2?'display:none;':'']);
                        $skinHtml .= '</td>';
                     
                        
                        echo $skinHtml;
                        ?>
    					</tr>
    				<?php endif;?>		
			     <?php endforeach;?>
			     <?php endif;?>
				</tbody>
			</table>
		</div>
	</div>
	
</div>
