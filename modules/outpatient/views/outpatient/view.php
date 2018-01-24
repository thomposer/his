<?php 
use yii\grid\GridView;
use app\common\Common;
use app\assets\AppAsset;
use app\modules\charge\models\ChargeInfo;
use app\modules\spot\models\RecipeList;
use yii\bootstrap\Html;
$baseUrl = Yii::$app->request->baseUrl;
$action=Yii::$app->controller->action->id;
AppAsset::addCss($this, '@web/public/css/outpatient/view.css');
?>



            <p class = 'title float'>收费项目</p>
            <?= GridView::widget([ 
                'dataProvider' => $dataProvider, 
                'options' => ['class' => 'grid-view table-responsive outpatient-view'], 
                'tableOptions' => ['class' => 'table table-hover table-bordered'], 
                'layout'=> '{items}', 
        
                'columns' => [
                   
                    [
                        'attribute' => 'name',
                        'label' => '名称',
                        'headerOptions' => [ 'class' => 'col-sm-3 col-md-3' ],
                        'format' => 'raw',
                        'value' => function($dataProvider){
                            if($dataProvider['type'] == ChargeInfo::$packgeType){
                                $mouseover = ($dataProvider['detail']['inspectList'] ? ('检验医嘱：<br/><p>' . Html::encode(implode('﹑', $dataProvider['detail']['inspectList'])) . '</p>') : '').
                                            ($dataProvider['detail']['checkList'] ? ('检查医嘱：<br/><p>' . Html::encode(implode('﹑', $dataProvider['detail']['checkList'])) . '</p>') : '').
                                            ($dataProvider['detail']['cureList'] ? ('治疗医嘱：<br/><p>' . Html::encode(implode('﹑', $dataProvider['detail']['cureList'])) . '</p>') : '').
                                            ($dataProvider['detail']['recipeList'] ? ('处方医嘱：<br/><p>' . Html::encode(implode('﹑',$dataProvider['detail']['recipeList'])) . '</p>') : '');
                                return Html::encode($dataProvider['name']) . ' <span class="fa fa-question-circle blue" data-toggle="tooltip" data-html="true" data-placement="right" data-original-title="' . $mouseover . '"></span>';
                            }   
                            return Html::encode($dataProvider['name']);
                        }
                    ],
                    [
                        'attribute' => 'unit',
                        'label' => '单位',
                        'headerOptions' => [ 'class' => 'col-sm-1 col-md-1' ],
                        'value' => function($dataProvider){
                            if($dataProvider['type'] == ChargeInfo::$recipeType){
                                return RecipeList::$getUnit[$dataProvider['unit']];
                            }   
                            return $dataProvider['unit'];
                        }
                    ],
                    [
                        'attribute' => 'price',
                        'label' => '单价',
                        'headerOptions' => [ 'class' => 'col-sm-2 col-md-2' ]
                    ],
                    [   
                        'attribute' => 'num',
                        'label' => '数量',
                        'headerOptions' => [ 'class' => 'col-sm-1 col-md-1'],
                        'value' => function($dataProvider){
                            if(!$dataProvider['num']){
                                return 1;
                            }
                            return $dataProvider['num'];
                        }
                    ],
                    [   
                        'attribute' => 'total_price',
                        'label' => '金额',
                        'contentOptions' => [ 'class' => 'total_price'],
                        'value' => function ($dataProvider){
                            $num = $dataProvider['num']?$dataProvider['num']:1;
                            return Common::num($num * $dataProvider['price']);
                        },
                        'headerOptions' => [ 'class' => 'col-sm-2 col-md-2']
                        
                    ],
                ], 
            ]); ?> 
            <div class = 'row margin'>
                <div class = 'col-xs-6 col-md-6'>
                    <div class = 'padding'>
                        诊金费用：¥<span class = 'left-price five-price'><?= Common::num($chargeTotal['price']); ?></span>
                    </div>
                    <div class = 'padding'>
                        实验室检查总费用：¥<span class = 'left-price inspect-price'><?= Common::num($chargeTotal['inspect_price']); ?></span>
                    </div>
                    <div class = 'padding'>
                        影像学检查总费用：¥<span class = 'left-price check-price'><?= Common::num($chargeTotal['check_price']); ?></span>
                    </div>
                    <div class = 'padding'>
                        治疗总费用：¥<span class = 'left-price cure-price'><?= Common::num($chargeTotal['cure_price']); ?></span>
                    </div>
                    <div class = 'padding'>
                        处方总费用：¥<span class = 'left-price recipe-price'><?= Common::num($chargeTotal['recipe_price']); ?></span>
                    </div>
                    <div class = 'padding'>
                        其他总费用：¥<span class = 'left-price material-price'><?= Common::num($chargeTotal['materialPrice']); ?></span>
                    </div>
                    <div class = 'padding'>
                        医嘱套餐总费用：¥<span class = 'left-price package-price'><?= Common::num($chargeTotal['packagePrice']); ?></span>
                    </div>
                </div>
                <div class = 'col-xs-6 col-md-6 right'>
                   
                        <label class = 'selected-text'>费用合计：<div class = 'selected-price'>¥<?= Common::num(!empty($chargeTotal)?array_sum($chargeTotal):0)?></div></label>
                   
                </div>
            </div>
        