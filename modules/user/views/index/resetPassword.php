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
    <?php AppAsset::addCss($this, 'https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css')?>
    <!-- Ionicons -->
    <?php AppAsset::addCss($this, 'https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css')?>
    <!-- Theme style -->
    <?php AppAsset::addCss($this,'@web/public/dist/css/AdminLTE.min.css');?>
    <!-- iCheck -->
    <?php AppAsset::addCss($this,'@web/public/plugins/iCheck/square/blue.css');?>   
    <?php $this->head() ?>
</head>
<body class="hold-transition login-page">
<?php $this->beginBody() ?>
<div class="login-box">
  <div class="login-logo">
    <?= Html::a(Html::tag('b','HIS系统'),['@manageIndex']) ?>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg">请输入你的重置密码</p>
    <?php $form = ActiveForm::begin([
        'method' => 'post',
        'action' => ''
    ])?>
      <div class="form-group has-feedback">
        <?= $form->field($model,'password')->passwordInput(['class' => 'form-control','placeholder' => '密码'])->label(false) ?>
        <?= Html::tag('span','',['class' => 'glyphicon glyphicon-lock form-control-feedback']) ?>
      </div>
      <div class="form-group has-feedback">
        <?= $form->field($model,'reType_password')->passwordInput(['class' => 'form-control','placeholder' => '确认密码'])->label(false) ?>
        <?= Html::tag('span','',['class' => 'glyphicon glyphicon-log-in form-control-feedback']) ?>
      </div>
    <div class="row">
        <!-- /.col -->
        <div class="col-xs-4">
          <?= Html::submitButton('重置',['class' => 'btn btn-primary btn-block btn-flat'])?>
        </div>
        <!-- /.col -->
      </div>
    <?php ActiveForm::end()?>
  </div>
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
