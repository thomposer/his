<?php

namespace app\modules\outpatient\models;

use Yii;
use app\modules\outpatient\models\DentalHistory;

/**
 * This is the model class for table "{{%dental_history_relation}}".
 *
 * @property integer $id
 * @property string $record_id
 * @property string $spot_id
 * @property string $dental_history_id
 * @property integer $type
 * @property string $position
 * @property string $content
 * @property integer $create_time
 * @property integer $update_time
 *
 * @property DentalHistory $dentalHistory
 */
class DentalHistoryRelation extends \app\common\base\BaseActiveRecord
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
        return '{{%dental_history_relation}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['record_id', 'record_type','spot_id', 'dental_history_id','create_time', 'update_time'], 'integer'],
            [['spot_id', 'dental_history_id', 'content'], 'required'],
            [['dental_history_id'], 'exist', 'skipOnError' => true, 'targetClass' => DentalHistory::className(), 'targetAttribute' => ['dental_history_id' => 'id']],
            [['position'],'default','value'=>''],
            [['dental_disease'],'default','value'=>0],
            [['dental_disease'],'validateDentalDisease'],
            [['position'],'validatePosition'],
            [['type'], 'validateType'],
            [['content'], 'validateContent'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'record_id' => '流水ID',
            'spot_id' => '诊所ID',
            'record_type' => '就诊类型(1-初诊，2-复诊)',
            'dental_history_id' => '口腔病历ID',
            'type' => '就诊类型(1-口腔检查,2-辅助检查,3-诊断,4-治疗方案,5-治疗)',
            'position' => '牙位',
            'content' => '文本内容',
            'dental_disease' => '病症',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDentalHistory()
    {
        return $this->hasOne(DentalHistory::className(), ['id' => 'dental_history_id']);
    }
    
    public static $getType = [
        1 => '口腔检查',
        2 => '辅助检查',
        3 => '诊断',
        4 => '治疗方案',
        5 => '治疗',
    ];
    public static $dentalDisease = [
        1 => '龋齿/缺损',
        2 => '根尖/牙髓',
        3 => '缺失',
        4 => '其他',
    ];
    
    public function validateContent($attribute) {
        if(count($this->content) > 0){
            foreach ($this->content as $key => $value) {
                if(mb_strlen($value) > 1000){
                    $this->addError($attribute, self::$getType[$this->type[$key]].'字数超过1000');
                }
            }
        }
        
    }
    
    public function validateType($attribute) {
        if(count($this->type) > 0){
            foreach ($this->type as $key => $value) {
                if(!array_key_exists($value, self::$getType)){
                    $this->addError($attribute, '检查类型错误');
                }
            }
        }
        
    }
    
    public function validatePosition($attribute){
        if(count($this->position) > 0){
            foreach ($this->position as $value) {
                if($value){
                    $data = explode(',', $value);
                    if(count($data) != 4){
                        $this->addError($attribute, '牙位输入错误');
                        return ;
                    }
                    if($this->validateSingle($data[0],1) || $this->validateSingle($data[1],2) || $this->validateSingle($data[2],2) || $this->validateSingle($data[3],1)){
                        $this->addError($attribute, '牙位输入错误');
                        return ;
                    }
                }
            }
        }
    }

    public function validateDentalDisease($attribute){
        if(count($this->dental_disease) > 0){
            foreach ($this->dental_disease as $key =>  $value) {
                if($this->position[$key] != ''){
                    if($value == 0 || $value ==''){
                        $this->addError($attribute, '牙位病症不能为空');
                    }
                }

                if (!preg_match("/^\s*[+-]?\d+\s*$/", $this->dental_disease[$key]) ) {
                    $this->addError($attribute, '牙位病症错误');
                }

                if ($this->dental_disease[$key] < 0 || $this->dental_disease[$key] > 4) {
                    $this->addError($attribute, '牙位病症错误');
                }
            }
        }

    }

    /*
     * 验证数据是否正确
     * @param $param 单个输入框的值
     * @param $type 数据排序类型 1为左边  2为右边
     * return 1001 输入错误  1002 排序错误
     */
    protected function validateSingle($param,$type) {
        $defaultData = ['1','2','3','4','5','6','7','8','A','B','C','D','E'];
        if (1 == $type) {
            $SortData = [
                '8' => 0, '7' => 0, '6' => 0, '5' => 0, 'E' => 0, '4' => 0, 'D' => 0,
                '3' => 0, 'C' => 0, '2' => 0, 'B' => 0, '1' => 0, 'A' => 0,
            ];
        } else {
            $SortData = [
                'A' => 0, '1' => 0, 'B' => 0, '2' => 0, 'C' => 0, '3' => 0, 'D' => 0,
                '4' => 0, 'E' => 0, '5' => 0, '6' => 0, '7' => 0, '8' => 0,
            ];
        }
        if($param){
            $len = strlen($param);
            for($i = 0; $i < $len; $i++){
                if(!in_array($param[$i], $defaultData)){//数据输入错误
                    return 1001;
                }else if($SortData[$param[$i]] > 0){//输入重复
                    return 1001;
                }
                $SortData[$param[$i]]++;
            }
            $sortString = '';
            foreach ($SortData as $key => $value) {
                if($value > 0){
                    $sortString .= $key;
                }
            }
            if(strcmp($param, $sortString) != 0){//排序不对
                return 1002;
            }
        }
        return 0;
    }
    /**
     * @desc 返回对应就诊流水的病历类型的数量
     * @param integer $recordId 就诊流水id
     * @param integer $recordType 就诊病历类型1-初诊，2-复诊
     */
    public static function getCount($recordId,$recordType){
        
        return self::find()->where(['record_id' => $recordId,'spot_id' => self::$staticSpotId,'record_type' => $recordType])->count(1);
    }
    /**
     * @desc 得到标记了的类型(口腔检查、辅助检查、诊断、治疗方案、治疗)
     * @param integer $recordId 就诊流水id
     * @return 返回标记了的类型(去掉重复的)
     */
    public static function getHasMarkType($recordId){
        return self::find()->select('type')->distinct()->where(['record_id' => $recordId,'spot_id' => self::$staticSpotId])->andWhere(['NOT',['position'=>'']])->asArray()->all();

    }
}
