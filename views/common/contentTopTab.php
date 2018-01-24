<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\web\View;

/* @var $this yii\web\View */
$params = Yii::$app->request->queryParams;
$titleData = [];
foreach ($data['titleData'] as $title) {
    $url = substr($title['url'], 0, strpos($title['url'], Yii::$app->urlManager->suffix));
    if (in_array($url, $this->params['permList'])) {
        $titleData[] = $title;
    }
}
foreach ($titleData as &$v) {
    $v['current'] = 0;
}
$activeData = (isset($data['activeData']) && !empty($data['activeData']['type'])) ? $data['activeData']['type'] : 1;
$type = isset($params['type']) ? $params['type'] : $activeData;
//}
$currentUrl = Yii::$app->request->baseUrl . '/' . Yii::$app->request->getPathInfo();
$arrCount = $titleData ? count($titleData) : 1;
$width = 100 / $arrCount . '%';
$bgColor = '#E9EFF6';
if (isset($data['tabLevel'])) {
    $data['tabLevel'] == 2 && $bgColor = '#F5F6F7';
}
?>
<?php $this->beginBlock('renderCss') ?>
<?php
$css = <<<CSS
     .top-bar{
        background: $bgColor;
        height: 50px;
        line-height: 50px;
        border-radius : 4px 4px 0px 0px;
        /*margin-bottom:  10px;*/
    }
    .top-bar a{
        color: #8190a7;
    }
    .con-background{
        background-color: #ffffff;
    }
    .left-bar{
        width: $width;
        float: left;
        text-align: center;
    }
    .center-bar{
        float: left;
        color: #b9bfca;
        line-height: 50px;
        width: 1px;
    }
    .cneter-bar-icon{
        border-left: 1px solid #b9bfca;
        font-size: 20px;
    }
    .cneter-bar-icon-active{
         border-left: 1px solid #b9bfca;
        font-size: 46px;
   }
    .right-bar{
        width: $width;
        float: left;
        text-align: center;
    }
    .triage_common{
        padding-left: 35px;
        font-size: 14px;
        line-height: 50px;
    }
    .triage_common_none{
        font-size: 14px;
        line-height: 50px;
    }
    .tab_triage_active{
        color: #76A6EF;
    }
    .tab_history_active{
        color: #76A6EF;
    }
    .bar-word{
        font-size: 14px;
    }
    .first-bar {
        border-radius: 4px 0 0 0;
    }
    .last-bar {
        border-radius: 0 4px 0 0;
    }
CSS;
$this->registerCss($css);
?>
<?php $this->endBlock() ?>
<div class="top-bar">
    <?php foreach ($titleData as $key => &$tab): ?>
        <?php
            $positionClass = '';
            if (count($titleData) > 0) {
                if ($key == 0) {
                    $positionClass = ' first-bar';
                } else if ($key === count($titleData) - 1) {
                   $positionClass = ' last-bar';
                }
            } 

        ?>
        <?php if (isset($tab['icon_img']) && !empty($tab['icon_img'])): ?>
            <?php
            $style = '';
            if ($currentUrl != $tab['url']) {
                if (isset($tab['type']) && $tab['type'] == $type) {
                    $style .= 'background:url(' . $tab['icon_img'] . ') no-repeat 10px 2px;';
                    $style .= ' background-size:14px;';
                    $class = 'tab_triage_active';
                    $con_class = 'con-background';

                    $tab['current'] = 1;
                    
                } else {
                    $style .= 'background:url(' . $tab['icon_img'] . ') no-repeat 10px -14px;';
                    $style .= ' background-size:14px;';
                    $class = '';
                    $con_class = '';
                }
            } else {
                $style .= 'background:url(' . $tab['icon_img'] . ') no-repeat 10px 2px;';
                $style .= ' background-size:14px;';
                $class = 'tab_triage_active';
                $con_class = 'con-background';
        
                $tab['current'] = 1;
            }
//                 echo '<div class="left-bar '.$con_class.'">';
//                echo Html::tag('span',Html::a($tab['title'],$tab['url']),['class' => 'triage_common '.$class,'style' => $style]);
//                echo '<a href='".$tab[\"url\"]."' class="left-bar"'.$con_class.'>';
            $a = '<a href="' . $tab['url'] . '" class="left-bar ' . $con_class . $positionClass .'" data-pjax = "0">';
            $a.=Html::tag('span', $tab['title'], ['class' => 'triage_common ' . $class, 'style' => $style]);
            if ($key != 0 && ($titleData[$key - 1]['current'] == 0) && ($titleData[$key]['current'] == 0)) {
                $a.='<div class="center-bar"> <span class="cneter-bar-icon"></span></div>';
            }
            $a.= '</a>';
            echo $a;
            ?>
        <?php else: ?>
            <?php
            if ($currentUrl != $tab['url']) {
                if (isset($tab['type']) && $tab['type'] == $type) {
                    $class = 'tab_triage_active';
                    $con_class = 'con-background';
                    $tab['current'] = 1;                   

                } else {
                    $class = '';
                    $con_class = '';
                    
                }
            } else {
                $class = 'tab_triage_active';
                $con_class = 'con-background';
                $tab['current'] = 1;

                
            }
//                echo '<div class="left-bar '.$con_class.'">';
////                echo Html::tag('span',Html::a($tab['title'],$tab['url']),['class' => 'triage_common '.$class);
//                echo Html::tag('span',Html::a($tab['title'],$tab['url']),['class' => $class]);
//                $a='<a href="'.$tab['url'].'" class="left-bar '.$con_class.'">';
            $a = '<a href="' . $tab['url'] . '" class="left-bar ' . $con_class . $positionClass .'">';
            $a.=Html::tag('span', $tab['title'], ['class' => 'triage_common_none ' . $class]);
            if ($key != 0 && ($titleData[$key - 1]['current'] == 0) && ($titleData[$key]['current'] == 0)) {
                $a.='<div class="center-bar"> <span class="cneter-bar-icon"></span></div>';
            }
            $a.= '</a>';
            echo $a;
            ?>
        <?php endif; ?>
        <?php if ($key != ($arrCount - 1)): ?>
        <?php endif; ?>
    <?php endforeach; ?>

</div>