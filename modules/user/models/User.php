<?php

namespace app\modules\user\models;

use Yii;
use app\common\base\BaseActiveRecord;
use yii\web\IdentityInterface;
/**
 * This is the model class for table "{{%user}}".
 *
 * @property integer $id
 * @property string $user_id
 * @property string $username
 * @property string $email
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property integer $type
 * @property integer $status
 * @property integer $create_time
 * @property integer $update_time
 *
 * @property AuthAssignment[] $authAssignments
 */
class User extends BaseActiveRecord implements IdentityInterface
{
    public $password;
    public $reType_password;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','user_id', 'password_hash', 'password_reset_token', 'type'], 'required'],
            [['password'], 'required','on'=>['login','register']],
            [['username','email','reType_password'],'required','on' => ['register']],
            ['reType_password','compare','compareAttribute' => 'password','message' => '请再输入确认密码'],
            ['email','email'],
            [['id','type', 'status', 'create_time', 'update_time'], 'integer'],
            [['user_id', 'username', 'auth_key'], 'string', 'max' => 64],
            [['password_hash', 'password_reset_token'], 'string', 'max' => 255],
            [['id','user_id','username'], 'unique'],
        ];
    }
    public function scenarios(){
        
        $parent = parent::scenarios();
        $parent['login'] = ['username','password'];
        $parent['register'] = ['username','password','reType_password', 'email'];
        $parent['update'] = ['username','password', 'email','status','update_time'];
        return $parent;
        
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'username' => '用户名',
            'auth_key' => 'Auth_Key',
            'password_hash' => '密码',
            'password' => '密码',
            'reType_password' => '确认密码',
            'password_reset_token' => 'password_reset_token',
            'type' => '类型',
            'status' => '状态',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }
    
/**
     * 根据给到的ID查询身份。
     *
     * @param string|integer $id 被查询的ID
     * @return IdentityInterface|null 通过ID匹配到的身份对象
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * 根据 token 查询身份。
     *
     * @param string $token 被查询的 token
     * @return IdentityInterface|null 通过 token 得到的身份对象
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
        //return static::findOne(['password_reset_token' => $token]);
    }
    public static function findByUsername($username){
        return static::findOne(['username' => $username]);
    }
    public static function findByEmail($email){
        
        return static::findOne(['email' => $email]);
    }
    /**
     * @return int|string 当前用户ID
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * @return string 当前用户user_id
     */
    public function getUserId(){
        return $this->user_id;
    }
    /**
     * 
     * @param string $user_id 默认为email值
     * 
     */
    public function generateUserId($user_id = null){
        $this->user_id = $user_id?$user_id:$this->email;
    }
    /**
     * @return string 当前用户的（cookie）认证密钥
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @param string $authKey
     * @return boolean if auth key is valid for current user
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }
    public function validatePassword($password,$password_hash)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $password_hash);
    }
    
    public function generatePasswordHash()
    {
        $this->password_hash = Yii::$app->getSecurity()->generatePasswordHash($this->password);
    }
    
    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->getSecurity()->generateRandomString();
    }
    
    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->getSecurity()->generateRandomString() . '_' . time();
    }
    
    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
     
     
    public function login()
    {
        if(!$this->validate()){
            return false;
        }
         
        $user = User::findOne(['email' => $this->email]);
         
        if($user!==null){
            if($this->validatePassword($this->password,$user->password_hash)){
                \Yii::$app->user->login($user,50000);
                return true;
            }
        }
         
        return false;
    }
    
    public function beforeSave($insert)
    {
        
        if($insert)
        {
            if($this->isNewRecord){
                
                $this->generateUserId($this->email);
                $this->generateAuthKey();
                $this->generatePasswordResetToken();
            }           
            $this->generatePasswordHash();            
        }
        if(!$insert && !empty($this->password))
        {
            $this->generatePasswordHash();
        }
        
        return parent::beforeSave($insert);
    }
    /**
     * @return user_id,username
     * @return \yii\db\ActiveQuery
     */
    public static function getUserData()
    {
        return self::find()->select(['user_id','username'])->asArray()->all();
    }
}
