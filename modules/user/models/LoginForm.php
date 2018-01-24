<?php

namespace app\modules\user\models;

use Yii;
use yii\base\Model;
use app\modules\spot\models\Spot;

/**
 * LoginForm is the model behind the login form.
 */
class LoginForm extends Model
{
    public $email;
    public $password;
    public $spot;
    public $rememberMe = true;
    public $verifyCode;
    private $_user = false;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['email', 'password','spot'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            ['spot','string','max' => 10],
            // password is validated by validatePassword()
            ['spot','validateSpot'],
            ['password', 'validatePassword'],
        ];
    }
    public function attributeLabels()
    {
        return [
    
            'email' => '邮箱地址或手机号码',
            'password' => '密码',
            'verifyCode' => '验证码',
            'rememberMe' => '记住我',
            'spot' => '机构代码'
    
        ];
    }
    
    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
      
            $user = $this->getUser();
            if($user===null)
            {
                $this->addError('email', '该邮箱或手机号码不存在');
            }else if($user->status == 2) {
                $this->addError('email', '账号已被停用，请联系管理员');
            }else{
                if($user->password_hash){
                    if (!$user->validatePassword($this->password,$user->password_hash)) {
                        $this->password = '';
                        $this->addError($attribute, '密码错误');
                        
                    }
                }else{
                    $this->password = '';
                    $this->addError($attribute,'密码错误');
                }
            }
            
        }
    }
    public function validateSpot($attribute,$params){
        
        if(!$this->hasErrors()){
//             if($this->email === Yii::getAlias('@rootEmail') || $this->email === Yii::getAlias('@rootIphone')){
//                 return true;
//             }           
            $result = Spot::find()->select(['status'])->where(['spot' => $this->spot])->asArray()->limit(1)->one();
            if(!$result || $result['status'] == 2){
                $this->password = '';
                $this->addError($attribute,'机构代码不存在');
            }else{
                $user = $this->getUser();
                if(!$user){
                    $this->addError('email','该邮箱或手机号码不存在');
                }
            }
        }
    }
    /**
     * Logs in a user using the provided username and password.
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            $parentSpot = Spot::find()->select(['id','spot_name','spot'])->where(['spot' => $this->spot])->asArray()->one();
            $cache = Yii::$app->cache;
            if($this->rememberMe)
            {
//                 $this->_user->generateAuthKey();
                $expireTime = Yii::getAlias('@loginCookieExpireTime');
                setcookie('rememberMe',1,time()+$expireTime,'/',null,null);
//                 $cache->set('rememberMe', 1,$expireTime);
                
            }else{
                $expireTime = Yii::getAlias('@loginSessionExpireTime');
                setcookie('rememberMe',0,time()+$expireTime,'/',null,null);
//                 $cache->set('rememberMe',0,$expireTime);
            }
//             setcookie('parentSpotCode',$parentSpot['spot'],$expireTime,'/',null,null,true);
            setcookie('parentSpotId',$parentSpot['id'],time()+$expireTime,'/',null,null);
//             $cache->set(Yii::getAlias('@parentSpotCode').$parentSpot['id'].$user->id, $parentSpot['spot'],$expireTime);
//             $cache->set(Yii::getAlias('@parentSpotName').$parentSpot['id'].$user->id, $parentSpot['spot_name'],$expireTime);
            
            Yii::$app->user->login($this->getUser(), $this->rememberMe == 1 ? 3600*24*7 : 0);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {

        if ($this->_user === false) {
                $this->_user = User::findByEmail(trim($this->email),trim($this->spot));
        }

        return $this->_user;
    }
}
