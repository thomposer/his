<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use app\assets\AppAsset;
$report['record_id'] = Yii::$app->request->get('id');
if(isset($val['report_time'])){

    $report['report_time'] = $val['report_time'];
    $report['username'] = $val['username'];
}

/* @var $this yii\web\View */
/* @var $model app\modules\cure\models\Cure */
/* @var $form yii\widgets\ActiveForm */
?>
<?php AppAsset::addCss($this, '@web/public/css/outpatient/tab.css') ?>
<?php
    if($val['status']==1){
        echo $this->render('_inspectRecordContent',
            [
                'val' => $val,
                'dataProvider' => $val['inspectUnionDataProvider'],
            ]
        );
    }else{
        echo $this->render('_noContent',
            [
                'val' => $val,
                'dataProvider' => $val['inspectUnionDataProvider'],
            ]
        );
    }
?>