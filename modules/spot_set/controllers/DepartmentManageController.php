<?php

namespace app\modules\spot_set\controllers;

use Yii;
use yii\web\Response;
use app\modules\spot_set\models\SecondDepartment;
use app\modules\spot\models\search\OnceDepartmentSearch;
use app\common\base\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use app\modules\spot_set\models\SecondDepartmentUnion;
use yii\db\Exception;
use app\modules\spot_set\models\OnceDepartment;


class DepartmentManageController extends BaseController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @desc 诊所下二级科室的展示与保存
     * @return mixed
     */
    public function actionIndex()
    {
        $isError = true;
        if(Yii::$app->request->isPost){
            $data = Yii::$app->request->post('secondDepartmentUnionId');
            $onceInputData = Yii::$app->request->post('onceDepartmentId');

            //根据当前诊所下的一级科室获取当前诊所下的所有二级科室
            $secondDepartmentArr = SecondDepartment::getSecondDepartment($onceInputData,null);
            $secondDepartmentByOnce =[];
            if(!empty($secondDepartmentArr)){
                $secondDepartmentByOnce = array_column($secondDepartmentArr,'id');
            }
            $saveData = [];
            if (!empty($data)){
                if(is_array($data)){//防止前端修改数据
                    foreach ($data as $onceDepartmentId => $departmentIdArr) {
                        if( !is_array($departmentIdArr) || !is_numeric($onceDepartmentId) || !$isError){
                            $isError = false;
                            break;
                        }
                        foreach ($departmentIdArr as $secondDepartmentId) {
                            if(!is_numeric($secondDepartmentId)){
                                $isError = false;
                                break;
                            }
                            $saveData[] = [
                                'second_department_id' => $secondDepartmentId,
                                'spot_id' => $this->spotId,
                                'create_time' => time(),
                                'update_time' => time(),
                            ];
                        }
                    }
                }else{
                    $isError = false;
                }
            }
            if($isError){//数据格式正确，保存
                $dbTrans = Yii::$app->db->beginTransaction();
                try {
                    $db = Yii::$app->db;
                    //区分是否为搜索一级科室之后保存
                    $db->createCommand()->delete(SecondDepartmentUnion::tableName(), ['spot_id' => $this->spotId,'second_department_id' => $secondDepartmentByOnce])->execute();
                    $db->createCommand()->batchInsert(SecondDepartmentUnion::tableName(), ['second_department_id', 'spot_id', 'create_time','update_time'], $saveData)->execute();
                    $dbTrans->commit();
                    Yii::$app->getSession()->setFlash('success', '保存成功');
                    return $this->redirect(['index']);
                } catch (Exception $e) {
                    $dbTrans->rollBack();
                    Yii::$app->getSession()->setFlash('error', '保存失败');
                    return $this->redirect(['index']);
                }
            }else{
                Yii::$app->getSession()->setFlash('error', '保存失败');
                return $this->redirect(['index']);
            }
        }
        $searchModel = new OnceDepartmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$this->pageSize,$isPage = 0);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

}
