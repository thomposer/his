<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use rkit\yii2\plugins\ajaxform\Asset;
use yii\bootstrap\Tabs;
use yii\db\ActiveQuery;
/* @var $this yii\web\View */
/* @var $model app\modules\check\models\Check */
/* @var $form yii\widgets\ActiveForm */

Asset::register($this);
$permissonType = [];
$items = [];

if(count($checkList) > 1){
    foreach ($checkList as $key=> $v){
        $active = false;
        if($key == 0){
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
    <div class="check-record col-xs-12">
        <div class = 'tab-content'>
            <?php
            foreach($checkList as $key =>  $val){
                $active = '';
                if($key == 0){
                    $active = 'active';
                }

                echo Html::tag('div',$this->render('_checkForm',[
                    'model' => $model,
                    'status'=>$status,
                    'val'=>$val,
                    'soptInfo' => $soptInfo,
                ]),['class' => 'tab-pane '.$active,'id' => $val['id']]);

                if($status == 1){
                    echo Html::tag('div',$this->render('_printCheckForm',[
                        'model' => $model,
                        'status'=>$status,
                        'val'=>$val,
                        'soptInfo' => $soptInfo,
                        'triageInfo'=>$triageInfo,
                        'baseUrl' => $baseUrl,
                        'spotConfig' => $spotConfig
                    ]),['class' => 'tab-pane _printCheckForm','id' => $val['id'].'myshow']);
                }
            }
            ?>
        </div>
    </div>
</div>