<?php

namespace app\modules\charge\models;

use app\modules\spot_set\models\Material;
use app\modules\stock\models\MaterialStockInfo;


class MaterialCharge extends Material
{
    public $stockNum;//库存数量
    public $num;//数量
    public $deleted;
    public $stockId;
    public $isNewRecord;//是否是新增数据
    public $chargeInfoId;//待收费记录id
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['num','remark'],'validateCreateMaterial','skipOnEmpty' => false],
            [['stockNum','deleted','stockId','isNewRecord','chargeInfoId'],'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '名称',
            'specification' => '规格',
            'unit' => '包装单位',
            'price' => '零售价',
            'remark' => '备注',
            'num' => '数量',
            'stockNum' => '库存'
        ];
    }
    
    public function validateCreateMaterial($attribute,$params){
        if(!$this->hasErrors()){
            $rows = [];
            if(count($this->stockId)){
                foreach ($this->stockId as $key => $v){
                    if($this->deleted[$key] != 1){
                     
                        if($this->num[$key] == ''){
                            $this->addError('num','数量不能为空');
                        }else if (!preg_match("/^\s*[+-]?\d+\s*$/", $this->num[$key]) || !isset($this->num[$key])) {
                            $this->addError('num', '数量必须为一个数字');
                        }else if($this->num[$key]  <= 0 || $this->num[$key] > 100){
                            $this->addError('num', '数量必须在1~100范围内');
                        }else if($this->remark[$key] != '' && mb_strlen($this->remark[$key]) > 100){
                            $this->addError('num', '备注不能多于100个字符');
                        }
                        $attributeValue = Material::getOneInfo($this->stockId[$key],['id','attribute'],['status' => 1])['attribute'];
                        if($attributeValue && $attributeValue == 2){
                            $rows[$this->stockId[$key]][] = $this->num[$key];
                        }else if(!$attributeValue){
                            $this->addError('num','该记录已被禁用/删除');
                        }
                    }
                    if($this->chargeInfoId[$key] != ''){
                        $record = ChargeInfo::find()->select(['id'])->where(['id' => $this->chargeInfoId[$key],'spot_id' => $this->spotId,'status' => [1,2]])->asArray()->one();
                        if(!empty($record)){
                            $this->addError('num','存在已经收费或退费的项目');
                        }
                    }
                }
                
//                if (!empty($rows)) {//需要record_id 暂时不做验证，在逻辑层做判断
//                    $materialIdList = array_keys($rows);
//                    $totalNum = MaterialStockInfo::getTotal($materialIdList);
//                    foreach ($rows as $key => $v){
//                        if(array_sum($v) > $totalNum[$key]){
//                            $this->addError('num', '数量不能大于库存数量');
//                        }
//                    }
//                
//                }
            }else{
                $this->addError('num','请选择其他收费');
            }
        }
    }
    
    public static function getMaterialChargeInfo($id){
        
    }
}
