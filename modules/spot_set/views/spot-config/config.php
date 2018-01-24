<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\helpers\Url;
use johnitvn\ajaxcrud\CrudAsset;

CrudAsset::register($this);
/* @var $this yii\web\View */
/* @var $model app\modules\spot_set\models\SpotConfig */
/* @var $form yii\widgets\ActiveForm */
$this->title = '打印参数配置';
$this->params['breadcrumbs'][] = ['label' => '打印设置'];

$baseUrl = Yii::$app->request->baseUrl;
$attributeLabels=$model->attributeLabels();
$versionNumber = Yii::getAlias("@versionNumber");
?>
<?php  AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>

<?php  $this->beginBlock('renderCss')?>

<?php AppAsset::addCss($this, '@web/public/css/spot_set/spotConfig.css')?>

<?php AppAsset::addCss($this, '@web/public/dist/css/cropper.min.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/lib/upload.css')?>

<?php  $this->endBlock();?>
<?php  $this->beginBlock('content')?>
<div class="spot-config-update col-xs-12">
    <div class = "box">
        <div class="box-header with-border">
            <span class = 'left-title'><?= Html::encode($this->title) ?></span>

        </div>
        <div class = "box-body">
                <?=$this->render('_form',[
                    'model'=>$model,
                    'baseUrl'=>$baseUrl,
                ]);
                ?>

        </div>
    </div>
</div>
<?php  $this->endBlock()?>

<?php  $this->beginBlock('renderJs')?>

<script type="text/javascript">
    var baseUrl='<?= $baseUrl ?>';
    var uploadUrl = '<?= Url::to(['@manageSitesUpload']); ?>';
    require(["<?= $baseUrl ?>"+"/public/js/spot_set/spotConfig.js?v="+'<?= $versionNumber ?>'],function(main){
        main.init();
    })

</script>
<?php  $this->endBlock()?>
<?php  AutoLayout::end()?>
