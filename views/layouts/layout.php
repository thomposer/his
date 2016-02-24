<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use yii\helpers\Url;

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
    <!-- Font Awesome -->
    <?php AppAsset::addCss($this,'https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css');?>
    <!-- Ionicons -->
    <?php AppAsset::addCss($this,'https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css');?>
    <!-- Theme style -->
    <?php AppAsset::addCss($this,'@web/public/dist/css/AdminLTE.min.css');?>
    <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
    <?php AppAsset::addCss($this,'@web/public/dist/css/skins/all-skins.css');?>
    <!-- iCheck -->
    <?php AppAsset::addCss($this,'@web/public/plugins/iCheck/flat/blue.css');?>
    <!-- Morris chart -->
    <?php //AppAsset::addCss($this,'@web/public/plugins/morris/morris.css');?>
    <!-- jvectormap -->
    <?php //AppAsset::addCss($this,'@web/public/plugins/jvectormap/jquery-jvectormap-1.2.2.css');?>
    <!-- Date Picker -->
    <?php //AppAsset::addCss($this,'@web/public/plugins/datepicker/datepicker3.css');?>
    <!-- Daterange picker -->
    <?php //AppAsset::addCss($this,'@web/public/plugins/daterangepicker/daterangepicker-bs3.css');?>
    <!-- bootstrap wysihtml5 - text editor -->
    <?php //AppAsset::addCss($this,'@web/public/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css');?>
    <?php AppAsset::addCss($this,'@web/public/css/base.css');?>
    <?= $renderCss; ?>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<?php $this->beginBody() ?>
    <div class="wrapper">
   
    <header class="main-header">
    <!-- Logo -->
    <a href="index2.html" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>HIS</b></span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b>HIS系统</b></span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top" role="navigation">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">切换</span>
      </a>

      <div class="navbar-custom-menu">
      <?php 
        // echo NavBar::begin(['options' => [
        //             'class' => 'navbar-inverse navbar-fixed-top',
        //         ]]);
        echo Nav::widget([
                'options' => ['class' => 'nav navbar-nav'],
                'dropDownCaret' => '',
                'encodeLabels' => false,
                'items' => [
                    [
                      'label' =>  Html::tag('i','',['class' => 'fa fa-envelope-o']).Html::tag('span',4,['class' => 'label label-success']),
                      'options' => ['class' => 'dropdown messages-menu'],
                      'linkOptions' => ['class' => 'dropdown-toggle','data-toggle' => 'dropdown'],
                      
                      'items' => [
                            [
                                'label' => 'You have 4 messages',
                                'options' => ['class' => 'header']
                            ],
                            [
                                'label' => '',
                                
                                'items' => [
                                    [
                                        'label' => Html::tag('div',Html::img('@web/public/dist/img/user2-160x160.jpg',['class' => 'img-circle','alt' => 'User Image']),['class' => 'pull-left']).
                                                   Html::tag('h4','Support Team'.Html::tag('small',Html::tag('i','',['class' => 'fa fa-clock-o']).'5 mins')).
                                                   Html::tag('p','Why not buy a new awesome theme?'),
                                        'url' => '#',
                                        'clientOptions' => ['class' => 'menu'],
                                    ],
                                    [
                                    'label' => Html::tag('div',Html::img('@web/public/dist/img/user2-160x160.jpg',['class' => 'img-circle','alt' => 'User Image']),['class' => 'pull-left']).
                                    Html::tag('h4','Support Team'.Html::tag('small',Html::tag('i','',['class' => 'fa fa-clock-o']).'5 mins')).
                                    Html::tag('p','Why not buy a new awesome theme?'),
                                    'url' => '#',
                                    'clientOptions' => ['class' => 'menu'],
                                    ],
                                    [
                                    'label' => Html::tag('div',Html::img('@web/public/dist/img/user2-160x160.jpg',['class' => 'img-circle','alt' => 'User Image']),['class' => 'pull-left']).
                                    Html::tag('h4','Support Team'.Html::tag('small',Html::tag('i','',['class' => 'fa fa-clock-o']).'5 mins')).
                                    Html::tag('p','Why not buy a new awesome theme?'),
                                    'url' => '#',
                                    'clientOptions' => ['class' => 'menu'],
                                    ]
                                ]
                            ],
                            [
                                'label' => 'See All Messages',
                                'url' => '#',
                                'options' => ['class' => 'footer'],
                                'linkOptions' => []
                            ]
                          
                        ]
                    ],
                    ['label' => Yii::$app->session->get('spot_name'), 'url' => ['@manageDefaultIndex']],                   
                    Yii::$app->user->isGuest ?
                        ['label' => '登录', 'url' => ['@userIndexLogin']] :
                        ['label' => Yii::$app->user->identity->username],
                        ['label' => '注销',
                            'url' => ['@userIndexLogout'],
                            'linkOptions' => ['data-method' => 'post']
                            
                      ],
                ],
            ]);
        // echo NavBar::end();
      ?>
      </div>
    </nav>
  </header>
    <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image">
          <img src="<?= $baseUrl.'/public/dist/img/user2-160x160.jpg' ?>" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p>Alexander Pierce</p>
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>
      <!-- search form -->
      <form action="<?= Url::to(['@moduleMenuSearch']) ?>" method="get" class="sidebar-form">
        <div class="input-group">
          <input type="hidden" value="<?= Yii::$app->request->csrfToken ?>" name="_csrf" />
          <input type="text" name="description" class="form-control" placeholder="菜单名称">
          
              <span class="input-group-btn">
                <button type="submit" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
        </div>
      </form>
      <!-- /.search form -->
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu">
        <li class="header">MAIN NAVIGATION</li>
        <?php if($this->params['layoutData']):?>
            <?php foreach ($this->params['layoutData'] as $v):?>
                <li class=" treeview">
                   <a href="#">
                    <i class="fa fa-dashboard"></i> <span><?=$v['module_description']?></span> <i class="fa fa-angle-left pull-right"></i>
                   </a>
                   <ul class="treeview-menu">
                   <?php foreach ($v['children'] as $k):?>
                        <li class = "<?php if(stripos($k['menu_url'],$this->params['request_module_controller']) === 0){echo "active";}?>">
                            <?= Html::a('<i class="fa fa-circle-o"></i>'.$k['description'],[$k['menu_url']]) ?>
                        </li>
                   <?php endforeach;?>
                   </ul>
                </li>
            <?php endforeach;?>
        <?php endif;?>
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>
  
        <div class="content-wrapper">
      
            <!-- Main content -->
        <section class="content">
            <!-- Small boxes (Stat box) -->
        <div class="row">
            <?= $content ?>
        </div>
        </section>
        </div>
    </div>

    <script  type="text/javascript"  src="<?php echo $baseUrl.'/public/js/lib/require.js'?>"></script>
    <script  type="text/javascript">
    require.config({
        baseUrl : "<?php echo $baseUrl.'/';?>",
        paths : {
            'jquery' : 'public/js/lib/jquery.min',
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
