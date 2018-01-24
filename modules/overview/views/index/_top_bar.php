<?php

use yii\bootstrap\Tabs;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$attributeLabels = $model->attributeLabels();
?>
<div class = 'col-sm-6 col-md-3'>
    <div class="today-job">
        <div class="doctor-h2">系统概况</div>
        <div class="today-ren">
            <div class="today-left">
                <div class="doctor-yijiezhen"><?= $overviewNum['agencyNum'] ?></div>
                <div class="doctor-yijiezhen-text">总机构数</div>
            </div>
            <div class="today-right">
                <div class="doctor-yijiezhen"><?= $overviewNum['spotNum'] ?></div>
                <div class="doctor-yijiezhen-text">总诊所数</div>
            </div>
        </div>
    </div>
</div>
<div class = 'col-sm-12 col-md-9 serarch-right'>
    <div class="doctor-search-right">
        <?=
        Tabs::widget([
            'renderTabContent' => false,
            'navType' => ' nav-tabs overview-search',
            'items' => [
                [
                    'label' => '机构查询',
                    'options' => ['id' => 'agency'],
                    'active' => $type == 1 ? true : false
                ],
                [
                    'label' => '诊所查询',
                    'options' => ['id' => 'clinic'],
                    'active' => $type == 2 ? true : false
                ]
            ]
        ]);
        ?>

        <div class = 'tab-content overview-tab-content'>

            <div id = 'agency' class="tab-pane <?= $type == 1 ? 'active' : '' ?>">
                <?php
                $form = ActiveForm::begin([
                            'method' => 'get',
                            'options' => ['class' => ''],
                            'fieldConfig' => [
                                'template' => "{input}",
                            ],
                            'action' => Url::to(['@overviewIndexList'])
                ]);
                ?>
                <div class="doctor-search-btn">
                    <div class="search-doctor">
                        <div class="search-input-div">
                            <div class="icon_search"></div>
                            <div class="icon_search fa fa-search"></div>
                            <div class="search-input-input">
                                <?php
                                $spot_name = $model->spot_name;
                                ?>
                                <input name="OverviewSearch[spot_name]" class="search-input" placeholder="请输入机构名称" value="<?= \yii\helpers\Html::encode($spot_name) ?>"/>
                            </div>
                        </div>
                        <div  class="search-btn"><button  class="search-btn-serarch"  type="submit">搜索</button></div>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>


            <div id = 'clinic' class="tab-pane <?= $type == 2 ? 'active' : '' ?>">
                <?php
                $form = ActiveForm::begin([
                            'method' => 'get',
                            'options' => ['class' => ''],
                            'fieldConfig' => [
                                'template' => "{input}",
                            ],
                            'action'=>  Url::to(['@overviewIndexIndex'])
                ]);
                ?>
                <div class="doctor-search-btn">
                    <div class="search-doctor input-group">
                        <div class="search-input-div">
                            <div class="icon_search"></div>
                            <div class="icon_search fa fa-search"></div>
                            <div class="search-input-input">
                                <?php
                                $spot_name = $model->spot_name;
                                ?>
                                <input name="OverviewSearch[spot_name]" class="search-input" placeholder="请输入诊所名称" value="<?= \yii\helpers\Html::encode($spot_name) ?>"/>
                            </div>
                        </div>
                        <div  class="search-btn"><button  class="search-btn-serarch"  type="submit">搜索</button></div>
                    </div>

                </div>
                <?php ActiveForm::end(); ?>
            </div>

        </div>

    </div>
</div>