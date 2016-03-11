<?php
use yii\helpers\Html;
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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1,minimum-scale=1, user-scalable=no,minimal-ui" name="viewport">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
     <?php $this->head() ?>
    <!-- alertifyJs -->
    <link  href="<?php echo $baseUrl.'/public/alertifyJs/build/css/alertify.min.css'?>" rel="stylesheet">
    <link  href="<?php echo $baseUrl.'/public/alertifyJs/build/css/themes/bootstrap.css'?>" rel="stylesheet">
    <link  href="<?php echo $baseUrl.'/public/css/bootstrap/bootstrap.css'?>" rel="stylesheet">
</head>
<body style="background: white;">
<?php $this->beginBody();?>
    <script  type="text/javascript"  src="<?php echo $baseUrl.'/public/js/lib/require.js'?>"></script>
    <script  type="text/javascript">
        require.config({
            baseUrl : "<?php echo $baseUrl.'/';?>",
            paths : {
                'jquery' : 'public/js/lib/jquery.min',
                'dist' : 'public/dist/js',
                'js' : 'public/js',
                'plugins' : 'public/plugins',
                'alertifyJs' : 'public/alertifyJs',
            }
        });
        var title = "<?= Yii::$app->request->get('title') ?>";
    	var message = "<?= Yii::$app->request->get('message'); ?>";
    	var url = "<?= Yii::$app->request->get('url') ?>";
    	require(["<?= $baseUrl ?>"+"/public/js/lib/alert.js"],function(main){
     		main.init();
    	});
    
    </script>
    <?php $this->endBody() ?>
</body>
 </html>              
