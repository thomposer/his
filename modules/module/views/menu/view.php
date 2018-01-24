<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\common\AutoLayout;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model app\modules\module\models\Menu */

$this->title ='菜单信息详情';
$this->params['breadcrumbs'][] = ['label' => '模块菜单', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
<?php $this->endBlock();?>
<?php $this->beginBlock('content')?>
<div class="main_bd col-xs-12">
    <div class = "box">
      <div class="box-header with-border">
      <span class = 'left-title'><?= Html::encode($this->title) ?></span>
      <?= Html::a(Html::img($baseUrl.'/public/img/common/icon_back.png').'返回',Yii::$app->request->referrer,['class' => 'right-cancel']) ?>
     </div>
       <div class = "box-body">
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