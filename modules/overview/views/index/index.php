<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\widgets\Pjax;
use yii\grid\GridView;
use yii\helpers\Url;
use app\modules\overview\models\Overview;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\overview\models\Search\OverviewSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '系统概况';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>
<?php AppAsset::addCss($this, '@web/public/css/lib/search.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/overview/index.css') ?>
<?php $this->endBlock() ?>
<?php $this->beginBlock('content'); ?>

<div class="overview-index col-xs-12">
        <?php // Pjax::begin(['id' => 'crud-datatable-pjax'])  ?>
    <div class = 'row'>
<?= $this->render('_top_bar', ['model' => $searchModel, 'type' => 2, 'overviewNum' => $overviewNum]); ?>
    </div>
    <div class = "box">
        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'options' => ['class' => 'grid-view table-responsive'],
            'tableOptions' => ['class' => 'table table-hover header'],
            'layout' => '{items}<div class="text-right">{pager}</div>',
            'headerRowOptions' => ['class' => 'header'],
            'pager' => [
                //'options'=>['class'=>'hidden']//关闭自带分页
                
                'firstPageLabel' => Yii::getAlias('@firstPageLabel'),
                'prevPageLabel' => Yii::getAlias('@prevPageLabel'),
                'nextPageLabel' => Yii::getAlias('@nextPageLabel'),
                'lastPageLabel' => Yii::getAlias('@lastPageLabel'),
            ],
            /* 'filterModel' => $searchModel, */
            'columns' => [
//                'spot_name',
                [
                    'attribute' => 'spot_name',
//                    'rowOptions' => ['title' => '测试机构','data-toggle'=>'tooltip'],
                    'format' => 'raw',
                    'value' => function ($model) {
                        $agency = Overview::getParentSpotName($model->id);
                        return '<span title=所属机构：' . Html::encode($agency['spot_name']) . ' data-toggle="tooltip">' . Html::encode($model->spot_name) . '</span>';
                    }
                ],
                'contact_name',
                'contact_iphone',
                'create_time:datetime',
                [

                    'class' => 'app\common\component\ActionColumn',
                    'contentOptions' => ['class' => 'op-group', 'style' => 'padding-left:38px'],
                    'template' => '{spot-view}{spot-go}',
                    'buttons' => [
                        'spot-view' => function($url, $model, $key) {
                            if (!isset($this->params['permList']['role']) && !in_array(Yii::getAlias('@overviewIndexSpotView'), $this->params['permList'])) {
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
                                'spot-go' => function($url, $model, $key) {
                            if (!isset($this->params['permList']['role'])) {
                                return false;
                            }
                            $options = array_merge([
                                'title' => Yii::t('yii', '进入诊所'),
                                'aria-label' => Yii::t('yii', '进入诊所'),
                                'data-pjax' => '0',
                                'id' => $key,
                                'class' => 'spot-go',
                            ]);
                            /* fa-eye是查看 */
                            return Html::a('<span class="icon_button_view fa fa-arrow-right" title="进入诊所", data-toggle="tooltip"></span>', 'javascript:void(0);', $options);
                        }
                            ]
                        ],
                    ],
                ]);
                ?>
            </div>
        <?php // Pjax::end()  ?>
        </div>
        <?php $this->endBlock(); ?>
        <?php $this->beginBlock('renderJs'); ?>
        <script type = "text/javascript">
            var baseUrl = '<?= $baseUrl ?>';
            var selectSpotUrl = '<?= Url::to(['@manageSites']); ?>';
            require(['<?= $baseUrl ?>' + '/public/js/overview/detail.js?v=11'], function (main) {
                main.init();
            })
        </script>
        <?php $this->endBlock(); ?>
        <?php AutoLayout::end(); ?>
