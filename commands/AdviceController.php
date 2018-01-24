<?php

namespace app\commands;

use app\modules\spot\models\CheckList;
use app\modules\spot\models\Spot;
use app\modules\spot_set\models\CheckListClinic;
use app\modules\spot\models\CureList;
use app\modules\spot_set\models\ClinicCure;
use Yii;
use yii\console\Controller;
use yii\db\Query;
use app\modules\spot\models\Inspect;
use app\modules\spot_set\models\InspectClinic;
use app\modules\outpatient\models\RecipeTemplateInfo;
use app\modules\spot_set\models\RecipelistClinic;
use app\modules\spot\models\RecipeList;
use app\modules\spot_set\models\MedicalFeeClinic;
use app\modules\spot\models\MedicalFee;
use app\modules\spot\models\InspectItemUnion;
use app\modules\spot_set\models\InspectItemUnionClinic;
use app\modules\spot_set\models\Material;
use app\modules\spot\models\CardDiscount;
use app\modules\spot\models\CardRechargeCategory;
use app\modules\outpatient\models\DentalHistory;
use app\modules\outpatient\models\DentalHistoryRelation;
use app\modules\triage\models\TriageInfo;
use app\modules\triage\models\ChildAssessment;
use app\modules\outpatient\models\InspectRecord;
use app\modules\patient\models\Patient;
use app\modules\patient\models\PatientRecord;
use app\modules\outpatient\models\InspectRecordUnion;
use app\modules\spot\models\ConfigureClinicUnion;
use app\modules\charge\models\ChargeInfo;
use app\modules\spot\models\Consumables;

/**
 * This command echoes the first argument that you have entered.
 * This command is provided as an example for you to learn how to create console commands.
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AdviceController extends Controller
{

    public function actionCheckList() {
        //获取所有的机构id
        $parentSpotList = (new Query())
                ->select(["id"])
                ->from(Spot ::tableName())
                ->where(["parent_spot" => 0])
                ->all();

        foreach ($parentSpotList as $key => $value) {
            //获取机构下所有的诊所id
            $spotList = (new Query())
                    ->select(["id"])
                    ->from(Spot ::tableName())
                    ->where(["parent_spot" => $value["id"]])
                    ->all();
            $checkList = (new Query())
                    ->select(["id", "price", "default_price"])
                    ->from(CheckList ::tableName())
                    ->where(["spot_id" => $value["id"]])
                    ->all();
            $params = [];
            foreach ($spotList as $spotKey => $spotValue) {
                foreach ($checkList as $checkKey => $checkValue) {
                    array_push($params, [$spotValue["id"], $checkValue["id"], $checkValue['price'], $checkValue['default_price'], time(), time()]);
                }
            }
            Yii::$app->db->createCommand()
                    ->batchInsert(CheckListClinic ::tableName(), ["spot_id", "check_id", 'price', 'default_price', 'create_time', 'update_time'], $params)
                    ->execute();
        }
    }

    public function actionCureList() {
        //获取所有的机构id
        $parentSpotList = (new Query())
                ->select(["id"])
                ->from(Spot ::tableName())
                ->where(["parent_spot" => 0])
                ->all();

        foreach ($parentSpotList as $key => $value) {
            //获取机构下所有的诊所id
            $spotList = (new Query())
                    ->select(["id"])
                    ->from(Spot ::tableName())
                    ->where(["parent_spot" => $value["id"]])
                    ->all();
            $cureList = (new Query())
                    ->select(["id", "price", "default_price"])
                    ->from(CureList ::tableName())
                    ->where(["spot_id" => $value["id"]])
                    ->all();

            $params = [];
            foreach ($spotList as $spotKey => $spotValue) {
                foreach ($cureList as $cureKey => $cureValue) {
                    array_push($params, [$spotValue["id"], $cureValue["id"], $cureValue["price"], $cureValue["default_price"], time(), time()]);
                }
            }
            Yii::$app->db->createCommand()
                    ->batchInsert(ClinicCure ::tableName(), ["spot_id", "cure_id", "price", "default_price", 'create_time', 'update_time'], $params)
                    ->execute();
        }
    }

    public function actionInspectSave($parentSpotId) {

        $spotIdArray = Spot::find()->select(['id'])->where(['parent_spot' => $parentSpotId])->asArray()->all();
        $inspectList = Inspect::find()->where(['spot_id' => $parentSpotId])->asArray()->all();
        $rows = [];
        if (!empty($inspectList)) {

            foreach ($spotIdArray as $value) {
                foreach ($inspectList as $v) {
                    $rows[] = [$value['id'], $v['id'], $v['inspect_price'], $v['cost_price'], $v['deliver'], $v['specimen_type'], $v['cuvette'], $v['inspect_type'], $v['status'], $v['create_time'], $v['update_time']];
                }
            }

            $db = Yii::$app->db;
            $db->createCommand()->batchInsert(InspectClinic::tableName(), ['spot_id', 'inspect_id', 'inspect_price', 'cost_price', 'deliver', 'specimen_type', 'cuvette', 'inspect_type', 'status', 'create_time', 'update_time'], $rows)->execute();

            $query = new Query();
            $query->from(['a' => InspectClinic::tableName()]);
            $query->select(['a.id', 'a.inspect_id', 'a.spot_id', 'b.item_id', 'b.create_time', 'b.update_time']);
            $query->rightJoin(['b' => InspectItemUnion::tableName()], '{{a}}.inspect_id = {{b}}.inspect_id');

            $clincResult = $query->all();
            if (!empty($clincResult)) {
                $clincRows = [];
                foreach ($clincResult as $value) {
                    if ($value['inspect_id'] && $value['item_id']) {
                        $clincRows[] = [$value['inspect_id'], $value['item_id'], $value['id'], $value['spot_id'], $value['create_time'], $value['update_time']];
                    }
                }
                $db->createCommand()->batchInsert(InspectItemUnionClinic::tableName(), ['inspect_id', 'item_id', 'clinic_inspect_id', 'spot_id', 'create_time', 'update_time'], $clincRows)->execute();
            }
            echo '诊所检验医嘱同步数据为：' . count($rows);
            echo '诊所检验项目同步数据为：' . count($clincRows);
        }
    }

    public function actionSyncClinicRecipeId() {
        //获取所有的处方模版配置
        $recipeTemplateInfoList = (new Query())
                ->select(['id', 'spot_id', 'recipe_id'])
                ->from(RecipeTemplateInfo ::tableName())
                ->all();

        foreach ($recipeTemplateInfoList as $value) {
            $clinicRecipe = (new Query())
                    ->select(['id'])
                    ->from(RecipelistClinic ::tableName())
                    ->where(['spot_id' => $value['spot_id'], 'recipelist_id' => $value['recipe_id']])
                    ->one();
            if ($clinicRecipe) {
                Yii::$app->db->createCommand()->update(RecipeTemplateInfo::tableName(), ['clinic_recipe_id' => $clinicRecipe['id']], ['id' => $value['id']])->execute();
            }
        }
    }

    public function actionRecipeList() {


        //获取所有的机构id
        $parentSpotList = (new Query())
                ->select(["id"])
                ->from(Spot::tableName())
                ->where(["parent_spot" => 0])
                ->all();

        foreach ($parentSpotList as $key => $value) {
            //获取机构下所有的诊所id
            $spotList = (new Query())
                    ->select(["id"])
                    ->from(Spot ::tableName())
                    ->where(["parent_spot" => $value["id"]])
                    ->all();
            $recipeList = (new Query())
                    ->select(["id", "price", "default_price", 'address', 'status'])
                    ->from(RecipeList::tableName())
                    ->where(["spot_id" => $value["id"]])
                    ->all();
            $params = [];
            foreach ($spotList as $spotKey => $spotValue) {
                foreach ($recipeList as $checkKey => $checkValue) {
                    array_push($params, [$spotValue["id"], $checkValue["id"], $checkValue['price'], $checkValue['default_price'], $checkValue['address'], $checkValue['status'], time(), time()]);
                }
            }
            Yii::$app->db->createCommand()
                    ->batchInsert(RecipelistClinic::tableName(), ["spot_id", "recipelist_id", 'price', 'default_price', 'address', 'status', 'create_time', 'update_time'], $params)
                    ->execute();
        }
    }

    public function actionMedicalFee() {
        //获取所有的机构id
        $parentSpotList = (new Query())
                ->select(["id"])
                ->from(Spot::tableName())
                ->where(["parent_spot" => 0])
                ->all();

        foreach ($parentSpotList as $key => $value) {
            //获取机构下所有的诊所id
            $spotList = (new Query())
                    ->select(["id"])
                    ->from(Spot ::tableName())
                    ->where(["parent_spot" => $value["id"]])
                    ->all();
            $checkList = (new Query())
                    ->select(["id", "status"])
                    ->from(MedicalFee::tableName())
                    ->where(["spot_id" => $value["id"]])
                    ->all();
            $params = [];
            if (empty($checkList)) {
                continue;
            }
            foreach ($spotList as $spotKey => $spotValue) {
                foreach ($checkList as $checkKey => $checkValue) {
                    $params [] = [$spotValue["id"], $checkValue["id"], $checkValue['status'], time(), time()];
                }
            }
//             MedicalFeeClinic::deleteAll();
            Yii::$app->db->createCommand()
                    ->batchInsert(MedicalFeeClinic::tableName(), ["spot_id", "fee_id", 'status', 'create_time', 'update_time'], $params)
                    ->execute();
        }
    }

    /**
     * advice/material
     * @desc 将其他配置由机构转移到相应的诊所下
     */
    public function actionMaterial() {
        //获取所有的机构id
        $parentSpotList = (new Query())
                ->select(['id', 'spot_name'])
                ->from(Spot::tableName())
                ->where(["parent_spot" => 0])
                ->all();
        $dbTrans = Yii::$app->db->beginTransaction();
        try {
            foreach ($parentSpotList as $key => $value) {
                //获取机构下所有的诊所id
                $spotList = (new Query())
                        ->select(['id', 'spot_name'])
                        ->from(Spot ::tableName())
                        ->where(["parent_spot" => $value["id"]])
                        ->all();
                $materialList = (new Query())
                        ->from(Material::tableName())
                        ->where(["spot_id" => $value["id"]])
                        ->all();
                $params = [];
                if (empty($materialList)) {
                    continue;
                }
                foreach ($spotList as $spotKey => $spotValue) {
                    foreach ($materialList as $mKey => $mValue) {
                        $params [] = [$spotValue["id"], $mValue["product_number"], $mValue['name'], $mValue['product_name'], $mValue['en_name'], $mValue['type'], $mValue['attribute'],
                            $mValue['specification'], $mValue['unit'], $mValue['price'], $mValue['default_price'], $mValue['meta'], $mValue['manufactor'], $mValue['warning_num'], $mValue['warning_day'],
                            $mValue['remark'], $mValue['status'], $mValue['tag_id'], time(), time()];
                    }
                }
                echo 'spotCount:' . count($spotList) . PHP_EOL;
                echo "parentSpot:[{$value['id']}] [{$value['spot_name']}] need insert :" . count($params) . PHP_EOL;
                $insertCount = Yii::$app->db->createCommand()
                        ->batchInsert(Material::tableName(), ["spot_id", "product_number", 'name', 'product_name', 'en_name', 'type', 'attribute', 'specification', 'unit', 'price',
                            'default_price', 'meta', 'manufactor', 'warning_num', 'warning_day', 'remark', 'status', 'tag_id', 'create_time', 'update_time'], $params)
                        ->execute();
                echo "inserted:" . $insertCount . PHP_EOL;
                //删除原机构下的数据
                $delNum = Material::deleteAll(['spot_id' => $value["id"]]);
                echo "deleted:" . $delNum . PHP_EOL;
                sleep(2);
            }
            $dbTrans->commit();
        } catch (Exception $e) {
            $dbTrans->rollBack();
            echo 'save error' . json_encode($e->errorInfo, true) . PHP_EOL;
        }
        exit();
    }

    /**
     * advice/card-discount
     * @desc 将其卡中心配置的折扣由机构转移到相应的诊所下   需要在机构下新增一条诊金折扣
     */
    public function actionCardDiscount() {
        //获取所有的卡种
        $catList = CardRechargeCategory::find()->select(['f_physical_id', 'f_spot_id'])->where(['>', 'f_parent_id', 0])->asArray()->all();
        foreach ($catList as $val) {
            $row[] = [$val['f_spot_id'], $val['f_physical_id'], 0, time(), time()];
        }
        echo 'spotCount:' . count($row) . PHP_EOL;
        $insertCount = Yii::$app->get('cardCenter')->createCommand()
                ->batchInsert(CardDiscount::tableName(), ["spot_id", "recharge_category_id", 'tag_id', 'create_time', 'update_time'], $row)
                ->execute();
        echo "inserted:" . $insertCount . PHP_EOL;
    }

    public function actionSyncDentalRecordData() {
        //获取所有的口腔病历数据
        $db = Yii::$app->db;
        $recordList = DentalHistory::find()->select(['id', 'record_id', 'type', 'spot_id'])->asArray()->all();
        foreach ($recordList as $value) {
            //删除多余的数据
            $db->createCommand()->delete(DentalHistoryRelation::tableName(), 'record_type <> :record_type AND dental_history_id = :dental_history_id AND record_id = :record_id AND spot_id = :spot_id', [
                ':record_type' => $value['type'],
                ':dental_history_id' => $value['id'],
                ':record_id' => $value['record_id'],
                ':spot_id' => $value['spot_id']
            ])->execute();
        }
    }

    /**
     * @action advice/sync-assessment
     * @desc 修复患者疼痛/跌倒评估 数据
     */
    public function actionSyncAssessment() {
        $data = TriageInfo::find()->select(['record_id', 'spot_id', 'pain_score', 'fall_score'])->where(' pain_score>=0 OR fall_score>=0 ')->asArray()->all();
        $row = [];
        foreach ($data as $key => $value) {
            if ($value['pain_score'] != NULL && $value['pain_score'] >= 0) {
                $row[] = [$value['spot_id'], $value['record_id'], $value['pain_score'], 1, time(), time()];
            }
            if ($value['fall_score'] != NULL && $value['fall_score'] >= 0) {
                $row[] = [$value['spot_id'], $value['record_id'], $value['fall_score'], 2, time(), time()];
            }
        }
        echo 'dataCount:' . count($data) . PHP_EOL;
        echo 'spotCount:' . count($row) . PHP_EOL;
        $insertCount = Yii::$app->db->createCommand()
                ->batchInsert(ChildAssessment::tableName(), ["spot_id", "record_id", 'score', 'type', 'create_time', 'update_time'], $row)
                ->execute();
        echo "inserted:" . $insertCount . PHP_EOL;
    }

    /*
     * 修复套餐实验室医嘱价格问题
     */
    public function actionSyncInspectRecordPrice(){
        $db = Yii::$app->db;
        $data = InspectRecord::find()->select(['id', 'record_id', 'spot_id', 'inspect_id', 'name'])->where('package_record_id <> 0')->asArray()->all();
        foreach ($data as $value) {
            $inspectPrice = (new Query())
                        ->select(['inspect_price'])
                        ->from(InspectClinic ::tableName())
                        ->where(['spot_id' => $value['spot_id'], 'inspect_id' => $value['inspect_id']])
                        ->one();
            $update = $db->createCommand()->update(InspectRecord::tableName(), ['price' => $inspectPrice['inspect_price']], ['spot_id' => $value['spot_id'], 'id' => $value['id']]);
            echo $update->getRawSql().PHP_EOL;
            $update->execute();
        }
    }


    /**
     * @action advice/inspect-deliver
     * @desc 修复检验配置  外送数据
     */
    public function actionInspectDeliver() {
        $origin = [
            [
                'patient_number' => '0000001',
                'spot_id' => 60,
                'name' => '尿常规',
                'deliver_organization' => 2
            ],
            [
                'patient_number' => '0000001',
                'spot_id' => 60,
                'name' => '尿常规',
                'deliver_organization' => 2
            ],
            [
                'patient_number' => '0000001',
                'spot_id' => 60,
                'name' => '尿常规',
                'deliver_organization' => 2
            ],
        ];
        $rows = 0;
        foreach ($origin as $val) {
            $data = (new Query())->from(['a' => InspectRecord::tableName()])
                    ->select(['a.id'])
                    ->leftJoin(['b' => InspectItemUnion::tableName()], '{{a}}.id={{b}}.inspect_record_id')
                    ->leftJoin(['c' => PatientRecord::tableName()], '{{c}}.id={{a}}.record_id')
                    ->leftJoin(['d' => Patient::tableName()], '{{c}}.patient_id={{d}}.id')
                    ->where(['d.patient_number' => $val['patient_number'], 'd.spot_id' => $val['spot_id'], 'b.name' => $val['name'], 'a.deliver' => 1])
                    ->all();
            print_r($data);
            exit;
            if (!empty($data)) {
                $id = $data['id'];
                $res = InspectRecord::updateAll(['deliver_organization' => $val['deliver_organization']], ['id' => $id]);
                $rows+=$res;
            }
            exit;
        }
        echo "updated:" . $rows . PHP_EOL;
    }


    /**
     * 修复疼痛评分数据显示问题
     */
    public function actionRepairScore(){

            $query = new Query();
            $query->from(['a' => ChildAssessment::tableName()]);
            $query->select(['id','record_id','score','assesment_time','type']);
            $result = $query->all();
            $rows = [];
            if(!empty($result)){
                foreach ($result as $v){
                    if($v['type'] == 1){
                        $rows[$v['record_id']]['pain_score'] = $v['score'];

                    }else{
                        $rows[$v['record_id']]['fall_score'] = $v['score'];
                    }
                }
                foreach ($rows as $key => $value){
                    $updateRows = [];
                    TriageInfo::updateAll($value,['record_id' => $key]);
                }
            }
            var_dump($rows);
            var_dump($result);

    }


    /*
     * 同步机构下处方医嘱与适用诊所关联
     */
    public function actionSyncRecipeClinicUnion(){
        ConfigureClinicUnion::deleteAll(['type' => ChargeInfo::$recipeType]);
        $parentSpotList = Spot::find()->select(['id'])->where(['type' => 1, 'parent_spot' => 0])->asArray()->all();

        foreach ($parentSpotList as $value) {
            $spotIdList = Spot::find()->select(['id','parent_spot'])->where(['type' => 2, 'parent_spot' => $value['id']])->andWhere('parent_spot <> 0')->asArray()->all();

            $configureList = RecipeList::find()->select(['id'])->where(['spot_id' => $value['id']])->asArray()->all();
            $rows = [];
            foreach ($configureList as $configureInfo) {
                foreach ($spotIdList as $spotInfo) {
                    $rows [] = [$value['id'], $configureInfo['id'], $spotInfo['id'], ChargeInfo::$recipeType, time(), time()];
                }
            }
            $insertCount = Yii::$app->db->createCommand()->batchInsert(ConfigureClinicUnion::tableName(), ['parent_spot_id', 'configure_id', 'spot_id', 'type', 'create_time', 'update_time'], $rows)->execute();
            echo 'recipeList:' . count($configureList) . PHP_EOL;
            echo 'spotIdList:' . count($spotIdList) . PHP_EOL;
            echo 'inserted:' . $insertCount . PHP_EOL;
        }
    }

    /*
     * 同步机构下影像学检查与适用诊所关联
     */
    public function actionSyncCheckClinicUnion(){
        ConfigureClinicUnion::deleteAll(['type' => ChargeInfo::$checkType]);
        $parentSpotList = Spot::find()->select(['id'])->where(['type' => 1, 'parent_spot' => 0])->asArray()->all();

        foreach ($parentSpotList as $value) {
            $spotIdList = Spot::find()->select(['id','parent_spot'])->where(['type' => 2, 'parent_spot' => $value['id']])->andWhere('parent_spot <> 0')->asArray()->all();

            $configureList = CheckList::find()->select(['id'])->where(['spot_id' => $value['id']])->asArray()->all();
            $rows = [];
            foreach ($configureList as $configureInfo) {
                foreach ($spotIdList as $spotInfo) {
                    $rows [] = [$value['id'], $configureInfo['id'], $spotInfo['id'], ChargeInfo::$checkType, time(), time()];
                }
            }
            $insertCount = Yii::$app->db->createCommand()->batchInsert(ConfigureClinicUnion::tableName(), ['parent_spot_id', 'configure_id', 'spot_id', 'type', 'create_time', 'update_time'], $rows)->execute();
            echo 'checkList:' . count($configureList) . PHP_EOL;
            echo 'spotIdList:' . count($spotIdList) . PHP_EOL;
            echo 'inserted:' . $insertCount . PHP_EOL;
        }
    }


    /*
     * 同步机构下治疗医嘱与适用诊所关联
     */
    public function actionSyncCureClinicUnion(){
        ConfigureClinicUnion::deleteAll(['type' => ChargeInfo::$cureType]);
        $parentSpotList = Spot::find()->select(['id'])->where(['type' => 1, 'parent_spot' => 0])->asArray()->all();

        foreach ($parentSpotList as $value) {
            $spotIdList = Spot::find()->select(['id','parent_spot'])->where(['type' => 2, 'parent_spot' => $value['id']])->andWhere('parent_spot <> 0')->asArray()->all();

            $configureList = CureList::find()->select(['id'])->where(['spot_id' => $value['id']])->asArray()->all();
            $rows = [];
            foreach ($configureList as $configureInfo) {
                foreach ($spotIdList as $spotInfo) {
                    $rows [] = [$value['id'], $configureInfo['id'], $spotInfo['id'], ChargeInfo::$cureType, time(), time()];
                }
            }
            $insertCount = Yii::$app->db->createCommand()->batchInsert(ConfigureClinicUnion::tableName(), ['parent_spot_id', 'configure_id', 'spot_id', 'type', 'create_time', 'update_time'], $rows)->execute();
            echo 'cureList:' . count($configureList) . PHP_EOL;
            echo 'spotIdList:' . count($spotIdList) . PHP_EOL;
            echo 'inserted:' . $insertCount . PHP_EOL;
        }
    }

    /*
    * 同步机构下实验室检查与适用诊所关联
    */
    public function actionSyncInspectClinicUnion(){
        ConfigureClinicUnion::deleteAll(['type' => ChargeInfo::$inspectType]);
        $parentSpotList = Spot::find()->select(['id'])->where(['type' => 1, 'parent_spot' => 0])->asArray()->all();

        foreach ($parentSpotList as $value) {
            $spotIdList = Spot::find()->select(['id','parent_spot'])->where(['type' => 2, 'parent_spot' => $value['id']])->andWhere('parent_spot <> 0')->asArray()->all();

            $configureList = Inspect::find()->select(['id'])->where(['spot_id' => $value['id']])->asArray()->all();
            $rows = [];
            foreach ($configureList as $configureInfo) {
                foreach ($spotIdList as $spotInfo) {
                    $rows [] = [$value['id'], $configureInfo['id'], $spotInfo['id'], ChargeInfo::$inspectType, time(), time()];
                }
            }
            $insertCount = Yii::$app->db->createCommand()->batchInsert(ConfigureClinicUnion::tableName(), ['parent_spot_id', 'configure_id', 'spot_id', 'type', 'create_time', 'update_time'], $rows)->execute();
            echo 'inspectList:' . count($configureList) . PHP_EOL;
            echo 'spotIdList:' . count($spotIdList) . PHP_EOL;
            echo 'inserted:' . $insertCount . PHP_EOL;
        }
    }
    
    /*
     * 同步机构下医疗耗材与适用诊所关联
     */
    public function actionSyncConsumableClinicUnion(){
        ConfigureClinicUnion::deleteAll(['type' => ChargeInfo::$consumablesType]);
        $parentSpotList = Spot::find()->select(['id'])->where(['type' => 1, 'parent_spot' => 0])->asArray()->all();
        
        foreach ($parentSpotList as $value) {
            $spotIdList = Spot::find()->select(['id','parent_spot'])->where(['type' => 2, 'parent_spot' => $value['id']])->andWhere('parent_spot <> 0')->asArray()->all();
            
            $configureList = Consumables::find()->select(['id'])->where(['spot_id' => $value['id']])->asArray()->all();
            $rows = [];
            foreach ($configureList as $configureInfo) {
                foreach ($spotIdList as $spotInfo) {
                    $rows [] = [$value['id'], $configureInfo['id'], $spotInfo['id'], ChargeInfo::$consumablesType, time(), time()];
                }
            }
            $insertCount = Yii::$app->db->createCommand()->batchInsert(ConfigureClinicUnion::tableName(), ['parent_spot_id', 'configure_id', 'spot_id', 'type', 'create_time', 'update_time'], $rows)->execute();
            echo 'Consumables:' . count($configureList) . PHP_EOL;
            echo 'spotIdList:' . count($spotIdList) . PHP_EOL;
            echo 'inserted:' . $insertCount . PHP_EOL;
        }
    }
}
