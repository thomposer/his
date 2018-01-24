<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\widgets\Pjax;
use yii\grid\GridView;
use app\modules\spot\models\CaseTemplate;
use johnitvn\ajaxcrud\CrudAsset;
use yii\helpers\Url;

CrudAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\modules\spot\models\search\CaseTemplateSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = '儿保模板';
$this->params['breadcrumbs'][] = ['label' => '医生门诊', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>

<?php $this->beginBlock('renderCss') ?>

<?php $this->endBlock() ?>

<?php $this->beginBlock('content'); ?>
<?php echo $this->render(Yii::getAlias('@templateSidebar'), ['type' => 2]) ?>
<?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>
<div class="case-template-index col-xs-10">
    <div class = "box">

        <div class = 'row search-margin'>
            <div class='col-sm-2 col-md-2'>
                <?php if (isset($this->params['permList']['role']) || in_array($this->params['requestModuleController'] . '/child-create', $this->params['permList'])): ?>
                    <?= Html::a("<i class='fa fa-plus'></i>新增", ['child-create'], ['class' => 'btn btn-default font-body2', 'data-pjax' => 0]) ?>
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
                    'value' => function ($searchModel) {
                return CaseTemplate::$getType[$searchModel->type];
            }
                ],
                'create_time:datetime',
                [
                    'attribute' => 'user_name',
                    'value' => function($model) {
                        return $model->user_name;
                    },
                ],
                [
                    'class' => 'app\common\component\ActionColumn',
                    'template' => '{child-view}{child-update}{child-delete}',
                    'buttons' => [
                        'child-view' => function ($url, $model, $key) {
                            if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/child-view', $this->params['permList'])) {
                                return false;
                            }
                            return Html::a('', Url::to(['child-view', 'id' => $model->id]), [ 'class' => 'icon_button_view fa fa-eye', 'title' => '查看', 'data-toggle' => 'tooltip','data-pjax' => 0]);
                        },
                                'child-update' => function ($url, $model, $key) {
                            if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/child-update', $this->params['permList'])|| $model->type == 1) {
                                return false;
                            }
                            return Html::a('', Url::to(['child-update', 'id' => $model->id]), [ 'class' => 'icon_button_view fa fa-pencil-square-o', 'title' => '修改', 'data-toggle' => 'tooltip','data-pjax' => 0]);
                        },
                                'child-delete' => function ($url, $model, $key) {
                            if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/child-delete', $this->params['permList'])|| $model->type == 1) {
                                return false;
                            }
                            $options = array_merge([
                                'data-confirm' => false,
                                'data-method' => false,
                                'data-request-method' => 'post',
                                'role' => 'modal-remote',
                                'data-toggle' => 'tooltip',
                                'data-confirm-title' => '系统提示',
                                'data-delete' => false,
                                'data-confirm-message' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                'data-pjax' => '1',
                                'class' => 'icon_button_view fa fa-trash-o',
                                'title' => '删除',
                                'data-toggle' => 'tooltip'
                            ]);
                            return Html::a('', Url::to(['child-delete', 'id' => $model->id]), $options);
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
