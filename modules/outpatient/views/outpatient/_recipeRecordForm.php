<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\Pjax;
use app\modules\spot\models\RecipeList;
use yii\helpers\Json;
use app\modules\outpatient\models\RecipeRecord;
use app\modules\spot\models\CureList;
use app\modules\outpatient\models\CureRecord;
use app\modules\spot_set\models\ClinicCure;


/* @var $this yii\web\View */
/* @var $model app\modules\outpatient\models\CureRecord */
/* @var $form ActiveForm  */
$attributes = $model->attributeLabels();
$chargeInfoList = $chargeInfoList ? $chargeInfoList : array();
$skinList = array_column(ClinicCure::getCureList(null,['b.type' => 1]), 'name', 'id');
$baseUrl = Yii::$app->request->baseUrl;
$firstCheckWeightEdit = !($patientOtherInfo['firstCheckCount'] && $patientOtherInfo['weightkg']);
$flag = [];
?>
<div class="cure-record-index col-xs-12">
<?php if($firstCheckWeightEdit): ?>
    <div class="first-check-info-tips" style="color: #55657d;font-weight: normal;padding-top: 20px;">
       <?php  if(isset($this->params['permList']['role'])||in_array(Yii::getAlias('@outpatientOutpatientUpdatePatientInfo'), $this->params['permList'])):?>
        填写初步诊断﹑体重值后，才能开处方医嘱    
       <?= 
           Html::a("<i class='fa fa-plus' style=\"text-decoration-line: underline;\"></i> 请补充", Url::to(['@outpatientOutpatientUpdatePatientInfo', 'id' => Yii::$app->request->get('id'), 'type' => 2]),
               ['data-pjax' => 0, 'role' => 'modal-remote', 'data-toggle' => 'tooltip', 'data-modal-size' => 'large', 'style' => "text-decoration:underline"]
               )
        ?>
       <?php endif?>
    </div>
<?php else://如果没填写初步诊断或体重值，隐藏选择模版 ?>
<div id ='recipe-template-select-ui'>
    <div class="recipe-template-select-container">
        <div id ="recipe-template-select" class="recipe-template-sel"></div>
        <div class="recipe-template-desc ">请选择处方模板<span class="glyphicon glyphicon-triangle-bottom recipe-template-arrow"></span></div>
    </div>
</div>
<?php endif ?>
<?php

Pjax::begin([
    'id' => 'recipePjax',
    'timeout' => 5000
]
)?>

     <?php

    $form = ActiveForm::begin([
        'action' => Url::to([
            '@outpatientOutpatientRecipeRecord',
            'id' => Yii::$app->request->get('id')
        ]),
        'id' => 'recipe-record',
        'options' => [
            'data' => [
                'pjax' => '#recipePjax'
            ]
        ]
    ])?>
    <div class='box-body recipe-record-form'>
        <?php

        echo $form->field($model, 'recipeName')->dropDownList([], [
            'class' => 'form-control select2',
            'style' => 'width:100%;',
            'disabled' => $firstCheckWeightEdit,
        ]);
        ?>
    </div>
	<div class='box'>
		<div id="w3" class="grid-view table-responsive">
			<table class="table table-hover recipe-form">
				<thead>
					<tr class="header">
						<th class="col-md-2"><?= $attributes['name'] ?></th>
						<th class="col-md-1"><?= $attributes['dosage_form'] ?></th>
                        <th class="col-md-1"><?= $attributes['price'] ?></th>
						<th class="col-md-2"><?= $attributes['dose'] ?></th>
						<th><?= $attributes['used'] ?></th>
						<th><?= $attributes['frequency'] ?></th>
						<th class = "col-md-2" style="width:13%"><?= $attributes['day'] ?></th>
						<th class = "col-md-1" style="width:17%"><?= $attributes['num'] ?></th>
						<th class="status-recipe-column" style = "width:65px"><?= $attributes['status'] ?></th>
					</tr>
				</thead>
				<tbody>
				<?php if($recipeRecordDataProvider):?>
				<?php foreach ($recipeRecordDataProvider as $v): ?>
					
				    <?php
                         if($v['cureStatus'] == 1 || $v['cureStatus'] == 2 || $v['cureChargeStatus'] == 1 || $v['cureChargeStatus'] == 2){//皮试治疗中，已治疗，收费，退费
                             $skinIsEdit = true;
                            }else{
                             $skinIsEdit = false;
                         }
                        if($v['drug_type'] == 20){//精神类药
                            $flag[$v['drug_type']] = 1;
                        }else{
                            $flag[1] = 1;//其他类药，则归纳为1
                        }
				        $isEdit = false;//是否可修改，默认为false
				        $numContent = $v['num'].RecipeList::$getUnit[$v['unit']];
				        if(!in_array($v['id'], $chargeInfoList) && $v['status'] == 3  && $v['package_record_id'] == 0){
				            $numContent = Html::tag('span',$v['num'],['id'=>'num']).Html::input('hidden','RecipeRecord[num][]',$v['num'],['data-id' => $v['recipe_id'],'class'=>'form-control recipeNum num_'.$v['recipe_id']]).RecipeList::$getUnit[$v['unit']];
				            $isEdit = true;
				        }
				        if(in_array($v['id'], $chargeInfoList) || $v['package_record_id'] > 0){
				            $hasChargeed[$v['recipe_id']] += $v['num'];
				        }
				        if(in_array($v['id'], $chargeInfoList) && $v['status'] == 3 || $v['package_record_id'] > 0) {
				            $numContent = Html::tag('p',$v['num'].RecipeList::$getUnit[$v['unit']],['class'=>'recipeNum num_'.$v['recipe_id'],'readOnlyNum'=>$v['num']]);
				        }
				        //目前总库存量
				        $totalNum = isset($recipeTotalNumsList[$v['recipe_id']])?array_sum($recipeTotalNumsList[$v['recipe_id']]):0;
				        //已经占用的数量
				        $totalUsedNum = isset($recipeUsedTotalNums[$v['recipe_id']])?array_sum($recipeUsedTotalNums[$v['recipe_id']]):0;
				        //当前就诊纪录占用的数量
				        $nowNums = 0;
				        if(isset($nowTotalNums[$v['recipe_id']])){
// 				            if($v['type'] == 1){
				                $nowNums = array_sum($nowTotalNums[$v['recipe_id']]);
// 				            }
				        }
				        $overNum = ($totalNum - $totalUsedNum - $nowNums) > 0 ?$totalNum - $totalUsedNum -$nowNums : 0;//默认为0
				    ?>
    					<tr class = "recipeNameTd">
    						<td class="recipeName" data-type="recipeName">
    						  <?php
            $specification = '';
            if (! empty($v['specification'])) { // 判断是否有规格
                $specification = "(" . Html::encode($v['specification']) . ")";
            }

            $showValue = '';
            $v['manufactor'] && $showValue = $showValue . '生产商：' . Html::encode(Html::encode($v['manufactor'])) . '<br/>';
            $highRisk = '';
            if($v['high_risk'] == 1){
              $highRisk ='<span class="high-risk">高危</span>';
            }
            if ($isEdit) {
                $html = '<span data-toggle="tooltip" data-html="true" data-placement="bottom" data-original-title="' . $showValue . '">';
                $html .= $highRisk.Html::encode($v['name']) . $specification;
                $html .= '</span>';
                $html .= Html::input('hidden', 'RecipeRecord[recipe_id][]', Json::encode(array_merge($v, [
                    'isNewRecord' => 0
                ]), JSON_ERROR_NONE)) . Html::hiddenInput('RecipeRecord[totalNum][]', $totalNum - $totalUsedNum);
            } else {
                $html = '<span data-toggle="tooltip" data-html="true" data-placement="bottom" data-original-title="' . $showValue . '">';
                $html .= $highRisk.Html::encode($v['name']) . $specification;
                $html .= '</span>';
            }
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
						<td><?= RecipeList::$getType[$v['dosage_form']] ?></td>
                            <td> <?=$v['price']?></td>
						<td>

    						  <?= $isEdit?Html::tag('span',$v['dose']).Html::input('hidden','RecipeRecord[dose][]',$v['dose'],['class'=>'form-control','style'=>'width: 45%;display: initial;']):$v['dose']?>
                              <?= $isEdit?Html::tag('span',RecipeList::$getDoseUnit[$v['r_dose_unit']]).Html::dropDownList('RecipeRecord[dose_unit][]',$v['r_dose_unit'],$v['l_dose_unit'],['class' => 'form-control','style' => 'display:none;width: 50%;float: right;']):RecipeList::$getDoseUnit[$v['r_dose_unit']] ?></td>
						<td><?= $isEdit?Html::tag('span',RecipeList::$getDefaultUsed[$v['used']]).Html::dropDownList('RecipeRecord[used][]',$v['used'],RecipeList::$getDefaultUsed,['class' => 'form-control','prompt' => '请选择','style' => 'display:none']):RecipeList::$getDefaultUsed[$v['used']]  ?></td>
						<td><?= $isEdit?Html::tag('span',RecipeList::$getDefaultConsumption[$v['frequency']]).Html::dropDownList('RecipeRecord[frequency][]',$v['frequency'],RecipeList::$getDefaultConsumption,['class' => 'form-control','prompt' => '请选择','style' => 'display:none']):RecipeList::$getDefaultConsumption[$v['frequency']] ?></td>
						<td><?= $isEdit?Html::tag('span',$v['day']).Html::input('hidden','RecipeRecord[day][]',$v['day'],['class'=>'form-control']):$v['day'] ?></td>
						<td id="unit"><?= $numContent?></td>
						<td class="status-recipe-column" style = "display:table-cell;">
    						  <?php
            echo Html::tag('div',RecipeRecord::$getStatusOtherDesc[$v['status']], ['style' => 'color:'.RecipeRecord::getStatusColor($v['status'])]);
            if ($v['type'] == 2) {
                unset($v['charge_status']);
            }
            // if (isset($v['charge_status'])) {
            //     echo Html::tag('i', '', RecipeRecord::getChargeStatusOptions($v['charge_status']));
            // }
            ?>
    						</td>
<!--                            -->
					</tr>
					<tr>
						<td><section
								<?php if($v['type'] == 2){echo 'style="display:none"';} ?>>库存：<?= Html::tag('i',$overNum,['class' => 'totalNum_'.$v['recipe_id']]) ?></section></td>
						<td class='desc-td' colspan="5"><?= $isEdit?Html::tag('span',Html::encode($v['description'])).Html::input('hidden','RecipeRecord[description][]',$v['description'],['class'=>'form-control','placeholder' => '请输入说明/描述,不超过35个字']):Html::encode($v['description']) ?></td>
						<td><?= $isEdit?Html::tag('span',RecipeList::$getAddress[$v['type']]).Html::dropDownList('RecipeRecord[type][]',$v['type'],$v['type'] == 1?[$v['type']=>RecipeList::$getAddress[$v['type']]]:RecipeList::$getAddress,['class' => 'form-control recipeOut','style' => 'display:none','data-id' => $v['recipe_id']]):Html::tag('span',RecipeList::$getAddress[$v['type']],['recipeOutData'=>$v['type'],'class'=>'recipeOutData']) ?></td>
						<td class='recipe-delete op-group'>
    						  <?php
                                    if (((! in_array($v['id'], $chargeInfoList) || $v['type'] == 2) && $v['status'] == 3) && $v['package_record_id'] == 0) {
                                        echo Html::hiddenInput('RecipeRecord[deleted][]') . Html::img(Yii::$app->request->baseUrl . '/public/img/common/delete.png');
                                    }
                              ?>
    						</td>
                        <td></td>
                        <td></td>

                        <?php //echo $isEdit?'<td colspan="2"></td>':'' ?>
					</tr>
    					<?php if($v['skin_test_status'] != 0):  ?>
    					<tr class = "skinTestTr">

                            <?php
                              if($isEdit){
                                $skinHtml = '<td colspan = "2">';
                                $skinHtml .= '<span>'.($v['skin_test_status'] == 2?'皮试：免': ('皮试：需要' . ($v['skin_test'] ? ('（' . Html::encode($v['skin_test']) . '）') : '') )) . '</span>';

                                if($skinIsEdit){//皮试治疗中，已治疗，收费，退费
                                    $skinHtml .= '<label class="skin-test-content" style="display:none">' . ('皮试：需要' . ($v['skin_test'] ? ('（' . Html::encode($v['skin_test']) . '）') : '') ) . '</label>'.Html::hiddenInput('RecipeRecord[skin_test_status][]',$v['skin_test_status']);
                                }else{
                                    $skinHtml .= '<label class="skin-test-content" style="display:none">皮试：</label>'.Html::dropDownList('RecipeRecord[skin_test_status][]',$v['skin_test_status'],RecipeRecord::$getSkinTestStatus,['prompt' => ['text' => '请选择', 'options' => ['value' => 0]], 'class' => 'skinTestStatus cure-skin-select form-control','style' => 'display:none;']);
                                }
                                $skinHtml .= '</td>';

                                $skinHtml .= '<td colspan = "2">';
                                $skinHtml .= ($skinIsEdit) ? '' : '<label class="skin-test-content skin-test-status '.($v['skin_test_status'] == 2?'no-need-skin-test':'').'" style="display:none">' . ($v['skin_test'] ? ('皮试内容：'.Html::encode($v['skin_test'])) : '' ) . '</label>';
                                $skinHtml .= '</td>';

                                $skinHtml .= '<td colspan = "3">';
                                $skinHtml .= $v['skin_test_status'] == 2 ? '' : '<span>皮试类型：'.Html::encode($v['cureName']).'</span>';
                                if($skinIsEdit){//皮试治疗中，已治疗，收费，退费
                                    $skinHtml .= '<label class="skin-test-content skin-test-status '.($v['skin_test_status'] == 2?'no-need-skin-test':'').'" style="display:none">皮试类型：'. Html::encode($v['cureName']) . '</label>'.Html::hiddenInput('RecipeRecord[curelist_id][]',$v['curelist_id']);
                                }else{
                                    $skinHtml .= '<label class="skin-test-content skin-test-status '.($v['skin_test_status'] == 2?'no-need-skin-test':'').'" style="display:none">皮试类型：</label>'.Html::dropDownList('RecipeRecord[curelist_id][]',$v['curelist_id'],$skinList,['class' => 'skin-test-status cure-skin-select form-control '.($v['skin_test_status'] == 2?'no-need-skin-test':''),'style' => 'display:none;']);
                                }
                                $skinHtml .= '</td>';

                                $skinHtml .= '<td colspan = "1" class="skinTd">';
                                $skinHtml .= ($v['skin_test_status'] == 2  || empty($v['cureResult']))? '' : '<span>皮试结果：'.($v['cureResult'] == 2 ? '<b class="red">'.CureRecord::$getCureResult[$v['cureResult']].'</b>' : CureRecord::$getCureResult[$v['cureResult']]).'</span>';
                                $skinHtml .= '<label class="skin-test-content skin-test-status skin-test-result '.($v['skin_test_status'] == 2?'no-need-skin-test':'').'" style="display:none">' . ($v['cureResult'] ? ('皮试结果：'.($v['cureResult'] == 2 ? '<b class="red">'.CureRecord::$getCureResult[$v['cureResult']].'</b>' : CureRecord::$getCureResult[$v['cureResult']])) : '') . '</label>';
                                $skinHtml .= '</td>';
                              }else{
                                  if($v['skin_test_status'] == 2){
                                      $skinHtml = '<td colspan = "2">皮试：免</td><td colspan = "2"></td><td colspan = "3"></td><td colspan = "1" class="skinTd"></td>';
                                  }else{
                                      $skinHtml = '<td colspan = "2">' . ('皮试：需要' . ($v['skin_test'] ? ('（' . Html::encode($v['skin_test']) . '）') : '') ) . '</td>';
                                      $skinHtml .= '<td colspan = "2"></td>';
                                      $skinHtml .= '<td colspan = "3">皮试类型：'.Html::encode($v['cureName']).'</td>';
                                      $skinHtml .= '<td colspan = "1" class="skinTd">' . ($v['cureResult'] ? ('皮试结果：'.($v['cureResult'] == 2 ? '<b class="red">'.CureRecord::$getCureResult[$v['cureResult']].'</b>' : CureRecord::$getCureResult[$v['cureResult']])) : '').'</td>';
                                  }

                              }
                            $skinHtml .= '<td></td>';
                              echo $skinHtml;
                            ?>

    					</tr>
    					<?php elseif($isEdit):?>

    						<?= Html::hiddenInput('RecipeRecord[skin_test_status][]','');//若没有配置皮试，则默认为空。占位 ?>
                            <?= Html::hiddenInput('RecipeRecord[curelist_id][]',0);//若没有配置皮试，则默认皮试配置id为0 ?>

    					<?php endif;?>
			     <?php endforeach;?>
			     <?php endif;?>
				</tbody>
			</table>
		</div>
	</div>
	<div class="form-group">
        <?= Html::button('修改', ['class' => 'btn btn-default btn-form btn-disalbed-custom','disabled' => $firstCheckWeightEdit])?>
        <span>
             <?php if($recipeBackStatus == 1){
                 echo Html::a('退药',Url::to(['@outpatientOutpatientRecipeBack', 'id' => Yii::$app->request->get('id')]),  [ 'role'=>'modal-remote','class' => 'btn btn-default btn-form recipe-back','data-modal-size' => 'large']);
             }
             ?>
        </span>

        <span class="btn-recipe-check-application">
        <?php
            if(isset($flag[1])){ //儿科
                echo Html::a('打印儿科处方',Url::to(['@outpatientOutpatientRecipePrinkInfo', 'id' => Yii::$app->request->get('id'),'filterType' => 2]),  ['data-modal-size' => 'normal', 'role'=>'modal-remote','class' => 'btn btn-default btn-form print-check', 'name'=>Yii::$app->request->get('id').'recipe-myshow']);
            }
            if(isset($flag[20])){//精二
                echo Html::a('打印精二处方',Url::to(['@outpatientOutpatientRecipePrinkInfo', 'id' => Yii::$app->request->get('id'),'filterType' => 1]),  ['data-modal-size' => 'normal', 'role'=>'modal-remote','class' => 'btn btn-default btn-form print-check', 'name'=>Yii::$app->request->get('id').'recipe-myshow']);
             }
        ?>

        </span>
    </div>
    <?php ActiveForm::end()?>


<div id='recipe_print' class="tab-pane hide"></div>
    <?php
    $recipeCount = count($recipeRecordDataProvider);
    $hasChargeed = json_encode($hasChargeed, true);
    $recipeTotalNumsList = json_encode($recipeTotalNumsList,true);
    $recipeUsedTotalNums = json_encode($recipeUsedTotalNums,true);
    $js = <<<JS
       baseUrl = '$baseUrl';
       recipeCount = '$recipeCount';
       hasChargeed = $hasChargeed;
       recipeTemplateMenu = $recipeTemplateMenu; 
       recipeTotalNumsList = $recipeTotalNumsList;
       recipeUsedTotalNums = $recipeUsedTotalNums;
       require(["$baseUrl/public/js/outpatient/highRisk.js"], function (mainHighRisk) {
            mainHighRisk.init();
            $('#recipe-template-select').hide();
    });
JS;
    $this->registerJs($js);
    ?>

<?php Pjax::end()?>

</div>
