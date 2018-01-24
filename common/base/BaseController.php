<?php

namespace app\common\base;

use app\modules\message\models\MessageCenter;
use app\modules\patient\models\Patient;
use app\modules\spot_set\models\Room;
use app\modules\user\models\User;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\modules\module\models\Title;
use app\modules\module\models\Menu;
use yii\helpers\Url;
use app\modules\behavior\models\BehaviorRecord;
use yii\helpers\Json;
use app\common\Common;
use app\modules\rbac\models\AssignmentForm;
use app\modules\rbac\models\ItemChildForm;
use app\modules\spot\models\Spot;
use yii\db\Query;
use app\modules\user\models\UserSpot;
use app\modules\spot_set\models\PaymentConfig;
use yii\caching\DbDependency;
use app\modules\spot_set\models\MedicalTips;

class BaseController extends Controller
{

    public $wxcode; //当前诊所编码
    public $spotId; //当前诊所ID
    public $spotName;//诊所名称
    public $parentSpotId; //当前机构ID
    public $parentSpotCode; //当前机构编码
    public $parentSpotName;//当前机构名称
    public $rolePrefix; //当前诊所角色前缀
    public $permissionPrefix; //当前诊所权限分类前缀
    public $rootRole; //当前诊所的角色根分类
    public $rootPermission; //当前诊所的权限根分类
    public $parentRolePrefix; //当前诊所角色前缀
    public $parentPermissionPrefix; //当前诊所权限分类前缀
    public $parentRootRole; //当前诊所的角色根分类
    public $parentRootPermission; //当前诊所的权限根分类
    public $layoutData; //用户菜单列表
    public $pageSize = 20; //分页大小
    public $userInfo; //用户的user信息;
    public $isSuperSystem = false;
    public $manager;
    public $result; //json返回容器
    public $createSpot; //是否需要默认创建诊所，1为是，0为否
    private $readApi = array('index', 'view', 'list', 'get'); // 不做记录的动作

    public function init() {
        parent::init();
        $this->userInfo = Yii::$app->user->identity;
        $this->createSpot = isset($_COOKIE['createSpot']) ? $_COOKIE['createSpot'] : null;
        $this->spotId = isset($_COOKIE['spotId']) ? $_COOKIE['spotId'] : null;
        $this->parentSpotId = isset($_COOKIE['parentSpotId']) ? $_COOKIE['parentSpotId'] : null;
        $cacheSuffix = $this->spotId.$this->userInfo->id;
        $this->wxcode = Yii::$app->cache->get(Yii::getAlias('@spot').$cacheSuffix) ? Yii::$app->cache->get(Yii::getAlias('@spot').$cacheSuffix) : null;
        $this->spotName = Yii::$app->cache->get(Yii::getAlias('@spotName').$cacheSuffix)?Yii::$app->cache->get(Yii::getAlias('@spotName').$cacheSuffix) : null;
        
        $this->parentSpotCode = Yii::$app->cache->get(Yii::getAlias('@parentSpotCode').$cacheSuffix) ? Yii::$app->cache->get(Yii::getAlias('@parentSpotCode').$cacheSuffix) : null;
        $this->parentSpotName = Yii::$app->cache->get(Yii::getAlias('@parentSpotName').$cacheSuffix) ? Yii::$app->cache->get(Yii::getAlias('@parentSpotName').$cacheSuffix) : null;

        $this->manager = Yii::$app->authManager;
        $this->result['success'] = true;
        $this->result['errorCode'] = 0; //默认为0，则没有错误
        $this->result['msg'] = '';
        if ($this->spotId) {
            //设置微信支付的配置key信息
            $expireTime = time() + Yii::getAlias('@loginCookieExpireTime');
            setcookie('wechatSpotId', $this->spotId, $expireTime, '/', null, null, true);
        }
    }

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
        parent::beforeAction($action);
        $this->layout = false;
        $view = Yii::$app->view;
        $this->rolePrefix = $this->wxcode . '_roles_';
        $this->permissionPrefix = $this->wxcode . '_permissions_';
        $this->rootRole = $this->wxcode . '_roles';
        $this->rootPermission = $this->wxcode . '_permissions';

        $this->parentRolePrefix = $this->parentSpotCode . '_roles_';
        $this->parentPermissionPrefix = $this->parentSpotCode . '_permissions_';
        $this->parentRootRole = $this->parentSpotCode . '_roles';
        $this->parentRootPermission = $this->parentSpotCode . '_permissions';
        $moduleId = \Yii::$app->controller->module->id;
        $controllerId = Yii::$app->controller->id;
        $requestUrl = '/' . $moduleId . '/' . $controllerId . '/' . $action->id; // 用户访问的路径
        $view->params['requestModuleController'] = '/' . $moduleId . '/' . $controllerId;
        $view->params['requestUrl'] = $requestUrl;
        
        
        //若站点信息失效，则直接重新登录
        if ($this->userInfo == null || $this->parentSpotId == null || $this->parentSpotId == '' || $this->parentSpotCode == null || $this->wxcode = null || !$this->spotId) {
            $currentUrl = Yii::$app->request->baseUrl . '/' . Yii::$app->request->getPathInfo();
            $expireTime = time() + Yii::getAlias('@loginCookieExpireTime');
            setcookie('requestUrl', $currentUrl, $expireTime, '/', null, null, true);
            Common::logout();
            $this->redirect(Url::to(['@userIndexLogin']));
            return;
        }
        $this->setWxPayConfig();
        $this->getMessageCenter();
//         Yii::$app->cache->set(Yii::getAlias('@doctorWarning').$this->spotId.'_'.$this->userInfo->id,0);
        $view->params['doctorWarningCount'] = Yii::$app->cache->get(Yii::getAlias('@doctorWarning').$this->spotId.'_'.$this->userInfo->id);
        
        
        $systemPermission = Yii::getAlias('@systemPermission');
        //如果用户拥有系统管理员－systems角色，则免验证
        if ($this->manager->checkAccess($this->userInfo->id, $systemPermission)) {
            $this->isSuperSystem = true;
            $this->getUserRole($systemPermission);
            return parent::beforeAction($action);
        }
        if ($this->createSpot == 1 && ($requestUrl !== Yii::getAlias('@spotIndexCreate'))) {
            $this->redirect(Url::to(['@spotIndexCreate']));
            return;
        }
        //若有站点权限，则站点首页免验证,或者错误页面 	   
        if ($requestUrl === Yii::getAlias('@manageIndex') || $requestUrl == Yii::getAlias('@userDefaultError')) {
            $this->getUserRole();
            return parent::beforeAction($action);
        }

        //允许直接访问的url
        $allowUrl = [
            Yii::getAlias('@moduleMenuSearch'),
            Yii::getAlias('@userManageEditPassword'),
            Yii::getAlias('@userManageInfo'),
            Yii::getAlias('@spot_setBoardPreview'), //公告板的url
        ];
        // 无权限可以访问的url
        if (in_array($requestUrl, $allowUrl)) {
            $this->getUserRole();
            return parent::beforeAction($action);
        }
        $userPerm = Yii::$app->cache->get(Yii::getAlias('@commonAllPerm') . $this->userInfo->id);
        if ($userPerm) {
            if (in_array($requestUrl, $userPerm)) {
                $this->getUserRole();
                return parent::beforeAction($action);
            }
        }
        if (!$this->manager->checkAccess($this->userInfo->id, $this->wxcode . $requestUrl) && !$this->manager->checkAccess($this->userInfo->id, $this->parentSpotCode . $requestUrl)) {
            $this->getUserRole();
            return Common::showMessage();
        }
        $this->getUserRole();
        return parent::beforeAction($action);
    }

    //获取用户当前站点所有的权限，并渲染进layout
    private function getUserRole($role = null) {
        $datas = '';
        $list = [];
        $spotList = '';
        $view = Yii::$app->view;
        $session = Yii::$app->session;
        $cache = Yii::$app->cache;
        $user_id = $this->userInfo->id;
        $datas = Title::getMenus($this->isSuperSystem);
        //获取该机构下用户所属所有诊所列表，并缓存
        $parentSpotId = $this->parentSpotId;
        if(!$parentSpotId){
            Common::logout();
            $this->redirect(Url::to(['@userIndexLogin']));
            return;
        }
        $spotListCache = Yii::getAlias('@spotList') . $this->parentSpotCode . '_' . $user_id;
        if (!$cache->get($spotListCache)) {
            $query = new Query();
            $query->from(['a' => Spot::tableName()]);
            $query->select(['a.id', 'a.spot_name']);
            $query->where(['a.parent_spot' => $parentSpotId, 'a.status' => 1]);
            if ($this->isSuperSystem) {
                $dependencySpotSql = 'select count(1) from ' . Spot::tableName() . ' where parent_spot = "' . $parentSpotId . '" and status = 1';
            } else {
                $dependencySpotSql = 'select count(1) from ' . Spot::tableName() . ' as a left join ' . UserSpot::tableName() . ' as b on a.id = b.spot_id where a.parent_spot = "' . $parentSpotId . '" and a.status = 1 and b.user_id = ' . $user_id;
                $query->addSelect('b.spot_id');
                $query->leftJoin(['b' => UserSpot::tableName()], '{{a}}.id = {{b}}.spot_id');
                $query->andWhere(['b.user_id' => $user_id]);
            }
            $spotList = $query->all();
            $dependencySpot = new \yii\caching\DbDependency([
                'sql' => $dependencySpotSql
            ]);
            $cache->set($spotListCache, $spotList, 300, $dependencySpot);
        }
        $commonAllPermCache = Yii::getAlias('@commonAllPerm') . $user_id . '_' . $this->parentSpotCode; //普通用户全部权限列表缓存key
        if ($role != null) {
            $list = ['role' => true];
            $cache->set($commonAllPermCache,$list);
        } else {
            $dependency = new \yii\caching\DbDependency([
                'sql' => 'select count(1) from ' . AssignmentForm::tableName() . ' as a left join ' . ItemChildForm::tableName() . ' as b on a.item_name = b.parent where a.user_id = "' . $user_id . '" and a.item_name like "' . $this->parentSpotCode . '%"',
            ]);
            $commonRoleMenuCache = Yii::getAlias('@commonRoleMenu') . $user_id . '_' . $this->parentSpotCode; //普通用户菜单url缓存key
//             $commonAllPermCache = Yii::getAlias('@commonAllPerm') . $user_id . '_' . $this->parentSpotCode; //普通用户全部权限列表缓存key
            $commonRoleMenu = $cache->get($commonRoleMenuCache);
            $list = $cache->get($commonAllPermCache);
            if (!$commonRoleMenu || !$list) {
                $allPerms = $this->manager->getPermissionsByUser($user_id);

                //过滤字段
                if ($allPerms) {
                    foreach ($allPerms as $v) {
                        if (strpos($v->name, $this->wxcode) === 0) {
                            $list[] = ltrim($v->name, $this->wxcode); //获取用户有权限的菜单url
                        } else if (strpos($v->name, $this->parentSpotCode) === 0) {
                            $list[] = ltrim($v->name, $this->parentSpotCode);
                        }
                    }
                    if (!empty($list)) {
                        array_unique($list);
                    }
                }
                //获取当前诊所的预约类型做特殊处理
                //$type = Spot::find()->select(['appointment_type'])->where(['id' => $this->spotId])->asArray()->one();
                //判断模块权限，然后是url权限
                
                foreach ($datas as $key => $data) {
                    // 单个url权限判断
                    foreach ($data['children'] as $subKey => $child) {
                        if(isset($child['children']) && !empty($child['children'])){
                            $thirdCount  = count($child['children']);
                            foreach ($child['children'] as $t => $thridChild){
                                $permName = $this->wxcode . $thridChild['menu_url'];
                                $organizationPerm = $this->parentSpotCode . $thridChild['menu_url'];
                                
                                if (!isset($allPerms[$permName]) && !isset($allPerms[$organizationPerm])) {
                                    $thirdCount--;
                                    unset($datas[$key]['children'][$subKey]['children'][$t]);
                                } else if ($thridChild['role_type'] == 1) {
                                    $thirdCount--;
                                    unset($datas[$key]['children'][$subKey]['children'][$t]);
                                }else{
                                    
                                    if(!isset($datas[$key]['children'][$subKey]['menuSort']) || $datas[$key]['children'][$subKey]['menuSort'] < $thridChild['titleSort']){
                                        $datas[$key]['children'][$subKey]['menu_url'] = $thridChild['menu_url'];
                                        $datas[$key]['children'][$subKey]['description'] = $child['module_description'];
                                        $datas[$key]['children'][$subKey]['menuSort'] = $thridChild['titleSort'];
                                    }
                                }
                            }
                            if($thirdCount == 0){
                                unset($datas[$key]['children'][$subKey]);
                            }
                        }else{
                            $permName = $this->wxcode . $child['menu_url'];
                            $organizationPerm = $this->parentSpotCode . $child['menu_url'];
                            if (!isset($allPerms[$permName]) && !isset($allPerms[$organizationPerm])) {
                                unset($datas[$key]['children'][$subKey]);
                            } else if ($child['role_type'] == 1) {
                                unset($datas[$key]['children'][$subKey]);
                            }
                            
                            if ($child['menu_url'] == Yii::getAlias('@make_appointmentAppointmentRoomConfig')) {
                                unset($datas[$key]['children'][$subKey]);
                            }
                        }
                    }
                }
                $cache->set($commonAllPermCache, $list, 86400, $dependency);
                $cache->set($commonRoleMenuCache, $datas, 86400, $dependency);
            } else {
                $datas = $commonRoleMenu;
            }
        }
        $view->params['defaultUrl'] = null;
        foreach ($datas as $key => $data) {
            if (count($data['children']) === 0) {
                unset($datas[$key]);
            } else {
                
                if ($view->params['defaultUrl'] == null) {
                    $view->params['defaultUrl'] = $data['children'][0]['menu_url'];
                }
            }
        }
        $view->params['permList'] = $list;
        $view->params['layoutData'] = $datas;
    }

    /**
     * (non-PHPdoc)
     * @property 行为日志记录
     * @see \yii\base\Controller::afterAction()
     */
    public function afterAction($action, $result) {
        // 过滤不记录掉读的接口
        if (in_array($action->id, $this->readApi)) {
            return parent::afterAction($action, $result);
        }

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
        $postData = $request->post();
        if ($request->isPost) {
            /* 通用数据上报开始 */
            $dataReport = Yii::$app->params['dataReport'];
            if (isset($dataReport[$moduleId])) {
                if (in_array($controllerId, array_keys($dataReport[$moduleId]))) {
                    if (in_array($action->id, $dataReport[$moduleId][$controllerId])) {//当前action需要数据上报
                        $reportDataType = $postData['reportDataType'] ? $postData['reportDataType'] : ( $getData['reportDataType'] ? $getData['reportDataType'] : '');
                        //数据上报
                        $reportData = [
                            'url' => $request->getHostInfo() . $request->getUrl(),
                            'eventType' => 1, //1为普通URL 2为普通点击事件
                            'ip' => $request->userIP,
                            'module' => $module['module_name'],
                            'action' => $requestUrl,
                            'name' => '',//eventType==1时可为空,eventType==2时需要给出点击事件的数据统计用途(如：增加家庭成员点击数据上报)
                            'reportDataType' => $reportDataType
                        ];
                        Common::dataReport($this->userInfo->id, $this->spotId, $reportData);
                    }
                }
            }
            /* 通用数据上报结束 */


            if ($module && (count($getData) > 0 || count($bodyData) > 0)) {
                $data = Json::encode(array(
                            'GET' => $getData,
                            'BODY' => $bodyData,
                ));
                BehaviorRecord::log(
                        $this->userInfo->id, $request->userIP, $this->spotId, $module['module_name'], $requestUrl, $data
                );
            }
        }
        return parent::afterAction($action, $result);
    }

    /**
     * @return 设置微信支的配置文件 
     */
    public function setWxPayConfig() {
        if ($this->spotId) {
            $dependency = new \yii\caching\DbDependency(['sql' => 'select MAX(update_time) from ' . PaymentConfig::tableName() . ' where type=1 and spot_id=' . $this->spotId]);
            $cacheKey = Yii::getAlias('@wxPayConfig') . $_COOKIE['wechatSpotId'];
            $config = Yii::$app->cache->get($cacheKey);
            if (empty($config)) {
                $paymentConfig = PaymentConfig::find()
                                ->select(['appid', 'mchid', 'payment_key'])->where(['type' => 1, 'spot_id' => $this->spotId])->asArray()->one();
                if ($paymentConfig) {
                    $config['appid'] = $paymentConfig['appid'];
                    $config['mchid'] = $paymentConfig['mchid'];
                    $config['key'] = $paymentConfig['payment_key'];
                }
                Yii::$app->cache->set($cacheKey, $config, 300, $dependency);
            }
        }
    }

    /*
     * @return 获取系统通知信息
     */

    public function getMessageCenter() {
            $view = Yii::$app->view;
            $user_id = $this->userInfo->id;
            $messageNormal = [];
            $messageMedical = [];
            $dependency = new DbDependency(['sql' => 'select count(1) from '. MessageCenter::tableName().' where spot_id = '.$this->spotId.' and status = 0 and user_id = '. $user_id]);
            $messageCacheKey = Yii::getAlias('@messageCenter').$this->spotId.'_'.$user_id;
            $messageCacheInfo = Yii::$app->cache->get($messageCacheKey);
            if($messageCacheInfo === false){
                $query = new Query();
                $query->from(['a' => MessageCenter::tableName()]);
                $query->select(['a.type', 'a.content', 'b.username','b.sex','b.birthday', 'a.create_time', 'c.clinic_name', 'a.url', 'a.id','a.category']);
                $query->leftJoin(['b' => Patient::tableName()], '{{a}}.patient_id = {{b}}.id');
                $query->leftJoin(['c' => Room::tableName()], '{{a}}.room_id = {{c}}.id');
                $query->where(['a.spot_id' => $this->spotId, 'a.status' => 0, 'a.user_id' => $user_id]);
                $query->orderby('a.id desc');
                $messageTotals = $query->all();
                if(!empty($messageTotals)){
                    foreach ($messageTotals as $v){
                        if($v['category'] == 1){//普通消息类型
                            $messageNormal[] = $v;
                        }else if($v['category'] == 2 && date('Y-m-d',$v['create_time']) == date('Y-m-d',time()) ){
                            
                            $messageMedical[] = $v;
                        }
                    }
                }
                Yii::$app->cache->set($messageCacheKey, $messageTotals,0,$dependency);
            }else{
                if(!empty($messageCacheInfo)){
                    foreach ($messageCacheInfo as $v){
                        if($v['category'] == 1){//普通消息类型
                            $messageNormal[] = $v;
                        }else if($v['category'] == 2 && date('Y-m-d',$v['create_time']) == date('Y-m-d',time())){
                            $messageMedical[] = $v;
                        }
                    }
                }
            }
        $view->params['messageList'] = $messageNormal;
        $view->params['messageNum'] = count($messageNormal);
        $view->params['medicalList'] = $messageMedical;
        $view->params['medicalNum'] = count($messageMedical);
    }
 
}
