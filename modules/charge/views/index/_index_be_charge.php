<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use app\modules\patient\models\Patient;
use yii\widgets\Pjax;
use app\common\Common;
use app\modules\patient\models\PatientRecord;
?> 
    <?=  GridView::widget([
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
                    'headerOptions' => ['class' => 'table-time-width'],
                    'value' => function ($model) {
                        return Patient::dateDiffage($model->birthday, time());
                    },
                ],
                [
                    'attribute' => 'diagnosis_time',
                    'value' => function ($model) {
                        return $model->diagnosis_time?date('Y-m-d H:i', $model->diagnosis_time) : '--';
                    },
                ],
                [
                    'attribute' => 'diagnosis_doctor',
                    'value' => function ($model) {
                        return $model->diagnosis_doctor?$model->diagnosis_doctor : '--';
                    }
                ],
                [
                    'attribute' => 'type_description',
                    'value' => function ($model) {
                        return $model->type_description?$model->type_description : '--';
                    }
                ],
                [
                    'attribute' => 'price',
                    'header'=>'待收费金额（元）',
                    'value' => function ($model) {
                        $price = 0;
                        $priceList = explode(',',$model->unit_price);
                        $numList = explode(',',$model->num);
                        foreach ($priceList as $key => $value) {
                            $price += $priceList[$key] * $numList[$key];
                        }
                        return Common::num($price);
                    },
                ],
                [
                   'class' => 'app\common\component\ActionColumn',
                    'headerOptions' => ['class' => 'col-sm-1 col-md-1'],
                   'template' => '{create}',
                   'buttons'=>[
                       'create' => function($url,$model,$key){
                           if(!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'].'/create', $this->params['permList'])){
                               return false;
                            }
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
