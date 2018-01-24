<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\widgets\Pjax;
use yii\grid\GridView;
use app\modules\spot\models\CaseTemplate;
use johnitvn\ajaxcrud\CrudAsset;

CrudAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\modules\spot\models\search\CaseTemplateSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '非专科病历模板';
$this->params['breadcrumbs'][] = ['label' => '医生门诊', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>
<?php AppAsset::addCss($this, '@web/public/css/lib/search.css') ?>
<?php $this->endBlock() ?>
<?php $this->beginBlock('content'); ?>
<?php echo $this->render(Yii::getAlias('@templateSidebar'), ['type' => 2]) ?>
<?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>
<div class="case-template-index col-xs-10">
    <div class = "box">

        <div class = 'row search-margin'>
            <div class = 'col-sm-2 col-md-2  case-top '>
                <?php if (isset($this->params['permList']['role']) || in_array($this->params['requestModuleController'] . '/case-create-template', $this->params['permList'])): ?>
                    <?= Html::a("<i class='fa fa-plus'></i>新增", ['@outpatientOutpatientCreateCaseTemplate'], ['class' => 'btn btn-default font-body2', 'data-pjax' => 0]) ?>
<?php endif ?>
            </div>
        </div>

        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'options' => ['class' => 'grid-view table-responsive add-table-padding'],
            'tableOptions' => ['class' => 'table table-hover table-border header'],
            'layout' => '{items}<div class="text-right">{pager}</div>',
            'pager' => [
                //'options'=>['class'=>'hidden']//关闭自带分页

                'firstPageLabel' => Yii::getAlias('@firstPageLabel'),
                'prevPageLabel' => Yii::getAlias('@prevPageLabel'),
                'nextPageLabel' => Yii::getAlias('@nextPageLabel'),
                'lastPageLabel' => Yii::getAlias('@lastPageLabel'),
            ],
            /* 'filterModel' => $searchModel, */
            'columns' => [
                [
                    'attribute' => 'name',
                    'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2']
                ],
                [
                    'attribute' => 'type',
                    'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2'],
                    'value' => function ($data) {
                return CaseTemplate::$getType[$data->type];
            }
                ],
                'create_time:datetime',
                'user_name',
                [
                    'class' => 'app\common\component\ActionColumn',
                    'template' => '{case-view-template}{case-update-template}{case-delete-template}',
                    'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2'],
                    'buttons' => [
                        'case-view-template' => function($url, $model, $key) {
                            if ((!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/case-view-template', $this->params['permList']))) {
                                return false;
                            }
                            $options = array_merge([
                                'title' => Yii::t('yii', 'View'),
                                'aria-label' => Yii::t('yii', 'View'),
                                'data-pjax' => '0',
                            ]);
                            /* fa-eye是查看 */
                            return Html::a('<span class="icon_button_view fa fa-eye" title="查看", data-toggle="tooltip"></span>', $url, $options);
                        },
                                'case-update-template' => function($url, $model, $key) {
                            if ((!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/case-update-template', $this->params['permList'])) || $model->type == 1) {
                                return false;
                            }
                            $options = array_merge([
                                'title' => Yii::t('yii', 'Update'),
                                'aria-label' => Yii::t('yii', 'Update'),
                                'data-pjax' => '0',
                            ]);
                            return Html::a('<span class="icon_button_view fa fa-pencil-square-o" title="修改", data-toggle="tooltip"></span>', $url, $options);
                        },
                                'case-delete-template' => function($url, $model, $key) {
                            if ((!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/case-delete-template', $this->params['permList'])) || $model->type == 1) {
                                return false;
                            }
                            $options = [
                                'data-confirm' => false,
                                'data-method' => false,
                                'data-request-method' => 'post',
                                'role' => 'modal-remote',
                                'data-toggle' => 'tooltip',
                                'data-confirm-title' => '系统提示',
                                'data-delete' => false,
                                'data-confirm-message' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                            ];
                            return Html::a('<span class="icon_button_view fa fa-trash-o" title="删除", data-toggle="tooltip"></span>', ['@outpatientOutpatientDeleteCaseTemplate', 'id' => $model->id, 'type' => $model->type], $options);
                        },
                            ]
                        ],
                    ],
                ]);
                ?>
            </div>
        </div>
        <?php Pjax::end() ?>
        <?php $this->endBlock(); ?>
        <?php $this->beginBlock('renderJs'); ?>

        <?php $this->endBlock(); ?>
        <?php AutoLayout::end(); ?>
