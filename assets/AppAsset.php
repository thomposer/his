<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;
use yii;
/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle {

    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
//         'public/css/bootstrap/font-awesome.min.css',
//         'public/dist/css/AdminLTE.min.css',
//         'public/dist/css/skins/all-skins.css',
//         'public/plugins/iCheck/flat/blue.css',

        'public/css/lib/main.min.css?v=20171225',
        'public/css/lib/base.css?v=20171225'

    ];
    public $jsOptions = [
        'position' => \yii\web\View::POS_END
    ];
    public $cssOptions = [
        'position' => \yii\web\View::POS_HEAD
    ];
    public $js = [
        'public/js/bootbox/bootbox.js',
        'public/js/bootbox/main.js',
        'public/js/lib/common.js'
    ];
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
        'yii\web\YiiAsset',
        'bedezign\yii2\audit\web\JSLoggingAsset'
    ];

    //定义按需加载JS方法，注意加载顺序在最后
    public static function addScript($view, $jsfile) {
        $versionNumber = Yii::getAlias("@versionNumber");
        $view->registerJsFile($jsfile.'?v='.$versionNumber, [AppAsset::className(), 'depends' => 'app\assets\AppAsset']);
    }

    //定义按需加载css方法，注意加载顺序在最后
    public static function addCss($view, $cssfile) {
        $versionNumber = Yii::getAlias("@versionNumber");
        $view->registerCssFile($cssfile."?v=$versionNumber", [AppAsset::className(), 'depends' => 'app\assets\AppAsset']);
    }

}
