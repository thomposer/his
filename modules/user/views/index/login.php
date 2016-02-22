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
    <?php AppAsset::addCss($this, '@web/public/css/user/login.css')?> 
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
    <p class="login-box-msg">Sign in to start your session</p>
    <?php $form = ActiveForm::begin([
        'method' => 'post',
        'action' => ''
    ])?>
      <div class="form-group has-feedback">
        <?= $form->field($model,'email')->textInput(['class' => 'form-control','placeholder' => 'Email'])->label(false)?>
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <?= $form->field($model,'password')->passwordInput(['class' => 'form-control','placeholder' => '密码'])->label(false) ?>
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="row">
         <div class="col-xs-8">
          <div class="checkbox icheck">

              <?= $form->field($model,'rememberMe')->checkbox()->label(false); ?>
            
          </div>
        </div>        
        <!-- /.col -->
        <div class="col-xs-4">
          <?= Html::submitButton('登录',['class' => 'btn btn-primary btn-block btn-flat'])?>
        </div>
        <!-- /.col -->
      </div>
    <?php ActiveForm::end()?>

    <div class="social-auth-links text-center">
      <p>- OR -</p>
      <a href="#" class="btn btn-block btn-social btn-facebook btn-flat"><i class="fa fa-facebook"></i> Sign in using
        Facebook</a>
      <a href="#" class="btn btn-block btn-social btn-google btn-flat"><i class="fa fa-google-plus"></i> Sign in using
        Google+</a>
    </div>
    <!-- /.social-auth-links -->

    <?= Html::a('忘记密码','#') ?><br>
    <?= Html::a('注册新账号',['@userIndexRegister'],['class' => 'text-center']) ?>

  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->
<script  type="text/javascript"  src="<?php echo $baseUrl.'/public/js/lib/require.js'?>"></script>
    <script>
    require.config({
        baseUrl: "<?php echo $baseUrl.'/';?>",
        paths: {
        	'jquery' : 'public/js/lib/jquery.min',
            'js' : 'public/js',
            'plugins' : 'public/plugins'
        }
    });
    require(["<?php echo $baseUrl ?>"+"/public/js/user/login.js"],function(main){
    	main.init();
	});
    </script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
