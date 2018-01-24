<?php

namespace app\modules\user\models;

use Yii;
use app\common\base\BaseActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;
use app\modules\spot\models\Spot;
use app\modules\user\models\UserSpot;
use app\modules\spot_set\models\SecondDepartment;
use yii\base\Object;
use yii\db\Query;
use yii\helpers\Json;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use app\modules\spot_set\models\SecondDepartmentUnion;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property integer $id
 * @property string $username 用户名
 * @property string $email 邮箱
 * @property string $card 身份证号
 * @property string $head_img 头像
 * @property string $introduce 个人介绍
 * @property string $auth_key
 * @property string $password_hash 密码
 * @property string $password_reset_token 重置密码token
 * @property string $role
 * @property integer $spot_id 机构id
 * @property integer $default_spot 默认诊所id
 * @property integer $status 状态
 * @property integer $create_time 创建时间
 * @property integer $update_time 更新时间
 * @property integer $sex 性别
 * @property integer $iphone 手机号码
 * @property integer $occupation 职位
 * @property integer $occupation_type 职位性质(1-全职,2-半全职,3-兼职)
 * @property integer $position_title 职称
 * @property date $birthday 出生日期
 * @property string  $expire_time 重置密码token有效期
 * @property AuthAssignment[] $authAssignments
 */
class User extends BaseActiveRecord implements IdentityInterface
{

    public $password; //重置密码
    public $reType_password; //确认重置密码
    public $oldPassword; //修改密码－旧密码
    public $clinic_id; //诊所
    public $department; //科室
    public $role; //角色
    public $code; //验证码
    public $code_btn; //验证码
    public $code_error;
    public $doctor_name;
    public $department_name;
    public $doctor_id;
    public $clinic_name;//所属诊所名称
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['code', 'oldPassword', 'password_hash', 'password_reset_token', 'spot_id', 'username', 'email', 'password', 'reType_password', 'sex', 'birthday', 'iphone', 'occupation', 'occupation_type'], 'required'],
            ['reType_password', 'compare', 'compareAttribute' => 'password', 'message' => '与第一次密码输入不符合,请重新输入'],
            ['email', 'email'],
            [['id', 'spot_id', 'code', 'status', 'create_time', 'update_time', 'occupation', 'occupation_type', 'position_title'], 'integer'],
            ['position_title','default','value'=>0],
            [['username', 'auth_key', 'head_img'], 'string', 'max' => 64],
            [['password_hash', 'password_reset_token', 'introduce'], 'string', 'max' => 255],
            [['birthday'], 'date', 'max' => date('Y-m-d')],
            ['card', 'match', 'pattern' => '/^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/'],
            [['iphone'], 'match', 'pattern' => '/^\d{11}$/'],
            [['expire_time'], 'safe'],
            [['username', 'email', 'introduce'], 'trim'],
            [['iphone', 'email'], 'validateIphoneEmail', 'on' => 'register'],
            ['oldPassword', 'validateOldPassword'],
            ['clinic_id', 'validateClinic'],
            ['department', 'validateDepartment'],
            ['code', 'validatecode', 'on' => 'resetPassword'],
            ['password', 'match', 'pattern' => '/(?!^[0-9]+$)(?!^[A-z]+$)(?!^[^A-z0-9]+$)^[^\s]{8,20}$/', 'message' => '密码不符合要求'],
        ];
    }

    public function scenarios() {

        $parent = parent::scenarios();
        $parent['login'] = ['username', 'password'];
        $parent['register'] = ['username', 'spot_id', 'email', 'birthday', 'sex', 'iphone', 'card', 'occupation', 'occupation_type', 'position_title', 'introduce', 'head_img', 'clinic_id', 'department', 'role', 'status'];
        $parent['resetPassword'] = ['password_reset_token', 'password', 'reType_password', 'code']; //重置密码场景
        $parent['resetSave'] = ['expire_time', 'id'];
        $parent['editPassword'] = ['password', 'oldPassword', 'reType_password']; //修改密码
        $parent['registerSystem'] = ['username', 'email', 'iphone', 'spot_id']; //添加机构管理员人员信息场景
        $parent['delete'] = ['username', 'status']; //删除场景
        return $parent;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'username' => '用户名',
            'email' => '邮箱',
            'auth_key' => 'Auth_Key',
            'oldPassword' => '旧密码',
            'password_hash' => '密码',
            'password' => '新密码',
            'reType_password' => '确认新密码',
            'password_reset_token' => 'password_reset_token',
            'spot_id' => '机构ID',
            'clinic_id' => '所属诊所',
            'department' => '科室',
            'status' => '状态',
            'birthday' => '出生日期',
            'iphone' => '手机号码',
            'card' => '身份证号码',
            'occupation' => '职位',
            'occupation_type' => '职位性质',
            'position_title' => '职称',
            'introduce' => '个人介绍',
            'head_img' => '头像',
            'sex' => '性别',
            'role' => '角色',
            'code' => '验证码',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'pushpassword' => '输入密码',
            'surepassword' => '确认密码',
            'iphone_code' => '手机验证码',
            'department_name' => '所在科室',
            'doctor_name' => '医生姓名',
            'appointment_status' => '开放预约',
            'clinic_name' => '所属诊所',
        ];
    }

    /**
     * @var 性别
     */
    public static $getSex = [
        1 => '男',
        2 => '女',
//        3 => '不详',
//        4 => '其他',
    ];

    /**
     * 
     * @var 职称
     */
    public static $getPositionTitle = [
        1 => '医师',
        2 => '主治医师',
        3 => '副主任医师',
        4 => '主任医师',
        5 => '护士',
        6 => '护师',
        7 => '主管护师',
        8 => '副主任护师',
        9 => '主任护师',
        10 => '初级药士',
        11 => '初级药师',
        12 => '中级主管药师',
        13 => '副主任药剂师',
        14 => '主任药剂师',
        15 => '初级检验技士',
        16 => '初级检验技师',
        17 => '检验主管技师',
        18 => '副主任检验师',
        19 => '主任检验师',
        20 => '初级放射技士',
        21 => '初级放射技师',
        22 => '放射主管技师',
        23 => '副主任放射技师',
        24 => '主任放射技师',
    ];

    /**
     * @var 职位性质
     */
    public static $getOccupationType = [
        1 => '全职',
        2 => '兼职',
    ];

    /**
     * @var 职位
     */
    public static $getOccuption = [
        1 => '管理员',
        2 => '医生',
        3 => '护士',
        4 => '前台',
        5 => '检验技师',
        6 => '检查技师',
        7 => '药剂师',
        8 => '行政',
        10 => '健康顾问',
        9 => '其他',
    ];
    public static $getStatus = [
        1 => '正常',
        2 => '停用',
    ];
    public static $getDepartment = [
        1 => '儿保科',
        2 => '全科',
    ];

    /**
     * 
     * @param unknown $attribute 字段属性
     * @param unknown $params 
     */
    public function validateOldPassword($attribute, $params) {
        if (!$this->hasErrors()) {
            $user = self::findIdentity(self::getId());

            if (!self::validatePassword($this->oldPassword, $user->password_hash)) {
                $this->addError($attribute, '密码错误.');
            }
        }
    }

    public function validateClinic($attribute, $params) {
        if (!$this->hasErrors()) {
            $oldCount = count($this->$attribute);
            /*if ($oldCount >= 2) {
                if (count(array_filter($this->$attribute)) != count(array_filter(array_unique($this->$attribute)))) {
                    echo count(array_filter($this->$attribute)) . '**' . count(array_filter(array_unique($this->$attribute)));
                    $this->addError($attribute, '选择诊所不能重复');
                }
            }*/
            if ($oldCount != count(array_filter($this->$attribute))) {
                $this->addError($attribute, '选择诊所不能为空');
            }
        }
    }

    public function validateDepartment($attribute, $params) {
        if (!$this->hasErrors()) {
            foreach ($this->clinic_id as $key => $value){
                $depart = $this->department[$key]?$this->department[$key]:0;
                if(isset($departs[$value][$depart])){
                    $this->addError($attribute, '所属诊所和科室重复，请重新选择'); //重复
                    continue;
                }
                $departs[$value][$depart] = $depart;
            }
        }
    }

    public function validateIphoneEmail($attribute, $params) {
        if (!$this->hasErrors()) {
            if ($this->isNewRecord) {
//                $hasRecord = User::find()->select(['id'])->where(['spot_id' => $_COOKIE['parentSpotId'], $attribute => $this->$attribute])->asArray()->limit(1)->one();
                //检测手机号是否存在
                $hasIphoneRecord = $this->checkDuplicate('iphone', $this->iphone);
                if ($hasIphoneRecord) {
                    $this->addError('iphone', $this->attributeLabels()['iphone'] . '已经被占用');
                }
                //检测email是否存在
                $hasEmailRecord = $this->checkDuplicate('email', $this->email);
                if ($hasEmailRecord) {
                    $this->addError('email', $this->attributeLabels()['email'] . '已经被占用');
                }
            } else {
                $oldIphone = $this->getOldAttribute('iphone');
                $oldEmail = $this->getOldAttribute('email');
                if ($oldIphone != $this->iphone) {
                    $hasRecord = $this->checkDuplicate('iphone', $this->iphone);
                    if ($hasRecord) {
                        $this->addError('iphone', $this->attributeLabels()['iphone'] . '已经被占用');
                    }
                }
                if ($oldEmail != $this->email) {
                    $hasRecord = $this->checkDuplicate('email', $this->email);
                    if ($hasRecord) {
                        $this->addError('email', $this->attributeLabels()['email'] . '已经被占用');
                    }
                }
            }
        }
    }

    /**
     * 
     * @param 手机号／邮箱 $attribute
     * @param unknown $params
     * @return 查看手机号／邮箱在机构下是否存在
     */
    protected function checkDuplicate($attribute, $params) {

        $hasRecord = User::find()->select(['id'])->where(['spot_id' => $this->parentSpotId, $attribute => $params])->asArray()->limit(1)->one();
        if ($hasRecord) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 根据给到的ID查询身份。
     *
     * @param string|integer $id 被查询的ID
     * @return IdentityInterface|null 通过ID匹配到的身份对象
     */
    public static function findIdentity($id) {
        return static::find()->select(['id', 'username', 'spot_id', 'default_spot', 'email', 'password_hash', 'auth_key', 'head_img'])->where(['id' => $id])->one();
    }

    /**
     * 根据 token 查询身份。
     *
     * @param string $token 被查询的 token
     * @return IdentityInterface|null 通过 token 得到的身份对象
     */
    public static function findIdentityByAccessToken($token, $type = null) {
        return null;
        //return static::findOne(['password_reset_token' => $token]);
    }

    public static function findByUsername($username) {
        return static::findOne(['username' => $username]);
    }

    public static function findByEmail($email, $spot) {
        $num = substr_count($email, '@');
        if ($num == 1) {
            $condition['email'] = $email;
        } else {
            $condition['iphone'] = $email;
        }

        if ($email != Yii::getAlias('@rootEmail') && $email != Yii::getAlias('@rootIphone')) {
            $result = Spot::find()->select(['id'])->where(['spot' => $spot])->asArray()->limit(1)->one();
            $condition['spot_id'] = $result['id'];
        }

        return static::find()->select(['id', 'username', 'spot_id', 'default_spot', 'email', 'password_hash', 'auth_key', 'status'])->where($condition)->andWhere('status != 3')->one();
    }

    /**
     * @return int|string 当前用户ID
     */
    public function getId() {
        return $this->id;
    }

    /**
     * 
     * @param string $user_id 默认为email值
     * 
     */
    // public function generateUserId($user_id = null){
    //     $this->user_id = $user_id?$user_id:$this->email;
    // }
    /**
     * @return string 当前用户的（cookie）认证密钥
     */
    public function getAuthKey() {
        return $this->auth_key;
    }

    /**
     * @param string $authKey
     * @return boolean if auth key is valid for current user
     */
    public function validateAuthKey($authKey) {
        return $this->getAuthKey() === $authKey;
    }

    public function validatePassword($password, $password_hash) {
        return Yii::$app->getSecurity()->validatePassword($password, $password_hash);
    }

    public function generatePasswordHash() {
        $this->password_hash = Yii::$app->getSecurity()->generatePasswordHash($this->password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey() {
        $this->auth_key = Yii::$app->getSecurity()->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken() {
        $this->password_reset_token = Yii::$app->getSecurity()->generateRandomString() . '_' . time();
    }

    /**
     * Generates new code
     */
    public function generateCode() {
        $this->code = Yii::$app->getSecurity()->generateRandomString(16) . $this->email . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken() {
        $this->password_reset_token = null;
    }

//     public function login() {
//         if (!$this->validate()) {
//             return false;
//         }
//         $user = User::findOne(['email' => $this->email]);
//         if ($user !== null) {
//             if ($this->validatePassword($this->password, $user->password_hash)) {
//                 \Yii::$app->user->login($user, 3600*24*30);
//                 return true;
//             }
//         }
//         return false;
//     }

    public function beforeSave($insert) {
        if ($insert) {
            if ($this->isNewRecord) {

                // $this->generateUserId($this->email);
                $this->generateAuthKey();
                $this->generatePasswordResetToken();
//                 $this->generateCode();
            }
            if (!empty($this->password)) {
                $this->generatePasswordHash();
            }
        }
        if (!$insert && !empty($this->password)) {
            $this->generatePasswordHash();
        }

        return parent::beforeSave($insert);
    }

    /**
     *
     * 发送注册邮件验证
     */
    public function sendRegisterMail($data, $spotInfo = null, $emailFile = null) {
        if (empty($emailFile)) {
            $emailFile = Yii::getAlias('@registerEmail');
        }
        if (!empty($spotInfo)) {
            $parentSpotName = $spotInfo['parentSpotName'];
            $parentSpotCode = $spotInfo['parentSpotCode'];
        } else {
            $parentSpotName = Yii::$app->cache->get(Yii::getAlias('@parentSpotName') . $this->spotId . $this->userInfo->id);
            $parentSpotCode = Yii::$app->cache->get(Yii::getAlias('@parentSpotCode') . $this->spotId . $this->userInfo->id);

//             $parentSpotName = $_COOKIE['parentSpotName'];
//             $parentSpotCode = $_COOKIE['parentSpotCode'];
        }
        $model = User::find()->select(['id', 'expire_time'])->where(['id' => $data->id])->one();
        $model->scenario = 'resetSave';
        $model->expire_time = time() + 86400;
        if ($model->save()) {

            $mail = Yii::$app->mailer->compose($emailFile, ['data' => $data, 'parentSpotName' => $parentSpotName, 'parentSpotCode' => $parentSpotCode]);
            $mail->setTo($data->email);
            $mail->setSubject("欢迎加入" . $parentSpotName);
            //邮件发送成功后，重置expire_time
            if ($mail->send()) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 用户与诊所关联
     */
    public function getUserSpot() {
        return $this->hasMany(UserSpot::className(), ['user_id' => 'id']);
    }

    /**
     * @return user_id,username
     * @return \yii\db\ActiveQuery
     */
    public static function getUserData() {
        return self::find()->select(['email', 'username'])->asArray()->all();
    }

    /**
     * 
     * @param 医生名称（可筛选） $name
     * @param 科室ID（可筛选） $department_id
     * @param 诊所ID $spot_id
     * @param 职业(可筛选) $occupation
     * @param 是否开放预约(针对按医生预约) $timeConfig
     * @param 排序 $sort_type
     * @param 根据医生id分组（多科室情况下，医生重复）$groupBy
     * @return 获取员工列表
     */
    public static function getWorkerList($name, $department_id, $spot_id, $occupation = 0, $timeConfig = false, $sort_type = 0,$groupBy = false) {
        $query = new Query();
        $query->from(['u' => self::tableName()]);
        $query->leftJoin(['us' => UserSpot::tableName()], '{{u}}.id={{us}}.user_id');
        $query->leftJoin(['sd' => SecondDepartment::tableName()], '{{sd}}.id={{us}}.department_id');
        $query->select(['doctor_name' => 'u.username', 'u.occupation', 'doctor_id' => 'u.id']);
        $query->where(['us.spot_id' => $spot_id, 'u.status' => 1]);
        if($sort_type == 0){
            $query->orderBy(['u.occupation' => SORT_ASC]);
        }else{
            $query->orderBy(['u.occupation' => SORT_DESC]);
        }

        if ($name) {
            $query->andWhere(['like', 'u.username', $name]);
        }
        if (!empty($department_id)) {
            $query->andWhere(['sd.id' => $department_id]); //科室ID
        }
        if ($occupation) {
            $query->andWhere(['u.occupation' => $occupation]); //职业
        }
        if ($timeConfig) {
            $query->andWhere(['us.status' => 1]); //职业
        }
        if($groupBy){
            $query->groupBy('u.id'); 
        }
        $list = $query->all();
        $data = [];
        if (!empty($list)) {
            foreach ($list as &$val) {
                $val['occupation'] = self::$getOccuption[$val['occupation']];
            }
        }
        return $list;
    }

    /**
     * 
     * @param 医生姓名 $name
     * @param 二级科室id $department_id
     * @param 诊所id $spot_id
     * @return 返回医生与对应科室列表
     */
    public static function getWorkerRoomList($name, $department_id, $spot_id) {
        $query = new Query();
        $query->from(['u' => self::tableName()]);
        $query->leftJoin(['us' => UserSpot::tableName()], '{{u}}.id={{us}}.user_id');
        $query->leftJoin(['sd' => SecondDepartment::tableName()], '{{sd}}.id={{us}}.department_id');
        $query->select([ 'doctor_id' => 'u.id', 'doctor_name' => 'u.username', 'department_name' => 'sd.name','sd.status']);
        $query->where(['us.spot_id' => $spot_id, 'u.occupation' => 2, 'u.status' => 1]);
        $query->andFilterWhere(['like', 'u.username', $name]);
        $query->andFilterWhere(['sd.id' => $department_id]);
        $query->indexBy('doctor_id');
        $list = $query->all();
//        if($department_id){
            $ids = array();
            foreach ($list as $value) {
                  array_push($ids,$value['doctor_id']);
            }
            $ids = array_unique($ids);
            $departmentList = self::getDepartmentByDoctorId($ids);
            foreach ($list as $key => $val) {
                $list[$key]['department_name'] = str_replace(',','，',$departmentList[$val['doctor_id']]['department_name']);
            }
//        }
        return $list;
    }

    /**
     *@param 医生id数组 $$doctorIdArray
     * @return 根据医生id返回二级科室的列表
     */
    public static function getDepartmentByDoctorId($doctorIdArray) {
        $query = new Query();
        $query->from(['u' => self::tableName()]);
        $query->leftJoin(['us' => UserSpot::tableName()], '{{u}}.id={{us}}.user_id');
        $query->leftJoin(['sd' => SecondDepartment::tableName()], '{{sd}}.id={{us}}.department_id');
        $query->leftJoin(['sdu' => SecondDepartmentUnion::tableName()], '{{sd}}.id={{sdu}}.second_department_id');
        $query->select([ 'doctor_id' => 'u.id','department_name' => 'group_concat(sd.name)']);
        $query->where(['us.spot_id' => self::$staticSpotId,'sd.status' => 1, 'u.occupation' => 2, 'u.status' => 1, 'sdu.spot_id' => self::$staticSpotId]);
        $query->andFilterWhere(['u.id' => $doctorIdArray]);
        $query->indexBy('doctor_id');
        $query->groupBy(['doctor_id']);
        $list = $query->all();
        return $list;
    }

    /**
     * 
     * @return 返回二级科室的列表
     */
    public static function getDepartmentInfo() {//需修改
        $query = new Query();
        $query->from(['a' => SecondDepartment::tableName()]);
        $query->leftJoin(['b' => SecondDepartmentUnion::tableName()], '{{a}}.id = {{b}}.second_department_id');
        $query->select([ 'department_name' => 'a.name', 'department_id' => 'a.id']);
        $query->where(['a.spot_id' => self::$staticParentSpotId, 'b.spot_id' => self::$staticSpotId, 'a.status' => 1]);
        $secondDepartmentInfo = $query->all();
        return $secondDepartmentInfo;
    }

    /**
     * 
     * @param 诊所 $spot_id
     */
    public static function getWorker($user_id) {
        $query = new Query();
        $query->from(['u' => self::tableName()]);
//        $query->leftJoin(['us' => UserSpot::tableName()], '{{u}}.id={{us}}.user_id');
        $query->select(['doctor_name' => 'u.username', 'u.occupation', 'doctor_id' => 'u.id']);
        $query->where(['u.id' => $user_id, 'u.status' => 1]);
        $worker = $query->all();
        $worker[0]['doctor_name'] = '排班';
        return $worker;
    }

    /**
     * @param 诊所id $spot_id
     * @return 诊所的用户的id和username
     */
    public static function getUserList($spot_id) {
        $query = new Query();
        $query->from(['a' => UserSpot::tableName()]);
        $query->select(['id' => 'a.user_id', 'b.username']);
        $query->leftJoin(['b' => self::tableName()], '{{a}}.user_id = {{b}}.id');
        $query->where(['a.spot_id' => $spot_id, 'b.status' => 1]);
        return $query->all();
    }

    public function validatecode($attribute, $params) {
        if (!$this->hasErrors()) {
            $hasRecord = Code::find()->select(['id', 'expire_time', 'code'])->where(['spot_id' => $this->spot_id, 'iphone' => $this->iphone, 'user_id' => $this->id, 'type' => 1])->orderBy('id DESC')->asArray()->one();
            if ($hasRecord) {
                if (time() > $hasRecord['expire_time']) {
                    $this->code = '';
                    $this->addError($attribute, '验证码失效，请重新获取验证码');
                } else if ($hasRecord['code'] != $this->$attribute) {
                    $this->code = '';
                    $this->addError($attribute, '验证码错误，请重新输入验证码');
                }
            } else {
                $this->code = '';
                $this->addError($attribute, '验证码错误,请重新输入验证码');
            }
        }
    }

    /**
     *
     * @param 医生姓名 $name
     * @param 二级科室id $department_id
     * @param 诊所id $spot_id
     * @return 返回医生与对应科室列表
     */
    public static function getDoctorRoomList($spot_id, $pageSize = 20) {
        $query = new ActiveQuery(User::className());
        $query->from(['u' => self::tableName()]);
        $query->leftJoin(['us' => UserSpot::tableName()], '{{u}}.id={{us}}.user_id');
        $query->leftJoin(['sd' => SecondDepartment::tableName()], '{{sd}}.id={{us}}.department_id');
        $query->select([ 'doctor_id' => 'u.id', 'doctor_name' => 'u.username', 'department_name' => 'sd.name', 'us.status']);
        $query->where(['us.spot_id' => $spot_id, 'u.occupation' => 2, 'u.status' => 1]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
                'defaultOrder' => [
                    'u.id' => SORT_DESC
                ],
                'attributes' => ['u.id']
            ]
        ]);
        return $dataProvider;
    }

    /**
     * @param $userId 用户id
     * @param $field 要查询的用户字段信息  默认为全部信息
     * @return 用户的信息
     */
    public static function getUserInfo($userId, $field = null) {
        if ($field == null) {
            $field = '*';
        }
        return self::find()->select($field)->where(['id' => $userId])->asArray()->one();
    }

    /**
     * @return 获取当前诊所所有有效医生
     */
    public static function getDoctorList(){
        $query = new Query();
        $query->from(['a' => UserSpot::tableName()]);
        $query->select(['doctor_id' => 'DISTINCT(a.user_id)', 'doctorName'=>'b.username']);
        $query->leftJoin(['b' => self::tableName()], '{{a}}.user_id = {{b}}.id');
        $query->where(['a.spot_id' => self::$staticSpotId, 'b.status' => 1,'b.occupation' => 2]);
        $query->indexBy('doctor_id');
        $query->orderBy(['b.id'=>SORT_ASC]);
        return $query->all();
    }
    
     /**
     * @return 获取当前机构医生列表
     */
    public static function getParentSpotDoctorList(){
        return self::find()->select(['id','username'])->where(['spot_id' => self::$staticParentSpotId, 'occupation' => 2])->asArray()->all();
    }

    /**
     * 获取当前诊所所有状态正常的员工
     */
    public static function getSpotUser(){
        $query = new query();
        $query->from(['a' => User::tableName()]);
        $query->leftJoin(['b' => UserSpot::tableName()], '{{a}}.id = {{b}}.user_id');
        $query->select(['a.id', 'a.username']);
        $query->where(['a.status' => 1, 'b.spot_id' => self::$staticSpotId]);
        $userInfo = $query->all();
        return $userInfo;
    }

    /**
     * 获取当前用户所在的诊所
     */
    public static function getSpotList($id){
        $query = new query();
        $query->from(['a' => User::tableName()]);
        $query->leftJoin(['b' => UserSpot::tableName()], '{{a}}.id = {{b}}.user_id');
        $query->leftJoin(['c' => Spot::tableName()], '{{b}}.spot_id = {{c}}.id');
        $query->select(['a.id','group_concat(distinct c.spot_name) as clinic_name']);
        $query->where(['a.id' => $id,'c.status'=>'1','a.spot_id' => self::$staticParentSpotId]);
        $query->groupBy('a.id');
        $query->indexBy('id');
        return $query->all();
    }
}
