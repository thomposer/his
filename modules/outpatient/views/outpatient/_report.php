<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Tabs;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\modules\outpatient\models\Outpatient */
/* @var $form yii\widgets\ActiveForm */
// AppAsset::addCss($this, '@web/public/css/patient/infomation.css');
// $this->registerCss('
//     #ajaxCrudModal .modal-body {
//         padding: 0px;
//     }
// ');
?>
<?php Pjax::begin([
    'id' => 'reportPjax',
    'timeout' => 5000,
])?>
<div class="patient-form">
    <?php
    $items = [];
    if (count($inspectCheckList) > 1) {
        foreach ($inspectCheckList as $key => $v) {
            $active = false;
            if ($key == 0) {
                $active = true;
            }
            if (iconv_strlen($v['name']) > 8) {
                $name = mb_substr($v['name'], 0, 8);
                $v['name'] = $name . '...';
            }
            if (isset($v['inspectUnionDataProvider'])) {//实验室检查 有列表
                $items[] = [
                    'options' => ['id' => 'reportInspect' . $v['id']],
                    'label' => $v['name'],
                    'active' => false
                ];
            } else {
                $items[] = [
                    'options' => ['id' => 'reportCheck' . $v['id']],
                    'label' => $v['name'],
                    'active' => false
                ];
            }
        }
    }
    echo Tabs::widget([
        'renderTabContent' => false,
        'navType' => 'nav nav-second  outpatient-form',
        'items' => $items
    ]);
    ?>
    <div class="cure-record-index row">
        <div class = 'tab-content'>
            <?php
            if (!empty($inspectCheckList)) {
                foreach ($inspectCheckList as $key => $val) {
                    if ($key == 0) {
                        $active = 'active';
                    }else{
                        $active = '';
                    }
                    if (isset($val['inspectUnionDataProvider'])) {//实验室检查 有列表
                        echo Html::tag('div', $this->render('_inspectReport', [
                                    'val' => $val,
                                    'dataProvider' => $val['inspectUnionDataProvider'],
                                ]), ['class' => 'tab-pane ' . $active, 'id' => 'reportInspect' . $val['id']]);
                        $hasActive = true;
                        echo '<div id="Inspect'.$val['id'].'" class="tab-pane inspectprint"> </div>';
                    } else {// 影像学检查
                        echo Html::tag('div', $this->render('_checkReport', [
                                    'val' => $val,
                                    'model' => $checkRecordModel,
                                ]), ['class' => 'tab-pane ' . $active, 'id' => 'reportCheck' . $val['id']]);
                        echo '<div id="Check'.$val['id'].'" class="tab-pane checkprint"> </div>';
                    }
                }
            } else {
                echo '<div class="no-content">暂无内容</div>';
            }
            ?>


        </div>
    </div>
<?php Pjax::end();?>