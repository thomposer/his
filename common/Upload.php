<?php 
 /**
  * 文件上传类
  */
namespace app\common;

use yii\helpers\Url;
use app\modules\patient\models\Patient;
/*
 * 尊重作者：mckee 来自www.phpddt.com
 * 可同时处理用户多个上传文件。效验文件有效性后存储至指定目录。
 * 可返回上传文件的相关有用信息供其它程序使用。（如文件名、类型、大小、保存路径）
 */
class Upload {

    protected $user_post_file = array();  //用户上传的文件
    protected $save_file_path;    //存放用户上传文件的路径
    protected $max_file_size;     //文件最大尺寸
    protected $last_error;     //记录最后一次出错信息
    //默认允许用户上传的文件类型
//    protected $allow_type = array('jpeg','gif', 'jpg', 'png', 'zip', 'rar', 'txt', 'doc', 'pdf','docx');
    protected $allow_type = array('jpeg','gif', 'jpg', 'png', 'zip', 'rar', 'doc', 'pdf','docx','bmp');
    protected $final_file_path;  //最终保存的文件名
    protected $save_info = array(); //返回一组有用信息，用于提示用户。
    /**
     * 构造函数
    */
    function __construct($file, $path, $size = 4194304, $type = '') {
        $this->user_post_file = $file;
        if(!is_dir($path)){ //存储路径文件不存在就创建
            mkdir($path);
            chmod($path,0777);
        }
        $this->save_file_path = $path;
        $this->max_file_size = $size;  //如果用户不填写文件大小，则默认为2M.
        if ($type != '')
            $this->allow_type = $type;
    }

    /**
     * 存储用户上传文件，检验合法性通过后，存储至指定位置。
     */
    function upload() {

        for ($i = 0; $i < count($this->user_post_file['name']); $i++) {
            //如果当前文件上传功能，则执行下一步。
            if ($this->user_post_file['error'][$i] == 0) {
                //取当前文件名、临时文件名、大小、扩展名，后面将用到。
                $name = $this->user_post_file['name'][$i];
                $tmpname = $this->user_post_file['tmp_name'][$i];
                $size = $this->user_post_file['size'][$i];
                $mime_type = $this->user_post_file['type'][$i];
                $type = $this->getFileExt($this->user_post_file['name'][$i]);
                //检测当前上传文件大小是否合法。
                if (!$this->checkSize($size)) {
                    $this->last_error = "文件太大，大小不能超过4M";
                    $this->halt($this->last_error);
                    $this->save_info = [
                        'msg' => $this->last_error,
                        'errorCode' => 1005,//非法提交
                    ];
                    return $this->save_info;
                    continue;
                }
                //检测当前上传文件扩展名是否合法。
                if (!$this->checkType($type)) {
                    $this->last_error = "上传文件类型错误";
                    $this->halt($this->last_error);
                    $this->save_info = [
                        'msg' => $this->last_error,
                        'errorCode' => 1014,
                    ];
                    return $this->save_info;
                    continue;
                }
                //检测当前上传文件是否非法提交。
                if(!is_uploaded_file($tmpname)) {
                    $this->last_error = "Invalid post file method. File name is: ".$name;
                    $this->halt($this->last_error);
                    $this->save_info = [
                        'msg' => $this->last_error,
                        'errorCode' => 1009,//非法提交
                    ];
                    return $this->save_info;
                    continue;
                }
                //移动文件后，重命名文件用。
                $basename = $this->getBaseName($name, ".".$type);
                //为防止文件名乱码
                $basename = iconv("gb2312","UTF-8", $basename);
                //移动后的文件名
                $saveas = trim($basename).date('YmdHis').$size.".".$type;
                //组合新文件名再存到指定目录下，格式：存储路径 + 文件名 + 时间 + 扩展名
                $this->final_file_path = $this->save_file_path."/".$saveas;
                if(!move_uploaded_file($tmpname, $this->final_file_path)) {
                    $this->last_error = $this->user_post_file['error'][$i];
                    $this->halt($this->last_error);
                    $this->save_info = [
                        'msg' => $this->last_error,
                        'errorCode' => 1001,//上传失败
                    ];
                    return $this->save_info;
                    continue;
                }
                //存储当前文件的有关信息，以便其它程序调用。
                $this->save_info =  array(
                    "errorCode" => 0,
                    "name" => $name, 
                    "type" => $type,
                    "mime_type" => $mime_type,
                    "size" => $size, 
                    "saveas" => $saveas,
                    "path" => $this->final_file_path,
                    "url" => '/'.$this->final_file_path
                );
            }
        }
        return $this->save_info;
//         return count($this->save_info); //返回上传成功的文件数目
    }

    /**
     * 返回一些有用的信息，以便用于其它地方。
     */
    function getSaveInfo() {
        return $this->save_info;
    }
    /**
     * 检测用户提交文件大小是否合法
    */
    function checkSize($size) {
        if ($size > $this->max_file_size) {
            return false;
        }
        else {
            return true;
        }
    }
    /**
     * 检测用户提交文件类型是否合法
    */
    function checkType($extension) {
        foreach ($this->allow_type as $type) {
            if (strcasecmp($extension , $type) == 0)
                return true;
        }
        return false;
    }

    /**
     * 显示出错信息
     */
    function halt($msg) {
        return $msg;
    }
    /**
     * 取文件扩展名
    */
    function getFileExt($filename) {
        $stuff = pathinfo($filename);
        return $stuff['extension'];
    }
    /**
     * 取给定文件文件名，不包括扩展名。
     */
    function getBaseName($filename, $type) {
        $filename = (new Patient())->generatePatientNumber();
        $basename = basename($filename, $type);
        return $basename;
    }
}
