<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\common\AutoLayout;
use app\modules\spot\models\ChildCareTemplate;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\CaseTemplate */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '医生门诊', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => '儿保模板', 'url' => ['child-index']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>

<?php $this->endBlock(); ?>
<?php $this->beginBlock('content') ?>
<?php echo $this->render(Yii::getAlias('@templateSidebar'),['type'=>2]) ?>
<?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>
<div class="case-template-view col-xs-10">
    <div class = "box">
        <div class="box-header with-border">
            <span class = 'left-title'> <?= Html::encode($this->title) ?></span>
            <?= Html::a(Html::img($baseUrl . '/public/img/common/icon_back.png') . '返回',['child-index'], ['class' => 'right-cancel','data-pjax' => 0]) ?>
        </div>
        <div class = "box-body">  
            <?=
            DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'name',
                    [
                        'attribute' => 'type',
                        'value' => ChildCareTemplate::$getType[$model->type],
                    ],
                    'content'
                ],
            ])
            ?>
        </div>
    </div>
</div>
  <?php  Pjax::end()?>
<?php $this->endBlock() ?>
<?php $this->beginBlock('renderJs') ?>

<?php $this->endBlock() ?>
<?php AutoLayout::end() ?>
