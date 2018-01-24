<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;
use app\assets\AppAsset;
use kartik\widgets\FileInput;
/* @var $this yii\web\View */
/* @var $model app\modules\outpatient\models\Outpatient */
/* @var $form yii\widgets\ActiveForm */
AppAsset::addCss($this, '@web/public/css/patient/infomation.css?v=33');
AppAsset::addCss($this, '@web/public/css/check/check.css?v=11');
//AppAsset::addCss($this, '@web/public/css/check/checkImage.css?v=11');
AppAsset::addCss($this, '@web/public/css/inspect/inspect.css?v=11');
$this->registerCss('
    #ajaxCrudModal .modal-body {
        padding: 0px;
    }
');
$items = [];
if (count($inspectCheckList) > 0) {
    foreach ($inspectCheckList as $key => $v) {
        $active = false;
        if ($key == 0) {
            $active = true;
        }
        if (isset($v['inspectUnionDataProvider'])) {//实验室检查 有列表
            $id = 'inspectOld'.$v['id'];
        }else{
            $id = 'checkOld'.$v['id'];
        }
        $items[] = [
            'label' => $v['name'],
            'options' => ['id' => $id],
            'active' => $active
        ];
    }
}

?>

<div class="inspect-check-upload-form">
    <?=
    Tabs::widget([
        'renderTabContent' => false,
        'navType' => 'inspect-check-upload-ul',
        'items' => $items
    ]);
    ?>
    <div class="col-xs-12">
        <div class = 'tab-content'>
            <?php
            if (!empty($inspectCheckList)) {
                foreach ($inspectCheckList as $key => $val) {
                    $active = '';
                    if ($key == 0) {
                        $active = 'active';
                    }
                    if (isset($val['inspectUnionDataProvider'])) {//实验室检查 有列表
                        echo Html::tag('div', $this->render('_inspectUploadForm', [
                                    'val' => $val,
                                    'hidden' => true,
                                    'status' => 1,
                                    'dataProvider' => $val['inspectUnionDataProvider'],
                                ]), ['class' => 'tab-pane ' . $active, 'id' => 'inspectOld'.$val['id']]);
                    } else {// 影像学检查
                        echo Html::tag('div', $this->render('_checkUploadForm', [
                                    'val' => $val,
                                    'hidden' => true,
                                    'status' => 1,
                                    'model' => $checkRecordModel,
                                ]), ['class' => 'tab-pane ' . $active, 'id' => 'checkOld'.$val['id']]);
                    }
                }
            }
            ?>
        </div>
    </div>
</div>
