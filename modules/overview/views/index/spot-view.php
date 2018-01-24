<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\common\AutoLayout;
use app\modules\spot\models\Spot;
/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\Spot */

$this->title = '诊所详情';
$this->params['breadcrumbs'][] = ['label' => '诊所管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php  AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php  $this->beginBlock('renderCss')?>

<?php  $this->endBlock();?>
<?php  $this->beginBlock('content')?>

<div class="spot-view col-xs-12">
    <div class = "box">
      <div class="box-header with-border">
      <span class = 'left-title'> <?=  Html::encode($this->title) ?></span>
       <?= Html::a(Html::img($baseUrl.'/public/img/common/icon_back.png').'返回',Yii::$app->request->referrer,['class' => 'right-cancel']) ?>
     </div>
        <div class = "box-body">  
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'icon_url',
                'format' => 'raw',
                'value' => '<span class="logo-lg">'.Html::img($model->icon_url?$model->icon_url:$baseUrl.'/public/img/common/img_clinic_default.png',['onerror'=>"this.src='/public/img/common/img_clinic_default.png'"]).'</span>',
            ],
            'spot_name',
            [
                'attribute' => 'status',
                'value' => Spot::$getStatus[$model->status]
            ],
            'telephone',
            'contact_iphone',
            'contact_name',
            [
                'attribute' => 'address',
                'value' => $model->province.$model->city.$model->area
            ],
            'detail_address',
            'fax_number',
        ],
    ]) ?>
        </div>
    </div>
</div>
<?php  $this->endBlock()?>
<?php  $this->beginBlock('renderJs')?>

<?php  $this->endBlock()?>
<?php  AutoLayout::end()?>
