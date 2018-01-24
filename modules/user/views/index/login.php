<?php 
use app\assets\AppAsset;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
AppAsset::register($this);
$baseUrl = Yii::$app->request->baseUrl;
$versionNumber = Yii::getAlias("@versionNumber");
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
    <?php AppAsset::addCss($this,'@web/public/plugins/iCheck/all.css');?>   
    <?php AppAsset::addCss($this, '@web/public/css/user/login.css')?> 
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<div class="Login-Box">
  <div class="login-logo">
    <?= Html::tag('p','智慧e院 诊所管家') ?>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body Login-Box-Body">
    <?php $form = ActiveForm::begin([
        'method' => 'post',
        'action' => '',
        'options' => ['class' => 'Bg-Input']
    ])?>
      <div class="row text-center Login-Box-Title">账号登录</div>
      <div class="form-group has-feedback">
        <?= $form->field($model,'email')->textInput(['class' => 'form-control','placeholder' => '邮箱地址或手机号码','autocomplete'=>'off'])->label(false)?>
         <span class="icon icon-user"></span>
      </div>
      <div class="form-group has-feedback">
        <?= $form->field($model,'password')->passwordInput(['class' => 'form-control','autocomplete' => 'off','placeholder' => '密码'])->label(false) ?>
        <span class="icon icon-pwd"></span>
      </div>
      <div class="form-group has-feedback">
        <?= $form->field($model,'spot')->textInput(['class' => 'form-control','placeholder' => '机构代码','maxlength' => 10])->label(false) ?>
        <span class="icon icon-adr"></span>
      </div>
      <div class="row margin-set">
        <!-- /.col -->
        <div class="col-xs-12">
          <?= Html::submitButton('登录',['class' => 'btn btn-login col-xs-12'])?>
        </div>
        <!-- /.col -->
      </div>
    <div class = 'row'>
        <div class="col-xs-12">
          <div class="checkbox icheck text-center">

              <?= $form->field($model,'rememberMe')->checkbox()->label(false); ?>
              <?php //echo Html::a('忘记密码','#') ?><br>
            
          </div>
        </div>        
    </div>
    
    <?php ActiveForm::end()?>
  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->
<script  type="text/javascript"  src="<?php echo $baseUrl.'/public/js/lib/require.js'?>"></script>
    <script>
    var baseUrl = "<?= $baseUrl ?>";//根目录路径

    var csrf = "<?= Html::encode($_COOKIE['_csrf']); ?>";
    require.config({
        baseUrl: "<?php echo $baseUrl.'/';?>",
        paths: {
            'js' : 'public/js',
            'plugins' : 'public/plugins'
        }
    });
    require(["<?php echo $baseUrl ?>"+"/public/js/user/login.js?v="+ '<?= $versionNumber ?>'],function(main){
    	main.init();
	});

    </script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
