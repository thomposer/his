<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\common\AutoLayout;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\apply\models\search\ApplyPermissionListSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '权限查询';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php AutoLayout::begin(['viewFile' => '@app/modules/apply/views/layouts/layout.php'])?>

<?php $this->beginBlock('renderCss')?>
    <?php $this->registerCssFile('@web/public/css/bootstrap/bootstrap.css')?>
    <?php $this->registerCssFile('@web/public/css/apply/search.css')?>
<?php $this->endBlock()?>
<?php $this->beginBlock('content')?>
<div class="apply-permission-list-index main_bd main_bootstrap">

    <?php echo $this->render('_search', ['model' => $searchModel,'roleList'=>$roleList,'spotList'=>$spotList,'dataProvider' => $dataProvider]); ?>
</div>
<?php $this->endBlock()?>
<?php $this->beginBlock('artemplate')?>
<?php $this->endBlock()?>
<?php AutoLayout::end()?>