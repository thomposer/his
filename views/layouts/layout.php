<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use yii\helpers\Url;
use yii\base\Widget;
use yii\web\View;
use johnitvn\ajaxcrud\CrudAsset;
use yii\bootstrap\Modal;
$cache = Yii::$app->cache;
/* @var $this \yii\web\View */
/* @var $content string */
AppAsset::register($this);
CrudAsset::register($this);
$baseUrl = Yii::$app->request->baseUrl;
$requestModuleController = $this->params['requestModuleController'];
$userInfo = Yii::$app->user->identity;
$parentSpotId = $_COOKIE['parentSpotId'];
$spotId = $_COOKIE['spotId'];
$sideBar = $_COOKIE['sidebar'];
$sidebarType = $_COOKIE['sidebarType'];
$defaultSpotId = isset($_COOKIE['defaultSpotId'])?$_COOKIE['defaultSpotId']:$userInfo->default_spot;
$spotList = $cache->get(Yii::getAlias('@spotList').$parentSpotId.'_'.$userInfo->id);
$iconImgCacheKey = Yii::getAlias('@spotIcon').$spotId.$userInfo->id;
$icon_img = $cache->get($iconImgCacheKey)?Yii::$app->params['cdnHost'].$cache->get($iconImgCacheKey):$baseUrl.'/public/img/common/img_clinic_default.png';
$spotName = $cache->get(Yii::getAlias('@spotName').$spotId.$userInfo->id);
$rootPath = Yii::getAlias('@RootPath');
$public_img_path = $baseUrl . '/public/img/';
Yii::info('诊所logo图：'.$icon_img);
$this->params['permList'] = $this->params['permList']?$this->params['permList']:[];
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">    
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <link rel="icon" href="<?php echo $baseUrl.'/favicon.ico'; ?>" type="image/x-icon">
    <?php $this->head() ?>
    <?= $renderCss; ?>
</head>
<body class="hold-transition <?=$sideBar?$sideBar:'skin-blue sidebar-mini'?>">
<?php $this->beginBody() ?>
    <div class="wrapper <?=($sidebarType==1)?'':'fixed'?>">
   
    <header class="main-header">
    <!-- Logo -->
        <?=
            Html::a(Html::tag('span', Html::img($icon_img, ['onerror' => "this.src='{$baseUrl}/public/img/common/img_clinic_default.png'"]), ['class' => 'logo-mini']) .
            Html::tag('div',Html::tag('span', Html::img($icon_img, ['style' => 'margin-left:5px;','onerror' => "this.src='{$baseUrl}/public/img/common/img_clinic_default.png'"]) ,['class'=>'pull-left']) .
            Html::tag('div',Html::tag('p',Html::encode($spotName)),['class'=>'clinic-name pull-left']), ['class' => 'logo-lg']), ['@manageIndex'], ['class' => 'logo', 'title' => $spotName])
        ?>

    <nav class="navbar navbar-static-top" role="navigation">
      <!-- Sidebar toggle button-->
      <?= Html::a(Html::tag('span','切换',['class' => 'sr-only']),'#',['class' => 'sidebar-toggle sidebar-status','data-toggle' => 'offcanvas','role' => 'button']) ?>
      <div class="navbar-custom-left-menu">
      <?=
        Breadcrumbs::widget([
            'homeLink' => false,
            'itemTemplate' => "<li>{link}</li>\n", // template for all links
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],            
        ]);
      ?>
      </div>
      <div class="navbar-custom-menu">
      		<?php if((isset($this->params['permList']['role'])||in_array(Yii::getAlias('@outpatientOutpatientUpdate'), $this->params['permList']))):?>
      		<?= $this->render('_medical',['medicalNum' => $this->params['medicalNum'],'medicalList' => $this->params['medicalList']]) ?>
           	<?php endif;?>
            <?= $this->render('_board') ?>
            <?= $this->render('_message',['messageNum' => $this->params['messageNum'],'messageList' => $this->params['messageList']]) ?>
            <?php
            $items[] = [
                          'label' => '账号信息',
                          'url' => ['@userManageInfo'],
                       ];
            if(!isset($this->params['permList']['role'])){
                if(count($spotList) > 1){
                    $items[] = [
                                    'label' => '切换诊所',
                                    'url' => false,
                                    'options' => ['data-toggle'=>"modal",'data-target'=>'#myModal'],
                                ];
                }
            }
            $items[] = [
                          'label' => '修改密码',
                          'url' => ['@userManageEditPassword'],
                       ];
            $items[] = [
                         'label' => '退出',
                         'url' => ['@userIndexLogout'],
                         'linkOptions' => ['data-method' => 'post']
                       ];
              
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav nav'],
                'encodeLabels' => false,
                'items' => [
                    [
                        'label' => Html::img($userInfo->head_img?Yii::$app->params['cdnHost'].$userInfo->head_img:$baseUrl.'/public/img/user/img_user_small.png',['class' => 'user-image','onerror'=>"this.src='{$public_img_path}default.png'"]).Html::tag('span',Html::encode($userInfo->username),['class' => 'hidden-xs']), 
                        'options' => ['class' => 'dropdown'],
                        'linkOptions' => ['class' => 'dropdown-toggle','data-toggle' => 'dropdown'],
                        'items' => $items
                        
                    ],
                ],
            ]);          
            ?>
      </div>
    </nav>
  </header>
    <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar" id="sidebar">
      <ul class="sidebar-menu">
<!--        <li class="naviHeader">&nbsp;</li>-->
        <?php if($this->params['layoutData']):?>
            
            <?php foreach ($this->params['layoutData'] as $v):?>
            <?php if(isset($v['type'] )&&$v['type'] == 2):?>
                <li class=" treeview">
                   <a href="#">
                    <?= Html::tag('i','',['class' => 'icon_url','style' => 'background:url(\''.$v['icon_url'].'\');background-size:16px;']) ?><span><?=$v['module_description']?></span> <i class="fa fa-angle-left pull-right"></i>
                   </a>
                   <ul class="treeview-menu">
                   <?php foreach ($v['children'] as $k):?>
                        <li>
                            <?= Html::a(Html::tag('i','',['class' => 'fa fa-circle-o circle_scale']).$k['description'],[$k['menu_url']]) ?>
                        </li>
                   <?php endforeach;?>
                   </ul>
                </li>
            <?php else:?>
                <li class = 'treeview'>
                	<?php
                	   reset($v['children']);
                	   $current = current($v['children']); 
                	?>
                    <?= Html::a(Html::tag('i','',['class' => 'icon_url ','style' => 'background:url(\''.$v['icon_url'].'\');background-size:16px;']).Html::tag('span',$v['module_description']),[$current['menu_url']]) ?>
                </li>
            <?php endif;?>
            <?php endforeach;?>
            
        <?php endif;?>
      </ul>
     </section>
    <!-- /.sidebar -->
  </aside>
  
        <div class="content-wrapper">
        
        <!-- Main content -->
        <section class="content" id = 'content'>
            <!-- Small boxes (Stat box) -->
        <div class="row">
            <?= $content ?>
        </div>
        </section>
        </div>
    </div>
<!-- Modal 切换诊所 -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" id = 'modal-select-spot' role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">切换诊所</h4>
      </div>
      <div class="modal-body">
           <?= $this->render('_switchSpot',['spotList' => $spotList,'defaultSpotId' => $defaultSpotId]) ?>
      </div>
    </div>
  </div>
</div>
<?php 
$options = [
    'data-method' => false,
    'data-request-method' => 'get',
    'role' => 'modal-remote',
    'class' => 'hidden',
    'id' => 'inspect-warn-global',
    'data-modal-size' => 'large'
];
if($this->params['doctorWarningCount'] > 0){//全局实验室检查告警按钮
    echo Html::a('告警',['@apiMessageInspectWarnInfo'],$options);
}
?>

<?php 

AppAsset::addScript($this,'@web/public/js/lib/require.js');
AppAsset::addScript($this,'@web/public/plugins/slimScroll/jquery.slimscroll.min.js');
AppAsset::addScript($this,'@web/public/dist/js/app.min.js');
$selectSpotUrl = Url::to(['@manageSites']);
$successMsg = Yii::$app->getSession()->getFlash('success');
$errorMsg = Yii::$app->getSession()->getFlash('error');
$versionNumber = Yii::getAlias("@versionNumber");
$cdnHost = Yii::$app->params['cdnHost'];
$doctorWarningCount  = $this->params['doctorWarningCount'];
$layoutJs = <<<JS
    var versionNumber = '$versionNumber';//版本号
    require.config({
        baseUrl : "$baseUrl/",
        paths : {
            'dist' : 'public/dist/js',
            'js' : 'public/js',
            'plugins' : 'public/plugins',
            'template' : 'public/js/lib/template',
            'tpl' : 'public/tpl',
            'upload' : 'public/upload',
        },
        urlArgs : "bust=" + versionNumber
    });
    define('jquery', function() {return window.$;});
    var requestModuleController = '$baseUrl$requestModuleController/';
    var successMsg = '$successMsg';
    var errorMsg = '$errorMsg';
    var selectSpotUrl = '$selectSpotUrl';
    var cdnHost = '$cdnHost';//cdn域名
    var baseUrl = '$baseUrl';//根目录路径
    var doctorWarningCount = '$doctorWarningCount';
    require([baseUrl+"/public/js/lib/layout.js"],function(main){
     	main.init();
 	  });
JS;
$this->registerJs($layoutJs,View::POS_END);

?>
<?php Modal::begin([
    "id"=>"ajaxCrudModal",
    'class' => 'ajaxCrudModalLayout',
    "footer"=>"",// always need it for jquery plugin
    'clientOptions' => ['backdrop' => 'static'],
    'options'=>['tabindex' => '']
])?>
<?php  Modal::end(); ?>
<?php $this->endBody() ?>
<?= $renderJs; ?>
</body>
</html>
<?php $this->endPage() ?>
