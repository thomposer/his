<?php

use yii\helpers\Url;
use yii\helpers\Html;
use app\assets\AppAsset;

$actionId = Yii::$app->controller->action->id;
$curUrlArr = explode('-', $actionId);
$curUrl = $curUrlArr[0];
$navData=$this->params['navData'];
?>
<?php AppAsset::addCss($this, '@web/public/css/template-manage/sidebar.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/lib/search.css') ?>
<?php AppAsset::addScript($this, '@web/public/dist/js/app.min.js'); ?>
<div class="col-md-2">
    <div class=" box">
        <div class="template-sidebar">
            <div class="tmpe-bar-title"><?= $navData['title'] ?></div>
            <section class="sidebar-wrapper">
                <ul class="sidebar-menu-template">
                    <li class="treeview active">
                        <ul class="treeview-menu menu-open" style="display: block !important;">
                            <?php
//                            print_r($this->params['permList']);exit;
                            $a = '';
                            foreach ($navData['menu'] as $key => $val) {
                                if (!isset($this->params['permList']['role']) && !in_array(Yii::getAlias($val['urlAlias']), $this->params['permList'])) {
                                    continue;
                                }
                                $a.='<li ';
                                if ($curUrl == $val['curUrl']) {
                                    $a.=' class=active ';
                                }
                                $a.=' >';
                                $a.=Html::a($val['name'], Url::to([$val['urlAlias']]));
                                $a.='</li>';
                            }
                            echo $a;
                            ?>
                        </ul>
                    </li>
                </ul>
            </section>
        </div>
    </div>
</div>
