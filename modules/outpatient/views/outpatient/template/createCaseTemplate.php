<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\CaseTemplate */

$this->title = '新增非专科病历模板';
$this->params['breadcrumbs'][] = ['label' => '医生门诊', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => '非专科病历模板', 'url' => ['case-template']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php  AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php  $this->beginBlock('renderCss')?>

<?php  $this->endBlock();?>
<?php  $this->beginBlock('content')?>
<?php echo $this->render(Yii::getAlias('@templateSidebar'),['type'=>2]) ?>
<?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>
    <div class="case-template-create col-xs-10">
        <div class = "box">
            <div class="box-header with-border">
                <span class = 'left-title'><?= Html::encode($this->title) ?></span>
                <?=  Html::a(Html::img($baseUrl.'/public/img/common/icon_back.png').'返回',  Url::to(['case-template']),['class' => 'right-cancel','data-pjax' => 0]) ?>
            </div>
            <div class = "box-body">

                <?= $this->render('@app/modules/spot/views/case-template/_form', [
                    'model' => $model,
                    'hidden' =>  $hidden
                ]) ?>
            </div>
        </div>
    </div>
  <?php  Pjax::end()?>
<?php  $this->endBlock()?>
<?php  $this->beginBlock('renderJs')?>

<?php  $this->endBlock()?>
<?php  AutoLayout::end()?>