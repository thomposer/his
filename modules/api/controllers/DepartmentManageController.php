<?php

namespace app\modules\api\controllers;

use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use Yii;
use app\modules\spot\models\SecondDepartment;
use yii\web\NotFoundHttpException;
use app\modules\spot_set\models\SecondDepartmentUnion;

/**
 *
 * @author 庄少雄
 * @property 科室管理接口API
 */
class DepartmentManageController extends CommonController
{

    public function behaviors() {
        $parent = parent::behaviors();
        $current = [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'spot-second-department-subclass' => ['post'],
                    'spotset-second-department-subclass' => ['post'],
                ],
            ],
        ];
        return ArrayHelper::merge($current, $parent);
    }



    /**
     * spot-second-department-subclass
     * @desc 机构下二级科室的展开
     */
    public function actionSpotSecondDepartmentSubclass(){
        if (isset($_POST['expandRowKey'])) {
            $dataProvider = SecondDepartment::findSubDataProvider($_POST['expandRowKey']);
            return $this->renderPartial('@spotSecondDepartmentSubclassViewPath', ['dataProvider' => $dataProvider]);
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }


    /**
     * spotset-second-department-subclass
     * @desc 诊所下二级科室的展开
     */
    public function actionSpotsetSecondDepartmentSubclass(){
        if (isset($_POST['expandRowKey'])) {
            $dataProvider = SecondDepartment::findSubDataProvider($_POST['expandRowKey']);
            //获取当前诊所下已勾选的二级科室
            $selectSecondDepartment = SecondDepartmentUnion::getSelectSecondDepartment();
            $secondDepartmentId = [];
            if(!empty($selectSecondDepartment)){
                $secondDepartmentId = array_column($selectSecondDepartment,'second_department_id');
            }
            return $this->renderPartial('@spot_setSecondDepartmentSubclassViewPath', [
                'dataProvider' => $dataProvider,
                'secondDepartmentId' => $secondDepartmentId
            ]);
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }


}
