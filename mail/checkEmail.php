<?php  
use Yii;
use yii\helpers\Html;  
 
/* @var $this yii\web\View */
/* @var $model app\modules\user\models\User */
  
$code_url = Yii::$app->urlManager->createAbsoluteUrl(['aa', 'code' => $aa]);  
?>
<p>欢迎加入拉勾网</p>
<p>你的登录邮箱是： <?= $model->email; ?></p>
<p>请点击以下链接验证你的邮箱地址，验证后就可以使用HIS平台的所有功能啦! </p>
http://account.lagou.com/account/emailCheck.html?code=5b2f8c8444f8eb4df3582c5126bc33f6662352b7fabc7b05a157ae85df0e5065
<p>如果以上链接无法访问，请将该网址复制并粘贴至新的浏览器窗口中。</p>


