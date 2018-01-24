<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\common\AutoLayout;
/* @var $this yii\web\View */
/* @var $model app\modules\doctor\models\Doctor */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Doctors', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php  AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php  $this->beginBlock('renderCss')?>

<?php  $this->endBlock();?>
<?php  $this->beginBlock('content')?>

<div class="doctor-view col-xs-12">
    <div class = "box">
      <div class="box-header with-border">
      <span class = 'left-title'> <?=  Html::encode($this->title) ?></span>
       <?= Html::a(Html::img($baseUrl.'/public/img/common/icon_back.png').'返回',Yii::$app->request->referrer,['class' => 'right-cancel']) ?>
     </div>
        <div class = "box-body">  
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'record_id',
            'spot_id',
            'incidence_date',
            'heightcm',
            'weightkg',
            'bloodtype',
            'temperature_type',
            'temperature',
            'breathing',
            'pulse',
            'shrinkpressure',
            'diastolic_pressure',
            'oxygen_saturation',
            'pain_score',
            'personalhistory',
            'genetichistory',
            'allergy',
            'chiefcomplaint',
            'historypresent',
            'case_reg_img',
            'pasthistory',
            'physical_examination',
            'examination_check',
            'first_check',
            'cure_idea',
            'remark',
            'doctor_id',
            'room_id',
            'create_time:datetime',
            'update_time:datetime',
            'diagnosis_time',
            'triage_time',
        ],
    ]) ?>
        </div>
    </div>
</div>
<?php  $this->endBlock()?>
<?php  $this->beginBlock('renderJs')?>

<?php  $this->endBlock()?>
<?php  AutoLayout::end()?>
