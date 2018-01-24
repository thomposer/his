<?php

use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\helpers\Url;
use yii\widgets\Pjax;
use johnitvn\ajaxcrud\CrudAsset;

CrudAsset::register($this);

/* @var $this yii\web\View */
/* @var $searchModel app\modules\triage\models\search\TriageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '分诊';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$versionNumber = Yii::getAlias("@versionNumber");
$public_img_path = $baseUrl . '/public/img/';
$tabData = [
    'titleData' => [
        ['title' => '待分诊', 'url' => Url::to(['@triageTriageIndex', 'type' => 3]), 'type' => 3, 'icon_img' => $public_img_path . '/triage/tab_triage.png', 'param' => [['key' => 'type', 'val' => 3]]],
        ['title' => '已分诊', 'url' => Url::to(['@triageTriageIndex', 'type' => 4]), 'type' => 4, 'icon_img' => $public_img_path . '/triage/tab_history.png', 'param' => [['key' => 'type', 'val' => 4]]]
    ],
    'activeData' => ['type' => 3]
];
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>
<?php AppAsset::addCss($this, '@web/public/css/triage/triage.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/lib/search.css') ?>
<?php AppAsset::addCss($this, '@web/public/plugins/select2/select2.min.css') ?>
<?php $this->endBlock() ?>
<?php $this->beginBlock('content'); ?>

<div class="triage-index col-xs-12">
    <?php  Pjax::begin(['id' => 'crud-datatable-pjax'])  ?>

<?php echo $this->render(Yii::getAlias('@contentTopTab'), ['data' => $tabData]); ?>
    <div class = "box delete_gap">
        <div class = 'row no-gap'>
            <div class = 'col-sm-12 col-md-12'>
				<?php echo $this->render('_search', ['model' => $searchModel,'secondDepartmentInfo' => $secondDepartmentInfo, 'doctorInfo' => $doctorInfo]); ?>
            </div>
        </div>
        <?php
        $params = Yii::$app->request->queryParams;
        $type = isset($params['type']) ? $params['type'] : 3;
        if ($type == 3) { //待分诊字段
            echo $this->render('_index_to_triage', ['dataProvider' => $dataProvider, 'baseUrl' => $baseUrl, 'searchModel' => $searchModel]);
        } else {//
            echo $this->render('_index_end_triage', ['dataProvider' => $dataProvider, 'baseUrl' => $baseUrl, 'searchModel' => $searchModel]);
        }
        ?>
    </div>
<?php  Pjax::end()  ?>
</div>
<?php $this->endBlock(); ?>

<?php $this->beginBlock('renderJs'); ?>
<script type = "text/javascript">
    var baseUrl = '<?= $baseUrl ?>';
    var modalUrl = '<?= Url::to(['@triageTriageModal']) ?>';
    var infoUrl = '<?= Url::to(['@triageTriageInfo']) ?>';
    var doctorModalUrl = '<?= Url::to(['@triageTriageDoctor']) ?>';
    var roomModalUrl = '<?= Url::to(['@triageTriageRoom']) ?>';
    var choseDoctUrl = '<?= Url::to(['@triageTriageChosedoctor']) ?>';
    var choseRoomUrl = '<?= Url::to(['@triageTriageChoseroom']) ?>';
    require(['<?= $baseUrl ?>' + '/public/js/triage/triage.js?v='+ '<?= $versionNumber ?>'], function (main) {
        main.init();
    })
</script>
<?php $this->endBlock(); ?>
<?php AutoLayout::end(); ?>
