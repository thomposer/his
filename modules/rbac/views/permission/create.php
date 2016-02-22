<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
/* @var $this yii\web\View */
/* @var $model app\modules\rbac\models\Permission */

$this->title = '添加权限';
$this->params['breadcrumbs'][] = ['label' => 'Permissions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
    <?php AppAsset::addCss($this,'@web/public/css/rbac/rbac.css')?>
<?php $this->endBlock();?>
<?php $this->beginBlock('content')?>
<div class="permission-create col-xs-12">
    <div class = 'box'>
        <div class = 'box-body'>
            <?= $this->render('_form', [
                'model' => $model,
               'categories' =>$categories
            ]) ?>
        </div>
    </div>
</div>
<?php $this->endBlock()?>
<?php $this->beginBlock('renderJs')?>

<?php $this->endBlock()?>
<?php AutoLayout::end();?>