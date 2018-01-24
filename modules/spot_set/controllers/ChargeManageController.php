<?php
namespace app\modules\spot_set\controllers;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/15
 * Time: 15:46
 */
use app\common\base\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;
class ChargeManageController extends BaseController{
    //实验室检查配置
    use InspectClinicTrait;
    //影像学检查配置
    use CheckListClinicTrait;
    //治疗配置
    use ClinicCureTrait;
    //处方配置
    use RecipeListClinicTrait;
    //耗材配置
    use ConsumablesClinicTrait;
    //其他配置
    use MaterialTrait;
    //诊金配置
    use MedicalFeeClinicTrait;
    //左侧导航配置
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
                    'inspect-clinic-delete' => ['post'],
                    'check-list-clinic-delete' => ['post'],
                    'cure-clinic-delete' => ['post'],
                    'consumables-clinic-delete' => ['post'],
                    'material-delete' => ['post'],
                    'medical-fee-clinic-delete' => ['post'],
                    'recipe-clinic-delete' => ['post'],

                ],
            ],
        ];
    }


    /**
     *
     * @return 获取左侧二级导航
     */
    protected function getNavData() {
        $data = [
            'title' => '收费项管理',
            'menu' => [
                [
                    'name' => '实验室检查配置',
                    'urlAlias' => '@spot_setChargeManageInspectClinicIndex',
                    'curUrl' => 'inspect'
                ],
                [
                    'name' => '影像学检查配置',
                    'urlAlias' => '@spot_setChargeManageCheckListClinicIndex',
                    'curUrl' => 'check'
                ],
                [
                    'name' => '治疗配置',
                    'urlAlias' => '@spot_setChargeManageCureClinicIndex',
                    'curUrl' => 'cure'
                ],
                [
                    'name' => '处方配置',
                    'urlAlias' => '@spot_setChargeManageRecipeClinicIndex',
                    'curUrl' => 'recipe'
                ],
                [
                    'name' => '耗材配置',
                    'urlAlias' => '@spot_setChargeManageConsumablesClinicIndex',
                    'curUrl' => 'consumables'
                ],
                [
                    'name' => '其他配置',
                    'urlAlias' => '@spot_setChargeManageMaterialIndex',
                    'curUrl' => 'material'
                ],
            ]
        ];
        return $data;
    }


}