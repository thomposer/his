<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\common\AutoLayout;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model app\modules\module\models\Menu */

$this->title ='菜单信息详情';
$this->params['breadcrumbs'][] = ['label' => 'Menus', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
<?php $this->endBlock();?>
<?php $this->beginBlock('content')?>
<div class="main_bd col-xs-12">
    <div class = "box">
       <div class = "box-body">
       <p class="button-group">
        <?php  if(in_array($this->params['requestModuleController'].'/update', $this->params['permList'])):?>
            <?= Html::a('修改', ['update', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
        <?php endif;?>
        <?php  if(in_array($this->params['requestModuleController'].'/delete', $this->params['permList'])):?>
            <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => '你确定要删除此项吗?',
                        'method' => 'post',
                    ],
                ]) ?>
        <?php endif;?>
        <?= Html::a('返回列表', ['index'], ['class' => 'btn btn-primary']) ?>
       </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'menu_url:text',
            [
                'label' => '左侧菜单',
                'value' => $model->type == 1 ? '渲染' : '不渲染' 
            ],
            'description',
            [
                'label' => '所属模块',
                'value' => $parent_description
            ],
            [
                'label' => '状态',
                'value' => $model->status == 1 ? '启用' : '禁用',
            ],
            [
                'label' => '所属类型',
                'value' => $model->role_type == 1 ? '超级管理员' : '通用'
            ]
        ],
    ]) ?>
    </div>
    </div>
</div>
<?php $this->endBlock();?>
<?php $this->beginBlock('renderJs')?>

<?php $this->endBlock();?>
<?php AutoLayout::end()?>