<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\assets\AppAsset;
$actionId=Yii::$app->controller->action->id;
$curUrlArr=explode('-',$actionId);
$curUrl=$curUrlArr[0];
?>
<?php AppAsset::addCss($this, '@web/public/css/template-manage/sidebar.css') ?>
<div class="col-md-2">
    <div class=" box">
        <div class="template-sidebar">
            <div class="tmpe-bar-title">模板目录</div>
            <section class="sidebar-wrapper">
                <ul class="sidebar-menu-template">
                    <li class="treeview active">
                        <ul class="treeview-menu menu-open" style="display: block !important;">
                            <li <?php echo ($curUrl == 'package') ? 'class=active' : ''; ?>><?= Html::a('医嘱模板/套餐', Url::to(['@spot_setOutpatientPackageTemplatePackageTemplateIndex'])) ?></li>

                        </ul>
                    </li>
                </ul>
            </section>
        </div>
    </div>
</div>