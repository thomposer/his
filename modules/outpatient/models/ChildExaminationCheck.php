<?php

namespace app\modules\outpatient\models;

use Yii;

/**
 * This is the model class for table "{{%child_examination_check}}".
 * 儿童体检-体格检查
 * @property integer $id
 * @property integer $spot_id
 * @property integer $record_id
 * @property integer $appearance
 * @property string $appearance_remark
 * @property integer $skin
 * @property string $skin_remark
 * @property integer $headFace
 * @property string $headFace_remark
 * @property integer $eye
 * @property string $eye_remark
 * @property integer $ear
 * @property string $ear_remark
 * @property integer $nose
 * @property string $nose_remark
 * @property integer $throat
 * @property string $throat_remark
 * @property integer $tooth
 * @property string $tooth_remark
 * @property string $chest
 * @property string $chest_remark
 * @property integer $bellows
 * @property string $bellows_remark
 * @property integer $cardiovascular
 * @property string $cardiovascular_remark
 * @property integer $belly
 * @property string $belly_remark
 * @property integer $genitals
 * @property string $genitals_remark
 * @property integer $back
 * @property string $back_remark
 * @property integer $limb
 * @property string $limb_remark
 * @property integer $nerve
 * @property string $nerve_remark
 * @property integer $create_time
 * @property integer $update_time
 */
class ChildExaminationCheck extends \app\common\base\BaseActiveRecord
{
    public function init(){
    
        parent::init();
        $this->spot_id = $this->spotId;
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%child_examination_check}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spot_id', 'record_id'], 'required'],
            [['spot_id', 'record_id', 'appearance', 'skin', 'headFace', 'eye', 'ear', 'nose', 'throat', 'tooth', 'bellows', 'cardiovascular', 'belly', 'genitals', 'back', 'limb', 'nerve', 'create_time', 'update_time'], 'integer'],
            [['appearance_remark', 'skin_remark', 'headFace_remark', 'eye_remark', 'ear_remark', 'nose_remark', 'throat_remark', 'tooth_remark', 'bellows_remark', 'cardiovascular_remark', 'belly_remark', 'genitals_remark', 'back_remark', 'limb_remark', 'nerve_remark', 'chest_remark'], 'string', 'max' => 64],
            [['appearance', 'skin', 'headFace', 'eye', 'ear', 'nose', 'throat', 'tooth', 'bellows', 'cardiovascular', 'belly', 'genitals', 'back', 'limb', 'nerve', 'create_time', 'update_time', 'chest'], 'default','value' => 0],
            [['appearance_remark', 'skin_remark', 'headFace_remark', 'eye_remark', 'ear_remark', 'nose_remark', 'throat_remark', 'tooth_remark', 'bellows_remark', 'cardiovascular_remark', 'belly_remark', 'genitals_remark', 'back_remark', 'limb_remark', 'nerve_remark', 'chest_remark'], 'default', 'value' => ''],
            
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '自增id',
            'spot_id' => '诊所id',
            'record_id' => '就诊流水id',
            'appearance' => '1.整体外观',
            'appearance_remark' => '备注',
            'skin' => '2.皮肤',
            'skin_remark' => '备注',
            'headFace' => '3.头/脸部',
            'headFace_remark' => '备注',
            'eye' => '4.眼睛',
            'eye_remark' => '备注',
            'ear' => '5.耳',
            'ear_remark' => '备注',
            'nose' => '6.鼻/鼻窦',
            'nose_remark' => '备注',
            'throat' => '7.口腔/喉',
            'throat_remark' => '备注',
            'tooth' => '8.牙齿',
            'tooth_remark' => '备注',
            'chest' => '9.胸部',
            'chest_remark' => '备注',
            'bellows' => '10.肺部',
            'bellows_remark' => '备注',
            'cardiovascular' => '11.心血管',
            'cardiovascular_remark' => '备注',
            'belly' => '12.腹部',
            'belly_remark' => '备注',
            'genitals' => '13.生殖器',
            'genitals_remark' => '备注',
            'back' => '14.背部',
            'back_remark' => '备注',
            'limb' => '15.四肢',
            'limb_remark' => '备注',
            'nerve' => '16.神经',
            'nerve_remark' => '备注',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }
    /**
     * @return 获取对应的体格检查的类型
     * @var array
     */
    public static $getType = [
//        0 => '未查',
        1 => '无特殊',
//        2 => '需随访',
//        3 => '需转诊',
        4 => '注意或异常项目'
    ];
    
    public static function fromatCheckData(){
        
    }
}
