<?php  
use yii\helpers\Html;  
 
/* @var $this yii\web\View */
/* @var $model app\modules\user\models\User */
  
$code_url = Yii::$app->urlManager->createAbsoluteUrl([Yii::getAlias('@userIndexResetPassword'), 'token' => $data['password_reset_token']]);  
?>

<p>亲爱的 <?= $data['username'] ?> 您好！请点击下面的链接输入重置密码</p>

<p><?= Html::a($code_url,$code_url) ?></p>

<p>（如果链接无法点击，请将它复制并粘贴到浏览器的地址栏中访问，该链接使用一次或24小时后失效）</p>

<p>本邮件是系统自动发送的，请勿直接回复！</p>

<p>如有其它问题，请联系 张震宇帅哥，他会全程帮你解答</p>

<p>医信科技有限公司</p>




