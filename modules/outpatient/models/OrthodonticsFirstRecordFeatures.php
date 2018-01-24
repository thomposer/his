<?php

namespace app\modules\outpatient\models;

use Yii;

/**
 * This is the model class for table "{{%orthodontics_first_record_features}}".
 *
 * @property integer $id
 * @property integer $spot_id
 * @property integer $record_id
 * @property string $dental_age
 * @property string $bone_age
 * @property integer $second_features
 * @property integer $frontal_type
 * @property integer $symmetry
 * @property integer $abit
 * @property integer $face
 * @property integer $smile
 * @property string $smile_other
 * @property integer $upper_lip
 * @property integer $lower_lip
 * @property integer $side
 * @property integer $nasolabial_angle
 * @property integer $chin_lip
 * @property integer $mandibular_angle
 * @property integer $upper_lip_position
 * @property integer $lower_lip_position
 * @property integer $chin_position
 * @property integer $create_time
 * @property integer $update_time
 */
class OrthodonticsFirstRecordFeatures extends \app\common\base\BaseActiveRecord
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
        return '{{%orthodontics_first_record_features}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spot_id', 'record_id'], 'required'],
            [['spot_id', 'record_id', 'second_features', 'frontal_type', 'symmetry', 'abit', 'face', 'smile', 'upper_lip', 'lower_lip', 'side', 'nasolabial_angle', 'chin_lip', 'mandibular_angle', 'upper_lip_position', 'lower_lip_position', 'chin_position', 'create_time', 'update_time'], 'integer'],
            [['dental_age', 'bone_age'],'integer','max' => 100,'min' => 0],
            [['dental_age', 'bone_age'], 'string', 'max' => 32],
            [['smile_other'], 'string', 'max' => 10],
            [['dental_age', 'bone_age','smile_other'],'default','value' => ''],
            [['second_features', 'frontal_type', 'symmetry', 'abit', 'face', 'smile', 'upper_lip', 'lower_lip', 'side', 'nasolabial_angle', 'chin_lip', 'mandibular_angle', 'upper_lip_position', 'lower_lip_position', 'chin_position', 'create_time', 'update_time'],'default','value' => 0]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'spot_id' => '诊所ID',
            'record_id' => '就诊记录ID',
            'dental_age' => '牙龄',
            'bone_age' => '骨龄',
            'second_features' => '第二特征',
            'frontal_type' => '正面型',
            'symmetry' => '对称性',
            'abit' => '唇齿位',
            'face' => '脸型',
            'smile' => '微笑',
            'smile_other' => '其他内容',
            'upper_lip' => '上唇',
            'lower_lip' => '下唇',
            'side' => '侧面型',
            'nasolabial_angle' => '鼻唇角',
            'chin_lip' => '颏唇沟',
            'mandibular_angle' => '下颌角',
            'upper_lip_position' => '上唇位',
            'lower_lip_position' => '下唇位',
            'chin_position' => '颏位',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }
    /**
     * 第二特征(1-无，2-有)
     * @var array
     */
    public static $getSecondFeatures = [
        1 => '无',
        2 => '有'
    ];
    /**
     * 正面型(1-短，2-均，3-长)
     * @var array
     */
    public static $getFrontalType = [
        1 => '短',
        2 => '均',
        3 => '长'
    ];
    /**
     * 对称性(1-对称,2-左侧丰满,3-右侧丰满)
     * @var array
     */
    public static $getSymmetry = [
        
        1 => '对称',
        2 => '左侧丰满',
        3 => '右侧丰满'
    ];
    
    
    /**
     * 唇齿位(1-正常,2-闭唇不全,3-右侧丰满)
     * @var array
     */
    public static $getAbit = [
        1 => '正常',
        2 => '闭唇不全',
        3 => '右侧丰满'
    ];
    /**
     * 脸型(1-方，2-圆，3-长)
     * @var array
     */
    public static $getFace = [
        1 => '方',
        2 => '圆',
        3 => '长'
    ];
    
    /**
     * 微笑(1-正常，2-露龈，3-其他)
     * @var array
     */
    public static $getSmile = [
        1 => '正常',
        2 => '露龈',
        3 => '其他'
    ];
    /**
     * 上唇(1-短，2-肥厚，3-菲薄，4-外翻)
     * @var array
     */
    public static $getUpperLip = [
        1 => '短',
        2 => '肥厚',
        3 => '菲薄',
        4 => '外翻'
    ];
    /**
     * 下唇(1-短，2-肥厚，3-菲薄，4-外翻)
     * @var array
     */
    public static $getLowerLip = [
        1 => '短',
        2 => '肥厚',
        3 => '菲薄',
        4 => '外翻'
    ];
    
    /**
     * 侧面型(1-凹面型，2-直面型，3-凸面型)
     * @var array
     */
    public static $getSide = [
        1 => '凹面型',
        2 => '直面型',
        3 => '凸面型'
    ];
    /**
     * 鼻唇角(1-大，2-小，3-正常)
     * @var array
     */
    public static $getNasolabialAngle = [
        1 => '大',
        2 => '小',
        3 => '正常'
    ];
    /**
     * 
     * @var array 颏唇沟(1-深，2-浅，3-正常)
     */
    public static $getChinLip = [
        1 => '深',
        2 => '浅',
        3 => '正常'
    ];
    
    /**
     * 下颌角(1-钝，2-锐，3-正常)
     * @var array
     */
    public static $getMandibularAngle = [
        1 => '钝',
        2 => '锐',
        3 => '正常'
    ];
    
    
    /**
     *
     * @var array 上唇位(1-前，2-后，3-正常)
     */
    public static $getUpperLipPosition = [
        1 => '前',
        2 => '后',
        3 => '正常'
    ];
    /**
     * 
     * @var array 下唇位(1-前，2-后，3-正常)
     */
    public static $getLowerLipPosition = [
        1 => '前',
        2 => '后',
        3 => '正常'
    ];
    
    /**
     * 
     * @var array 颏位(1-前，2-后，3-正常)
     */
    public static $getChinPosition = [
        1 => '前',
        2 => '后',
        3 => '正常'
    ];
    
    
}
