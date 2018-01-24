<?php

namespace app\modules\message\models;

use app\common\base\BaseActiveRecord;
use Yii;
use app\modules\patient\models\Patient;
use yii\web\ViewAction;
use app\modules\report\models\Report;
use yii\db\Query;
use app\modules\patient\models\PatientRecord;
use app\modules\triage\models\TriageInfo;
use app\modules\outpatient\models\OutpatientRelation;
use app\modules\triage\models\TriageInfoRelation;
use app\modules\outpatient\models\FirstCheck;
use app\modules\outpatient\models\MedicalFile;
use app\modules\outpatient\models\DentalHistory;
use app\modules\outpatient\models\DentalHistoryRelation;

/**
 * This is the model class for table "{{%message_center}}".
 *
 * @property string $id
 * @property string $spot_id
 * @property string $type
 * @property integer $category
 * @property string $content
 * @property string $patient_id
 * @property string $url
 * @property integer $status
 * @property integer $user_id
 * @property integer $room_id
 * @property string $create_time
 * @property string $update_time
 * @property string $record_id
 *
 * @property Patient $patient
 */
class MessageCenter extends BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%message_center}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spot_id', 'patient_id','user_id'], 'required'],
            [['spot_id', 'patient_id', 'status', 'create_time', 'update_time','user_id','room_id','record_id','category'], 'integer'],
            [['type'], 'string', 'max' => 64],
            [['content','url'], 'string', 'max' => 255],
            [['category'],'default','value' => 1],
            [['status'],'default','value' => 0],
            [['patient_id'], 'exist', 'skipOnError' => true, 'targetClass' => Patient::className(), 'targetAttribute' => ['patient_id' => 'id']],
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
            'type' => '消息类型',
            'category' => '消息种类',
            'content' => '消息内容',
            'url'=>'跳转url',
            'user_id'=>'角色id',
            'record_id'=>'流水id',
            'patient_id' => '患者id',
            'room_id'=>'诊室id',
            'status' => '状态(0-未读，1-已读)',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPatient()
    {
        return $this->hasOne(Patient::className(), ['id' => 'patient_id']);
    }

    /**
     * @param $user_id 角色id
     * @param $patient_id 患者id
     * @param $url 页面跳转
     * @param $content 内容
     * @param $type 类型
     * @param int $room_id 诊室id
     * @param int $record_id 流水id
     * @return bool 获取系统通知信息
     */
    public static function saveMessageCenter($user_id,$patient_id,$url,$content,$type,$room_id = 0,$record_id=0){
        if ($type != '待接诊'  || ($messagemodel = MessageCenter::findOne(['spot_id' => self::$staticSpotId ,'user_id' => $user_id ,'patient_id' => $patient_id ,'type' => $type,'content'=>$content,'category' => 1])) === null) {
            $messagemodel = new static();
        }
        $messagemodel->spot_id = self::$staticSpotId;
        $messagemodel->user_id = $user_id;
        $messagemodel->patient_id = $patient_id;
        $messagemodel->room_id = $room_id;
        $messagemodel->type = $type;
        $messagemodel->category = 1;
        $messagemodel->record_id = $record_id;
        $messagemodel->content = $content;
        $messagemodel->url = $url;
        $messagemodel->status = 0;
        $messagemodel->create_time=time();
        $messagemodel->update_time=time();
        $messagemodel->save();
        return true;
    }
    /**
     * 
     * @param integer $user_id 医生id
     * @param number $category 消息种类 1-普通类型，例如待接诊。2-病历未填写
     * @return number|string 返回对应类型的医生的消息数量
     */
    public static function messageNums($user_id,$category = 1){
        $num = self::find()->where(['spot_id' => self::$staticSpotId, 'status' => 0, 'user_id' => $user_id,'category' => $category])->asArray()->count(1);
        return $num;
    }
    
    /**
     * @desc 检查病历是否有填写，若没填写，新增提示信息
     * @param integer $recordId 就诊流水id
     */
    public static function saveMedicalTips($recordId){
        $info = Report::getFieldsList($recordId,['record_type','doctor_id','spot_id','patient_id']);
        if($info['record_type'] == 3){//口腔
            
            $dentalHistoryInfo = DentalHistory::getFieldsList($recordId,['type','chiefcomplaint','historypresent','pasthistory','returnvisit','advice','remarks']);
            $count = DentalHistoryRelation::getCount($recordId, $dentalHistoryInfo['type']);
            if($dentalHistoryInfo['type'] == 1){//初诊
                
                if($dentalHistoryInfo['chiefcomplaint'] == '' && $dentalHistoryInfo['historypresent'] == '' && $dentalHistoryInfo['pasthistory'] == '' && $dentalHistoryInfo['advice'] == '' && $dentalHistoryInfo['remark'] == '' && $count == 0){
                    self::saveInfo($recordId,$info['doctor_id'],$info['spot_id'],$info['patient_id']);
                }
            }else{//复诊
                if($dentalHistoryInfo['returnvisit'] == '' && $dentalHistoryInfo['advice'] == '' && $dentalHistoryInfo['remarks'] == '' && $count == 0){
                    self::saveInfo($recordId,$info['doctor_id'],$info['spot_id'],$info['patient_id']);
                }
            }
            
        }else{//非专科，儿保
            $query = new Query();
            $query->from(['a' => PatientRecord::tableName()]);
            $query->select(['a.id','b.food_allergy','b.meditation_allergy','b.examination_check','b.cure_idea','c.chiefcomplaint','c.historypresent','c.pasthistory','c.personalhistory','c.genetichistory','c.physical_examination','d.pastdraghistory','d.followup']);
            $query->leftJoin(['b' => TriageInfo::tableName()],'{{a}}.id = {{b}}.record_id');
            $query->leftJoin(['c' => OutpatientRelation::tableName()],'{{a}}.id = {{c}}.record_id');
            $query->leftJoin(['d' => TriageInfoRelation::tableName()],'{{a}}.id = {{d}}.record_id');
             
            $query->where(['a.id' => $recordId,'a.spot_id' => $info['spot_id']]);
            $medicalInfo = $query->one();
            $firstCheckCount = FirstCheck::getCount($recordId);
            $fileCount = MedicalFile::getCount($recordId);
            if ($medicalInfo['food_allergy'] == '' && $medicalInfo['meditation_allergy'] == '' && $medicalInfo['examination_check'] == '' && $medicalInfo['cure_idea'] == '' && $medicalInfo['chiefcomplaint'] == '' && $medicalInfo['historypresent'] == ''
                && $medicalInfo['pasthistory'] == ''&& $medicalInfo['personalhistory'] == ''&& $medicalInfo['genetichistory'] == '' && $medicalInfo['physical_examination'] == '' && $medicalInfo['pastdraghistory'] == '' && $medicalInfo['followup'] == '' && $firstCheckCount == 0 && $fileCount == 0){
    
                    self::saveInfo($recordId,$info['doctor_id'],$info['spot_id'],$info['patient_id']);
            }
             
        }
    }
    /**
     *@desc 保存病历未填写 消息种类 记录
     * @param integer $recordId 就诊流水id
     * @param integer $doctorId 医生id
     * @param integer $spotId 诊所id
     * @param integer $patientId 患者id
     */
    public static function saveInfo($recordId,$doctorId,$spotId,$patientId){
    
        $model = self::findOne(['spot_id' => $spotId,'record_id' => $recordId,'category' => 2,'status' => 0]);
        if(!$model){
            $model = new static();
            $model->spot_id = $spotId;
            $model->record_id = $recordId;
            $model->user_id = $doctorId;
            $model->status = 0;
            $model->type = '病历未填写';
            $model->patient_id = $patientId;
            $model->category = 2;
            $model->url = Yii::$app->urlManager->createAbsoluteUrl([Yii::getAlias('@outpatientOutpatientUpdate'),'id' => $recordId]);
            $model->create_time = time();
            $model->update_time = time();
            $model->save();
        }
         
    }
     /**
     * @desc 修改 消息种类 状态为已读
     * @param integer $recordId 就诊流水id
     * @param integer $spotId 诊所id
     * @param integer $category 消息种类，1-普通消息，2-病历未填写
     */
    public static function updateStatus($recordId,$spotId,$category){
        $model = self::findOne(['spot_id' => $spotId,'record_id' => $recordId,'category' => $category]);
        if($model){
            $model->status = 1;
            $model->save();
        }
    }
}

