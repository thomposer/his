<?php

namespace app\common;
use Yii;
use yii\web\NotAcceptableHttpException;

    class Common{
        
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
    	public static function showInfo($msg='操作成功',$url=NULL){
    	    
    	    header('Content-Type: text/html; charset=UTF-8');
        	$url = is_null($url) ? 'window.history.back()' : "window.location.href='$url'";
        	echo "<script>alert('$msg');$url</script>";
        	die;
        }
        
}