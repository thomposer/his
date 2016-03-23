<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\common\AutoLayout;
/* @var $this yii\web\View */
/* @var $model app\modules\make_appointment\models\Patient */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Patients', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php  AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php  $this->beginBlock('renderCss')?>

<?php  $this->endBlock();?>
<?php  $this->beginBlock('content')?>

<div class="patient-view col-xs-12">
    <div class = "box">
        <div class = "box-body">  
            <h2><?= Html::encode($this->title) ?></h2>
        
            <p>
            <?php if(in_array($this->params['requestModuleController'].'/update', $this->params['permList'])):?>
                <?= Html::a('修改', ['update', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
            <?php endif; ?>
            <?php if(in_array($this->params['requestModuleController'].'/delete', $this->params['permList'])):?>
                <?= Html::a('删除', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => '你确定要删除此项吗?',
                        'method' => 'post',
                    ],
                ]) ?>
            <?php endif; ?>
                <?= Html::a('返回列表',['index'],['class' => 'btn btn-primary'])?>
            </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'user_name',
            'sex',
            'birthday',
            'nation',
            'marriage',
            'occupation',
            'province',
            'city',
            'area',
            'detail_address',
        ],
    ]) ?>
        </div>
    </div>
</div>
<?php  $this->endBlock()?>
<?php  $this->beginBlock('renderJs')?>

<?php  $this->endBlock()?>
<?php  AutoLayout::end()?>
