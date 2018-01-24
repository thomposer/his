<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\helpers\Url;
use app\modules\spot\models\RecipeList;
/* @var $this yii\web\View */
/* @var $model app\modules\pharmacy\models\Stock */

$this->title = '编辑出库';
$this->params['breadcrumbs'][] = ['label' => '其他管理', 'url' => Url::to(['@materialIndexOutboundIndex'])];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$versionNumber = Yii::getAlias("@versionNumber");
?>
<?php  AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php  $this->beginBlock('renderCss')?>
<?php AppAsset::addCss($this, '@web/public/plugins/select2/select2.min.css')?>
<?php  $this->endBlock();?>
<?php  $this->beginBlock('content')?>
<?php echo $this->render(Yii::getAlias('@templateSidebar'),['type'=>3]) ?>
    <div class="stock-create col-xs-10">
        <div class = "box">
            <div class="box-header with-border">
                <span class = 'left-title'><?= Html::encode($this->title) ?></span>
                <?=  Html::a(Html::img($baseUrl.'/public/img/common/icon_back.png').'返回',['@materialIndexOutboundIndex'],['class' => 'right-cancel second-cancel']) ?>
            </div>
            <div class = "box-body">

                <?= $this->render('_outboundForm', [
                    'model' => $model,
                    'departmentList' => $departmentList,
                    'userList' => $userList,
                    'dataProvider' => $dataProvider,
                    'materialList' => $materialList,

                ]) ?>
            </div>
        </div>
    </div>
<?php  $this->endBlock()?>
<?php  $this->beginBlock('renderJs')?>
    <script type="text/javascript">
        var baseUrl = '<?= $baseUrl ?>';
        var inboundIndexUrl = '<?= Url::to(['@materialIndexOutboundIndex']); ?>';
        var materialList = <?= json_encode($materialList,true); ?>;
        require(["<?= $baseUrl ?>"+"/public/js/material/outbound.js"],function(main){
            main.init();
        });
    </script>

<?php  $this->endBlock()?>
<?php  AutoLayout::end()?>