<?php

namespace app\modules\user\controllers;

use Yii;
use app\modules\user\models\User;
use app\modules\user\models\search\UserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use app\modules\user\models\LoginForm;
use app\common\Common;
use yii\helpers\Url;
use app\modules\apply\models\ApplyPermissionList;
use yii\db\Query;
use yii\base\Object;
/**
 * IndexController implements the CRUD actions for User model.
 */
class IndexController extends Controller
{
    public function behaviors()
    {
        $parent = parent::behaviors();
        $current =  [
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
    public function beforeAction($action){
        date_default_timezone_set('Asia/Shanghai');
        return parent::beforeAction($action);
    }
    /**
     * 登录
     */
    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            $defaultUrl = Url::to(['@manage']);
            return $this->goBack($defaultUrl);
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }
    /**
     * 注销
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        $this->redirect(Url::to(['@userIndexLogin']));
        return;
    }
    /**
     * 注册
     */
    public function actionRegister(){
        
        $model = new User();
        $model->scenario = 'register';
        if($model->load(Yii::$app->request->post())){
            $model->status = 0;
            $model->create_time = time();
            $model->type = 1;
            if($model->validate() && $model->save()){
                
                $result = $this->sendCheckMail($model);
                if($result){
                    Common::showInfo('注册成功',Url::to(['@userIndexLogin']));
                }
            }
        }
        return $this->render('register',['model' => $model]);
    }
    /**
     * 重置密码api
     */
    public function actionReset($id){
        $query = new Query();
        $query->from(['a' => ApplyPermissionList::tableName()]);
        $query->select(['a.user_id','u.id','u.email','u.username','u.password_reset_token']);
        $query->leftJoin(['u' => User::tableName()],'{{a}}.user_id = {{u}}.user_id');
        $query->where(['a.id' => $id]);
        $result = $query->one();
        if(!$result){
            throw new NotFoundHttpException('你所请求的页面不存在',404);
        }
        $this->sendResetEmail($result);
    }
    /**
     * 重置密码验证页面
     * @return string
     */
    public function actionResetPassword(){
        $resetToken = Yii::$app->request->get('token');
        $model = User::find()->select(['id','expire_time'])->where(['password_reset_token' => $resetToken])->one();
        if(!$model || time() > $model->expire_time){
            return $this->render('overdue');
        }
        $model->scenario = 'resetPassword';        
        if($model->load(Yii::$app->request->post())){
            $model->update_time = time();       
            $model->generatePasswordResetToken(); 
            if($model->validate() && $model->save()){
                Common::showInfo('重置成功',Url::to(['@userIndexLogin']));
            }
        }        
       
        return $this->render('resetPassword',['model' => $model]);
    }
    //发送重置密码邮件
    public function sendResetEmail($data){
        $model = User::findOne($data['id']);
        $model->expire_time = time()+86400;
        if($model->save()){
        
            $mail= Yii::$app->mailer->compose(Yii::getAlias('@resetEmail'),['data' => $data]);
            $mail->setTo($data['email']);
            $mail->setSubject("欢迎加入HIS平台");
            //邮件发送成功后，重置expire_time
            if($mail->send()){
                Common::showInfo('发送邮件成功',Url::to(['@rbacApplyIndex']));
            }else {
                Common::showInfo('邮件发送失败');
            }
        }else{
            Common::showInfo('邮件发送失败');
        }
    }
    /**
     * 发送注册邮件验证
     */
    public function sendCheckMail($model){
        $mail= Yii::$app->mailer->compose(Yii::getAlias('@checkEmail'),['model' => $model]);
        $mail->setTo($model->email);
        $mail->setSubject("欢迎加入HIS平台，请验证登录邮箱");
        //$mail->setTextBody('zheshisha ');   //发布纯文字文本
//         $mail->setHtmlBody("<br>问我我我我我");    //发布可以带html标签的文本
        if($mail->send())
            return true;
        else
            return false;
    }
}
