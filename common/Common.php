<?php

namespace app\common;

use Yii;
use yii\web\NotAcceptableHttpException;
use yii\base\Controller;
use yii\helpers\Json;
use yii\helpers\Url;
use app\modules\behavior\models\SmsRecord;

class Common extends Controller
{

    private static $sync_path = '/data/web/eapd/script/sync2cdn.sh ';

    public static function showMessage($message = null, $title = '提示', $params = []) {
        if ($message === null) {
            $message = '权限不足，无法进行此项操作 ';
        }
        Yii::error($message, 'NotAcception');
        throw new NotAcceptableHttpException($message, 406);
    }

    //提示信息
    public static function showInfo($msg = '操作成功', $url = NULL) {

        header('Content-Type: text/html; charset=UTF-8');
        $url = is_null($url) ? 'window.history.back()' : "window.location.href='$url'";
        echo "<script>alert('$msg');$url</script>";
        die;
    }

    public static function mkdir($dir, $recursion = true) {
        if (!file_exists($dir)) {
            if (!mkdir($dir, 0777, $recursion)) {
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
    public static function read($input) {
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
    public static function write($content, $output, $filename, $type = 'w') {
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

    public static function varDump($data) {
        echo "<pre>";
        var_dump($data);
        echo "</pre>";
    }

    public static function varExport($data) {
        echo "<pre>";
        var_export($data);
        echo "</pre>";
    }

    /**
     * 
     * @param 数字 $num
     * @return 保留小数点后2位，而不四舍五入
     */
    public static function num($num) {
        return sprintf("%.2f", $num);
    }

    /**
     * 
     * 发送手机短信消息(curl 可能由于服务器curl插件版本问题导致发送不成功，所以没有curl)
     * @param string $mlobile 手机号
     * @param string $content 发送内容（需事先添加短信模板）
     * @param int $type 发送短信的类型 1为营销类 0为系统通知类
     * @return bool Description
     */
    public static function mobileSend($mobile, $content, $type = 0) {
        if (empty($mobile) || empty($content)) {
            return FALSE;
        }

        //C++服务只能用测试redis服务（目前）
        $redis = new \Redis(); //TODO redis
//        $redis_host = '10.66.106.109';
//        $redis_port = '6379';
//        $redis_instanceid = 'crs-5i6fn2ax';
//        $redis_pwd = '123!!123';
//        $redis_config = config_item('redis_config');\
        $redisConfig = Yii::$app->params['redis'];
        $redis_host = $redisConfig['redisHost'];
        $redis_port = $redisConfig['redisPort'];
        $redis_instanceid = $redisConfig['redisInstanceid'];
        $redis_pwd = $redisConfig['redisPwd'];
        $redis->connect($redis_host, $redis_port);
        $redis->auth($redis_instanceid . ":" . $redis_pwd);
        $cacheKey = 'queue_sms_send';
        $queue_data = array(
            'phone' => "{$mobile}",
            'content' => "{$content}",
            'timestamp' => time(),
        );
        if ($type) {
            $queue_data['type'] = 1;
        }
        Yii::info("mobile_send_data " . json_encode($queue_data) . "]");
        $res = $redis->rPush($cacheKey, json_encode($queue_data));
        if (!empty($res)) {
            Yii::info("mobile_send_c_success result[$res]_data[" . json_encode($queue_data) . "]");
            return true;
        } else {
            Yii::info("mobile_send_c_error result[$res]_data[" . json_encode($queue_data) . "]");
            return FALSE;
        }
        $apikey = 'f9c841ee4e26cd6e6ed5594487b904b4';
        $send_url = 'https://sms.yunpian.com/v1/sms/send.json';
        $context = stream_context_create(array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-Type: application/x-www-form-urlencoded',
                'content' => http_build_query(array(
                    'text' => $content,
                    'apikey' => $apikey,
                    'mobile' => $mobile
                )),
//                'protocol_version' => 1.1,
//                'timeout' => 10,
//                'ignore_errors' => true
            )
        ));
        $result = file_get_contents($send_url, false, $context);
        $json_res = json_decode($result, TRUE);
        if (!empty($json_res) && $json_res['code'] == '0') {
            return TRUE;
        } else {
            Yii::info("mobile_send_code_error result[$result]");
            return FALSE;
        }
    }

    /**
     * @abstract 发送手机短信消息－智能匹配发送模式,提交短信时，系统会自动匹配审核通过的模板，匹配成功任意一个模板即可发送
     * @param curl流 $ch
     * @param 发送内容 $data 例如:['text'=>$text,'apikey'=>$apikey,'mobile'=>$mobile];
     * @return 返回发送状态信息
     */
    public static function send($ch, $data) {
        curl_setopt($ch, CURLOPT_URL, 'https://sms.yunpian.com/v1/sms/send.json');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        return curl_exec($ch);
    }

    /**
     * 
     * @param curl流 $ch
     * @param 发送内容 $data 例如:['tpl_id'=>'1','tpl_value'=>('#code#').'='.urlencode('1234').'&'.urlencode('#company#').'='.urlencode('欢乐行'),'apikey'=>$apikey,'mobile'=>$mobile]
     * @return 返回发送状态信息
     * return {
     *           "code": 0,
     *           "msg": "OK",
     *           "result": {
     *               "count": 1,   //成功发送的短信个数
     *               "fee": 0.05,     //扣费金额，70个字按一条计费，超出70个字时按每67字一条计费，类型：双精度浮点型/double
     *               "sid": 1097   //短信id；多个号码时以该id+各手机号尾号后8位作为短信id,
     *                           //（数据类型：64位整型，对应Java和C#的long，不可用int解析)
     *           }
     *       }
     *
     */
    public static function tplSend($ch, $data) {
        curl_setopt($ch, CURLOPT_URL, 'https://sms.yunpian.com/v1/sms/tpl_send.json');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        return curl_exec($ch);
    }

    /**
     * 
     * @param curl流 $ch
     * @param 发送内容 $data 例如:['code'=>'9876','apikey'=>$apikey,'mobile'=>$mobile]
     * @return 
     *      {
     *           "code": 0,
     *           "msg": "OK",
     *           "result": {
     *               "count": 1,   //成功发送的语音呼叫次数
     *               "fee": 1,     //扣费金额，一次语音验证码呼叫扣一条短信
     *               "sid": "931ee0bac7494aab8a422fff5c6be3ea"   //记录id，32位的唯一字符串
     *           }
     *       }
     */
    public static function voiceSend($ch, $data) {
        curl_setopt($ch, CURLOPT_URL, 'http://voice.yunpian.com/v1/voice/send.json');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        return curl_exec($ch);
    }

    public static function curlPost($url, $data, $dataType = 'array') {
        // 1. 初始化
        $ch = curl_init();
        // 2. 设置选项，包括URL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0); //设置header

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        //curl_setopt($ch, CURLOPT_TIMEOUT_MS, 1); //设置不用等待

        curl_setopt($ch, CURLOPT_POST, 1);
//            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        if ($dataType != 'json') {
            $data = http_build_query($data);
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        // 3. 执行并获取HTML文档内容
        $output = curl_exec($ch);
//            var_dump($output);exit;
        if ($output === FALSE) {
            Yii::info('post curl error:' . curl_error($ch));
            Yii::info('post curl errno:' . curl_errno($ch));
//                echo "cURL Error: " . curl_error($ch);
        }
        // 4. 释放curl句柄
        curl_close($ch);
        return $output;
    }

    public static function curlGet($url, $data) {

        $url = $url . http_build_query($data);
        // 1. 初始化
        $ch = curl_init();
        // 2. 设置选项，包括URL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0); //设置header

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        // curl_setopt($ch, CURLOPT_TIMEOUT, 100);
//    curl_setopt($ch, CURLOPT_TIMEOUT_MS, 1); //设置不用等待
        // 3. 执行并获取HTML文档内容
        $output = curl_exec($ch);
        //var_dump($output);exit;
        if ($output === FALSE) {
            //echo "cURL Error: " . curl_error($ch);
            Yii::info('get curl error:' . curl_error($ch));
        }
        // 4. 释放curl句柄
        curl_close($ch);
        return $output;
    }

    /**
     * 
     * @param 图片路径 $src
     * @param 脚本编号 $sync_num
     * @return 同步图片到cdn服务器
     */
    public static function syncImg($src, $sync_num = 108) {
        $cmd = self::$sync_path . " " . $sync_num . " $src 2>&1";
        $str = shell_exec($cmd);
        $str = str_replace(PHP_EOL, '', $str);
        Yii::info($cmd, 'syncImgCmd');
        Yii::info($str, 'syncImg');
        return $str;
    }

    /**
     * @return 登出系统
     */
    public static function logout() {
        Yii::$app->user->logout();
        setcookie('createSpot', '', time() - 1000, '/');
        setcookie('parentSpotId', '', time() - 1000, '/');
        setcookie('spotId', '', time() - 1000, '/');
        setcookie('parentSpotCode', '', time() - 1000, '/');
        setcookie('parentSpotName', '', time() - 1000, '/');
        setcookie('spot', '', time() - 1000, '/');
        setcookie('spotIcon', '', time() - 1000, '/');
        setcookie('spotName', '', time() - 1000, '/');
        setcookie('spotTelephone', '', time() - 1000, '/');
        setcookie('wechatSpotId', '', time() - 1000, '/');
        setcookie('rememberMe', '', time() - 1000, '/');
    }

    public static function strTransfer($string = null, $length = null) {
        preg_match_all("/./us", $string, $match);
        $strLength = count($match[0]);
        if ($strLength > $length) {
            return mb_substr($string, 0, $length, "utf-8") . '...';
        } else {
            return $string;
        }
    }

    /**
     * 
     * @param type $clientId 用户ID
     * @param type $spotId 诊所ID
     * @param array $tagsData  需要上报的数据
     * @return type  数据上报
     */
    public static function dataReport($clientId, $spotId, $tagsData) {
        try {
            $reportData = [
                [
                    'endpoint' => YII_DEBUG ? 'test' . $clientId : strval($clientId),
                    'metric' => YII_DEBUG ? 'test' . $spotId : strval($spotId),
                    'timestamp' => time(),
                    'value' => 0,
                    'step' => 86400,
                    'counterType' => 'GAUGE',
                    'tags' => 'url=' . $tagsData['url'] . ',event_type=' . $tagsData['eventType'] . ',ip=' . $tagsData['ip'] . ',title=his_action,ts=' . time()
                    . ',module=' . $tagsData['module'] . ',action=' . $tagsData['action'] . ',name=' . $tagsData['name'] . ',report_data_type=' . $tagsData['reportDataType']
                ]
            ];
            $url = 'http://dtph.easyhin.com/data/push';
            $res = self::curlPost($url, json_encode($reportData), 'json');
            Yii::info('dataReport result:[' . var_export($res, true) . ']');
        } catch (Exception $exc) {
            Yii::info('dataReport error:[' . $exc->getTraceAsString() . ']');
            return;
        }
    }

    public static function monitorReport($api, $errorCode, $desc) {
        try {
            $reportData = [
                [
                    'endpoint' => YII_DEBUG ? 'hisGroupTest' : 'hisGroup',
                    'metric' => 'accessInterface',
                    'timestamp' => time(),
                    'value' => 1,
                    'step' => 60,
                    'counterType' => 'GAUGE',
                    'tags' => 'api=' . $api . ',err_code=' . $errorCode . ',desc=' . $desc
                ]
            ];
            $url = 'http://v1.push.easyhin.com/v1/push';
            $res = self::curlPost($url, json_encode($reportData), 'json');
            Yii::info('monitorReport result:[' . var_export($res, true) . ']');
        } catch (Exception $exc) {
            Yii::info('monitorReport error:[' . $exc->getTraceAsString() . ']');
            return;
        }
    }

    /**
     *  参数说明
     *  $string  欲截取的字符串
     *  $sublen  截取的长度
     *  $start   从第几个字节截取，默认为0
     *  $code    字符编码，默认UTF-8
     */
    public static function cutStr($string, $sublen, $start = 0, $code = 'UTF-8') {
        if ($code == 'UTF-8') {
            $pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
            preg_match_all($pa, $string, $t_string);
            if (count($t_string[0]) - $start > $sublen)
                return join('', array_slice($t_string[0], $start, $sublen)) . ".....";
            return join('', array_slice($t_string[0], $start, $sublen));
        } else {
            $start = $start * 2;
            $sublen = $sublen * 2;
            $strlen = strlen($string);
            $tmpstr = '';
            for ($i = 0; $i < $strlen; $i++) {
                if ($i >= $start && $i < ($start + $sublen)) {
                    if (ord(substr($string, $i, 1)) > 129) {
                        $tmpstr.= substr($string, $i, 2);
                    } else {
                        $tmpstr.= substr($string, $i, 1);
                    }
                }
                if (ord(substr($string, $i, 1)) > 129)
                    $i++;
            }
            if (strlen($tmpstr) < $strlen)
                $tmpstr.= "";
            return $tmpstr;
        }
    }

    /**
     * 发送手机短信消息(curl 可能由于服务器curl插件版本问题导致发送不成功，所以没有curl)
     * @param string $mlobile 手机号
     * @param string $content 发送内容（需事先添加短信模板）
     * @return bool Description将短信写入Redis队列 用C++调用发短信服务
     */
    public static function mobileSendByRedis($mobile, $content) {
        if (empty($mobile) || empty($content)) {
            return FALSE;
        }

        //C++服务只能用测试redis服务（目前）
        $redis = new Redis(); //TODO redis
        $redis_host = '10.66.106.109';
        $redis_port = '6379';
        $redis_instanceid = 'crs-5i6fn2ax';
        $redis_pwd = '123!!123';
//    $redis_config = config_item('redis_config');
//    $redis_host = $redis_config['redis_host'];
//    $redis_port = $redis_config['redis_port'];
//    $redis_instanceid = $redis_config['redis_instanceid'];
//    $redis_pwd = $redis_config['redis_pwd'];
        $redis->connect($redis_host, $redis_port);
        $redis->auth($redis_instanceid . ":" . $redis_pwd);
        $cacheKey = 'queue_sms_send';
        $queue_data = array(
            'phone' => "{$mobile}",
            'content' => "{$content}",
            'timestamp' => time(),
        );
        $res = $redis->rPush($cacheKey, json_encode($queue_data));
        if (!empty($res)) {
            return true;
        } else {
            msg_tlog(LOG_INFO, "mobile_send_c_error result[$res]_data[" . json_encode($queue_data) . "]");
            return FALSE;
        }
        $apikey = 'f9c841ee4e26cd6e6ed5594487b904b4';
        $send_url = 'https://sms.yunpian.com/v1/sms/send.json';
        $context = stream_context_create(array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-Type: application/x-www-form-urlencoded',
                'content' => http_build_query(array(
                    'text' => $content,
                    'apikey' => $apikey,
                    'mobile' => $mobile
                )),
//                'protocol_version' => 1.1,
//                'timeout' => 10,
//                'ignore_errors' => true
            )
        ));
        $result = file_get_contents($send_url, false, $context);
        $json_res = json_decode($result, TRUE);
        if (!empty($json_res) && $json_res['code'] == '0') {
            return TRUE;
        } else {
            msg_tlog(LOG_INFO, "mobile_send_code_error result[$result]");
            return FALSE;
        }
    }

    /**
     * @param $date 要获取的日期
     * @return 周几格式
     */
    public static function getWeekDay($date) {
        $time = strtotime($date);
        $dateOfWeek = getdate($time)['wday'];
        $weekdayArray = [
            0 => '周日',
            1 => '周一',
            2 => '周二',
            3 => '周三',
            4 => '周四',
            5 => '周五',
            6 => '周六',
        ];
        $weekday = $weekdayArray[$dateOfWeek];
        return $weekday;
    }

    /**
     * XML编码
     * @param mixed $data 数据
     * @param string $encoding 数据编码
     * @param string $root 根节点名
     * @return string  仅适用于和金域对接的接口返回XML格式
     */
    public static function xmlEncode($data, $dataNode = true, $root = 'Data', $encoding = 'utf-8') {
        $xml = '<?xml version="1.0" encoding="' . $encoding . '"?>';
        $xml .= '<' . $root . '>';
        $xml .= $dataNode ? '<Data_Row>' : '';
        $xml .= self::dataToXml($data);
        $xml .= $dataNode ? '</Data_Row>' : '';
        $xml .= '</' . $root . '>';
        return $xml;
    }

    /**
     * 数据XML编码
     * @param mixed $data 数据
     * @return string  仅适用于和金域对接的接口返回XML格式
     */
    public static function dataToXml($data) {
        $xml = '';
        foreach ($data as $key => $val) {
            if ((is_array($val) || is_object($val))) {
                foreach ($val as $v) {
                    $xml .= "<$key>";
                    $xml .= ( is_array($v) || is_object($v)) ? self::dataToXml($v) : $v;
                    list($key, ) = explode(' ', $key);
                    $xml .= "</$key>";
                }
            } else {
                is_numeric($key) && $key = "item id=\"$key\"";
                $xml .= "<$key>";
                $xml .= $val;
                list($key, ) = explode(' ', $key);
                $xml .= "</$key>";
            }
        }
        return $xml;
    }

    /**
     *
     * 发送手机短信消息
     * @param string $phone 手机号
     * @param string $content 发送内容（需事先添加短信模板）
     * @param int $sendTime 发送时间 0:立即发送
     * @return bool Description
     */
    public static function sendSms($phone, $content, $sendTime = 0) {
        if ($sendTime == 0) {
            return Common::mobileSend($phone, $content, 1);
        } else {
            $smsRecord = new SmsRecord();
            $smsRecord->iphone = $phone;
            $smsRecord->content = $content;
            $smsRecord->send_time = $sendTime;
            $smsRecord->create_time = strtotime('now');
            $smsRecord->update_time = strtotime('now');
            return $smsRecord->save();
        }
    }

}
