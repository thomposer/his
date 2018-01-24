<?php

/**
 * @property api接口类继承的公共类
 */

namespace app\modules\api\controllers;

use yii\filters\AccessControl;
use Yii;
use yii\web\Response;
use app\modules\user\models\User;
use app\modules\user\models\Code;
use app\common\Common;
use yii\helpers\Json;
use app\modules\module\models\Menu;

class CommonController extends \yii\web\Controller
{

    public $result;
    public $spotId; //诊所ID
    public $parentSpotId; //机构ID
    public $parentSpotCode; //当前机构编码
    public $userInfo; //登陆用户信息
    public $pageSize = 20; //分页大小
    public static $staticSpotId; //静态变量，当前诊所id
    public static $staticParentSpotId; //静态变量，当前机构id
    public function behaviors() {

        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    // 允许认证用户
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                // 默认禁止其他用户
                ],
            ],
        ];
    }

    public function beforeAction($action) {
        $this->spotId = $_COOKIE['spotId'];
        $this->parentSpotId = $_COOKIE['parentSpotId'];
        
        self::$staticSpotId = isset($_COOKIE['spotId']) ? $_COOKIE['spotId'] : '';
        self::$staticParentSpotId = isset($_COOKIE['parentSpotId']) ? $_COOKIE['parentSpotId'] : '';
        $this->userInfo = Yii::$app->user->identity;
        $this->result['success'] = true;
        $this->result['errorCode'] = 0; //错误代码
        $this->result['data'] = []; //数据
        $this->result['msg'] = ''; //提示信息
        $this->layout = false;
        $view = Yii::$app->view;
        $cacheSuffix = $this->spotId.$this->userInfo->id;
        $moduleId = \Yii::$app->controller->module->id;
        $controllerId = Yii::$app->controller->id;
        $requestUrl = '/' . $moduleId . '/' . $controllerId . '/' . $action->id; // 用户访问的路径
        $view->params['requestModuleController'] = '/' . $moduleId . '/' . $controllerId;
        $view->params['requestUrl'] = $requestUrl;
        $this->parentSpotCode = Yii::$app->cache->get(Yii::getAlias('@parentSpotCode').$cacheSuffix) ? Yii::$app->cache->get(Yii::getAlias('@parentSpotCode').$cacheSuffix) : null;
        $commonAllPermCache = Yii::getAlias('@commonAllPerm') . $this->userInfo->id . '_' . $this->parentSpotCode; //普通用户全部权限列表缓存key
        $view->params['permList'] = Yii::$app->cache->get($commonAllPermCache);
        return parent::beforeAction($action);
    }

    /**
     * (non-PHPdoc)
     * @property 行为日志记录
     * @see \yii\base\Controller::afterAction()
     */
    public function afterAction($action, $result) {

        $moduleId = Yii::$app->controller->module->id;
        $controllerId = Yii::$app->controller->id;
        $requestUrl = '/' . $moduleId . '/' . $controllerId . '/' . $action->id;
        $menu = Menu::findOne(['menu_url' => $requestUrl]);
        $module = null;

        if ($menu) {
            $module = $menu->getTitle()->select(['module_name'])->asArray()->one();
        }

        $request = Yii::$app->request;
        $getData = $request->get();
        $bodyData = $request->getBodyParams();
        if ($request->isPost) {
            /* 通用数据上报开始 */
            $dataReport = Yii::$app->params['dataReport'];
            if (isset($dataReport[$moduleId])) {
                if (in_array($controllerId, array_keys($dataReport[$moduleId]))) {
                    if (in_array($action->id, $dataReport[$moduleId][$controllerId])) {//当前action需要数据上报
                        //数据上报
                        $reportData = [
                            'url' => $request->getHostInfo() . $request->getUrl(),
                            'eventType' => 1, //1为普通URL 2为普通点击事件
                            'ip' => $request->userIP,
                            'module' => $module['module_name'],
                            'action' => $requestUrl,
                            'name' => ''//eventType==1时可为空,eventType==2时需要给出点击事件的数据统计用途(如：增加家庭成员点击数据上报)
                        ];
                        Common::dataReport($this->userInfo->id, $this->spotId, $reportData);
                    }
                }
            }
            /* 通用数据上报结束 */
        }
        return parent::afterAction($action, $result);
    }

}
