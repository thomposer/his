<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;
use app\assets\AppAsset;
use app\modules\patient\models\Patient;
use app\common\Common;
use app\common\Percentage;
/* @var $this yii\web\View */
/* @var $model app\modules\inspect\models\Inspect */
/* @var $form yii\widgets\ActiveForm */

$this->registerCss('
    #ajaxCrudModal .modal-body {
        padding: 0px;
    }
    .print{
        margin-right:10px!important;
    }
');
foreach ($weight as $key => $weightV){
    foreach ($weightV as $v) {
        $age = $v['age'];
        if($key == 0){//小于5岁的
            $age = $v['age'] / 30;
        }
        $weightTh[$key]['th3'][] = [$age,$v['th3']];
        $weightTh[$key]['th15'][] = [$age,$v['th15']];
        $weightTh[$key]['th50'][] = [$age,$v['th50']];
        $weightTh[$key]['th85'][] = [$age,$v['th85']];
        $weightTh[$key]['th97'][] = [$age,$v['th97']];
        $yAxisWeightTh[$key]['Th3'] = $v['th3'] + 0.6;
        $yAxisWeightTh[$key]['Th15'] = $v['th15'] + 0.6;
        $yAxisWeightTh[$key]['Th50'] = $v['th50'] + 0.6;
        $yAxisWeightTh[$key]['Th85'] = $v['th85'] + 0.6;
        $yAxisWeightTh[$key]['Th97'] = $v['th97'] + 0.6;
    }

}
foreach ($height as $key => $heightV){
    foreach ($heightV as $v) {
        $age = $v['age'];
        if($key == 0){//小于5岁的
            $age = $v['age'] / 30;
        }
        $heightTh[$key]['th3'][] = [$age,$v['th3']];
        $heightTh[$key]['th15'][] = [$age,$v['th15']];
        $heightTh[$key]['th50'][] = [$age,$v['th50']];
        $heightTh[$key]['th85'][] = [$age,$v['th85']];
        $heightTh[$key]['th97'][] = [$age,$v['th97']];
        $yAxisHeightTh[$key]['Th3'] = $v['th3'] + 1.4;
        $yAxisHeightTh[$key]['Th15'] = $v['th15'] + 1.4;
        $yAxisHeightTh[$key]['Th50'] = $v['th50'] + 1.4;
        $yAxisHeightTh[$key]['Th85'] = $v['th85'] + 1.4;
        $yAxisHeightTh[$key]['Th97'] = $v['th97'] + 1.4;
    }
    
}
foreach ($headCircumference as $key => $headCircumferenceV){
    foreach ($headCircumferenceV as $v){
        $age = $v['age'];
        if($key == 0){//小于5岁的
            $age = $v['age'] / 30;
        }
        $headCircumferenceTh[$key]['th3'][] = [$age,$v['th3']];
        $headCircumferenceTh[$key]['th15'][] = [$age,$v['th15']];
        $headCircumferenceTh[$key]['th50'][] = [$age,$v['th50']];
        $headCircumferenceTh[$key]['th85'][] = [$age,$v['th85']];
        $headCircumferenceTh[$key]['th97'][] = [$age,$v['th97']];
        $yAxisHeadCircumferenceTh[$key]['Th3'] = $v['th3'] + 0.4;
        $yAxisHeadCircumferenceTh[$key]['Th15'] = $v['th15'] + 0.4;
        $yAxisHeadCircumferenceTh[$key]['Th50'] = $v['th50'] + 0.4;
        $yAxisHeadCircumferenceTh[$key]['Th85'] = $v['th85'] + 0.4;
        $yAxisHeadCircumferenceTh[$key]['Th97'] = $v['th97'] + 0.4;
    }
}
foreach ($bmi as $key => $bmiV){
    foreach ($bmiV as $v) {
        $age = $v['age'];
        if($key == 0){//小于5岁的
            $age = $v['age'] / 30;
        }
        $bmith[$key]['th3'][] = [$age,$v['th3']];
        $bmith[$key]['th15'][] = [$age,$v['th15']];
        $bmith[$key]['th50'][] = [$age,$v['th50']];
        $bmith[$key]['th85'][] = [$age,$v['th85']];
        $bmith[$key]['th97'][] = [$age,$v['th97']];
        $yAxisBmiTh[$key]['Th3'] = $v['th3'] + 0.4;
        $yAxisBmiTh[$key]['Th15'] = $v['th15'] + 0.4;
        $yAxisBmiTh[$key]['Th50'] = $v['th50'] + 0.4;
        $yAxisBmiTh[$key]['Th85'] = $v['th85'] + 0.4;
        $yAxisBmiTh[$key]['Th97'] = $v['th97'] + 0.4;
    }
}
//Z值的数据整合
//身高
foreach ($zscoreHeight as $key => $zscoreHeightV){
    foreach ($zscoreHeightV as $v) {
        $age = $v['age'];
        if($key == 0){//小于5岁的
            $age = $v['age'] / 30;
        }
        $heightSd[$key]['Sd3neg'][] = [$age,$v['sd3neg']];
        $heightSd[$key]['Sd2neg'][] = [$age,$v['sd2neg']];
        $heightSd[$key]['Sd1neg'][] = [$age,$v['sd1neg']];
        $heightSd[$key]['Sd0'][] = [$age,$v['sd0']];
        $heightSd[$key]['Sd1'][] = [$age,$v['sd1']];
        $heightSd[$key]['Sd2'][] = [$age,$v['sd2']];
        $heightSd[$key]['Sd3'][] = [$age,$v['sd3']];

        $yAxisHeightSd[$key]['Sd3neg'] = $v['sd3neg'] + 1.5;
        $yAxisHeightSd[$key]['Sd2neg'] = $v['sd2neg'] + 1.5;
        $yAxisHeightSd[$key]['Sd1neg'] = $v['sd1neg'] + 1.5;
        $yAxisHeightSd[$key]['Sd0'] = $v['sd0'] + 1.5;
        $yAxisHeightSd[$key]['Sd1'] = $v['sd1'] + 1.5;
        $yAxisHeightSd[$key]['Sd2'] = $v['sd2'] + 1.5;
        $yAxisHeightSd[$key]['Sd3'] = $v['sd3'] + 1.5;
    }
}
//体重z值
foreach ($zscoreWeight as $key => $zscoreWeightV){
    foreach ($zscoreWeightV as $v) {
        $age = $v['age'];
        if($key == 0){//小于5岁的
            $age = $v['age'] / 30;
        }
        $weightSd[$key]['Sd3neg'][] = [$age,$v['sd3neg']];
        $weightSd[$key]['Sd2neg'][] = [$age,$v['sd2neg']];
        $weightSd[$key]['Sd1neg'][] = [$age,$v['sd1neg']];
        $weightSd[$key]['Sd0'][] = [$age,$v['sd0']];
        $weightSd[$key]['Sd1'][] = [$age,$v['sd1']];
        $weightSd[$key]['Sd2'][] = [$age,$v['sd2']];
        $weightSd[$key]['Sd3'][] = [$age,$v['sd3']];

        $yAxisWeightSd[$key]['Sd3neg'] = $v['sd3neg'] + 0.4;
        $yAxisWeightSd[$key]['Sd2neg'] = $v['sd2neg'] + 0.4;
        $yAxisWeightSd[$key]['Sd1neg'] = $v['sd1neg'] + 0.4;
        $yAxisWeightSd[$key]['Sd0'] = $v['sd0'] + 0.4;
        $yAxisWeightSd[$key]['Sd1'] = $v['sd1'] + 0.4;
        $yAxisWeightSd[$key]['Sd2'] = $v['sd2'] + 0.4;
        $yAxisWeightSd[$key]['Sd3'] = $v['sd3'] + 0.4;
    }
}
//头围z值
foreach ($zscoreHeadCircumference as $key => $zscoreHeadCircumferenceV){
    foreach ($zscoreHeadCircumferenceV as $v) {
        $age = $v['age'];
        if($key == 0){//小于5岁的
            $age = $v['age'] / 30;
        }
        $headCircumferenceSd[$key]['Sd3neg'][] = [$age,$v['sd3neg']];
        $headCircumferenceSd[$key]['Sd2neg'][] = [$age,$v['sd2neg']];
        $headCircumferenceSd[$key]['Sd1neg'][] = [$age,$v['sd1neg']];
        $headCircumferenceSd[$key]['Sd0'][] = [$age,$v['sd0']];
        $headCircumferenceSd[$key]['Sd1'][] = [$age,$v['sd1']];
        $headCircumferenceSd[$key]['Sd2'][] = [$age,$v['sd2']];
        $headCircumferenceSd[$key]['Sd3'][] = [$age,$v['sd3']];

        $yAxisHeadCircumferenceSd[$key]['Sd3neg'] = $v['sd3neg'] + 0.4;
        $yAxisHeadCircumferenceSd[$key]['Sd2neg'] = $v['sd2neg'] + 0.4;
        $yAxisHeadCircumferenceSd[$key]['Sd1neg'] = $v['sd1neg'] + 0.4;
        $yAxisHeadCircumferenceSd[$key]['Sd0'] = $v['sd0'] + 0.4;
        $yAxisHeadCircumferenceSd[$key]['Sd1'] = $v['sd1'] + 0.4;
        $yAxisHeadCircumferenceSd[$key]['Sd2'] = $v['sd2'] + 0.4;
        $yAxisHeadCircumferenceSd[$key]['Sd3'] = $v['sd3'] + 0.4;
    }
}
//bmi
foreach ($zscoreBmi as $key => $zscoreBmiV){
    foreach ($zscoreBmiV as $v) {
        $age = $v['age'];
        if($key == 0){//小于5岁的
            $age = $v['age'] / 30;
        }
        $bmiSd[$key]['Sd3neg'][] = [$age,$v['sd3neg']];
        $bmiSd[$key]['Sd2neg'][] = [$age,$v['sd2neg']];
        $bmiSd[$key]['Sd1neg'][] = [$age,$v['sd1neg']];
        $bmiSd[$key]['Sd0'][] = [$age,$v['sd0']];
        $bmiSd[$key]['Sd1'][] = [$age,$v['sd1']];
        $bmiSd[$key]['Sd2'][] = [$age,$v['sd2']];
        $bmiSd[$key]['Sd3'][] = [$age,$v['sd3']];

        $yAxisBmiSd[$key]['Sd3neg'] = $v['sd3neg'] + 0.4;
        $yAxisBmiSd[$key]['Sd2neg'] = $v['sd2neg'] + 0.4;
        $yAxisBmiSd[$key]['Sd1neg'] = $v['sd1neg'] + 0.4;
        $yAxisBmiSd[$key]['Sd0'] = $v['sd0'] + 0.4;
        $yAxisBmiSd[$key]['Sd1'] = $v['sd1'] + 0.4;
        $yAxisBmiSd[$key]['Sd2'] = $v['sd2'] + 0.4;
        $yAxisBmiSd[$key]['Sd3'] = $v['sd3'] + 0.4;
    }
}




function getPatientInfo($result,$yearsSex){
    $patientWeight = [];
    $patientHeightcm = [];
    $patientHeadCircumference = [];
    $patientBmi = [];
    $patientHeightcmZscore = [];
    $patientWeightZscore = [];
    $patientHeadCircumferenceZscore = [];
    $patientBmiZscore = [];

    if(!empty($result)){
        foreach ($result as $value){
//            if($value['status'] != 5 && $type == 1){
//                continue;
//            }
            $rows = Patient::dateDiffageTime($value['birthday'],$value['reportTime']);
            $recordMonth = $rows['year'] * 12 + $rows['month'];//月龄
            $recordDay = Common::num($rows['day']/30);
            $xResult = $recordMonth + $recordDay;
            $heightcmP = true;
            $heightcmZ = true;
            $weightkgP = true;
            $weightkgZ = true;
            if($yearsSex == 1){//0-5岁
                if($xResult > 60){//超过坐标系去掉该点
                    continue;
                }

                if($value['heightcm'] == null || $value['heightcm'] < 40 || $value['heightcm'] > 125){
                    $heightcmP = false;
                    $heightcmZ = false;
                }
                if($value['weightkg'] == null || $value['weightkg'] < 0 || $value['weightkg'] > 28){
                    $weightkgP = false;
                }
                if($value['weightkg'] == null ||$value['weightkg'] <= 0 || $value['weightkg'] > 30){
                    $weightkgZ = false;
                }
                if($value['head_circumference'] == null || $value['head_circumference'] < 30 || $value['head_circumference'] > 56){
                    $value['head_circumference'] = null;
                }
            }else if($yearsSex == 2){//6岁以上
                if($xResult <= 60 || $xResult >= 228){//超过坐标系去掉该点
                    continue;
                }
                if($value['heightcm'] == null || $value['heightcm'] < 90 || $value['heightcm'] > 200){
                    $heightcmP = false;
                    $heightcmZ = false;
                }
                if($value['weightkg'] == null || $value['weightkg'] < 10 || $value['weightkg'] > 50 || $xResult > 122){
                    $weightkgP = false;
                }
                if($value['weightkg'] == null || $value['weightkg'] < 10 || $value['weightkg'] > 60 || $xResult > 122){
                    $weightkgZ = false;
                }

            }
            if($heightcmP){//百分率身高数据
                $patientHeightcm[] = [$xResult,$value['heightcm'],Percentage::getPercentage($value['heightcm'], $value['sex'], 1, $value['birthday'],$value['diagnosis_time'])];
            }
            if($heightcmZ){//z值身高数据
    //             $patientHeightcmZscore[] = [$xResult,Percentage::getZScore($value['heightcm'], $value['sex'],1, $value['birthday'],$value['diagnosis_time'])];
                   $patientHeightcmZscore[] = [$xResult,$value['heightcm'],Percentage::getZScore($value['heightcm'], $value['sex'],1, $value['birthday'],$value['diagnosis_time'])];       
            }

            if($weightkgP){

                $patientWeight[] = [$xResult,$value['weightkg'],Percentage::getPercentage($value['weightkg'], $value['sex'], 2, $value['birthday'],$value['diagnosis_time'])];
            }
            if($weightkgZ){
    //             $patientWeightZscore[] = [$xResult,Percentage::getZScore($value['weightkg'], $value['sex'],2, $value['birthday'],$value['diagnosis_time'])];
                   $patientWeightZscore[] = [$xResult,$value['weightkg'],Percentage::getZScore($value['weightkg'], $value['sex'],2, $value['birthday'],$value['diagnosis_time'])];

            }


            if($value['head_circumference'] != null){

                $patientHeadCircumference[] = [$xResult,$value['head_circumference'],Percentage::getPercentage($value['head_circumference'], $value['sex'], 3, $value['birthday'],$value['diagnosis_time'])];
                $patientHeadCircumferenceZscore[] = [$xResult,$value['head_circumference'],Percentage::getZScore($value['head_circumference'], $value['sex'], 3, $value['birthday'],$value['diagnosis_time'])];
    //             $patientHeadCircumferenceZscore[] = [$xResult,Percentage::getZScore($value['head_circumference'], $value['sex'], 3, $value['birthday'],$value['diagnosis_time'])];
            }
            if($value['heightcm'] != null && $value['heightcm'] != 0){
                $height = $value['heightcm'] / 100;
                $bmiValue = $value['heightcm'] ? (sprintf('%.2f', round($value['weightkg'] / ($height * $height),2))) : (($value['heightcm'] == null) ? null : 0);
                $bmiP = true;
                $bmiZ = true;
                if($yearsSex == 1){//0-5岁
                    if($bmiValue < 9 || $bmiValue >22){
                        $bmiP = false;
                    }
                    if($bmiValue < 9 || $bmiValue > 24){
                        $bmiZ = false;
                    }
                }else if($yearsSex == 2){//6岁以上
                    if($bmiValue < 12 || $bmiValue > 30){
                        $bmiP = false;
                    }
                    if($bmiValue < 10 || $bmiValue > 38){
                        $bmiZ = false;
                    }
                }
                if($bmiP){
                    $patientBmi[] = [$xResult,$bmiValue,Percentage::getPercentage($bmiValue, $value['sex'], 4, $value['birthday'],$value['diagnosis_time'])]; 
                }
                if($bmiZ){
                    $patientBmiZscore[] = [$xResult,$bmiValue,Percentage::getZScore($bmiValue, $value['sex'], 4, $value['birthday'],$value['diagnosis_time'])];
    //                 $patientBmiZscore[] = [$xResult,Percentage::getZScore($bmiValue, $value['sex'], 4, $value['birthday'],$value['diagnosis_time'])];
                }
            }
        }
    }

    $info = array(
            'patientWeight' => $patientWeight,
            'patientHeightcm' => $patientHeightcm,
            'patientHeadCircumference' => $patientHeadCircumference,
            'patientBmi' => $patientBmi,
            'patientHeightcmZscore' => $patientHeightcmZscore,
            'patientWeightZscore' => $patientWeightZscore,
            'patientHeadCircumferenceZscore' => $patientHeadCircumferenceZscore,
            'patientBmiZscore' => $patientBmiZscore,
        );
    return $info;
}
if(!empty($result)){
    $patientInfo[] = getPatientInfo($result,1);
    $patientInfo[] = getPatientInfo($result,2);
}
$items = [
   [
        'label' => '身高-年龄',
        'options' => ['id' => 'growthHeight'],
        'active' => true
   ],
   [
        'label' => '体重-年龄',
        'options' => ['id' => 'growthWeight']
        
   ],
   [
        'label' => '头围-年龄',
        'options' => ['id' => 'growthHead']
   ],
    [
        'label' => 'BMI-年龄',
        'options' => ['id' => 'growthBmi']
    ]
];
AppAsset::addCss($this, '@web/public/css/lib/tab.css');
AppAsset::addCss($this, '@web/public/css/growth/search.css');
AppAsset::addCss($this, '@web/public/css/lib/commonPrint.css');
AppAsset::addCss($this, '@web/public/css/growth/growthPrint.css');
?>

     <div class="growth-cruve" >
                <?=
                Tabs::widget([
                    'renderTabContent' => false,
                    'navType' => ' nav-tabs outpatient-form',
                    'items' => $items
                ]);
                ?>
            <div class="col-xs-12">
                <div class = 'tab-content'>
                	<div id = "growthHeight" class = "tab-pane active">
                	
                		<div class="row search-margin padding-left">
                            <div class="col-sm-12 col-md-12">
                                <div class="btn-group pull-left select-age" id="J-select-box">
                                    <?php if(1 == $yearsSex):?>
                        		<span class="btn  btn-group-left active echarts-age" data-id="height-line" data-value="1" age="0">
                        			<a href="javascript:void(0)" class = "age0">0-5岁</a>
                        		</span>
                        		<span class="btn btn-group-right echarts-age" data-id="height-line" data-value="0" age="1">
                        			<a href="javascript:void(0)"  class = "age5">5-19岁</a>
                        		</span>
                                    <?php else: ?>
                                        <span class="btn  btn-group-left echarts-age" data-id="height-line" data-value="0" age="0">
                        			<a href="javascript:void(0)" class = "age0">0-5岁</a>
                        		</span>
                        		<span class="btn btn-group-right active echarts-age" data-id="height-line" data-value="1" age="1">
                        			<a href="javascript:void(0)"  class = "age5">5-19岁</a>
                        		</span>
                                    <?php endif; ?>
                        	</div>
                              <div class="btn-group pull-right select-type" id="J-select-box">
                        		<span class="btn  btn-group-left active echarts-growth" data-id = "height-line" data-value="1" name="百分位">
                        			<a href="javascript:void(0)" class = "heightMain">百分位值</a>
                        		</span>
                        		<span class="btn btn-group-right echarts-growth" data-id = "height-line" data-value="0" name="Z值">
                        			<a href="javascript:void(0)" class = "heightMainZscore">  Z值  </a>
                        		</span>
                        	  </div>
                           </div>
                        </div>
                            <?php if(1 == $yearsSex):?>
                                <div class="height-line" id="heightMain-0" style="width: 850px;height:450px;"></div>
                                <div class="height-line" id="heightMain-1" style="width: 850px;height:450px;display: none;"></div>
                            <?php else: ?>
                                <div class="height-line" id="heightMain-0" style="width: 850px;height:450px;display: none;"></div>
                                <div class="height-line" id="heightMain-1" style="width: 850px;height:450px;"></div>
                            <?php endif; ?>
                	<div class="height-line" id="heightMainZscore-0" style="width: 850px;height:450px;display: none;"></div>
                        <div class="height-line" id="heightMainZscore-1" style="width: 850px;height:450px;display: none;"></div>
                	</div>
                	<div id = "growthWeight" class = "tab-pane">
                	
                		<div class="row search-margin padding-left">
                            <div class="col-sm-12 col-md-12">
                                <div class="btn-group pull-left select-age" id="J-select-box">
                                <?php if(1 == $yearsSex):?>
                        		<span class="btn  btn-group-left active echarts-age" data-id="weight-line" data-value="1" age="0">
                        			<a href="javascript:void(0)" class = "age0">0-5岁</a>
                        		</span>
                        		<span class="btn btn-group-right echarts-age" data-id="weight-line" data-value="0" age="1">
                        			<a href="javascript:void(0)"  class = "age5">5-19岁</a>
                        		</span>
                                    <?php else: ?>
                                        <span class="btn  btn-group-left echarts-age" data-id="weight-line" data-value="0" age="0">
                        			<a href="javascript:void(0)" class = "age0">0-5岁</a>
                        		</span>
                        		<span class="btn btn-group-right active echarts-age" data-id="weight-line" data-value="1" age="1">
                        			<a href="javascript:void(0)"  class = "age5">5-19岁</a>
                        		</span>
                                    <?php endif; ?>
                                    </div>
                              <div class="btn-group pull-right select-type" id="J-select-box">
                        		<span class="btn  btn-group-left active echarts-growth" data-id = 'weight-line' data-value="1" name="百分位">
                        			<a href="javascript:void(0)" class = "weightMain">百分位值</a>
                        		</span>
                        		<span class="btn btn-group-right echarts-growth" data-id = 'weight-line' data-value="0" name="Z值">
                        			<a href="javascript:void(0)"  class = "weightMainZscore">  Z值  </a>
                        		</span>
                        	  </div>
                           </div>
                        </div>
                            <?php if(1 == $yearsSex):?>
                		<div class="weight-line" id="weightMain-0" style="width: 850px;height:450px;"></div>
                                <div class="weight-line" id="weightMain-1" style="width: 850px;height:450px;display: none;"></div>
                            <?php else: ?>
                                <div class="weight-line" id="weightMain-0" style="width: 850px;height:450px;display: none;"></div>
                                <div class="weight-line" id="weightMain-1" style="width: 850px;height:450px;"></div>
                            <?php endif; ?>
                		<div class="weight-line" id="weightMainZscore-0" style="width: 850px;height:450px;display: none;"></div>
                                <div class="weight-line" id="weightMainZscore-1" style="width: 850px;height:450px;display: none;"></div>
                	</div>
                	<div id = "growthHead" class = "tab-pane">
                	
                		<div class="row search-margin padding-left">
                            <div class="col-sm-12 col-md-12">
                              <div class="btn-group pull-right select-type" id="J-select-box">
                        		<span class="btn  btn-group-left active echarts-growth" data-id = "headCircum-line" name="百分位">
                        			<a href="javascript:void(0)" class = "headCircumference">百分位值</a>
                        		</span>
                        		<span class="btn btn-group-right echarts-growth" data-id = "headCircum-line" name="Z值">
                        			<a href="javascript:void(0)"  class = "headCircumferenceZscore">  Z值  </a>
                        		</span>
                        	  </div>
                           </div>
                        </div>
                        
                		<div class="headCircum-line" id="headCircumference-0" style="width: 850px;height:450px;"></div>
                	 	<div class="headCircum-line" id="headCircumferenceZscore-0" style="width: 850px;height:450px;display: none;"></div>
                	</div>
                	<div id = "growthBmi" class = "tab-pane">
                	
                		<div class="row search-margin padding-left">
                            <div class="col-sm-12 col-md-12">
                                <div class="btn-group pull-left select-age" id="J-select-box">
                                <?php if(1 == $yearsSex):?>
                        		<span class="btn  btn-group-left active echarts-age" data-id="bmi-line" data-value="1" age="0">
                        			<a href="javascript:void(0)" class = "age0">0-5岁</a>
                        		</span>
                        		<span class="btn btn-group-right echarts-age" data-id="bmi-line" data-value="0" age="1">
                        			<a href="javascript:void(0)"  class = "age5">5-19岁</a>
                        		</span>
                                    <?php else: ?>
                                        <span class="btn  btn-group-left echarts-age" data-id="bmi-line" data-value="0" age="0">
                        			<a href="javascript:void(0)" class = "age0">0-5岁</a>
                        		</span>
                        		<span class="btn btn-group-right active echarts-age" data-id="bmi-line" data-value="1" age="1">
                        			<a href="javascript:void(0)"  class = "age5">5-19岁</a>
                        		</span>
                                    <?php endif; ?>
                                    </div>
                              <div class="btn-group pull-right select-type" id="J-select-box">
                        		<span class="btn  btn-group-left active echarts-growth" data-id = "bmi-line" data-value="1" name="百分位">
                        			<a href="javascript:void(0)" class = "bmi">百分位值</a>
                        		</span>
                        		<span class="btn btn-group-right echarts-growth" data-id = "bmi-line" data-value="0" name="Z值">
                        			<a href="javascript:void(0)" class = "bmiZscore">Z值</a>
                        		</span>
                        	  </div>
                           </div>
                        </div>
                            <?php if(1 == $yearsSex):?>
                		<div class="bmi-line" id="bmi-0" style="width: 850px;height:450px;"></div>
                                <div class="bmi-line" id="bmi-1" style="width: 850px;height:450px;display: none;"></div>
                            <?php else: ?>
                                <div class="bmi-line" id="bmi-0" style="width: 850px;height:450px;display: none;"></div>
                                <div class="bmi-line" id="bmi-1" style="width: 850px;height:450px;"></div>
                            <?php endif; ?>    
                		<div class="bmi-line" id="bmiZscore-0" style="width: 850px;height:450px;display: none;"></div>
                                <div class="bmi-line" id="bmiZscore-1" style="width: 850px;height:450px;display: none;"></div>
                	</div>
                  
            </div>
        </div>

	</div>

<script type="text/javascript">
	var baseUrl = '<?= Yii::$app->request->baseUrl; ?>';
        var patientInfo = '<?= json_encode($patientInfo,true); ?>';
	var weightTh = <?= json_encode($weightTh,true); ?>;
        
	var heightTh = <?= json_encode($heightTh,true); ?>;
        
	var headCircumferenceTh = <?= json_encode($headCircumferenceTh,true) ?>;

	var bmith = <?= json_encode($bmith,true); ?>;
        
	var minMonth = '<?= json_encode($minMonth,true); ?>';//x轴最小月
	var maxMonth = '<?= json_encode($maxMonth,true); ?>';//x轴最大月

	var yAxisWeightTh = '<?= json_encode($yAxisWeightTh,true); ?>';//y轴

	var yAxisHeightTh = '<?= json_encode($yAxisHeightTh,true); ?>';//y轴
	
	var yAxisHeadCircumferenceTh = '<?= json_encode($yAxisHeadCircumferenceTh,true); ?>';//y轴	
	
	var yAxisBmiTh = '<?= json_encode($yAxisBmiTh,true);?>';//y轴

	var yearsSex = '<?= $yearsSex ?>';

	//z值的数据
	//身高的值
	var heightSd = <?= json_encode($heightSd,true); ?>;
	
        var yAxisHeightSd = '<?= json_encode($yAxisHeightSd,true); ?>';
	
	//体重的值
	var weightSd = <?= json_encode($weightSd,true); ?>;
	
        var yAxisWeightSd = '<?= json_encode($yAxisWeightSd,true); ?>';

    
	//头围值
	var headCircumferenceSd = <?= json_encode($headCircumferenceSd,true); ?>;
	
        var yAxisHeadCircumferenceSd = '<?= json_encode($yAxisHeadCircumferenceSd,true); ?>';
    
	//bmi值
	var bmiSd = <?= json_encode($bmiSd,true); ?>;;
	
        var yAxisBmiSd = '<?= json_encode($yAxisBmiSd,true); ?>';

	
    //	生长曲线打印
    var spotInfo = <?= json_encode($spotInfo,true) ?>;
    var cdnHost = '<?= Yii::$app->params['cdnHost'] ?>';
    var growthTriageInfo = <?= json_encode($result['0'],true)?>;
    var diagnosisTime = '<?= $diagnosisTime ?>';
    var spotConfig = <?= json_encode($spotConfig,true);?>;
    require([ baseUrl + "/public/js/growth/weight.js"], function (main) {
        main.init();
    });
</script>

