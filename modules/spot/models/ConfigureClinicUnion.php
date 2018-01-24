<?php

namespace app\modules\spot\models;

use Yii;
use yii\db\Exception;
use app\modules\spot\models\Spot;
use yii\db\Query;

/**
 * This is the model class for table "{{%configure_clinic_union}}".
 *
 * @property integer $id
 * @property string $parent_spot_id
 * @property string $configure_id
 * @property string $spot_id
 * @property integer $type
 * @property string $create_time
 * @property string $update_time
 *
 * @property Spot $spot
 */
class ConfigureClinicUnion extends \app\common\base\BaseActiveRecord
{
    
    public function init() {
        parent::init();
        $this->spot_id = $this->parentSpotId;
    }
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%configure_clinic_union}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_spot_id', 'configure_id', 'spot_id', 'type', 'create_time', 'update_time'], 'integer'],
            [['configure_id', 'spot_id', 'type', 'create_time', 'update_time'], 'required'],
            [['spot_id'], 'exist', 'skipOnError' => true, 'targetClass' => Spot::className(), 'targetAttribute' => ['spot_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_spot_id' => '机构id',
            'configure_id' => '机构-配置表的id',
            'spot_id' => '诊所id',
            'type' => '类型(1-实验室,2-影像学,3-治疗,4-处方,7-其他,8-耗材)',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSpot()
    {
        return $this->hasOne(Spot::className(), ['id' => 'spot_id']);
    }
    
    
    /*
     * 根据条件获取适用诊所
     * return spotIdList
     */
    static public function getClinicIdList($where) {
        return self::find()->select(['spot_id'])->where(['parent_spot_id' => self::$staticParentSpotId])->andWhere($where)->asArray()->all();
    }
    
    /*
     * 根据条件获取适用诊所名称
     * @param $configureId 配置表id列表
     * @param $type 类型
     * return array 
     */
    static public function getClinicNameListString($configureIdList,$type) {
        $query = new Query();
        $query->from(['a' => self::tableName()]);
        $query->select(['configure_id','spotName' => 'group_concat(b.spot_name SEPARATOR "，")']);
        $query->leftJoin(['b' => Spot::tableName()],'{{a}}.spot_id = {{b}}.id');
        $query->where(['a.parent_spot_id' => self::$staticParentSpotId, 'a.configure_id' => $configureIdList,  'a.type' => $type ,'b.status' => 1]);
        $query->groupBy('a.configure_id');
        $query->indexBy('configure_id');
        return $query->all();
    }
    
    /*
     * 保存关联关系
     * @param $configureId 配置表id
     * @param $spotIdList 适用诊所id列表
     * @param $type 类型
     * @return boolean
     */
    static public function saveInfo($configureId,$spotIdList,$type) {
        $spotList = Spot::getSpotList();
        $commonSpotIdList = array_column($spotList, 'id');
        if ( !is_array($spotIdList) || (count(array_diff($spotIdList, $commonSpotIdList)) > 0)) {
            Yii::info("非法参数 configureId[$configureId] type[$type] spotIdList[" . json_encode($spotIdList) . "]", 'configure-clinic-union-save');
            return false;
        }
        
        $dbTrans = Yii::$app->db->beginTransaction();
        try {
            self::deleteAll(['parent_spot_id' => self::$staticParentSpotId,'configure_id' => $configureId,'type' => $type]);
            if (!empty($spotIdList) && is_array($spotIdList)) {
                $rows = [];
                    foreach ($spotIdList as $v) {
                    $rows [] = [self::$staticParentSpotId, $configureId, $v, $type, time(), time()];
                }
                Yii::$app->db->createCommand()->batchInsert(self::tableName(), ['parent_spot_id', 'configure_id', 'spot_id', 'type', 'create_time', 'update_time'], $rows)->execute();
            }
            $dbTrans->commit();
            return true;
        } catch (Exception $e) {

            Yii::error($e->errorInfo,'spot-recipelist-create');
            $dbTrans->rollBack();
            return false;
        }
    }
}
