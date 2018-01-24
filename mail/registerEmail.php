<?php  
use yii\helpers\Html;  
 
/* @var $this yii\web\View */
/* @var $model app\modules\user\models\User */
  
$code_url = Yii::$app->urlManager->createAbsoluteUrl([Yii::getAlias('@userIndexResetPassword'), 'token' => $data['password_reset_token'],'dataType' => '1']);
$login_url = Yii::$app->urlManager->createAbsoluteUrl([Yii::getAlias('@userIndexLogin')]);
?>
<p>亲爱的<?= Html::encode($data['username'])?>，您好！</p>

<p style="text-indent: 2em;">欢迎加入<?= Html::encode($parentSpotName)?>（机构代码为<?= Html::encode($parentSpotCode) ?>）。智慧e院系统使用操作步骤如下： </p>

<br>

<p>1、设置密码，请点击下面的链接设置您的账户密码（为了您的使用安全，请尽量设置包含英文与数字的密码），</p>

<p style="text-indent: 2em;">如果链接无法点击，请将它复制并粘贴到浏览器的地址栏中访问，该链接使用一次或24小时后失效。</p>

<p style="text-indent: 2em;"><?= Html::a($code_url,$code_url) ?></p>



<p>2、登录系统，请点击下面的链接输入您的用户名（邮箱或手机号）、密码以及机构代码便可开始使用，推荐您使用谷歌浏览器。

<p style="text-indent: 2em;"><?= Html::a($login_url,$login_url) ?></p>

<br>


<p style="text-indent: 2em;">如有问题，请联系管理员帮忙处理。</p>

<p style="text-indent: 2em;">本邮件是系统自动发送，请勿直接回复！</p>

<p style="text-indent: 2em;">From：深圳市医信科技有限公司</p>



