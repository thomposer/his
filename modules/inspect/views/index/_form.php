<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use rkit\yii2\plugins\ajaxform\Asset;
use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $model app\modules\inspect\models\Inspect */
/* @var $form yii\widgets\ActiveForm */

Asset::register($this);
$permissonType = [];
$items = [];
if (count($inspectList) > 1) {
    foreach ($inspectList as $key => $v) {
        $active = false;
        if ($key == 0) {
            $active = true;
        }
        $items[] = [
            'label' => $v['name'],
            'options' => ['id' => $v['id']],
            'linkOptions' => ['title' => $v['name']],
            'active' => $active
        ];
    }
}
?>

<div class="patient-form">
    <?=
    Tabs::widget([
        'renderTabContent' => false,
        'navType' => ' nav-tabs outpatient-form',
        'items' => $items
    ]);
    ?>
    <div class="inspect-record col-xs-12">
        <div class = 'tab-content'>
            <?php
            foreach ($inspectList as $key => $val) {
                $active = '';
                if ($key == 0) {
                    $active = 'active';
                }
                echo Html::tag('div', $this->render('_inspectForm', [
                            'status' => $status,
                            'val' => $val,
                            'dataProvider' => $inspectUnionList[$val['id']],
                        ]), ['class' => 'tab-pane ' . $active, 'id' => $val['id']]);

                echo Html::tag('div', $this->render('_printInspectForm', [
                            'status' => $status,
                            'val' => $val,
                            'dataProvider' => $inspectUnionList[$val['id']],
                            'soptInfo' => $soptInfo,
                            'triageInfo' => $triageInfo,
                            'spotConfig' => $spotConfig,

                        ]), ['class' => 'tab-pane ', 'id' => $val['id'] . 'myshow']);
            }
            ?>
        </div>
    </div>
</div>





