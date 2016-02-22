<?php

use yii\helpers\Html;

use app\common\AutoLayout;
use yii\helpers\Url;
use app\assets\AppAsset;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\apply\models\search\ApplyPermissionListSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '权限申请列表';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>

<?php $this->beginBlock('renderCss')?>
    <?php AppAsset::addCss($this,'@web/public/css/search.css')?>
<?php $this->endBlock()?>
<?php $this->beginBlock('content')?>

<div class="apply-permission-list-index col-xs-12">   
    
    <?php echo $this->render('_search', ['model' => $searchModel,'systemsRole'=>$systemsRole,'dataProvider' => $dataProvider,'spotList' => $spotList,'roleList' =>$roleList,'status'=>$status]); ?>
</div>
<?php $this->endBlock()?>
<?php $this->beginBlock('renderJs')?>

	
<?php $this->endBlock()?>

<?php AutoLayout::end()?>