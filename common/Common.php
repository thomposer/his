<?php

namespace app\common;
use Yii;
use yii\web\NotAcceptableHttpException;
use yii\base\Controller;    
use yii\helpers\Url;
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
        	$url = is_null($url) ? 'window.history.back()' : "window.location.href='$url'";
//         	$alertUrl = Url::to(['/manage/default/message','title' => $title,'message' => $msg,'url' => $url]);
//         	echo "<script type='text/javascript'>window.location.href='{$alertUrl}';</script>";
        	echo "<script>alert('$msg');$url</script>";
        	die;
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