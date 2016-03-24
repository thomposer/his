<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
        <!-- Theme style -->
    <?php AppAsset::addCss($this,'@web/public/dist/css/AdminLTE.min.css');?>
    <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
    <?php AppAsset::addCss($this,'@web/public/dist/css/skins/all-skins.css');?>
    <!-- iCheck -->
    <?php AppAsset::addCss($this,'@web/public/plugins/iCheck/flat/blue.css');?>
    <?php AppAsset::addCss($this, '@web/public/css/lib/main_layout.css')?>
    
    <?= $renderCss;?>
</head>
<body>

<?php $this->beginBody() ?>
    <div class="wrap">
        <?php
            NavBar::begin([
                'brandLabel' => 'My Company',
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => [
                    ['label' => '申请站点', 'url' => ['@spotSitesCreate']],
//                     ['label' => 'About', 'url' => ['/site/about']],
//                     ['label' => 'Contact', 'url' => ['/site/contact']],
                    Yii::$app->user->isGuest ?
                        ['label' => '登录', 'url' => ['@userIndexLogin']] :
                        ['label' => Yii::$app->user->identity->username],
                        ['label' => '退出',
                            'url' => ['@userIndexLogout'],
                            'linkOptions' => ['data-method' => 'post']
                            
                      ],
                ],
            ]);
            NavBar::end();
        ?>

        <div class="container">
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
            <?= $content ?>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p class="pull-left">&copy; My Company <?= date('Y') ?></p>
            <p class="pull-right"><?= Yii::powered() ?></p>
        </div>
    </footer>
    <script  type="text/javascript"  src="<?php echo $baseUrl.'/public/js/lib/require.js'?>"></script>
    <script>
    require.config({
        baseUrl: "<?php echo $baseUrl.'/';?>",
        paths: {
            'dist' : 'public/dist/js',
            'js' : 'public/js',
            'plugins' : 'public/plugins'
        }
    });
    require(["<?php echo $baseUrl ?>"+"/public/js/lib/layout.js"],function(main){
    	main.init();
	});
    </script>
    
<?= $renderJs; ?>
    
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
