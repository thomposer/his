<?php

namespace app\modules\spot\controllers;

use Yii;
use app\modules\spot\models\Spot;
use app\modules\spot\models\search\SpotSearch;
use app\common\base\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\rbac\Permission;
use yii\rbac\Role;
use yii\db\Exception;
use app\modules\spot\models\SpotConfig;
use yii\web\Response;
use yii\helpers\Html;
use app\modules\user\models\UserSpot;
use yii\helpers\Url;
use app\modules\user\models\User;
use app\modules\spot_set\models\SpotType;
use app\modules\spot\models\CureList;
use app\modules\spot_set\models\ClinicCure;

/**
 * IndexController implements the CRUD actions for Spot model.
 */
class IndexController extends BaseController
{

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Spot models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new SpotSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->pageSize);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Spot model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Spot model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Spot();
        $model->scenario = 'spot';
        $model->parent_spot = $this->parentSpotId;
        $request = Yii::$app->request;
        $model->spot = Yii::$app->getSecurity()->generateRandomString(16);
        if ($this->createSpot == 1) {
            /*
             *   Process for ajax request
             */
            $model->scenario = 'createSpot';
            if ($model->load($request->post()) && $model->validate()) {
                $dbTrans = Yii::$app->db->beginTransaction();
                try {
                    $model->save();
//                     $this->insertSpotType($model->id);
                    $this->insertSpotConfig($model->id);
                    //给当前诊所新增一条治疗医嘱
                    $this->initCureClinic($model->id);
                    $_COOKIE['rememberMe'] ? time() + 86400 * 7 : time() + 86400;
                    if (isset($_COOKIE['rememberMe']) && $_COOKIE['rememberMe'] == 1) {
                        $expireTime = Yii::getAlias('@loginCookieExpireTime');
                    } else {
                        $expireTime = Yii::getAlias('@loginSessionExpireTime');
                    }
                    $userSpotModel = new UserSpot();
                    $userSpotModel->parent_spot_id = $this->parentSpotId;
                    $userSpotModel->spot_id = $model->id;
                    $userSpotModel->user_id = $this->userInfo->id;
                    $userSpotModel->department_id = 0;
                    $userSpotModel->save();

                    setcookie('createSpot', false, time(), '/', null, null);
                    setcookie('spotId', $model->id, time() + $expireTime, '/', null, null);

                    $cache = Yii::$app->cache;
                    $cacheSuffix = $model->id . $this->userInfo->id;
                    $parentSpotCode = $cache->get(Yii::getAlias('@parentSpotCode') . $this->parentSpotId . $this->userInfo->id);
                    $cache->set(Yii::getAlias('@parentSpotCode') . $cacheSuffix, $parentSpotCode, $expireTime); //机构代码

                    $cache->set(Yii::getAlias('@spot') . $cacheSuffix, $model->spot, $expireTime); //诊所代码
                    $cache->set(Yii::getAlias('@spotName') . $cacheSuffix, $model->spot_name, $expireTime); //诊所名称
                    $cache->set(Yii::getAlias('@spotIcon') . $cacheSuffix, $model->icon_url, $expireTime); //诊所图标
                    $dbTrans->commit();
                    Yii::$app->getSession()->setFlash('success', '保存成功');
                    return $this->redirect(['index']);
                } catch (Exception $e) {
                    $dbTrans->rollBack();
                }
            }
        } else if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            //$result = $this->template($model);//默认生成诊所权限与角色
            $dbTrans = Yii::$app->db->beginTransaction();
            try {
                $model->save();
//                     $this->insertSpotType($model->id);
                $this->insertSpotConfig($model->id);
                //给当前诊所新增一条治疗医嘱
                $this->initCureClinic($model->id);
                $dbTrans->commit();
                Yii::$app->getSession()->setFlash('success', '保存成功');
                return $this->redirect(['index']);
            } catch (Exception $e) {
                $dbTrans->rollBack();
            }
        }
        return $this->render('create', [
                    'model' => $model,
        ]);
    }

//     /**
//      * @desc 插入两条预约类型
//      * @param int $spotId 诊所id
//      */
//     public function insertSpotType($spotId) {
//         $spotTypeList = array([$spotId,'初诊',30,1,time(),time()],[$spotId,'复诊',20,1,time(),time()]);
//         Yii::$app->db->createCommand()->batchInsert(SpotType::tableName(), ['spot_id', 'type', 'time','is_delete', 'create_time','update_time'], $spotTypeList)->execute();
//     }

    /**
     * @desc 插入诊所开始时间和结束时间
     * @param int $spotId 诊所id
     */
    public function insertSpotConfig($spotId) {
        Yii::$app->db->createCommand()->insert(SpotConfig::tableName(), ['spot_id' => $spotId, 'begin_time' => '8:00', 'end_time' => '20:00', 'reservation_during' => 10, 'create_time' => time(), 'update_time' => time()])->execute();
    }

    /**
     * 
     * @param type $spotId 诊所ID
     * @return 给当前诊所新增一条皮试（青霉素）治疗医嘱
     */
    protected function initCureClinic($spotId) {
        $cure = CureList::find()->select(['id', 'name', 'spot_id'])->where(['spot_id' => $this->parentSpotId, 'type' => 1])->asArray()->one();
        if (!empty($cure)) {
            $model = new ClinicCure();
            $model->spot_id = $spotId;
            $model->cure_id = $cure['id'];
            $model->price = 0;
            $model->save();
        }
    }

    /**
     * Updates an existing Spot model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);
        $model->scenario = 'spot';
        $model->address = $model->province ? $model->province . '/' . $model->city . '/' . $model->area : '';
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            //若为当前诊所，则更新诊所icon图
            if ($model->id == $this->spotId) {
                Yii::$app->cache->set(Yii::getAlias('@spotIcon') . $this->spotId . $this->userInfo->id, $model->icon_url, $_COOKIE['rememberMe'] ? 86400 * 7 : 86400);
                Yii::$app->cache->set(Yii::getAlias('@spotName') . $this->spotId . $this->userInfo->id, $model->spot_name, $_COOKIE['rememberMe'] ? 86400 * 7 : 86400); //诊所名称
//                     setcookie('spotIcon',$model->icon_url,$_COOKIE['rememberMe']?time()+86400*7:time()+86400,'/');
            }
            $spotListCache = Yii::getAlias('@spotList') . $this->parentSpotId . '_' . $this->userInfo->id;
            Yii::$app->cache->delete($spotListCache);
            $spotList = (new Spot())->getCacheSpotList();
            Yii::$app->getSession()->setFlash('success', '保存成功');
            return $this->redirect(['index']);
        }
        return $this->render('update', [
                    'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Spot model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $request = Yii::$app->request;
        if ($request->isAjax) {

            /*
             *   Process for ajax request
             */
            $parentSpotId = $this->parentSpotId;
            $spotCount = Spot::find()->where(['parent_spot' => $parentSpotId, 'status' => '1'])->count();
            $model = $this->findModel($id);
            if ($model->status == 1 && $spotCount <= 1) {
                Yii::$app->getSession()->setFlash('error', '操作失败，至少需要有一个使用中的诊所');
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ['forceClose' => true, 'forceRedirect' => Url::to(['index'])];
            } else {
                $dbTrans = Yii::$app->db->beginTransaction();
                try {
                    $model->scenario = 'spot';
                    $model->status = 3;
                    $model->save();
                    //将所有默认关联关系清除
                    Yii::$app->db->createCommand()->update(User::tableName(), ['default_spot' => 0], ['spot_id' => $this->parentSpotId, 'default_spot' => $id])->execute();
                    Yii::$app->db->createCommand()->delete(UserSpot::tableName(), ['parent_spot_id' => $this->parentSpotId, 'spot_id' => $id])->execute();
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    $spotListCache = Yii::getAlias('@spotList') . $this->parentSpotId . '_' . $this->userInfo->id;
                    Yii::$app->cache->delete($spotListCache);
                    $spotList = (new Spot())->getCacheSpotList();
                    $dbTrans->commit();
                    return ['forceClose' => true, 'forceReload' => '#crud-datatable-pjax'];
                } catch (Exception $e) {
                    $dbTrans->rollBack();
                }
            }
        } else {
            /*
             *   Process for non-ajax request
             */
            return $this->redirect(['index']);
        }
    }





    /**
     * 初始化站点权限和角色
     * 赋予创建该站点的用户-站点管理员角色
     *
     * @throws NotFoundHttpException
     * @return string
     */
    public function template($targetSpot) {

        $this->wxcode = $targetSpot->spot;
        $this->rolePrefix = $this->wxcode . '_roles_';
        $this->permissionPrefix = $this->wxcode . '_permissions_';
        $this->rootRole = $this->wxcode . '_roles';
        $this->rootPermission = $this->wxcode . '_permissions';
        // 已经初始化
        $haspermission = $this->manager->getPermission($this->rootPermission);
        if ($haspermission) {
            return true;
        }
        $dbTrans = Yii::$app->db->beginTransaction();
        try {

            $categoryRoleSuffix = '_roles';
            $categoryPermSuffix = '_permissions';

            // 初始化权限分类总目录和角色分类总目录
            $categoryRole = new Role();
            $categoryRole->name = $this->rootRole;
            $categoryRole->description = $targetSpot->spot_name;
            $this->manager->add($categoryRole);

            $categoryPerm = new Permission();
            $categoryPerm->name = $this->rootPermission;
            $categoryPerm->description = $targetSpot->spot_name;
            $this->manager->add($categoryPerm);

            $templateSpotName = Yii::$app->session->get('parentSpotCode');
            $targetSpotName = $targetSpot->spot;

            // 复制模板的权限
            $templatePerms = $this->manager->getChildren($templateSpotName . $categoryPermSuffix);

            foreach ($templatePerms as $perm) {
                $tempPerm = new Permission();
                // 替换前缀为目标站点
                $tempPerm->name = str_replace($templateSpotName . '_', $targetSpotName . '_', $perm->name);
                $tempPerm->description = $perm->description;
                $tempPerm->data = $categoryPerm->name;

                $this->manager->add($tempPerm);
                $this->manager->addChild($categoryPerm, $tempPerm);

                // 复制该权限下的二级权限，即url资源权限
                $subPerms = $this->manager->getChildren($perm->name);
                foreach ($subPerms as $subPerm) {
                    $subPermName = $targetSpotName . ltrim($subPerm->name, $templateSpotName);
                    $tempSubPerm = $this->manager->getPermission($subPermName);
                    if (!$tempSubPerm) {
                        $tempSubPerm = new Permission();
                        $tempSubPerm->name = $subPermName;
                        $tempSubPerm->description = $subPerm->description;
                        $tempSubPerm->data = $tempPerm->name;

                        $this->manager->add($tempSubPerm);
                    }

                    $this->manager->addChild($tempPerm, $tempSubPerm);
                }
            }
            // 复制模板的角色
            $templateRoles = $this->manager->getChildren($templateSpotName . $categoryRoleSuffix);
            foreach ($templateRoles as $role) {
                $tempRole = new Role();
                // 替换前缀为目标站点
                $tempRole->name = str_replace($templateSpotName . '_', $targetSpotName . '_', $role->name);
                $tempRole->description = $role->description;
                $tempRole->data = $categoryRole->name;

                $this->manager->add($tempRole);
                $this->manager->addChild($categoryRole, $tempRole);

                // 复制该角色下的url资源权限
                $subPerms = $this->manager->getChildren($role->name);
                foreach ($subPerms as $subPerm) {

                    $subPermName = $targetSpotName . ltrim($subPerm->name, $templateSpotName);
                    $tempSubPerm = $this->manager->getPermission($subPermName);

                    $this->manager->addChild($tempRole, $tempSubPerm);
                }
            }

            // 判断是否有诊所管理员，没有则创建一个并赋予申请者
            $systemRoleName = $this->rolePrefix . 'system';
            $systemRole = $this->manager->getRole($systemRoleName);
            if (!$systemRole) {
                $systemRole = new Role();
                $systemRole->name = $systemRoleName;
                $systemRole->description = '诊所管理员';
                $this->manager->add($systemRole);

                // 将所有权限都给诊所管理员
                $allPerms = $this->manager->getChildren($this->rootPermission);
                foreach ($allPerms as $perm) {
                    $subPerms = $this->manager->getChildren($perm->name);

                    foreach ($subPerms as $subPerm) {

                        $this->manager->addChild($systemRole, $subPerm);
                    }
                }
            }
            // 将诊所管理员权限给申请者
//             if (!$this->manager->getAssignment($systemRoleName, $targetSpot->contact_email)) {
//                 $this->manager->assign($systemRole, $targetSpot->contact_email);
//             }
            $dbTrans->commit();
        } catch (Exception $e) {
            $dbTrans->rollBack();
        }
    }

    /**
     * Finds the Spot model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Spot the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (1 == $this->userInfo->id) {
            if (($model = Spot::find()->where(['id' => $id])->andWhere('status != :status', [':status' => 3])->one()) !== null) {
                return $model;
            } else {
                throw new NotFoundHttpException('你所请求的页面不存在');
            }
        } else {
            if (($model = Spot::find()->where(['id' => $id, 'parent_spot' => $this->parentSpotId])->andWhere('status != :status', [':status' => 3])->one()) !== null) {
                return $model;
            } else {
                throw new NotFoundHttpException('你所请求的页面不存在');
            }
        }
    }

}
