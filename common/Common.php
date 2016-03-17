<?php

namespace app\common;
use Yii;
use yii\web\NotAcceptableHttpException;
use yii\base\Controller;    
use yii\helpers\Url;
use yii\helpers\Json;
    class Common extends Controller{
        
        public static function showMessage($message = null, $title = '提示',$params=[])
    	{
    	    if ($message === null)
    	    {
    	        $message = '权限不足，无法进行此项操作 ';
    	    }
    	    Yii::error($message,'NotAcception');
            throw new NotAcceptableHttpException($message,406);
    	    
    	}
    	
    	//提示信息
    	public static function showInfo($msg='操作成功',$url=NULL,$title='提示'){
    	    
    	    
    	    header('Content-Type: text/html; charset=UTF-8');
//         	$url = is_null($url) ? 'window.history.back()' : "window.location.href='$url'";
        	$alertUrl = Url::to(['/manage/default/message','title' => $title,'message' => $msg,'url' => $url]);
        	echo "<script type='text/javascript'>window.location.href='{$alertUrl}';</script>";
//         	echo "<script>alert('$msg');$url</script>";
        	die;
        }
        public static function mkdir($dir, $recursion = true)
        {
            if(!file_exists($dir)) {
                if(!mkdir($dir, 0777, $recursion)){
                    throw new \Exception($dir . ' make error');
                }
            }
        
            return true;
        }
        
        /**
         * 读取文件，以字符串返回
         * @param unknown $input 文件地址
         * @throws \Exception
         * @return unknown
         */
        public static function read($input)
        {
            $fp = fopen($input, 'r');
        
            if ($fp === false) {
                throw new \Exception($input . ' open error');
            }
        
            $fs = filesize($input);
            $fs = $fs <= 0 ? 1 : $fs;
            $fc = fread($fp, $fs);
        
            if ($fc === false) {
                throw new \Exception($input . '  read error');
            }
        
            fclose($fp);
        
            return $fc;
        }
        
        /**
         * 写文件，将文件写入目标文件
         * @param unknown $content 内容, 可以是字符串或者其它对象
         * @param unknown $output 输出目录
         * @param unknown $filename 输出文件名称
         * @param string $type 写类型
         * @throws \Exception
         */
        public static function write($content, $output, $filename, $type = 'w')
        {
            $output = rtrim($output, '/');
        
            static::mkdir($output);
        
            $fp = fopen($output . '/' . $filename, $type);
        
            if ($fp === false) {
                throw new \Exception($output . '/' . $filename . ' open error');
            }
        
            if (!is_string($content)) {
                $content = Json::encode($content);
            }
        
            $fw = fwrite($fp, $content);
        
            if ($fw === false) {
                throw new \Exception($output . '/' . $filename . '  write error');
            }
        
            return fclose($fp);
        }
        public static function varDump($data){
            echo "<pre>";
                var_dump($data);
            echo "</pre>";
        }
        public static function varExport($data){
            echo "<pre>";
                var_export($data);
            echo "</pre>";
        }
}