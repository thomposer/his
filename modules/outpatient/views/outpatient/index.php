<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\grid\GridView;
use yii\helpers\Url;
use app\modules\patient\models\Patient;
use app\modules\outpatient\models\Outpatient;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\outpatient\models\search\OutpatientSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '医生门诊';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$public_img_path = $baseUrl . '/public/img/';
$tabData = [
    'titleData' => [
        ['title' => '今日病人', 'url' => Url::to(['@outpatientOutpatientIndex', 'type' => 3]), 'type' => 3, 'icon_img' => $public_img_path . '/triage/tab_outpatient.png', 'param' => [['key' => 'type', 'val' => 3]], 'type' => 3],
        ['title' => '历史记录', 'url' => Url::to(['@outpatientOutpatientIndex', 'type' => 4]), 'type' => 4, 'icon_img' => $public_img_path . '/triage/tab_history.png', 'param' => [['key' => 'type', 'val' => 4]], 'type' => 4]
    ],
    'activeData' => [
        'type' => 3
    ]
];
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>
<?php AppAsset::addCss($this, '@web/public/css/lib/search.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/outpatient/index.css') ?>
<?php $this->endBlock() ?>
<?php $this->beginBlock('content'); ?>

<div class="outpatient-index col-xs-12">
    <?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>

    <?php echo $this->render(Yii::getAlias('@contentTopTab'), ['data' => $tabData]); ?>
    <div class="box delete_gap">
        <div class='row no-gap'>
            <div class='col-sm-7 col-md-7'>
                <?php if (isset($this->params['permList']['role']) || in_array($this->params['requestModuleController'] . '/case-template', $this->params['permList'])): ?>
                    <?= Html::a("医生模板管理", ['@outpatientOutpatientCaseTemplate'], ['class' => 'btn btn-default font-body2', 'data-pjax' => 0]) ?>
                <?php endif ?>
                <div class="outpatient-index-notice">
                    <span class="bolder">温馨提示：</span>
                    <span>请在今日内完成接诊结束的病人的病历填写。</span>
                </div>
            </div>

            <div class = 'col-sm-5 col-md-5'>
                <?php echo $this->render('_search', ['model' => $searchModel]); ?>
            </div>
        </div>
        <?php
        $params = Yii::$app->request->queryParams;
        $type = isset($params['type']) ? $params['type'] : 3;
        if ($type == 3) { //今天
            echo $this->render('_today', ['dataProvider' => $dataProvider, 'baseUrl' => $baseUrl, 'searchModel' => $searchModel, 'followData' => $followData]);
        } else {//
            echo $this->render('_history', ['dataProvider' => $dataProvider, 'baseUrl' => $baseUrl, 'searchModel' => $searchModel, 'followData' => $followData]);
        }
        ?>

    </div>
    <?php Pjax::end(); ?>
</div>
<?php $this->endBlock(); ?>
<?php $this->beginBlock('renderJs'); ?>

<?php $this->endBlock(); ?>
<?php AutoLayout::end(); ?>
