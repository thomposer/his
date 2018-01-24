<?php

namespace app\modules\patient\controllers;

use app\modules\spot_set\models\CheckListClinic;
use Yii;
use app\common\base\BaseController;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\helpers\Html;
use app\modules\outpatient\models\RecipeRecord;
use app\modules\patient\models\UmpRecord;
use app\modules\spot\models\RecipeList;
use yii\helpers\Json;
use app\modules\patient\models\PatientRecord;
use app\modules\charge\models\ChargeInfo;
use app\modules\charge\models\ChargeRecord;
use app\modules\outpatient\models\InspectRecord;
use app\modules\outpatient\models\CheckRecord;
use app\modules\outpatient\models\CureRecord;
use app\modules\spot\models\Inspect;
use app\modules\spot\models\InspectItemUnion;
use app\modules\outpatient\models\InspectRecordUnion;
use app\modules\spot\models\CureList;
use app\modules\spot\models\CheckList;
use app\modules\check\models\Check;
use app\modules\outpatient\models\Report;
use app\modules\patient\models\Patient;
use yii\web\NotFoundHttpException;
use yii\db\Exception;
use app\modules\spot_set\models\InspectClinic;
use app\modules\spot_set\models\InspectItemUnionClinic;

/**
 * PatientController implements the CRUD actions for Patient model.
 */
class MakeupController extends BaseController
{

    public function behaviors() {
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
     * @return 接诊
     */
    public function actionReception($id, $patientId, $doctorId) {
        $model = new UmpRecord();
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* 实验室检查 */
        $inspectRecordDataProvider = InspectRecord::findInspectRecordDataProvider($id);
//        $inspectList = Inspect::getInspectList();
        $inspectList = InspectClinic::getInspectClinicList();
        $inspectRecordModel = UmpRecord::findInspectRecord($id);
        $inspectRecordModel->scenario = 'makeup';
        /* 影像学检查 */
        $checkRecordDataProvider = CheckRecord::getCheckRecordDataProvider($id);
//        $checkList = CheckList::getCkeckList();
        //获取当前诊所下的状态为启用的检查医嘱
        $checkList = CheckListClinic::getCheckListAll();
        $checkRecordModel = UmpRecord::findCheckRecord($id);
        $checkRecordModel->scenario = 'makeup';
        /* 治疗 */
        $cureRecordDataProvider = CureRecord::findCureRecordDataProvider($id); //治疗
        $cureRecordModel = UmpRecord::findCureRecord($id);
        $cureRecordModel->scenario = 'makeup';
        $cureList = CureList::getCureList();
        /* 处方 */
        $recipeRecordModel = UmpRecord::findRecipeRecord($id);
        $recipeRecordModel->scenario = 'makeup';
        $recipeRecordDataProvider = RecipeRecord::getRecipeRecordDataProvider($id);
        $recipeList = RecipeList::getReciptListByStock();
        $ret = [
            'title' => "医嘱补录",
            'content' => $this->renderAjax('reception', [
                'model' => $model,
                /* 实验室检查 */
                'inspectRecordModel' => $inspectRecordModel,
                'inspectRecordDataProvider' => $inspectRecordDataProvider,
                'inspectList' => $inspectList,
                /* 影像学检查 */
                'checkRecordModel' => $checkRecordModel,
                'checkRecordDataProvider' => $checkRecordDataProvider,
                'checkList' => $checkList,
                /* 治疗 */
                'cureRecordDataProvider' => $cureRecordDataProvider,
                'cureRecordModel' => $cureRecordModel,
                'cureList' => $cureList,
                /* 处方 */
                'recipeRecordModel' => $recipeRecordModel,
                'recipeRecordDataProvider' => $recipeRecordDataProvider,
                'recipeList' => $recipeList,
                'id' => $id,
                'patientId' => $patientId,
                'doctorId' => $doctorId,
            ]),
        ];
        return $ret;
    }

    /**
     * @return 保存补录的处方信息
     */
    public function actionSaveRecipe($id, $patientId, $doctorId) {
        $model = new RecipeRecord();
        $model->record_id = $id;
        $model->scenario = 'makeup';
        $recipeRecordIdArr = [];
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $dbTrans = Yii::$app->db->beginTransaction();
            try {
                if (($hasRecord = $this->checkRecord($model->record_id)) !== null) {
                    $db = Yii::$app->db;
                    $recipeList = [];
                    $recipePrice = [];
                    $isNewRecord = Yii::$app->request->post('RecipeRecord')['isNewRecord'];
                    if (count($model->recipe_id)) {
                        foreach ($model->recipe_id as $key => $v) {
                            $list = Json::decode($v);
                            $recipeWhere = ['id' => $list['id'], 'spot_id' => $this->spotId, 'record_id' => $model->record_id];
                            //旧记录
                            if (isset($isNewRecord[$key]) && $isNewRecord[$key] == 0) {
                                //若为删除操作 deleted == 1 , 修改操作deleted == ''
                                if ($model->deleted[$key] == 1) {
                                    //删除相应的处方检查信息记录
                                    $db->createCommand()->delete(RecipeRecord::tableName(), $recipeWhere)->execute();
                                    $recipePrice[] = -($list['price'] * intval($model->num[$key])); //删除的费用
                                    //若为接诊结束,则直接删除相应的收费详情记录
                                    if ($hasRecord['status'] == 5) {
                                        $result = ChargeInfo::find()->select(['status'])->where(['outpatient_id' => $list['id'], 'type' => ChargeInfo::$recipeType])->asArray()->one();
                                        //若为未收费
//                                        if ($result['status'] == 0) {
                                        $db->createCommand()->delete(ChargeInfo::tableName(), ['outpatient_id' => $list['id'], 'type' => ChargeInfo::$recipeType])->execute();
//                                        }
                                        if ($result['status'] == 2) {//已退费
                                            $recipePrice[] = $list['price'] * intval($model->num[$key]);
                                        }
                                    }
                                } else {
                                    //修改对应的记录
                                    $time = RecipeRecord::find()->select(['num'])->where($recipeWhere)->asArray()->one()['num'];
                                    $diffCount = intval($model->num[$key]) - intval($time);
                                    $db->createCommand()->update(RecipeRecord::tableName(), ['dose' => $model->dose[$key], 'dosage_form' => $list['type'], 'used' => $model->used[$key], 'frequency' => $model->frequency[$key], 'day' => $model->day[$key], 'num' => $model->num[$key], 'type' => 2, 'description' => $model->description[$key], 'dose_unit' => ($model->dose_unit[$key]) ? $model->dose_unit[$key] : 0, 'skin_test' => $list['skin_test'], 'skin_test_status' => $model->skin_test_status[$key], 'create_time' => strtotime($model->billingTime)], $recipeWhere)->execute();
                                    $db->createCommand()->update(ChargeInfo::tableName(), ['num' => $model->num[$key]], ['outpatient_id' => $list['id'], 'type' => ChargeInfo::$recipeType])->execute();
                                    //若次数有改变，则更新收费金额
                                    if ($diffCount != 0) {
                                        $recipePrice[] = $list['price'] * $diffCount; //修改后的费用
                                    }
                                }
                            } else {
                                //新记录,delete === 0或者空 为新增
                                if (!$model->deleted[$key]) {
                                    $recipePrice[] = $list['price'] * intval($model->num[$key]);
                                    $recipeList[] = ['recipe_id' => $list['id'], 'record_id' => $model->record_id, 'spot_id' => $this->spotId, 'name' => $list['name'], 'price' => $list['price'], 'dose' => $model->dose[$key], 'dosage_form' => $list['type'], 'used' => $model->used[$key], 'day' => $model->day[$key],
                                        'unit' => $list['unit'], 'frequency' => $model->frequency[$key], 'num' => $model->num[$key], 'type' => 2, 'status' => 1, 'description' => $model->description[$key], 'dose_unit' => ($model->dose_unit[$key]) ? $model->dose_unit[$key] : 0, 'skin_test' => $list['skin_test'], 'skin_test_status' => $model->skin_test_status[$key], 'create_time' => strtotime($model->billingTime), 'update_time' => time()];
                                }
                            }
                        }
                        //若有新记录，则批量插入
                        if (count($recipeList) > 0) {
                            foreach ($recipeList as $val) {
                                $db->createCommand()->insert(RecipeRecord::tableName(), $val)->execute();
                                $recipeRecordIdArr[] = $db->lastInsertID;
                            }
                            //如果状态为接诊结束，则生成收费清单
                            if ($hasRecord['status'] == 5) {
                                $this->saveCharge($id, $patientId, 0);
                                $createList = ChargeInfo::getChargeInfo(ChargeInfo::$recipeType, $model->record_id);
                                $chargeRecordId = ChargeRecord::findChargeRecord($model->record_id);
                                foreach ($createList as $c) {
                                    $rows[] = [$chargeRecordId, $this->spotId, $model->record_id, ChargeInfo::$recipeType, $c['id'], $c['name'], $c['unit'], $c['price'], $c['num'], $doctorId, 1, time()];
                                }
                                //批量插入新增收费记录
                                $db->createCommand()->batchInsert(ChargeInfo::tableName(), ['charge_record_id', 'spot_id', 'record_id', 'type', 'outpatient_id', 'name', 'unit', 'unit_price', 'num', 'doctor_id', 'status', 'update_time'], $rows)->execute();
                            }
                        }
                        //若有金额变动，则更新
                        if (count($recipePrice) > 0) {
                            $updatePrice = array_sum($recipePrice); //变动的金额
                            $this->savePrice($model->record_id, $updatePrice, ChargeInfo::$recipeType);
                        }
                    }
                    $this->result['errorCode'] = 0;
                    $this->result['data'] = $recipeRecordIdArr ? Json::encode($recipeRecordIdArr) : '';
                    $this->result['msg'] = '保存成功';
                }
                $db->createCommand()->update(PatientRecord::tableName(), ['pharmacy_record_time' => time()], ['id' => $id])->execute();
                $dbTrans->commit();
            } catch (Exception $e) {
                $dbTrans->rollBack();
                $this->result['errorCode'] = 0;
                $this->result['msg'] = '保存失败';
            }
        } else {
            $this->result['errorCode'] = '1014';
            $this->result['msg'] = $model->errors['recipe_id'][0];
        }

        return Json::encode($this->result);
    }

    /**
     * 
     * @param type $id          流水ID
     * @param type $patientId   患者ID
     */
    public function actionSaveInspect($id, $patientId, $doctorId) {
        $model = new InspectRecord();
        $model->record_id = $id;
        $model->scenario = 'makeup';
        $inspectRecordIdArr = [];
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $dbTrans = Yii::$app->db->beginTransaction();
            try {
                if (($hasRecord = $this->checkRecord($model->record_id)) !== null) {
                    $db = Yii::$app->db;
                    $inspectList = [];
                    $inspectPrice = [];
                    $isNewRecord = Yii::$app->request->post('InspectRecord')['isNewRecord'];
                    if (count($model->inspect_id)) {
                        foreach ($model->inspect_id as $key => $v) {
                            $list = Json::decode($v);
                            $inspectItemParam = Yii::$app->request->post('InspectItem');
                            $itemResult = $inspectItemParam['result'][$model->uuid[$key]];
                            //旧记录
                            if (isset($isNewRecord[$key]) && $isNewRecord[$key] == 0) {
                                //若为删除操作 deleted == 1
                                if ($model->deleted[$key] == 1) {
                                    //删除相应的实验室检查信息记录
                                    $res = $db->createCommand()->delete(InspectRecord::tableName(), ['id' => $list['id'], 'spot_id' => $this->spotId, 'record_id' => $model->record_id])->execute();

                                    $inspectPrice[] = -$list['price'];
                                    //若为接诊结束,则直接删除相应的收费详情记录
                                    if ($hasRecord['status'] == 5) {
//                                        $result = ChargeInfo::find()->select(['status'])->where(['outpatient_id' => $list['id'], 'type' => ChargeInfo::$inspectType])->asArray()->one();
                                        //若为未收费
//                                        if ($result['status'] == 0) {
                                        $db->createCommand()->delete(ChargeInfo::tableName(), ['outpatient_id' => $list['id'], 'type' => ChargeInfo::$inspectType, 'spot_id' => $this->spotId, 'record_id' => $model->record_id])->execute();
//                                        } else if ($result['status'] == 2) {
//                                            $inspectPrice[] = $list['price'];
//                                        }
                                    }
                                } else {
                                    //修改对应的记录
                                    //修改开单时间
                                    $db->createCommand()->update(InspectRecord::tableName(), ['create_time' => strtotime($model->billingTime)], ['id' => $list['id']])->execute();
                                    $inspectItemRecordList = InspectRecordUnion::getInspectItem($list['id']);
                                    if ($inspectItemRecordList) {
                                        //保存检验医嘱配置关联的项目的列表
                                        foreach ($inspectItemRecordList as $v) {
                                            $db->createCommand()->update(InspectRecordUnion::tableName(), ['result' => $itemResult[$v['id']]], ['id' => $v['id']])->execute();
                                        }
                                    }
                                }
                            } else {
                                //新记录,delete === 0或者空 为新增
                                if (!$model->deleted[$key]) {
                                    $inspectPrice[] = $list['price'];
                                    $itemList = [];
                                    $db->createCommand()->insert(
                                            InspectRecord::tableName(), [
                                        'record_id' => $model->record_id,
                                        'spot_id' => $this->spotId,
                                        'name' => $list['name'],
                                        'unit' => $list['unit'],
                                        'price' => $list['price'],
                                        'status' => 1,
                                        'create_time' => strtotime($model->billingTime),
                                        'update_time' => time()
                                    ])->execute();
                                    $inspectRecordIdArr[] = $db->lastInsertID;
                                    $inspectRecordId = $db->lastInsertID;
//                                    $inspectItemList = InspectItemUnion::getInspectItem($list['id']);
                                    $inspectItemList = InspectItemUnionClinic::getInspectItemClinic($list['id']);
                                    $inspectItemList = $inspectItemList[$list['id']];
                                    if ($inspectItemList) {
                                        //保存检验医嘱配置关联的项目的列表
                                        foreach ($inspectItemList as $v) {
                                            $itemList[] = [$inspectRecordId, $this->spotId, $model->record_id, $v['item_name'], $v['english_name'], $v['unit'], $v['reference'], $itemResult[$v['id']], time(), time()];
                                        }
                                        $db->createCommand()->batchInsert(
                                                InspectRecordUnion::tableName(), ['inspect_record_id', 'spot_id', 'record_id', 'name', 'english_name', 'unit', 'reference', 'result', 'create_time', 'update_time'], $itemList)->execute();
                                    }
                                }
                            }
                        }
                        //若有新记录，则批量插入
                        if (count($inspectPrice) > 0) {
//                        if (array_sum($inspectPrice) > 0) {
                            //如果状态为接诊结束，则生成收费清单
                            if ($hasRecord['status'] == 5) {
                                $this->saveCharge($id, $patientId, 0);
                                $createList = ChargeInfo::getChargeInfo(ChargeInfo::$inspectType, $model->record_id);
                                $chargeRecordId = ChargeRecord::findChargeRecord($model->record_id);
                                if ($createList) {
                                    foreach ($createList as $c) {
                                        $rows[] = [$chargeRecordId, $this->spotId, $model->record_id, ChargeInfo::$inspectType, $c['id'], $c['name'], $c['unit'], $c['price'], 1, $doctorId, 1, time()];
                                    }
                                    //批量插入新增收费记录
                                    $db->createCommand()->batchInsert(ChargeInfo::tableName(), ['charge_record_id', 'spot_id', 'record_id', 'type', 'outpatient_id', 'name', 'unit', 'unit_price', 'num', 'doctor_id', 'status', 'update_time'], $rows)->execute();
                                }
                            }
//                        }
                            //若有金额变动，则更新

                            $updatePrice = array_sum($inspectPrice);
                            $this->savePrice($model->record_id, $updatePrice, ChargeInfo::$inspectType);
                        }
                    }
                    $dbTrans->commit();
                    $this->result['errorCode'] = 0;
                    $this->result['data'] = $inspectRecordIdArr ? Json::encode($inspectRecordIdArr) : '';
                    $this->result['msg'] = '保存成功';
                }
            } catch (Exception $e) {
                $dbTrans->rollBack();
            }
        } else {
            $this->result['errorCode'] = 1014;
            $this->result['msg'] = $model->errors['inspect_id'][0];
        }
        return Json::encode($this->result);
    }

    /**
     * @property 门诊-影像学检查api接口
     */
    public function actionSaveCheck($id, $patientId, $doctorId) {
        $model = new CheckRecord();
        $model->record_id = $id;
        $model->scenario = 'makeup';
        $checkRecordIdArr = [];
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $dbTrans = Yii::$app->db->beginTransaction();
            try {
                if (($hasRecord = $this->checkRecord($model->record_id)) !== null) {
                    $db = Yii::$app->db;
                    $checkList = [];
                    $checkPrice = [];
                    $isNewRecord = Yii::$app->request->post('CheckRecord')['isNewRecord'];
                    if (count($model->check_id)) {
                        foreach ($model->check_id as $key => $v) {
                            $list = Json::decode($v);
                            //旧记录
                            if (isset($isNewRecord[$key]) && $isNewRecord[$key] == 0) {
                                //若为删除操作 deleted == 1
                                if ($model->deleted[$key] == 1) {
                                    //删除相应的影像学检查信息记录
                                    $db->createCommand()->delete(CheckRecord::tableName(), ['id' => $list['id'], 'spot_id' => $this->spotId, 'record_id' => $model->record_id])->execute();
                                    $checkPrice[] = -$list['price'];
                                    //若为接诊结束,则直接删除相应的收费详情记录
                                    if ($hasRecord['status'] == 5) {
//                                        $result = ChargeInfo::find()->select(['status'])->where(['outpatient_id' => $list['id'], 'type' => ChargeInfo::$checkType])->asArray()->one();
                                        //若为未收费
//                                        if ($result['status'] == 0) {
                                        $db->createCommand()->delete(ChargeInfo::tableName(), ['outpatient_id' => $list['id'], 'type' => ChargeInfo::$checkType, 'spot_id' => $this->spotId, 'record_id' => $model->record_id])->execute();
//                                        }
//                                        if ($result['status'] == 2) {
//                                            $checkPrice[] = $list['price'];
//                                        }
                                    }
                                } else {//修改
                                    //修改对应的记录
                                    $db->createCommand()->update(CheckRecord::tableName(), ['description' => $model->description[$key], 'result' => $model->result[$key], 'create_time' => strtotime($model->billingTime)], ['id' => $list['id']])->execute();
                                }
                            } else {
                                //新记录,delete === 0或者空 为新增
                                if (!$model->deleted[$key]) {
                                    $checkPrice[] = $list['price'];
                                    $checkList[] = ['record_id' => $model->record_id, 'spot_id' => $this->spotId, 'name' => $list['name'], 'unit' => $list['unit'], 'price' => $list['price'],
                                        'status' => 1, 'description' => $model->description[$key], 'result' => $model->result[$key], 'create_time' => strtotime($model->billingTime), 'update_time' => time()];
                                }
                            }
                        }
                        //若有新记录，则批量插入
                        if (count($checkList) > 0) {
                            foreach ($checkList as $val) {
                                $db->createCommand()->insert(CheckRecord::tableName(), $val)->execute();
                                $checkRecordIdArr[] = $db->lastInsertID;
                            }
                            //如果状态为接诊结束，则生成收费清单
                            if ($hasRecord['status'] == 5) {
                                $this->saveCharge($id, $patientId, 0);
                                $createList = ChargeInfo::getChargeInfo(ChargeInfo::$checkType, $model->record_id);
                                $chargeRecordId = ChargeRecord::findChargeRecord($model->record_id);
                                foreach ($createList as $c) {
                                    $rows[] = [$chargeRecordId, $this->spotId, $model->record_id, ChargeInfo::$checkType, $c['id'], $c['name'], $c['unit'], $c['price'], 1, $doctorId, 1, time()];
                                }
                                //批量插入新增收费记录
                                $db->createCommand()->batchInsert(ChargeInfo::tableName(), ['charge_record_id', 'spot_id', 'record_id', 'type', 'outpatient_id', 'name', 'unit', 'unit_price', 'num', 'doctor_id', 'status', 'update_time'], $rows)->execute();
                            }
                        }
                        //若有金额变动，则更新
                        if (count($checkPrice) > 0) {
                            $updatePrice = array_sum($checkPrice);
                            $this->savePrice($model->record_id, $updatePrice, ChargeInfo::$checkType);
                        }
                    }
                    $dbTrans->commit();
                    $this->result['errorCode'] = 0;
                    $this->result['data'] = $checkRecordIdArr ? Json::encode($checkRecordIdArr) : '';
                    $this->result['msg'] = '保存成功';
                }
            } catch (Exception $e) {
                $dbTrans->rollBack();
            }
        } else {
            $this->result['errorCode'] = 1014;
            $this->result['msg'] = $model->errors['check_id'][0];
        }
        return Json::encode($this->result);
    }

    /**
     * @property 门诊-治疗api接口
     * @throws NotFoundHttpException
     */
    public function actionSaveCure($id, $patientId, $doctorId) {

        $model = new CureRecord();
        $model->record_id = $id;
        $model->scenario = 'makeup';
        $cureRecordIdArr = [];
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $dbTrans = Yii::$app->db->beginTransaction();
            try {
                if (($hasRecord = $this->checkRecord($model->record_id)) !== null) {
                    $db = Yii::$app->db;
                    $cureList = [];
                    $curePrice = [];
                    $isNewRecord = Yii::$app->request->post('CureRecord')['isNewRecord'];
                    if (count($model->cure_id) > 0) {
                        foreach ($model->cure_id as $key => $v) {
                            $list = Json::decode($v);
                            $cureWhere = ['id' => $list['id'], 'spot_id' => $this->spotId, 'record_id' => $model->record_id];
                            //旧记录
                            if (isset($isNewRecord[$key]) && $isNewRecord[$key] == 0) {
                                //若未删除操作 deleted == 1 , 修改操作deleted == ''
                                if ($model->deleted[$key] == 1) {
                                    //删除相应的治疗检查信息记录
                                    $db->createCommand()->delete(CureRecord::tableName(), $cureWhere)->execute();
                                    $curePrice[] = -($list['price'] * intval($model->time[$key])); //删除的费用
                                    //若为接诊结束,则直接删除相应的收费详情记录
                                    if ($hasRecord['status'] == 5) {
                                        $result = ChargeInfo::find()->select(['status'])->where(['outpatient_id' => $list['id'], 'type' => ChargeInfo::$cureType])->asArray()->one();
                                        //若为未收费
//                                        if ($result['status'] == 0) {
                                        $db->createCommand()->delete(ChargeInfo::tableName(), ['outpatient_id' => $list['id'], 'type' => ChargeInfo::$cureType])->execute();
//                                        }
//                                        if ($result['status'] == 2) {
//                                            $curePrice[] = $list['price'] * intval($model->time[$key]);
//                                        }
                                    }
                                } else {
                                    //修改对应的记录
                                    $time = CureRecord::find()->select(['time'])->where($cureWhere)->asArray()->one()['time'];
                                    $diffCount = intval($model->time[$key]) - intval($time);
                                    $db->createCommand()->update(CureRecord::tableName(), ['time' => $model->time[$key], 'description' => $model->description[$key], 'remark' => $model->remark[$key], 'create_time' => strtotime($model->billingTime)], $cureWhere)->execute();
                                    $db->createCommand()->update(ChargeInfo::tableName(), ['num' => $model->time[$key]], ['outpatient_id' => $list['id'], 'type' => ChargeInfo::$cureType])->execute();
                                    //若次数有改变，则更新收费金额
                                    if ($diffCount != 0) {
                                        $curePrice[] = $list['price'] * $diffCount;
                                    }
                                }
                            } else {
                                //新记录,delete === 0或者空 为新增
                                if (!$model->deleted[$key]) {
                                    $curePrice[] = $list['price'] * intval($model->time[$key]);
                                    $cureList[] = ['record_id' => $model->record_id, 'spot_id' => $this->spotId, 'name' => $list['name'], 'unit' => $list['unit'], 'price' => $list['price'], 'time' => $model->time[$key], 'description' => $model->description[$key], 'status' => 1, 'remark' => $model->remark[$key], 'create_time' => strtotime($model->billingTime), 'update_time' => time()];
                                }
                            }
                        }
                        //若有新记录，则批量插入
                        if (count($cureList) > 0) {
                            foreach ($cureList as $val) {
                                $db->createCommand()->insert(CureRecord::tableName(), $val)->execute();
                                $cureRecordIdArr[] = $db->lastInsertID;
                            }
                            //如果状态为接诊结束，则生成收费清单
                            if ($hasRecord['status'] == 5) {
                                $this->saveCharge($id, $patientId, 0);
                                $createList = ChargeInfo::getChargeInfo(ChargeInfo::$cureType, $model->record_id);
                                $chargeRecordId = ChargeRecord::findChargeRecord($model->record_id);
                                foreach ($createList as $c) {
                                    $rows[] = [$chargeRecordId, $this->spotId, $model->record_id, ChargeInfo::$cureType, $c['id'], $c['name'], $c['unit'], $c['price'], $c['time'], $doctorId, 1, time()];
                                }
                                //批量插入新增收费记录
                                $db->createCommand()->batchInsert(ChargeInfo::tableName(), ['charge_record_id', 'spot_id', 'record_id', 'type', 'outpatient_id', 'name', 'unit', 'unit_price', 'num', 'doctor_id', 'status', 'update_time'], $rows)->execute();
                            }
                        }
                        //若有金额变动，则更新
                        if (count($curePrice) > 0) {
                            $updatePrice = array_sum($curePrice); //变动的金额
                            $this->savePrice($model->record_id, $updatePrice, ChargeInfo::$cureType);
                        }
                    }
                    $dbTrans->commit();
                    $this->result['errorCode'] = 0;
                    $this->result['data'] = $cureRecordIdArr ? Json::encode($cureRecordIdArr) : '';
                    $this->result['msg'] = '保存成功';
                }
            } catch (Exception $e) {
                $dbTrans->rollBack();
            }
        } else {

            $this->result['errorCode'] = '1014';
            $this->result['msg'] = $model->errors['time'][0];
        }

        return Json::encode($this->result);
    }

    /**
     * 
     * @param type $id
     * @param type $patientId
     * @param type $doctorId
     * @return 补录收费
     */
    public function actionCharge($id, $patientId, $doctorId) {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            $model = UmpRecord::getChargeModel($id);
            $model->scenario = 'charge';
            $model->chargeTime = $model->chargeTime ? date('Y-m-d H:i:s', $model->chargeTime) : '';
            $title = '收费信息';
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                UmpRecord::saveChargeRecord($model, $patientId, $id, $doctorId);
                return [
                    'forceReload' => '#ump_signsData' . $id,
                    'forceClose' => true,
                    'forceMessage' => "操作成功"
                ];
            } else {
                $ret = [
                    'title' => $title,
                    'content' => $this->renderAjax('_charge', [
//                    'content' => $this->renderAjax('/ump/_signsData', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('取消', ['class' => 'btn btn-cancel btn-form btn-custom', 'data-dismiss' => "modal"]) .
                    Html::button('保存', ['class' => 'btn btn-default btn-form btn-custom', 'type' => "submit", 'id' => 'btn-custom'])
//                    'footer'=>''
                ];
                return $ret;
            }
        }
    }

    /**
     * 先判断该就诊记录是否属于该诊所，若属于则可操作。否则返回404
     * @param 就诊记录 $id
     */
    private function checkRecord($id) {

        $hasRecord = PatientRecord::find()->select(['id', 'status'])->where(['id' => $id])->asArray()->one();
        if (!$hasRecord) {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
        return $hasRecord;
    }

    /**
     * @param 就诊流水ID $id
     * @param 就诊患者ID $patient_id
     * @param 诊疗费 $price
     * @property 生成就诊费用清单
     */
    private function saveCharge($id, $patient_id, $price) {
        $chargeRecord = ChargeRecord::findOne(['record_id' => $id]);
        if ($chargeRecord == null || empty($chargeRecord)) {
            $chargeRecordModel = new ChargeRecord();
            $chargeRecordModel->record_id = $id;
            $chargeRecordModel->patient_id = $patient_id;
            $chargeRecordModel->status = 1;
//            $chargeRecordModel->create_time = '';
            $result = $chargeRecordModel->save();
//            $chargeRecordModel->create_time='';
//            $chargeRecordModel->save();
        }
//        if ($result) {
////            $rows[] = [$chargeRecordModel->id, $this->spotId, $id, ChargeInfo::$priceType, 0, '诊疗费', '次', $price, 1, $this->userInfo->id, time(), time()];
//            $inspectResult = InspectRecord::find()->select(['id', 'name', 'unit', 'price'])->where(['record_id' => $id, 'spot_id' => $this->spotId])->asArray()->all();
//            if ($inspectResult) {
//                foreach ($inspectResult as $v) {
//                    $rows[] = [$chargeRecordModel->id, $this->spotId, $id, ChargeInfo::$inspectType, $v['id'], $v['name'], $v['unit'], $v['price'], 1, $this->userInfo->id, time(), time()];
//                }
//            }
//            $checkResult = CheckRecord::find()->select(['id', 'name', 'unit', 'price'])->where(['record_id' => $id, 'spot_id' => $this->spotId])->asArray()->all();
//            if ($checkResult) {
//                foreach ($checkResult as $v) {
//                    $rows[] = [$chargeRecordModel->id, $this->spotId, $id, ChargeInfo::$checkType, $v['id'], $v['name'], $v['unit'], $v['price'], 1, $this->userInfo->id, time(), time()];
//                }
//            }
//            $cureResult = CureRecord::find()->select(['id', 'name', 'unit', 'price', 'time'])->where(['record_id' => $id, 'spot_id' => $this->spotId])->asArray()->all();
//            if ($cureResult) {
//                foreach ($cureResult as $v) {
//                    $rows[] = [$chargeRecordModel->id, $this->spotId, $id, ChargeInfo::$cureType, $v['id'], $v['name'], $v['unit'], $v['price'], $v['time'], $this->userInfo->id, time(), time()];
//                }
//            }
//            $recipeResult = RecipeRecord::find()->select(['id', 'name', 'unit', 'price', 'num'])->where(['record_id' => $id, 'spot_id' => $this->spotId])->asArray()->all(); //补录不区分内外购
//            if ($recipeResult) {
//                foreach ($recipeResult as $v) {
//                    $rows[] = [$chargeRecordModel->id, $this->spotId, $id, ChargeInfo::$recipeType, $v['id'], $v['name'], $v['unit'], $v['price'], $v['num'], $this->userInfo->id, 1, time(), time()];
//                }
//            }
//            Yii::$app->db->createCommand()->batchInsert(ChargeInfo::tableName(), ['charge_record_id', 'spot_id', 'record_id', 'type', 'outpatient_id', 'name', 'unit', 'unit_price', 'num', 'doctor_id', 'status', 'create_time', 'update_time'], $rows)->execute();
//        }
    }

    /**
     *
     * @param 变更的金额数 $updatePrice
     * @param 就诊流水ID $record_id
     * @param 门诊金额变动类型 $type(1-实验室检查,2-影像学检查,3-治疗,4-处方)
     * @return 保存变更后的金额
     */
    private function savePrice($id, $updatePrice, $type) {

        $model = PatientRecord::findOne(['id' => $id]);
        if ($type == 1) {
            $model->inspect_price = $model->inspect_price + $updatePrice;
        } else if ($type == 2) {
            $model->check_price = $model->check_price + $updatePrice;
        } else if ($type == 3) {
            $model->cure_price = $model->cure_price + $updatePrice;
        } else if ($type == 4) {
            $model->recipe_price = $model->recipe_price + $updatePrice;
        }
        $model->completed_price = $model->completed_price + $updatePrice;
        $result = $model->save();
        $chargeRecord = ChargeRecord::findOne(['record_id' => $id]);
        if (!empty($chargeRecord)) {
            if ($chargeRecord->discount_type == 2) {//金额扣减
                $chargeRecord->price = $model->completed_price - $chargeRecord->discount_price;
            } elseif ($chargeRecord->discount_type == 3) {
                $chargeRecord->price = $model->completed_price * ($chargeRecord->discount_price / 100);
            } else {
                $chargeRecord->price = $model->completed_price;
            }
            $result = $chargeRecord->save();
        }
    }

    /**
     * @param $id，流水id
     * @return array
     * 实验室附件上传
     */
    public function actionInspectUpload($id) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $status = 1;
        $type = 1;
        $inspectCheckList = Report::reportData($id, 1, $status, $type);
        $patientRecord = PatientRecord::findOne(['id' => $id]);
        $patientId = $patientRecord->patient_id;
        try {
            $patientInfo = $this->findModel($patientId);
        } catch (NotFoundHttpException $exc) {
            throw new NotFoundHttpException('你所请求的页面不存在.');
        }
        $ret = [
            'title' => "实验室信息补录-" . Html::encode($patientInfo->username),
            'content' => $this->renderAjax('/ump/_upload', [
                'inspectCheckList' => $inspectCheckList,
            ]),
            'footer' => Html::button('关闭', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"])
        ];
        return $ret;
    }

    /**
     * @param $id，流水id
     * @return array
     * 影像学附件上传
     */
    public function actionCheckUpload($id) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $status = 1;
        $type = 2;
        $inspectCheckList = Report::reportData($id, 2, $status, $type);
        $checkRecordModel = new Check();
        $patientRecord = PatientRecord::findOne(['id' => $id]);
        $patientId = $patientRecord->patient_id;
        try {
            $patientInfo = $this->findModel($patientId);
        } catch (NotFoundHttpException $exc) {
            throw new NotFoundHttpException('你所请求的页面不存在.');
        }
        $ret = [
            'title' => "影像学信息补录-" . Html::encode($patientInfo->username),
            'content' => $this->renderAjax('/ump/_upload', [
                'checkRecordModel' => $checkRecordModel,
                'inspectCheckList' => $inspectCheckList,
            ]),
            'footer' => Html::button('关闭', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"])
        ];
        return $ret;
    }

    /**
     * Finds the Patient model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Patient the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Patient::findOne(['id' => $id, 'spot_id' => $this->parentSpotId])) !== null) {
            return $model;
        } else {
            if ($id) {
                throw new NotFoundHttpException('你所请求的页面不存在.');
            } else {
                return new Patient();
            }
        }
    }

}
