<?php

use yii\grid\GridView;
use app\modules\inspect\models\InspectRecordUnion;
use yii\helpers\Html;
use app\modules\patient\models\Patient;

/* @var $this yii\web\View */
/* @var $model app\modules\charge\models\ChargeRecord */
/* @var $form yii\widgets\ActiveForm */
$baseUrl = Yii::$app->request->baseUrl;
?>
<div class="warn-record-form">
	<div class = 'row' style = "margin-bottom: 10px;">
		<div class = 'col-md-6'>
			姓名：<?= Html::encode($patientInfo['username']) ?>
		</div>
		<div class = 'col-md-6'>
			病历号：<?= $patientInfo['patient_number'] ?>
		</div>
	</div>
	<div class = 'row' style = "margin-bottom: 10px;">
		<div class = 'col-md-6'>
			性别：<?= Patient::$getSex[$patientInfo['sex']] ?>
		</div>
		<div class = 'col-md-6'>
			年龄：<?= Patient::dateDiffage($patientInfo['birthday'],time()) ?>
		</div>
	</div>
	<div class = 'row' style = "margin-bottom: 10px;">
		<div class = 'col-md-6'>
			手机号：<?= $patientInfo['iphone'] ?>
		</div>
		<div class = 'col-md-6'>
			接诊时间：<?= $patientInfo['diagnosis_time']?date('Y-m-d H:i',$patientInfo['diagnosis_time']):'' ?>
		</div>
	</div>
	<div class = 'row'>
		<div class = 'col-md-12' style = 'margin: 20px 0px;'>
			<span class = 'red'><?= Html::encode($patientInfo['username']) ?></span>的以下检验项目出现<span class = "red">危急值</span>，请知晓并立即作出处理。
		</div>
	</div>
    <div class = 'row'>
        <div class = 'col-md-12'>
            <?=
            GridView::widget([
                'dataProvider' => $dataProvider,
                'options' => ['class' => 'grid-view table-responsive'],
                'tableOptions' => ['class' => 'table table-hover  add-table-border'],
                'headerRowOptions' => ['class' => 'header'],
                'layout' => '{items}<div class="text-right">{pager}</div>',
                'columns' => [
                    'name',
                    [
                        'attribute' => 'result',
                        'format' => 'raw',
                        'value' => function ($dataProvider){
                            $html = Html::encode($dataProvider->result);
                            $options = [];
                            if(in_array(strtoupper($dataProvider->result_identification), ['H','HH','P','Q','E'])){
                                $options['class'] = 'red';
                            }else if(in_array(strtoupper($dataProvider->result_identification), ['L','LL'])){
                                $options['class'] = 'blue';
                            }
                            $text = Html::tag('span',$html,$options);
                            return $text;
                        }
                    ],
                    [
                        'attribute' => 'result_identification',
                        'format' => 'raw',
                        'value' => function ($dataProvider){
                           return InspectRecordUnion::getResultIdentification($dataProvider->result_identification);
                        }
                    ],
                    'unit',
                    'reference'
                ],
            ])
            ?>
        </div>
    </div>
</div>
