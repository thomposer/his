<?php

use yii\helpers\Url;
use yii\helpers\Html;
use app\assets\AppAsset;
use yii\web\View;

$actionId = Yii::$app->controller->action->id;
$curUrlArr = explode('-', $actionId);
$curUrl = $curUrlArr[0];
/* @var $this yii\web\View */
?>
<?php AppAsset::addCss($this, '@web/public/css/template-manage/sidebar.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/lib/search.css') ?>
<?php if ($type == 1): ?>
    <div class="col-md-2">
        <div class=" box">
            <div class="template-sidebar">
                <div class="tmpe-bar-title">模板目录</div>
                <section class="sidebar-wrapper">
                    <ul class="sidebar-menu-template" >
                        <li class="treeview active">
                            <a href="javascript::viod(0)">
                                <div class="template-title">医生模板</div> <i class="fa fa-angle-left pull-right fa-lg fa-color" style="float: right;background-position-y: 95%;"></i>
                            </a>
                            <ul class="treeview-menu menu-open">
                                <li <?php echo ($curUrl == 'case') ? 'class=active' : ''; ?>><?= Html::a('病历模板', Url::to(['case-index'])) ?></li>
                                <li <?php echo ($curUrl == 'child') ? 'class=active' : ''; ?>><?= Html::a('儿保模板', Url::to(['child-index'])) ?></li>
                            </ul>
                        </li>
                        <li class="treeview active">
                            <a href="javascript::viod(0)">
                                <div class="template-title">护理模板</div> <i class="fa fa-angle-left pull-right fa-lg fa-color" style="float: right;background-position-y: 95%;"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li <?php echo ($curUrl == 'nursing') ? 'class=active' : ''; ?>><?= Html::a('护理模板', Url::to(['nursing-index'])) ?></li>
                            </ul>
                        </li>
                    </ul>
                </section>
            </div>
        </div>
    </div>
<?php elseif($type == 2): ?>
	<div class="col-md-2">
        <div class=" box">
            <div class="template-sidebar">
                <div class="tmpe-bar-title">模板目录</div>
                <section class="sidebar-wrapper">
                    <ul class="sidebar-menu-template" >
                        <li class="treeview active">
                            <a href="javascript:void(0)">
                                <div class="template-title">病历模板</div> <i class="fa fa-angle-left pull-right fa-lg fa-color" style="float: right;background-position-y: 95%;"></i>
                            </a>
                            <ul class="treeview-menu menu-open">
                                <li <?php echo ($curUrl == 'case') ? 'class=active' : ''; ?>><?= Html::a('非专科病历模板', Url::to(['case-template'])) ?></li>
                                <li <?php echo ($curUrl == 'child') ? 'class=active' : ''; ?>><?= Html::a('儿保病历模板', Url::to(['child-index'])) ?></li>
                                <li <?php echo ($curUrl == 'dentalfirst') ? 'class=active' : ''; ?>><?= Html::a('口腔初诊病历模板', Url::to(['dentalfirst-index'])) ?></li>
                                <li <?php echo ($curUrl == 'dentalreturnvisit') ? 'class=active' : ''; ?>><?= Html::a('口腔复诊病历模板', Url::to(['dentalreturnvisit-index'])) ?></li>
                                
                            </ul>
                        </li>
                        <li class="treeview active">
                            <a href="javascript:void(0)">
                                <div class="template-title">医嘱模板</div> <i class="fa fa-angle-left pull-right fa-lg fa-color" style="float: right;background-position-y: 95%;"></i>
                            </a>
                            <ul class="treeview-menu">
								<li <?php echo ($curUrl == 'recipe') ? 'class=active' : ''; ?>><?= Html::a('医嘱模板分类', Url::to(['recipe-type-index'])) ?></li>  
								<li <?php echo ($curUrl == 'inspect') ? 'class=active' : ''; ?>><?= Html::a('实验室检查模板', Url::to(['inspect-template-index'])) ?></li>
                           		<li <?php echo ($curUrl == 'check') ? 'class=active' : ''; ?>><?= Html::a('影像学检查模板', Url::to(['check-template-index'])) ?></li>
                                <li <?php echo ($curUrl == 'cure') ? 'class=active' : ''; ?>><?= Html::a('治疗模板', Url::to(['cure-template-index'])) ?></li>
                           		<li <?php echo ($curUrl == 'recipetemplate') ? 'class=active' : ''; ?>><?= Html::a('处方模板', Url::to(['recipetemplate-index'])) ?></li>
                          </ul>
                        </li>
                    </ul>
                </section>
            </div>
        </div>
    </div>
    
<?php elseif($type == 3): ?>
    <div class="col-md-2">
        <div class=" box">
            <div class="template-sidebar">
                <div class="tmpe-bar-title">目录</div>
                <section class="sidebar-wrapper">
                    <ul class="sidebar-menu-template">
                        <li class="treeview active">
                            <a href="#">
                                <div class="template-title">医嘱</div> <i class="fa fa-angle-left pull-right fa-lg fa-color" style="float: right;background-position-y: 95%;"></i>
                            </a>
                            <ul class="treeview-menu menu-open">
                                <li <?php echo ($curUrl == 'pharmacy') ? 'class=active' : ''; ?>><?= Html::a('处方', Url::to(['pharmacy-stock-info'])) ?></li>
                            </ul>
                        </li>
                        <li class="treeview active">
                            <a href="#">
                                <div class="template-title">非医嘱</div> <i class="fa fa-angle-left pull-right fa-lg fa-color" style="float: right;background-position-y: 95%;"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li <?php echo ($curUrl == 'consumables') ? 'class=active' : ''; ?>><?= Html::a('医疗耗材', Url::to(['consumables-stock-info'])) ?></li>
                                <li <?php echo ($curUrl == 'material') ? 'class=active' : ''; ?>><?= Html::a('其他', Url::to(['material-stock-info'])) ?></li>
                            </ul>
                        </li>
                    </ul>
                </section>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php 

$this->registerJs("
  $('.sidebar-menu-template .pull-right').on('click',function(){
	var hasActive = $(this).parent('a').parent('li.treeview').hasClass('active');
	if(hasActive){
		$(this).parent('a').siblings('.treeview-menu').hide();
		$(this).parent('a').parent('li.treeview').removeClass('active');
	}else{
		$(this).parent('a').siblings('.treeview-menu').show();
		$(this).parent('a').parent('li.treeview').addClass('active');

	}
	
});  

",View::POS_END);

?>