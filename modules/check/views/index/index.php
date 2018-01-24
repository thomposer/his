<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\widgets\Pjax;
use yii\grid\GridView;
use app\modules\patient\models\PatientRecord;
use app\modules\patient\models\Patient;
use app\modules\check\models\Check;
use yii\helpers\Url;
use johnitvn\ajaxcrud\CrudAsset;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\check\models\search\CheckSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
CrudAsset::register($this);
$this->title = '影像学检查';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$public_img_path = $baseUrl . '/public/img/';
$tabData = [
    'titleData' => [
        ['title' => '今日病人', 'url' => Url::to(['@checkIndexIndex', 'type' => 3]), 'type' => 3, 'icon_img' => $public_img_path . '/tab/tab_movie.png'],
        ['title' => '特需病人', 'url' => Url::to(['@checkIndexIndex', 'type' => 4]), 'type' => 4, 'icon_img' => $public_img_path . '/tab/tab_movie.png'],
        ['title' => '检查历史', 'url' => Url::to(['@checkIndexIndex', 'type' => 5]), 'type' => 5, 'icon_img' => $public_img_path . '/tab/tab_movie.png'],
    ],
    'activeData' => [
        'type' => 3
    ]
];
$params = [
    'searchName' => 'CheckSearch',
    'statusName' => 'status',
    'buttons' => [
        [
            'title' => '全部',
            'statusCode' => 0,
        ],
        [
            'title' => '待检查',
            'statusCode' => 3,
        ],
        [
            'title' => '检查中',
            'statusCode' => 2,
        ],
        [
            'title' => '已完成',
            'statusCode' => 1,
        ]
    ]
];
$type = Yii::$app->request->get('type');
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>
<?php AppAsset::addCss($this, '@web/public/css/lib/search.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/check/index.css') ?>
<?php $this->endBlock() ?>
<?php $this->beginBlock('content'); ?>

<div class="check-index col-xs-12">
    <?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>
    <?php echo $this->render(Yii::getAlias('@contentTopTab'), ['data' => $tabData]); ?>
    <div class="box delete_gap">
        <div class='row search-margin'>
            <div class='col-sm-6 col-md-6'>
                <?php if ($type != 5): ?>
                    <?= $this->render(Yii::getAlias('@searchStatus'), $params) ?>
                <?php endif; ?>
            </div>
            <div class='col-sm-6 col-md-6'>
                <?php echo $this->render('_search', ['model' => $searchModel]); ?>
            </div>
        </div>
        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'options' => ['class' => 'grid-view table-responsive add-table-padding'],
            'tableOptions' => ['class' => 'table table-hover table-border'],
            'layout' => '{items}<div class="text-right">{summary}{pager}</div>',
            'summary' =>'<div class="table-summary">( {totalCount} 结果，共 {pageCount} 页 )</div>',
            'pager' => [
                //'options'=>['class'=>'hidden']//关闭自带分页
                'hideOnSinglePage' => false,
                'firstPageLabel' => Yii::getAlias('@firstPageLabel'),
                'prevPageLabel' => Yii::getAlias('@prevPageLabel'),
                'nextPageLabel' => Yii::getAlias('@nextPageLabel'),
                'lastPageLabel' => Yii::getAlias('@lastPageLabel'),
            ],
            'columns' => [
                [
                    'attribute' => 'patientName',
                    'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2'],
                    'format' => 'raw',
                    'contentOptions' => ['class' => 'score-warn'],
                    'value' => function ($model) {
                        $user_sex = Patient::$getSex[$model->sex];
                        $dateDiffage = Patient::dateDiffage($model->birthday, time());
                        return Html::encode($model->patientName) . '(' . $user_sex . ' ' . $dateDiffage . ')' . Patient::getUserScore($model->pain_score, $model->fall_score);
                    },
                ],
                [
                    'attribute' => 'clinic_name',
                ],
                [
                    'attribute' => 'name',
                    'headerOptions' => ['class' => 'col-sm-3 col-md-3']
                ],
                'doctorName',
                [
                    'attribute' => 'type_description',
                ],
                [
                    'class' => 'app\common\component\ActionColumn',
                    'template' => '{check}{under-check}{complete}',
                    'headerOptions' => ['style' => 'width:290px'],
                    'buttons' => [
                        'check' => function ($url, $model, $key) use($checkStatusCount){
                            if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/check', $this->params['permList'])) {
                                return false;
                            }
                            $checkNum = $checkStatusCount[$model->id][3];
                            $btn_title = "待检查" . $checkNum;
                            $options = [
                                'class' => 'btn btn-delete',
                                'role' => 'modal-remote',
                                'data-toggle' => 'tooltip',
                            ];
                            if ($checkNum) {
                                return Html::a($btn_title, $url, $options);
                            } else {
                                return '';
                            }
                        },
                        'under-check' => function ($url, $model, $key) use($checkStatusCount) {
                            if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/under-check', $this->params['permList'])) {
                                return false;
                            }
                            $checkNum = $checkStatusCount[$model->id][2];
                            $btn_title = "检查中" . $checkNum;
                            $options = [
                                'class' => 'btn btn-success',
                                'data-pjax' => 0
                            ];
                            if ($checkNum) {
                                return Html::a($btn_title, $url, $options);
                            } else {
                                return '';
                            }
                        },
                        'complete' => function ($url, $model, $key) use($checkStatusCount) {
                            if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/complete', $this->params['permList'])) {
                                return false;
                            }
                            $checkNum = $checkStatusCount[$model->id][1];
                            $btn_title = "已完成" . $checkNum;
                            $options = [
                                'class' => 'btn btn-default',
                                'data-pjax' => 0
                            ];
                            if ($checkNum) {
                                return Html::a($btn_title, $url, $options);
                            } else {
                                return '';
                            }
                        }
                    ],
                ],
            ],
        ]);
        ?>
    </div>
    <?php Pjax::end() ?>
</div>

<?php $this->endBlock(); ?>
<?php $this->beginBlock('renderJs'); ?>

<?php $this->endBlock(); ?>
<?php AutoLayout::end(); ?>
