<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use yii\grid\GridView;
use yii\helpers\Url;
use app\modules\outpatient\models\CureRecord;
use app\modules\outpatient\models\CheckRecord;
use app\assets\AppAsset;
$checkRecordModel = new CheckRecord();
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
        echo $this->render('_checkContent',
            [
                'val' => $val,
                'model' => $checkRecordModel,
            ]
        );
    }else{
        echo $this->render('_noContent',
            [
                'val' => $val,
                'model' => $checkRecordModel,
            ]
        );
    }

?>



