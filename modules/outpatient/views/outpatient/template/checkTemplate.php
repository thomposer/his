<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\widgets\Pjax;
use yii\grid\GridView;
use app\modules\outpatient\models\CureTemplate;
use yii\bootstrap\Modal;
use johnitvn\ajaxcrud\CrudAsset;

CrudAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\modules\spot\models\search\CaseTemplateSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '影像学检查模板';
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
                <?php if (isset($this->params['permList']['role']) || in_array($this->params['requestModuleController'] . '/check-template-create', $this->params['permList'])): ?>
                    <?= Html::a("<i class='fa fa-plus'></i>新增", ['@outpatientOutpatientCheckTemplateCreate'], ['class' => 'btn btn-default font-body2', 'data-pjax' => 0]) ?>
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
                'name',
                'typeTemplateName',
                [
                    'attribute' => 'type',
                    'value' => function($searchModel) {
                        return CureTemplate::$getType[$searchModel->type];
                    }
                ],
                'create_time:datetime',
                'userName',
                [
                    'class' => 'app\common\component\ActionColumn',
                    'template' => '{check-template-update}{check-template-delete}',
                    'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2'],
                    'buttons' => [
                        'check-template-update' => function($url, $model, $key) {
                            if ((!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/check-template-update', $this->params['permList'])) || $model->type == 1) {
                                return false;
                            }
                            $options = array_merge([
                                'title' => Yii::t('yii', 'Update'),
                                'aria-label' => Yii::t('yii', 'Update'),
                                'data-pjax' => '0',
                            ]);
                            return Html::a('<span class="icon_button_view fa fa-pencil-square-o" title="修改", data-toggle="tooltip"></span>', $url, $options);
                        },
                        'check-template-delete' => function($url, $model, $key) {
                            if ((!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/check-template-delete', $this->params['permList'])) || $model->type == 1) {
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
                            return Html::a('<span class="icon_button_view fa fa-trash-o" title="删除", data-toggle="tooltip"></span>', ['@outpatientOutpatientCheckTemplateDelete', 'id' => $model->id, 'type' => $model->type], $options);
                        },
                    ]
                ],
            ],
        ]);
        ?>
    </div>
</div>
<?php Pjax::end() ?>
<?php
Modal::begin([
    "id" => "ajaxCrudModal",
    "footer" => "", // always need it for jquery plugin
])
?>
<?php Modal::end(); ?>
<?php $this->endBlock(); ?>
<?php $this->beginBlock('renderJs'); ?>
<?php $this->endBlock(); ?>
<?php AutoLayout::end(); ?>
