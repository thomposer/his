<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\common\AutoLayout;
use app\modules\outpatient\models\DentalFirstTemplate;
/* @var $this yii\web\View */
/* @var $model app\modules\outpatient\models\DentalFirstTemplate */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '医生门诊', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => '口腔初诊病历模板', 'url' => ['dentalfirst-index']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php  AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php  $this->beginBlock('renderCss')?>

<?php  $this->endBlock();?>
<?php  $this->beginBlock('content')?>
<?php echo $this->render(Yii::getAlias('@templateSidebar'), ['type' => 2]) ?>
<div class="dental-first-template-view col-xs-10">
    <div class = "box">
      <div class="box-header with-border">
      <span class = 'left-title'> <?=  Html::encode($this->title) ?></span>
       <?= Html::a(Html::img($baseUrl.'/public/img/common/icon_back.png').'返回',['dentalfirst-index'],['class' => 'right-cancel','data-pjax' => 0]) ?>
     </div>
        <div class = "box-body">  
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            [
                'attribute' => 'type',
                'value' => DentalFirstTemplate::$getType[$model->type]
            ],
            [
                'attribute' => 'username',
                
            ],
            'chiefcomplaint:ntext',
            'historypresent:ntext',
            'pasthistory:ntext',
            'oral_check:ntext',
            'auxiliary_check:ntext',
            'diagnosis:ntext',
            'cure_plan:ntext',
            'cure:ntext',
            'advice:ntext',
            'remark:ntext',
            
            'create_time:datetime',
            'update_time:datetime',
        ],
    ]) ?>
        </div>
    </div>
</div>
<?php  $this->endBlock()?>
<?php  $this->beginBlock('renderJs')?>

<?php  $this->endBlock()?>
<?php  AutoLayout::end()?>
