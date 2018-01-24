<?php

namespace app\modules\outpatient\models;

use Yii;

/**
 * This is the model class for table "{{%orthodontics_first_record_examination}}".
 *
 * @property integer $id
 * @property integer $spot_id
 * @property integer $record_id
 * @property string $hygiene
 * @property string $periodontal
 * @property string $ulcer
 * @property string $gums
 * @property string $tonsil
 * @property string $frenum
 * @property string $soft_palate
 * @property string $lip
 * @property string $tongue
 * @property integer $dentition
 * @property integer $arch_form
 * @property integer $arch_coordination
 * @property integer $overbite_anterior_teeth
 * @property string $overbite_anterior_teeth_abnormal
 * @property string $overbite_anterior_teeth_other
 * @property integer $overbite_posterior_teeth
 * @property string $overbite_posterior_teeth_abnormal
 * @property string $overbite_posterior_teeth_other
 * @property integer $cover_anterior_teeth
 * @property string $cover_anterior_teeth_abnormal
 * @property integer $cover_posterior_teeth
 * @property string $cover_posterior_teeth_abnormal
 * @property integer $left_canine
 * @property integer $right_canine
 * @property integer $left_molar
 * @property integer $right_molar
 * @property integer $midline_teeth
 * @property string $midline_teeth_value
 * @property integer $midline
 * @property string $midline_value
 * @property integer $create_time
 * @property integer $update_time
 */
class OrthodonticsFirstRecordExamination extends \app\common\base\BaseActiveRecord
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
        return '{{%orthodontics_first_record_examination}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spot_id', 'record_id','overbite_anterior_teeth','overbite_posterior_teeth','cover_anterior_teeth','cover_posterior_teeth','left_canine','right_canine','left_molar','right_molar','midline_teeth','midline'], 'required'],
            [['spot_id', 'record_id', 'dentition', 'arch_form', 'arch_coordination', 'overbite_anterior_teeth', 'overbite_posterior_teeth', 'cover_anterior_teeth', 'cover_posterior_teeth', 'left_canine', 'right_canine', 'left_molar', 'right_molar', 'midline_teeth', 'midline', 'create_time', 'update_time'], 'integer'],
            [['hygiene', 'periodontal', 'ulcer', 'gums', 'tonsil', 'frenum', 'soft_palate', 'lip', 'tongue'], 'string','max' => 1000],
            [['overbite_anterior_teeth_other', 'overbite_posterior_teeth_other'], 'string', 'max' => 30],
            [['midline_teeth_value', 'midline_value'], 'number'],
            [['dentition', 'arch_form', 'arch_coordination', 'create_time', 'update_time'], 'default','value' => 0],
            [['overbite_anterior_teeth_other', 'overbite_posterior_teeth_other','midline_teeth_value', 'midline_value','overbite_anterior_teeth_abnormal','overbite_posterior_teeth_abnormal','cover_anterior_teeth_abnormal','cover_posterior_teeth_abnormal'],'default','value' => ''],
            
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'spot_id' => '诊所id',
            'record_id' => '就诊流水ID',
            'hygiene' => '口腔卫生',
            'periodontal' => '牙周状况',
            'ulcer' => '溃疡',
            'gums' => '牙龈',
            'tonsil' => '扁桃体',
            'frenum' => '舌系带',
            'soft_palate' => '软腭',
            'lip' => '唇系带',
            'tongue' => '舌体',
            'dentition' => '牙列式',
            'arch_form' => '牙弓形态',
            'arch_coordination' => '牙弓协调性',
            'overbite_anterior_teeth' => '覆合前牙',
            'overbite_anterior_teeth_abnormal' => '覆合前牙异常',
            'overbite_anterior_teeth_other' => '前牙其他异常内容',
            'overbite_posterior_teeth' => '覆合后牙',
            'overbite_posterior_teeth_abnormal' => '覆合后牙异常',
            'overbite_posterior_teeth_other' => '后牙其他异常内容',
            'cover_anterior_teeth' => '覆盖前牙',
            'cover_anterior_teeth_abnormal' => '覆盖前牙异常',
            'cover_posterior_teeth' => '覆盖后牙',
            'cover_posterior_teeth_abnormal' => '覆盖后牙异常',
            'left_canine' => '尖牙左侧',
            'right_canine' => '尖牙右侧',
            'left_molar' => '磨牙左侧',
            'right_molar' => '磨牙右侧',
            'midline_teeth' => '牙中线',
            'midline_teeth_value' => '牙中线数值',
            'midline' => '面中线',
            'midline_value' => '面中线数值',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }
    
    /**
     *
     * @var array 牙列式(1-恒牙列，2-乳牙列，3-混合牙列)
     */
    public static $getDentition = [
        1 => '恒牙列',
        2 => '乳牙列',
        3 => '混合牙列'
    ];
    /**
     *
     * @var array 牙弓形态(1-尖圆形，2-卵圆形，3-方圆形)
     */
    public static $getArchForm = [
        1 => '尖圆形',
        2 => '卵圆形',
        3 => '方圆形'
    ];
    
    /**
     * 
     * @var array 牙弓协调性(1-协调，2-不协调)
     */
    public static $getArchCoordination = [
        1 => '协调',
        2 => '不协调'
    ];
    /**
     * 
     * @var array 覆合前牙(1-正常，2-异常)
     */
    public static $getOverbiteAnteriorTeeth = [
        1 => '正常',
        2 => '异常'
    ];
    /**
     * 
     * @var array 覆合前牙异常(1-开合，2-对刃，3-其他)
     */
    public static $getOverbiteAnteriorTeethAbnormal = [
        1 => '开合',
        2 => '对刃',
        3 => '其他'
    ];
    
    /**
     * 
     * @var array 覆合后牙(1-正常，2-异常)
     */
    public static $getOverbitePosteriorTeeth = [
        1 => '正常',
        2 => '异常'
    ];
    
    /**
     * 
     * @var array 覆合后牙异常(1-开合，2-对刃，3-其他)
     */
    public static $getOverbitePosteriorTeethAbnormal = [
        1 => '开合',
        2 => '对刃',
        3 => '其他'
    ];
    
    /**
     * 
     * @var array 覆盖前牙(1-正常，2-异常)
     */
    public static $getCoverAnteriorTeeth = [
        1 => '正常',
        2 => '异常'
    ];
    /**
     * 
     * @var array 覆盖前牙异常(1-深覆盖，2-反合，3-对刃)
     */
    public static $getCoverAnteriorTeethAbnormal = [
        1 => '深覆盖',
        2 => '反合',
        3 => '对刃'
    ];
    
    /**
     * 
     * @var array 覆盖后牙
     */
    public static $getCoverPosteriorTeeth = [
        1 => '正常',
        2 => '异常'
    ];
    
    /**
     * 
     * @var array 覆盖后牙异常(1-反合，2-锁合，3-反锁合)
     */
    public static $getCoverPosteriorTeethAbnormal = [
        1 => '反合',
        2 => '锁合',
        3 => '反锁合'
    ];
    
    /**
     *
     * @var array 左侧尖牙(1-Ⅰ°,2- Ⅱ°,3-Ⅲ°,4-尖对尖)
     */
    public static $getLeftCanine = [
        1 => 'Ⅰ°',
        2 => 'Ⅱ°',
        3 => 'Ⅲ°',
        4 => '尖对尖'
    ];
    /**
     *
     * @var array 右侧尖牙(1-Ⅰ°,2- Ⅱ°,3-Ⅲ°,4-尖对尖)
     */
    public static $getRightCanine = [
        1 => 'Ⅰ°',
        2 => 'Ⅱ°',
        3 => 'Ⅲ°',
        4 => '尖对尖'
    ];
    /**
     *
     * @var array 左侧磨牙(1-Ⅰ°,2- Ⅱ°,3-Ⅲ°,4-尖对尖)
     */
    public static $getLeftMolar = [
        1 => 'Ⅰ°',
        2 => 'Ⅱ°',
        3 => 'Ⅲ°',
        4 => '尖对尖'
    ];
    /**
     * 
     * @var array 右侧磨牙(1-Ⅰ°,2- Ⅱ°,3-Ⅲ°,4-尖对尖)
     */
    public static $getRightMolar = [
        1 => 'Ⅰ°',
        2 => 'Ⅱ°',
        3 => 'Ⅲ°',
        4 => '尖对尖'
    ];
    
    
    
    /**
     * 
     * @var array 牙中线(1-左偏,2-右偏)
     */
    public static $getMidlineTeeth = [
        1 => '左偏',
        2 => '右偏'
    ];
    
    /**
     * 
     * @var array 面中线(1-左偏,2-右偏)
     */
    public static $getMidline = [
        1 => '左偏',
        2 => '右偏'
    ];
    
    public function beforeSave($insert){
        
        if(is_array($this->overbite_anterior_teeth_abnormal)){
            $this->overbite_anterior_teeth_abnormal = implode(',', $this->overbite_anterior_teeth_abnormal);
        }
        if(is_array($this->overbite_posterior_teeth_abnormal)){
            $this->overbite_posterior_teeth_abnormal = implode(',', $this->overbite_posterior_teeth_abnormal);
        }
        if(is_array($this->cover_anterior_teeth_abnormal)){
            $this->cover_anterior_teeth_abnormal = implode(',', $this->cover_anterior_teeth_abnormal);
        }
        if(is_array($this->cover_posterior_teeth_abnormal)){
            $this->cover_posterior_teeth_abnormal = implode(',', $this->cover_posterior_teeth_abnormal);
        }
        return parent::beforeSave($insert);
    }
    
}
