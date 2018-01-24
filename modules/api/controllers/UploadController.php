<?php

namespace app\modules\api\controllers;

use app\modules\api\controllers\CommonController;
use app\modules\spot_set\models\Board;
use yii\filters\VerbFilter;
use yii\base\Object;
use yii\web\Response;
use Yii;
use app\common\Upload;
use app\modules\check\models\CheckRecordFile;
use app\modules\inspect\models\InspectRecordFile;
use yii\web\NotFoundHttpException;
use yii\helpers\Url;
use app\modules\outpatient\models\MedicalFile;
use app\modules\follow\models\FollowFile;
use app\common\Common;
use app\modules\message\models\MessageCenter;

/**
 * 
 * @author zhenyuzhang
 * @abstract 文件上传api接口
 */
class UploadController extends CommonController
{

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['post'],
                    'delete' => ['post']
                ],
            ],
        ];
    }

    /**
     * index
     * @param int $check_record_id 影像学检查id
     * @param int $record_id 就诊流水id
     * @param file $avatar 上传的文件
     * 
     * @return int errorCode 错误代码(0-成功,1001-参数错误,2001-上传文件不能多于6个)
     * @return int key 上传记录的id
     * @return string mime_type 文件mime类型，如:image/jpeg
     * @return string type 文件后缀类型
     * @return string name 原上传文件的名称
     * @return string path 上传文件保存的路径
     * @return string saveas 文件上传保存后的名称
     * @return int size 文件的大小
     * @return string url 文件删除的api路径
     * @desc 影像学检查-文件上传api
     */
    public function actionIndex() {


        $upload = new Upload($_FILES['avatar'], Yii::$app->params['uploadUrl'] . date('Y-m-d'));
        $check_record_id = Yii::$app->request->post('check_record_id');
        $record_id = Yii::$app->request->post('record_id');
        $count = CheckRecordFile::find()->where(['check_record_id' => $check_record_id])->asArray()->count();
        //上传用户文件，返回int值，为上传成功的文件个数。
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($count >= 6) {
            $this->result['errorCode'] = 2001;
            $this->result['msg'] = '上传文件不能多于6个';
            return $this->result;
        }
        $info = $upload->upload();
        if ($info['errorCode'] == 0) {
            Common::syncImg(ltrim($info['path'], Yii::$app->params['uploadUrl']));
            $path = pathinfo($info['path']);
            $type = 1;
            if (!in_array($path['extension'], ['jpg', 'jpeg', 'gif', 'png'])) {
                $type = 2;
            }
            $model = new CheckRecordFile();
            $model->spot_id = $this->spotId;
            $model->record_id = $record_id;
            $model->check_record_id = $check_record_id;
            $model->file_url = $info['url'];
            $model->size = $info['size'];
            $model->file_name = $info['name'];
            $model->type = $type;
            $model->save();
        }
        $default = [
            'url' => Url::to(['@apiUploadDelete']),
            'key' => $model->id
        ];
        $info = array_merge($info, $default);
        return $info;
    }

    /**
     * delete
     * @param int $id 影像学检查-文件上传记录id
     * 
     * @return int errorCode 错误代码(0-成功,1001-参数错误,404-记录不存在)
     * @return string msg 提示信息
     * @desc 影像学检查-文件删除api
     */
    public function actionDelete() {

        $id = Yii::$app->request->post('key');
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = CheckRecordFile::findOne(['id' => $id, 'spot_id' => $this->spotId]);
        if (!$model) {
            $this->result['errorCode'] = 404;
            $this->result['msg'] = '删除出错！请重新删除！';
            return $this->result;
        }
        $model->delete();
        return $this->result;
    }

    /**
     * inspect-upload
     * @param int $inspect_record_id 实验室检查id
     * @param int $record_id 就诊流水id
     * @param file $avatar 上传的文件
     *
     * @return int errorCode 错误代码(0-成功,1001-参数错误,2001-上传文件不能多于6个)
     * @return int key 上传记录的id
     * @return string mime_type 文件mime类型，如:image/jpeg
     * @return string type 文件后缀类型
     * @return string name 原上传文件的名称
     * @return string path 上传文件保存的路径
     * @return string saveas 文件上传保存后的名称
     * @return int size 文件的大小
     * @return string url 文件删除的api路径
     * @desc 实验室检查-文件上传api
     */
    public function actionInspectUpload() {

        $upload = new Upload($_FILES['avatar'], 'uploads/' . date('Y-m-d'));
        $inspect_record_id = Yii::$app->request->post('inspect_record_id');
        $record_id = Yii::$app->request->post('record_id');
        $count = InspectRecordFile::find()->where(['inspect_record_id' => $inspect_record_id])->asArray()->count();
        //上传用户文件，返回int值，为上传成功的文件个数。
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($count >= 6) {
            $this->result['errorCode'] = 2001;
            $this->result['msg'] = '上传文件不能多于6个';
            return $this->result;
        }
        $info = $upload->upload();
        if ($info['errorCode'] == 0) {
            $path = pathinfo($info['path']);
            Common::syncImg(ltrim($info['path'], Yii::$app->params['uploadUrl']));
            $type = 1;
            if (!in_array($path['extension'], ['jpg', 'jpeg', 'gif', 'png'])) {
                $type = 2;
            }
            $model = new InspectRecordFile();
            $model->spot_id = $this->spotId;
            $model->record_id = $record_id;
            $model->inspect_record_id = $inspect_record_id;
            $model->file_url = $info['url'];
            $model->size = $info['size'];
            $model->file_name = $info['name'];
            $model->type = $type;
            $model->save();
        }
        $default = [
            'url' => Url::to(['@apiInspectDelete']),
            'key' => $model->id
        ];
        $info = array_merge($info, $default);
        return $info;
    }

    /**
     * inspect-delete
     * @param int $key 实验室检查-文件上传记录id
     *
     * @return int errorCode 错误代码(0-成功,1001-参数错误,404-记录不存在)
     * @return string msg 提示信息
     * @desc 实验室检查-文件删除api
     */
    public function actionInspectDelete() {

        $id = Yii::$app->request->post('key');
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = InspectRecordFile::findOne(['id' => $id, 'spot_id' => $this->spotId]);
        if (!$model) {
            $this->result['errorCode'] = 404;
            $this->result['msg'] = '删除出错！请重新删除！';
            return $this->result;
        }
        $model->delete();
        return $this->result;
    }

    /**
     * medical-upload
     * @param int $record_id 就诊流水id
     * @param file $avatar 上传的文件
     *
     * @return int errorCode 错误代码(0-成功,1001-参数错误,2001-上传文件不能多于6个)
     * @return int key 上传记录的id
     * @return string mime_type 文件mime类型，如:image/jpeg
     * @return string type 文件后缀类型
     * @return string name 原上传文件的名称
     * @return string path 上传文件保存的路径
     * @return string saveas 文件上传保存后的名称
     * @return int size 文件的大小
     * @return string url 文件删除的api路径
     * @desc 门诊-病历-文件上传api
     */
    public function actionMedicalUpload() {
        //上传用户文件，返回int值，为上传成功的文件个数。
        Yii::$app->response->format = Response::FORMAT_JSON;
        $info = [];
        if (isset($_FILES['avatar']) && !empty($_FILES['avatar'])) {
            $upload = new Upload($_FILES['avatar'], 'uploads/' . date('Y-m-d'));
            $record_id = Yii::$app->request->post('record_id');
            $count = MedicalFile::find()->where(['record_id' => $record_id])->asArray()->count();
            if ($count >= 6) {
                $this->result['errorCode'] = 2001;
                $this->result['msg'] = '上传文件不能多于6个';
                return $this->result;
            }
            $info = $upload->upload();
            if ($info['errorCode'] == 0) {
                $path = pathinfo($info['path']);
                Common::syncImg(ltrim($info['path'], Yii::$app->params['uploadUrl']));
                $type = 1;
                if (!in_array($path['extension'], ['jpg', 'jpeg', 'gif', 'png'])) {
                    $type = 2;
                }
                $model = new MedicalFile();
                $model->spot_id = $this->spotId;
                $model->record_id = $record_id;
                $model->file_url = $info['url'];
                $model->size = $info['size'];
                $model->file_name = $info['name'];
                $model->type = $type;
                $model->save();
                MessageCenter::updateStatus($record_id, $this->spotId, 2);
            }
            $default = [
                'url' => Url::to(['@apiMedicalDelete']),
                'key' => $model->id
            ];
            $info = array_merge($info, $default);
        }
        return $info;
    }

    /**
     * medical-delete
     * @param int $key 门诊-病历-文件上传记录id
     *
     * @return int errorCode 错误代码(0-成功,1001-参数错误,404-记录不存在)
     * @return string msg 提示信息
     * @desc 门诊-病历-文件删除api
     */
    public function actionMedicalDelete() {

        $id = Yii::$app->request->post('key');
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = MedicalFile::findOne(['id' => $id, 'spot_id' => $this->spotId]);
        if (!$model) {
            $this->result['errorCode'] = 404;
            $this->result['msg'] = '删除出错！请重新删除！';
            return $this->result;
        }
        $model->delete();
        return $this->result;
    }

    /**
     * medical-upload
     * @param int $record_id 就诊流水id
     * @param file $avatar 上传的文件
     *
     * @return int errorCode 错误代码(0-成功,1001-参数错误,2001-上传文件不能多于6个)
     * @return int key 上传记录的id
     * @return string mime_type 文件mime类型，如:image/jpeg
     * @return string type 文件后缀类型
     * @return string name 原上传文件的名称
     * @return string path 上传文件保存的路径
     * @return string saveas 文件上传保存后的名称
     * @return int size 文件的大小
     * @return string url 文件删除的api路径
     * @desc 门诊-病历-文件上传api
     */
    public function actionFollowUpload() {
        //上传用户文件，返回int值，为上传成功的文件个数。
        Yii::$app->response->format = Response::FORMAT_JSON;
        $info = [];
        if (isset($_FILES['avatar']) && !empty($_FILES['avatar'])) {
            $upload = new Upload($_FILES['avatar'], 'uploads/' . date('Y-m-d'));
            $follow_id = Yii::$app->request->post('follow_id');
            $count = FollowFile::find()->where(['follow_id' => $follow_id])->asArray()->count();
            if ($count >= 5) {
                $this->result['errorCode'] = 2001;
                $this->result['msg'] = '上传文件不能多于5个';
                return $this->result;
            }
            $info = $upload->upload();
            if ($info['errorCode'] == 0) {
                $path = pathinfo($info['path']);
                Common::syncImg(ltrim($info['path'], Yii::$app->params['uploadUrl']));
                $type = 1;
                if (!in_array($path['extension'], ['jpg', 'jpeg', 'gif', 'png'])) {
                    $type = 2;
                }
                $model = new FollowFile();
                $model->spot_id = $this->spotId;
                $model->follow_id = $follow_id;
                $model->file_url = $info['url'];
                $model->size = $info['size'];
                $model->file_name = $info['name'];
                $model->type = $type;
                $model->save();
            }
            $default = [
                'url' => Url::to(['@apiFollowDelete']),
                'key' => $model->id
            ];
            $info = array_merge($info, $default);
        }
        return $info;
    }

    /**
     * medical-delete
     * @param int $key 门诊-病历-文件上传记录id
     *
     * @return int errorCode 错误代码(0-成功,1001-参数错误,404-记录不存在)
     * @return string msg 提示信息
     * @desc 门诊-病历-文件删除api
     */
    public function actionFollowDelete() {
        $id = Yii::$app->request->post('key');
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = FollowFile::findOne(['id' => $id, 'spot_id' => $this->spotId]);
        if (!$model) {
            $this->result['errorCode'] = 404;
            $this->result['msg'] = '删除出错！请重新删除！';
            return $this->result;
        }
        $model->delete();
        return $this->result;
    }

    /**
     * follow-message-upload
     * @param int $record_id 就诊流水id
     * @param file $avatar 上传的文件
     *
     * @return int errorCode 错误代码(0-成功,1001-参数错误,2001-上传文件不能多于6个)
     * @return int key 上传记录的id
     * @return string mime_type 文件mime类型，如:image/jpeg
     * @return string type 文件后缀类型
     * @return string name 原上传文件的名称
     * @return string path 上传文件保存的路径
     * @return string saveas 文件上传保存后的名称
     * @return int size 文件的大小
     * @return string url 文件删除的api路径
     * @desc 随访-对话消息-文件上传api
     */
    public function actionFollowMessageUpload() {
        //上传用户文件，返回int值，为上传成功的文件个数。
        Yii::$app->response->format = Response::FORMAT_JSON;
        $info = [];
        if (isset($_FILES['avatar']) && !empty($_FILES['avatar'])) {
            $upload = new Upload($_FILES['avatar'], 'uploads/' . date('Y-m-d'));
//            $follow_id = Yii::$app->request->post('follow_id');
//            $count = FollowFile::find()->where(['follow_id' => $follow_id])->asArray()->count();
//            if ($count >= 5) {
//                $this->result['errorCode'] = 2001;
//                $this->result['msg'] = '上传文件不能多于5个';
//                return $this->result;
//            }
            $info = $upload->upload();
            if ($info['errorCode'] == 0) {
                $path = pathinfo($info['path']);
                Common::syncImg(ltrim($info['path'], Yii::$app->params['uploadUrl']));
                $type = 1;
                if (!in_array($path['extension'], ['jpg', 'jpeg', 'gif', 'png'])) {
                    $type = 2;
                }
//                $model = new FollowFile();
//                $model->spot_id = $this->spotId;
//                $model->follow_id = $follow_id;
//                $model->file_url = $info['url'];
//                $model->size = $info['size'];
//                $model->file_name = $info['name'];
//                $model->type = $type;
//                $model->save();
            }
            $default = [
                'url' => Url::to(['@apiFollowMessageDelete']),
                'key' => 1
            ];
            $info = array_merge($info, $default);
        }
        return $info;
    }

    /**
     * follow-message-delete
     * @param int $key id
     *
     * @return int errorCode 错误代码(0-成功,1001-参数错误,404-记录不存在)
     * @return string msg 提示信息
     * @desc 随访-对话消息-文件删除api
     */
    public function actionFollowMessageDelete() {
        $id = Yii::$app->request->post('key');
        Yii::$app->response->format = Response::FORMAT_JSON;
//        $model = FollowFile::findOne(['id' => $id, 'spot_id' => $this->spotId]);
//        if (!$model) {
//            $this->result['errorCode'] = 404;
//            $this->result['msg'] = '删除出错！请重新删除！';
//            return $this->result;
//        }
//        $model->delete();
        return $this->result;
    }

    /**
     * board-upload
     * @param file $board 上传的文件
     *
     * @return int errorCode 错误代码(0-成功,1001-参数错误,2001-上传文件不能多于6个)
     * @return string mime_type 文件mime类型，如:image/jpeg
     * @return string type 文件后缀类型
     * @return string name 原上传文件的名称
     * @return string path 上传文件保存的路径
     * @return string saveas 文件上传保存后的名称
     * @return int size 文件的大小
     * @return string url 显示的文件的路径
     * @desc 诊所设置-公告配置-文件上传api
     */
    public function actionBoardUpload() {
        $upload = new Upload($_FILES['board'], 'uploads/' . date('Y-m-d'));
        Yii::$app->response->format = Response::FORMAT_JSON;
        $info = $upload->upload();
        $type = 1;
        if ($info['errorCode'] == 0) {
            $path = pathinfo($info['path']);
            if (!in_array($path['extension'], ['jpg', 'jpeg', 'gif', 'png'])) {
                $type = 2;
            }
            Common::syncImg(ltrim($info['path'], Yii::$app->params['uploadUrl']));
        }
        $fileType = ['type' => $type];
        $info = array_merge($info, $fileType);
        return $info;
    }

    /**
     * board-delete
     *
     * @return int errorCode 错误代码(0-成功,1001-参数错误,404-记录不存在)
     * @return string msg 提示信息
     * @desc 诊所设置-公告配置-文件删除api
     */
    public function actionBoardDelete() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return true;
    }

}
