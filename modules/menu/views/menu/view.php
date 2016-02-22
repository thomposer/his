<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\common\AutoLayout;
/* @var $this yii\web\View */
/* @var $model app\modules\menu\models\Menu */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Menus', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php  AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php  $this->beginBlock('renderCss')?>

<?php  $this->endBlock();?>
<?php  $this->beginBlock('content')?>

<div class="menu-view col-xs-12">
    <div class = "box">
        <div class = "box-body">  
            <h2><?= Html::encode($this->title) ?></h2>
        
            <p>
                <?= Html::a('修改', ['update', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
                <?= Html::a('删除', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => '你确定要删除此项吗?',
                        'method' => 'post',
                    ],
                ]) ?>
                <?= Html::a('返回列表',['index'],['class' => 'btn btn-primary'])?>
            </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'menu_url:url',
            'type',
            'description',
            'parent_id',
            'status',
            'role_type',
        ],
    ]) ?>
        </div>
    </div>
</div>
<?php  $this->endBlock()?>
<?php  $this->beginBlock('renderJs')?>

<?php  $this->endBlock()?>
<?php  AutoLayout::end()?>
