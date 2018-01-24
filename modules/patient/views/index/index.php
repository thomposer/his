<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\grid\GridView;
use app\modules\patient\models\Patient;
use yii\helpers\Url;
use yii\widgets\Pjax;
use johnitvn\ajaxcrud\CrudAsset;
use app\modules\patient\models\PatientRecord;
use yii\grid\GridViewAsset;
use kartik\file\FileInputAsset;

FileInputAsset::register($this);
GridViewAsset::register($this);

CrudAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\modules\patient\models\search\PatientSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '病历库';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$params = Yii::$app->request->queryParams;
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>
<?php AppAsset::addCss($this, '@web/public/css/lib/tab.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/lib/search.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/patient/form.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/check/print.css') ?>
<?php $this->endBlock() ?>
<?php $this->beginBlock('content'); ?>
<?php
Pjax::begin([
    'id' => 'crud-datatable-pjax',
    'timeout' => 3000
])
?>
<div class="patient-index col-xs-12">

    <div class="box">
        <div class='row search-margin'>
            <div class='col-sm-12 col-md-12'>
                <?php if (isset($this->params['permList']['role']) || in_array($this->params['requestModuleController'] . '/create', $this->params['permList'])): ?>
                    <?= Html::a("<i class='fa fa-plus'></i>新增", ['create'], ['class' => 'btn btn-default font-body2 patient-history-btn btn-add', 'data-pjax' => 0]) ?>
                <?php endif ?>
                <?php if (isset($this->params['permList']['role']) || in_array($this->params['requestModuleController'] . '/makeup', $this->params['permList'])): ?>
                    <?= Html::a("补录", ['makeup'], ['class' => 'btn btn-default font-body2 makeupBtn patient-history-btn btn-makeup', 'data-pjax' => 0]) ?>
                <?php endif ?>
                <button type="button" class="btn btn-default patient-history-btn senior-search" data-toggle="collapse" data-target="#patient-history-search-from" aria-expanded="<?= $params['status'] == 0 ? 'false' : 'true'; ?>"><span>高级搜索</span><?= $params['status'] == 0 ? '<i class="fa fa-angle-down"></i>' : '<i class="fa fa-angle-up"></i>' ?></button>  
            </div>
        </div>
        <div id="patient-history-search-from" class="row patient-history-search-from <?= $params['status'] == 0 ? ' collapse' : ' collapse in' ?>">
            <?php echo $this->render('_search', ['model' => $searchModel]); ?>
        </div>
        <?php
        echo '<div class="patient-radio">';
        echo '<div class="patientlist">';
        echo Html::input('radio', 'userList', '1') . '用户列表（'.$listCount['userListCount'].'）';
        echo '</div>';
        echo '<div class="patientlist">';
        echo Html::input('radio', 'patientList', '2') . '病历列表（'.$listCount['recordListCount'].'）';
        echo '</div>';
        echo '</div>';
        if ($params['type'] == 2) {
            echo $this->render('_patientList', [
                'patientRecord' => $patientRecord,
                'pagination' => $pagination,
                'nurseRecordData' => $nurseRecordData,
                'healthEducationData' => $healthEducationData,
                'inspectData' => $inspectData,
                'checkData' => $checkData,
                'cureData' => $cureData,
                'recipeData' => $recipeData,
                'inspectReportData' => $inspectReportData,
                'checkReportData' => $checkReportData,
                'dentalHistoryData' => $dentalHistoryData,
                'firstCheckData' => $firstCheckData,
                'allergyOutpatient' => $allergyOutpatient,
                'assessment' => $assessment
            ]);
        } else {
            echo $this->render('_indexGridView', [
                'dataProvider' => $dataProvider,
                'recordNum' => $recordNum,
                'makeUpData' => $makeUpData,
            ]);
        }
        ?>
    </div>
</div>

<div id='print-show-none'>
    <div id='print-view'>

    </div>
</div>
<?php $this->beginBlock('renderJs'); ?>
<?php AppAsset::addScript($this, '@web/public/js/lib/common.js') ?>
<?php $this->endBlock(); ?>
<?php $this->registerJs("
            var baseUrl = '$baseUrl';
            var versionNumber = '$versionNumber';
            require([baseUrl+'/public/js/report/record/index.js?v='+versionNumber],function(main){
		        main.init();
	        })
    if(getUrlParam('type') == 1 || !getUrlParam('type')){
        $(\"input[name='userList']\").attr('checked',true);
    }else {
        $(\"input[name='patientList']\").attr('checked',true);
    }
    $('.patient-radio').find('.patientlist').unbind('click').click(function () {
        var radioValue = $(this).find('input').val(), localUrl = location.href;
        localUrl = localUrl.replace(/type=1&/, '');
        localUrl = localUrl.replace(/type=2&/, '');
        localUrl = localUrl.replace(/status=0&/, '');
        localUrl = localUrl.replace(/status=1&/, '');
        localUrl = localUrl.replace(/status=0/, '');
        localUrl = localUrl.replace(/status=1/, '');
        var status = $('.senior-search').attr('aria-expanded') == 'true' ? 1 : 0;
        if (radioValue == 1) {
            if(localUrl.indexOf('?') > 0){
                localUrl = localUrl.replace(/\?/, '?type=1&status=' + status + '&');
                window.location.href = localUrl;
            }else{
                window.location.href = localUrl + '?type=1&status=' + status;
            }
        }else if(radioValue == 2){
            if(localUrl.indexOf('?') > 0){
                localUrl = localUrl.replace(/\?/, '?type=2&status=' + status + '&');
                window.location.href = localUrl;
            }else{
                window.location.href = localUrl + '?type=2&status=' + status;
            }
        }
    });
        ") ?>
<?php Pjax::end(); ?>
<?php $this->endBlock(); ?>

<?php AutoLayout::end(); ?>
