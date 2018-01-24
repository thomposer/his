<?php

/*
 * time: 2017-11-15 14:27:14.
 * author : yu.li.
 */

namespace app\modules\spot\controllers;

use Yii;
use app\common\base\BaseController;
use yii\filters\VerbFilter;
use yii\helpers\Url;

class ChargeManageController extends BaseController
{

    public function beforeAction($action) {
        parent::beforeAction($action);
        Yii::$app->view->params['navData'] = $this->getNavData();
        return parent::beforeAction($action);
    }

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'inspect-delete' => ['post'],
                ],
            ],
        ];
    }

/**
     * @desc 检验医嘱Trait
     */
    use InspectTrait;

/**
     * @desc 检验项目Trait
     */
    use ItemTrait;

/**
     * @desc 影像学检查医嘱
     */
    use CheckTrait;

/**
     * @desc 治疗医嘱
     */
    use CureTrait;

/**
     * @desc 处方医嘱
     */
    use RecipeTrait;

/**
     * @desc 医疗耗材医嘱
     */
    use ConsumablesTrait;

/**
     * @desc 诊金管理
     */
    use MedicalFeeTrait;

    /**
     * 
     * @return 获取左侧二级导航
     */
    protected function getNavData() {
        $data = [
            'title' => '收费项管理',
            'menu' => [
                [
                    'name' => '实验室检查管理',
                    'urlAlias' => '@spotChargeManageInspectIndex',
                    'curUrl' => 'inspect'
                ],
                [
                    'name' => '检验项目',
                    'urlAlias' => '@spotChargeManageItemIndex',
                    'curUrl' => 'item'
                ],
                [
                    'name' => '影像学检查管理',
                    'urlAlias' => '@spotChargeManageCheckIndex',
                    'curUrl' => 'check'
                ],
                [
                    'name' => '治疗医嘱',
                    'urlAlias' => '@spotChargeManageCureIndex',
                    'curUrl' => 'cure'
                ],
                [
                    'name' => '处方医嘱',
                    'urlAlias' => '@spotChargeManageRecipeIndex',
                    'curUrl' => 'recipe'
                ],
                [
                    'name' => '医疗耗材',
                    'urlAlias' => '@spotChargeManageConsumablesIndex',
                    'curUrl' => 'consumables'
                ],
            ]
        ];
        return $data;
    }

}
