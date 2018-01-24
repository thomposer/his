<?php 
use app\assets\AppAsset;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
AppAsset::register($this);
$baseUrl = Yii::$app->request->baseUrl;
/* @var $this yii\web\View */
/* @var $model app\modules\user\models\User */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <!-- Theme style -->
    <?php AppAsset::addCss($this,'@web/public/dist/css/AdminLTE.min.css');?>
    <?php AppAsset::addCss($this, '@web/public/css/user/overdue.css')?>
    <?php $this->head() ?>
</head>
<body class="hold-transition lockscreen">
<?php $this->beginBody() ?>
<!-- Automatic element centering -->
<div class="lockscreen-wrapper">
   <p>
    该页面已过期，请联系管理员重新发送密码重置邮件
    </p>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
