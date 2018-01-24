<?php 
use app\assets\AppAsset;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use rkit\yii2\plugins\ajaxform\Asset;
use yii\web\View;
AppAsset::register($this);
Asset::register($this);
$baseUrl = Yii::$app->request->baseUrl;
$versionNumber = Yii::getAlias("@versionNumber");
$attribute = $model->attributeLabels();
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
    <?php AppAsset::addCss($this, '@web/public/css/user/reset.css')?>
    <?php $this->head() ?>
</head>
<body class="hold-transition login-page">
<?php $this->beginBody() ?>
<div class="login-box">
  <div class="login-logo">
     <?php $dataType = yii::$app->request->get('dataType');
        if($dataType){
            echo Html::tag('p','设置密码');
        }else{
            echo Html::tag('p','重置密码');
        }
     ?>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg"><?= Html::img($baseUrl.'/public/img/common/icon_username.png').Html::encode($model->username)?></p>
    <?php $form = ActiveForm::begin([
        'method' => 'post',
        'action' => '',
         'id'=>'batch-form'
    ])?>
      <div class="form-group has-feedback">
        <?= $form->field($model,'password')->passwordInput(['class' => 'form-control','autocomplete' => 'off','placeholder' => '8-20位，包含数字、符号及字母至少两种'])->label($attribute['pushpassword']) ?>
        <?= Html::tag('span','',['class' => 'icon icon-pwd']) ?>
      </div>
      <div class="form-group has-feedback">
        <?= $form->field($model,'reType_password')->passwordInput(['class' => 'form-control','autocomplete' => 'off','placeholder' => ''])->label($attribute['surepassword']) ?>
        <?= Html::tag('span','',['class' => 'icon icon-pwd']) ?>
      </div>
      <div class="form-group has-feedback">
          <?= $form->field($model,'code')->textInput(['class' => 'form-control form-code','autocomplete' => 'off','placeholder' => '','maxlength'=>"6"])->label($attribute['iphone_code']) ?>
          <?= Html::button('<span>获取验证码</span>',['class' => 'btn-code-off','id'=>'btn','disabled'=>'disabled']) ?>
      </div>
      <div class="code-message">
      </div>

    <div class="row margin-button">
        <!-- /.col -->
        <div class="col-xs-12 btn-submit">
          <?= Html::submitButton('确定',['class' => 'btn btn btn-default btn-block', 'id' => 'batch-myform'])?>
        </div>
        <!-- /.col -->
      </div>
    <?php ActiveForm::end()?>
  </div>
</div>
<?php 
    AppAsset::addScript($this,'@web/public/js/lib/require.js');
    $token=  yii::$app->request->get('token');
    $disUrl= Url::to(['@userIndexSendCode']);
    $codeUrl= Url::to(['@userIndexValidateCode']);
    $layoutJs = <<<JS
        var token='$token';
        var disUrl='$disUrl';
        var codeUrl='$codeUrl';
        define('jquery', function() {return window.$;});
        require(['$baseUrl'+"/public/js/user/resetpassword.js?v="+ '$versionNumber'],function(main){
            main.init();
        });
JS;
    $this->registerJs($layoutJs,View::POS_END);
?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
