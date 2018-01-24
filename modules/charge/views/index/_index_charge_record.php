<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use app\modules\patient\models\Patient;
use yii\widgets\Pjax;

$template=$type==4?'update':'refund';
?> 


        <?=GridView::widget([
            'dataProvider' => $dataProvider,
            'options' => ['class' => 'grid-view table-responsive add-table-padding'],
            'tableOptions' => ['class' => 'table table-hover table-border'],
            'layout' => '{items}<div class="text-right">{summary}{pager}</div>',
            'summary' =>'<div class="table-summary">( {totalCount} 结果，共 {pageCount} 页 )</div>',
            'pager' => [
                //'options'=>['class'=>'hidden']//关闭自带分页
                'hideOnSinglePage' => false,//在只有一页时也显示分页
                'firstPageLabel' => Yii::getAlias('@firstPageLabel'),
                'prevPageLabel' => Yii::getAlias('@prevPageLabel'),
                'nextPageLabel' => Yii::getAlias('@nextPageLabel'),
                'lastPageLabel' => Yii::getAlias('@lastPageLabel'),
            ],
            /* 'filterModel' => $searchModel, */
            'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

                [
                    'attribute' => 'username',
                    'value' => function($searchModel)use($cardInfo){
                        $text = Html::encode($searchModel->username).Patient::getFirstRecord($searchModel->firstRecord).Patient::getUserVipInfo($cardInfo[$searchModel->iphone]);
                        return $text;
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'sex',
                    'headerOptions' => ['class' => 'table-sex-width'],
                    'value' => function ($model) {
                        return Patient::$getSex[$model->sex];
                    },
                ],
                [
                    'attribute' => 'birthday',
                    'value' => function ($model) {
                        return Patient::dateDiffage($model->birthday, time());
                    },
                    'headerOptions' => [ 'class' => '']
                ],
                [
                    'attribute' => 'diagnosis_time',
                    'value' => function ($model) {
                        return $model->diagnosis_time?date('Y-m-d H:i', $model->diagnosis_time):'--';
                    },
//                     'headerOptions' => ['class' => 'col-sm-2 col-md-2 table-time-width']
                ],
                [
                    'attribute' => 'diagnosis_doctor',
                    'value' => function ($model) {
                        return $model->diagnosis_doctor?$model->diagnosis_doctor : '--';
                    }
                ],
                [
                    'attribute' =>'charge_time',
                    'value' => function ($model) {
                        return $model->charge_time?date('Y-m-d H:i:s', $model->charge_time):'';
                    },
                    'headerOptions' => ['class' => 'col-sm-2 col-md-2'],
                    'label' => $type==4 ? '收费时间' : "退费时间",
                ],
                [
                    'attribute' =>"price",
                    'headerOptions' => [ 'class' => ''],
                    'header'=> $type==4 ? "收费金额（元）" : "退费金额（元）",
                ],
                [
                    'class' => 'app\common\component\ActionColumn',
                    'template' => "{".$template."}",
                    'headerOptions' => ['class' => 'col-sm-1 col-md-1'],
                    'buttons' => [
                        $template => function($url,$model,$key)use($template){
//                            if(!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'].'/'.$template, $this->params['permList'])){
//                                return false;
//                            }
                            $options = array_merge([
                                'title' => Yii::t('yii', 'View'),
                                'aria-label' => Yii::t('yii', 'View'),
                                'data-pjax' => '0',
                            ]);
                            return Html::a('<span class="icon_button_view fa fa-eye" title="查看"  data-toggle="tooltip"></span>', $url, $options);
                        }
                    ]
                ],
            ],
        ]);
        ?>
