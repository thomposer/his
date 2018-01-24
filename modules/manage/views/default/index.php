<?php 
use app\assets\AppAsset;
use yii\helpers\Html;
use  yii\helpers\Url;
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
    <!-- Ionicons -->
    <?php AppAsset::addCss($this, 'https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css')?>   
    <!-- iCheck -->
    <?php AppAsset::addCss($this,'@web/public/plugins/iCheck/square/blue.css');?>   
    <?php AppAsset::addCss($this,'@web/public/plugins/iCheck/all.css');?>   
    <?php AppAsset::addCss($this, '@web/public/css/user/select_spot.css')?> 
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
    <div class="Box">
      <div class="LoginBox-title">
            选择诊所
          </div>
      <div class="row LoginBox login">
          <div class="LoginBox-left col-sm-4">
              <div class="LogoBox">
                <img src="<?= $spotInfo['icon_url']?$spotInfo['icon_url']:$baseUrl.'/public/img/common/logo.jpg' ?>" alt="">
              </div>
              <div class="TextBox"><?= $spotInfo['spot_name']?Html::encode($spotInfo['spot_name']):'医信科技有限公司' ?></div>
              <?= Html::a('<span></span>登出账号',['@userIndexLogout'],
                  [
                      'class' => 'Btn-Exit',
                      'data' => [
                        'confirm' => '你确定要退出账号吗?',
                        'method' => 'post',
                        ],
                      'delete' => true,
              ]) ?>
          </div>
          <div class="LoginBox-right col-sm-8">
              <form action="<?= Url::to(['@manageSites']) ?>" method="post">
                <div class="form-group UserId">
                  <span class="icon-user"></span>
                  <p class = 'form-control'><?= Html::encode($username) ?></p>
                </div>
                <select class="form-control news-btn-default news-select-sm J-own-clinic valid" name="id">
                        <?php if($list):?>
						<?php foreach($list as $wxInfo):?>
                    <option value="<?= $wxInfo['id'] ?>"><?= Html::encode($wxInfo['spot_name']) ?></option>
						<?php endforeach; ?>
						<?php endif;?>
                </select>
                <div class="row">
                  <div class="col-sm-12">
                    <input type="hidden" value="<?= Yii::$app->request->csrfToken ?>" name="_csrf" />
                    
                     <input class="Btn-Enter" type="submit" value="进入">
                  </div>
                <?php if(Yii::$app->user->identity['email'] != Yii::getAlias('@rootEmail')): ?>
                  <div class="col-sm-12">
                    <div class="checkbox icheck">
                      <label>
                        <input type="checkbox" name = 'default_spot'> <span>设为默认登录诊所</span>
                      </label>
                    </div>            
                  </div>
                <?php endif;?>
                </div>
              </form>
          </div>
      </div>
    </div>
<!-- /.login-box -->
<script  type="text/javascript"  src="<?php echo $baseUrl.'/public/js/lib/require.js'?>"></script>
    <script>
        var baseUrl = "<?= $baseUrl ?>";//根目录路径
        require.config({
        baseUrl: baseUrl+"/",
        paths: {
        	'jquery' : 'public/js/lib/jquery.min',
            'js' : 'public/js',
            'plugins' : 'public/plugins'
        }
    });
    require(["<?php echo $baseUrl ?>"+"/public/js/user/select_spot.js?v="+'<?= $versionNumber ?>'],function(main){
    	main.init();
	});

    </script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
