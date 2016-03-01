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
                ],
            ],
        ];
        return ArrayHelper::merge($current, $parent);
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
            return $this->goBack();
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

        return $this->goHome();
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
                
                $result = $this->sendMail($model);
                if($result){
                    Common::showInfo('注册成功',Url::to(['@userIndexLogin']));
                }
            }
        }
        return $this->render('register',['model' => $model]);
    }
    /**
     * 发送邮件验证
     */
    public function sendMail($model){
        $mail= Yii::$app->mailer->compose(Yii::getAlias('@userIndexEmail'),['model' => $model]);
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
