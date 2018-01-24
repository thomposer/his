<?php

namespace app\modules\spot\models;

use Yii;
use app\common\base\BaseActiveRecord;
use yii\helpers\Html;

/**
 * This is the model class for table "gzh_inspect_item".
 *
 * @property string $id
 * @property string $spot_id
 * @property string $item_name
 * @property string $english_name
 * @property string $unit
 * @property string $reference
 * @property integer $status
 * @property string $create_time
 * @property string $update_time
 */
class InspectItem extends BaseActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'gzh_inspect_item';
    }

    public function init() {
        parent::init();
        $this->spot_id = $this->parentSpotId;  //机构ID
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'spot_id', 'status', 'create_time', 'update_time'], 'integer'],
            [['item_name', 'english_name', 'reference'], 'string', 'max' => 50],
            [['unit'], 'string', 'max' => 20],
            [['item_name', 'spot_id'], 'required'],
            [['item_name'], 'trim'],
            [['item_name'], 'validateInspectItemDuplicate'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'spot_id' => '机构名称',
            'item_name' => '项目名称',
            'english_name' => '英文缩写',
            'unit' => '单位',
            'reference' => '参考值',
            'status' => '状态',
            'create_time' => '创建时间',
            'update_time' => '修改时间',
        ];
    }

    public static $getStatus = [
        '1' => '正常',
        '2' => '停用'
    ];

    public static function getItemList() {
        $list = self::find()->select(['id', 'item_name', 'english_name', 'unit', 'reference'])
                        ->where(['spot_id' => self::$staticParentSpotId, 'status' => 1])
                        ->indexBy('id')
                        ->asArray()->all();
//        if (!empty($list)) {
//            foreach ($list as &$val) {
//                $val['item_name'] = Html::encode($val['item_name']);
//                $val['english_name'] = Html::encode($val['english_name']);
//                $val['unit'] = Html::encode($val['unit']);
//                $val['reference'] = Html::encode($val['reference']);
//            }
//        }
        return $list;
    }

    public function validateItemName($attribute) {
        $parentSpotId = $this->parentSpotId;
        if ($this->isNewRecord) {
            $hasRecord = InspectItem::find()->select(['id'])->where(['spot_id' => $parentSpotId, $attribute => $this->$attribute])->asArray()->limit(1)->one();
            if ($hasRecord) {
                $this->addError($attribute, '该检验项目已存在');
            }
        } else {
            $oldItemName = $this->getOldAttribute('item_name');
            if ($oldItemName != $this->item_name) {
                $hasRecord = $this->checkDuplicate('item_name', $this->item_name);
                if ($hasRecord) {
                    $this->addError('item_name', '该检验项目已存在');
                }
            }
        }
    }

    public function validateInspectItemDuplicate($attribute){
        if ($this->isNewRecord){
            $hasRecord = InspectItem::find()->select(['id'])->where(['spot_id' => $this->parentSpotId, 'item_name' => trim($this->item_name), 'unit' => trim($this->unit), 'reference' => trim($this->reference)])->asArray()->limit(1)->one();
            if($hasRecord){
                $this->addError('item_name','该检验项目已存在');
                $this->addError('unit', '该检验项目已存在');
                $this->addError('reference','该检验项目已存在');
            }
        }else{
            $hasRecord = InspectItem::find()->select(['id'])
                ->where('spot_id = :spot_id and item_name = :item_name and unit = :unit and reference = :reference and id != :id')
                ->addParams([':spot_id'=>$this->parentSpotId, ':item_name'=>trim($this->item_name), ':unit'=>trim($this->unit), ':reference'=>trim($this->reference), ':id'=>$this->id])
                ->asArray()->limit(1)->one();
            if($hasRecord){
                $this->addError('item_name','该检验项目已存在');
                $this->addError('unit', '该检验项目已存在');
                $this->addError('reference','该检验项目已存在');
            }
        }

    }


    protected function checkDuplicate($attribute, $params) {
        $parentSpotId = $this->parentSpotId;
        $hasRecord = InspectItem::find()->select(['id'])->where(['spot_id' => $parentSpotId, $attribute => $params])->asArray()->limit(1)->one();
        if ($hasRecord) {
            return true;
        } else {
            return false;
        }
    }

}
