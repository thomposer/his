<?php

namespace app\modules\user\controllers;

use Yii;
use app\modules\user\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use app\modules\user\models\LoginForm;
use yii\helpers\Url;
use app\modules\apply\models\ApplyPermissionList;
use yii\db\Query;
use app\modules\spot\models\Spot;
use app\modules\rbac\models\AssignmentForm;
use app\modules\user\models\UserSpot;
use app\common\Common;
use app\modules\user\models\Code;
use yii\helpers\Json;
use yii\base\Object;

/**
 * IndexController implements the CRUD actions for User model.
 */
class IndexController extends Controller
{

    public $result;

    public function behaviors() {
        $parent = parent::behaviors();
        $current = [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'reset' => ['post'],
                    'logout' => ['post']
                ],
            ],
        ];
        return ArrayHelper::merge($current, $parent);
    }

    public function beforeAction($action) {
        date_default_timezone_set('Asia/Shanghai');
        $this->result['errorCode'] = 0;
        $this->result['msg'] = '';
        if (!isset($_SERVER['HTTP_COOKIE']) && $action->id != 'reset-password') {
            $this->goBack();
        } else {
            if ($action->id == 'logout') {
                $this->enableCsrfValidation = false;
            }
            return parent::beforeAction($action);
        }
    }

    /**
     * 登录
     */
    public function actionLogin() {

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            $session = Yii::$app->session;
            $userInfo = Yii::$app->user->identity;
            $spotInfo = array();
            $defaultUrl = Url::to(['@manage']);
            if ($userInfo->email == Yii::getAlias('@rootEmail')) {
                return $this->redirect($defaultUrl);
            }
            $spotInfo = Spot::find()->select(['id', 'type', 'spot', 'spot_name', 'icon_url', 'status', 'telephone'])->where(['in', 'id', [$userInfo->default_spot, $userInfo->spot_id]])->asArray()->limit(2)->all();
            $expireTime = $model->rememberMe ? Yii::getAlias('@loginCookieExpireTime') : Yii::getAlias('@loginSessionExpireTime');
            foreach ($spotInfo as $v) {
                if ($v['type'] == 2) {//诊所
                    $spotInfo[0]['status'] = $v['status'];
                    $spotInfo[0]['spot'] = $v['spot'];
                    $spotInfo[0]['spot_name'] = $v['spot_name'];
                    $spotInfo[0]['icon_url'] = $v['icon_url'];
                    $spotInfo[0]['telephone'] = $v['telephone'];
                } else {
                    $spotInfo[1]['spot'] = $v['spot'];
                    $spotInfo[1]['spot_name'] = $v['spot_name'];
                }
            }
            Yii::info(json_encode($spotInfo), '诊所信息');
//             $spotCacheList = (new Spot())->getCacheSpotList();
            $cache = Yii::$app->cache;

            //若有默认诊所
            if ($userInfo->default_spot != 0 && $spotInfo[0]['status'] == 1) {
                setcookie('spotId', $userInfo->default_spot, time() + $expireTime, '/', null, null); //诊所id
//                 setcookie('spot',$spotInfo[0]['spot'],$expireTime,'/',null,null,true);//诊所代码
//                 setcookie('spotName',$spotInfo[0]['spot_name'],$expireTime,'/',null,null,true);//诊所名称
//                 setcookie('spotIcon',$spotInfo[0]['icon_url'],$expireTime,'/',null,null,true);
                $cacheSuffix = $userInfo->default_spot . $userInfo->id;
                $cache->set(Yii::getAlias('@parentSpotCode') . $cacheSuffix, $spotInfo[1]['spot'], $expireTime); //机构代码
                $cache->set(Yii::getAlias('@parentSpotName') . $cacheSuffix, $spotInfo[1]['spot_name'], $expireTime); //机构名称

                $cache->set(Yii::getAlias('@spot') . $cacheSuffix, $spotInfo[0]['spot'], $expireTime); //诊所代码
                $cache->set(Yii::getAlias('@spotName') . $cacheSuffix, $spotInfo[0]['spot_name'], $expireTime); //诊所名称
                $cache->set(Yii::getAlias('@spotIcon') . $cacheSuffix, $spotInfo[0]['icon_url'], $expireTime); //诊所logo

                $defaultUrl = Url::to(['@manageIndex']);
            } else {
                $parentSpotCode = $spotInfo[0]['spot'];
                $parentRoleSystem = $parentSpotCode . '_roles_system';
                $spotList = Spot::find()->select(['id'])->where(['parent_spot' => $userInfo->spot_id, 'status' => 1])->asArray()->all();
                if (empty($spotList)) {
                    setcookie('createSpot', true, time() + $expireTime, '/', null, null); //创建诊所弹框
//                     $cache->set('createSpot',true,time()+$expireTime);
                }
                $clinicList = UserSpot::find()->select(['spot_id'])->where(['user_id' => $userInfo->id, 'parent_spot_id' => $userInfo->spot_id])->indexBy('spot_id')->asArray()->all();
                unset($clinicList[$userInfo->spot_id]);
                $countClinic = count(array_intersect($spotList, $clinicList));
                if ($countClinic >= 1) {//若诊所数量大于1
                    $defaultUrl = Url::to(['@manage']);
                } else {
                    setcookie('spotId', $userInfo->spot_id, time() + $expireTime, '/', null, null); //诊所id
//                     setcookie('spot',$parentSpotCode,$expireTime,'/',null,null,true);//诊所代码
//                     setcookie('spotName',$spotInfo[0]['spot_name'],$expireTime,'/',null,null,true);//诊所名称
//                     setcookie('spotIcon',$spotInfo[0]['icon_url'],$expireTime,'/',null,null,true);
                    $cacheSuffix = $userInfo->spot_id . $userInfo->id;
                    $cache->set(Yii::getAlias('@parentSpotCode') . $cacheSuffix, $spotInfo[1]['spot'], $expireTime); //机构代码

                    $cache->set(Yii::getAlias('@spot') . $cacheSuffix, $parentSpotCode, $expireTime); //诊所代码
                    $cache->set(Yii::getAlias('@spotName') . $cacheSuffix, $spotInfo[0]['spot_name'], $expireTime); //诊所名称
                    $cache->set(Yii::getAlias('@spotIcon') . $cacheSuffix, $spotInfo[0]['icon_url'], $expireTime); //诊所logo
                    $defaultUrl = Url::to(['@manageIndex']);
                }
            }
//             exit();
            $session->set('currentSpot', '');
            return $this->goBack($defaultUrl);
        } else {
            if (!\Yii::$app->user->isGuest) {
                return $this->goHome();
            }
            return $this->render('login', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * 注销
     */
    public function actionLogout() {
        Common::logout();
        return $this->redirect(Url::to(['@userIndexLogin']));
    }

    /**
     * 注册
     */
    public function actionRegister() {

        $model = new User();
        $model->scenario = 'register';
        if ($model->load(Yii::$app->request->post())) {
            $model->status = 0;
            $model->create_time = time();
            $model->spot_id = 1;
            if ($model->validate() && $model->save()) {

                $result = $this->sendCheckMail($model);
                if ($result) {
                    Yii::$app->getSession()->setFlash('success', '注册成功');
                    return $this->redirect(Url::to(['@userIndexLogin']));
                }
            }
        }
        return $this->render('register', ['model' => $model]);
    }

    /**
     * 重置密码验证页面
     * @return string
     */
    public function actionResetPassword() {
        $resetToken = Yii::$app->request->get('token');
        $model = User::findOne(['password_reset_token' => $resetToken]);
        if (!$model || time() > $model->expire_time) {
            return $this->render('overdue');
        }
        $model->scenario = 'resetPassword';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $model->update_time = time();
            $model->generatePasswordResetToken();
            if ($model->save()) {
                //Yii::$app->getSession()->setFlash('success','重置成功');
                $this->redirect(Url::to(['@userIndexLogin']));
            }
        } else {
            return $this->render('resetPassword', ['model' => $model]);
        }
    }

    public function actionValidateCode() {
        $param = Yii::$app->request->post();
        $code_value = $param['content']['3']['value'];
        if (empty($code_value)) {
            $this->result['errorCode'] = 1001;
            $this->result['msg'] = '验证码不能为空';
        }

        $resetToken = Yii::$app->request->post('token');
        $model = User::find()->select(['id', 'expire_time', 'username', 'iphone', 'spot_id', 'password_reset_token'])->where(['password_reset_token' => $resetToken])->one();

        $hasRecord = Code::find()->select(['id', 'expire_time', 'code'])->where(['spot_id' => $model->spot_id, 'iphone' => $model->iphone, 'user_id' => $model->id, 'type' => 1])->orderBy('id DESC')->asArray()->one();

        if ($hasRecord) {
            if ($hasRecord['code'] != $code_value) {
                $this->result['errorCode'] = 1001;
                $this->result['msg'] = '验证码错误，请重新输入验证码';
            } else if (time() > $hasRecord['expire_time']) {
                $this->result['errorCode'] = 1001;
                $this->result['msg'] = '验证码失效，请重新获取验证码';
            }
        } else {
            $this->result['errorCode'] = 1001;
            $this->result['msg'] = '验证码错误,请重新输入验证码';
        }

        return Json::encode($this->result);
    }

    /**
     * @return string 发送短信验证吗
     */
    public function actionSendCode() {
        $token = Yii::$app->request->post('token');

        if (!$token) {
            $this->result['errorCode'] = 1001;
            $this->result['msg'] = '参数错误';
            return Json::encode($this->result);
        }

        $model = User::find()->select(['id', 'password_hash', 'expire_time', 'username', 'iphone', 'spot_id'])->where(['password_reset_token' => $token])->asArray()->one();

        $code = '';
        for ($i = 0; $i < 6; $i++) {
            $code .=rand(0, 9);
        }
        $code = intval($code);
        $contentCode = urlencode($code);
        if (empty($model['password_hash'])) {
//            $data=array('tpl_id'=>'1585346','tpl_value'=>('#code#').'='.urlencode($code),'mobile'=>$model['iphone']);
            $content = "【智慧e院】验证码为{$contentCode}，五分钟内有效。您正在设置智慧e院的登录密码，请在设置密码页面输入验证码完成验证，谢谢。";
        } else {
//            $data=array('tpl_id'=>'1585350','tpl_value'=>('#code#').'='.urlencode($code),'mobile'=>$model['iphone']);
            $content = "【智慧e院】验证码为{$contentCode}，五分钟内有效。您正在重置智慧e院的登录密码，请在重置密码页面输入验证码完成验证，谢谢。";
        }
        $arrayData = Common::mobileSend($model['iphone'], $content);
        $this->result['arrayData'] = Json::encode($arrayData);

        $this->result['iphone'] = $model['iphone'];

        if ($arrayData['code'] == 0) {
            $Code = new Code();
            $Code->code = $code;
            $Code->expire_time = time() + 300;
            $Code->user_id = $model['id'];
            $Code->iphone = $model['iphone'];
            $Code->spot_id = $model['spot_id'];
            $Code->create_time = time();
            $Code->update_time = time();
            $res = $Code->save();

            if ($res) {

                return Json::encode($this->result);
            }
        } else {
            $this->result['errorCode'] = 1001;
            if ($arrayData['code'] == '22') {
                $this->result['msg'] = '过于频繁，请稍后再试';
            } else if ($arrayData['code'] == '2') {
                $this->result['msg'] = '您的手机号码不正确，请联系管理员';
            } else if ($arrayData['code'] == '33') {
                $this->result['msg'] = '不能在30秒内重复获取验证码';
            } else {
                $this->result['msg'] = $arrayData['detail'];
            }
        }

        return Json::encode($this->result);
    }

}
