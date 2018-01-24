<?php
use yii\helpers\Url;
use yii\helpers\Html;
$requestUrl = Url::to([$this->params['requestUrl']]);
$queryParams = Yii::$app->request->queryParams;
$titleData = [];
foreach ($buttons as $title) {
    $url = substr($title['url'], 0, strpos($title['url'], Yii::$app->urlManager->suffix));
    if (in_array($url, $this->params['permList'])) {
        $titleData[] = $title;
    }
}

$params = '';
$allActive = 'active';
$middleActive = '';
$centerActive = '';
$rightActive = '';
if (($type = Yii::$app->request->get('type')) != null){
    $params .= 'type='.$type.'&';
}
if ($queryParams) {
    if(isset($queryParams[$searchName])){
        $search = $queryParams[$searchName];
        foreach ($search as $key => $v) {
            if ($key != $statusName) {
                $params .= $searchName.'[' . $key . ']=' . $v . '&';
            } else {
                if ($v == 3) {
                    $allActive = '';
                    $middleActive = 'active';
                    $centerActive = '';
                    $rightActive = '';
                } else if ($v == 1) {
                    $allActive = '';
                    $middleActive = '';
                    $centerActive = '';
                    $rightActive = 'active';
                }else if($v == 2){
                    $allActive = '';
                    $middleActive = '';
                    $centerActive = 'active';
                    $rightActive = '';
                }
            }
        }
    }
}
?>
<div class="btn-group" id="J-select-box">
    <?php
    if(!empty($titleData)){
        foreach ($titleData as $key => $v){
            switch ($v['statusCode']){
                case 0 :
                    echo Html::a(Html::tag('i','',['class' => ' commonSearchStatus btn-background']).$v['title'],(isset($v['url'])?$v['url']:$requestUrl).'?'.$params.$searchName.'['.$statusName.']=',['data-pjax' => 0,'class' => 'btn  btn-group-left btn-custom '.$allActive]);
                    break;
                case 3 :
                    $dotText=(isset($v['hasDot'])&&$v['hasDot'])?Html::tag('i','',['class' => 'fa fa-circle red commonSearchStatus']):'';
                    echo Html::a($dotText.$v['title'].(isset($v['num'])?$v['num'].'人':''),(isset($v['url'])?$v['url']:$requestUrl).'?'.$params.$searchName.'['.$statusName.']='.$v['statusCode'],['data-pjax' => 0,'class' => 'btn  btn-group-middle btn-custom '.$middleActive]);
                    break;
                case 2 :
                    $dotText=(isset($v['hasDot'])&&$v['hasDot'])?Html::tag('i','',['class' => 'fa fa-circle green commonSearchStatus']):'';
                    echo Html::a($dotText.$v['title'].(isset($v['num'])?$v['num'].'人':''),(isset($v['url'])?$v['url']:$requestUrl).'?'.$params.$searchName.'['.$statusName.']='.$v['statusCode'],['data-pjax' => 0,'class' => 'btn  btn-group-center btn-custom '.$centerActive]);
                    break;
                case 1:
                    $dotText=(isset($v['hasDot'])&&$v['hasDot'])?Html::tag('i','',['class' => 'fa fa-circle end commonSearchStatus']):'';
                    echo Html::a($dotText.$v['title'].(isset($v['num'])?$v['num'].'人':''),(isset($v['url'])?$v['url']:$requestUrl).'?'.$params.$searchName.'['.$statusName.']='.$v['statusCode'],['data-pjax' => 0,'class' => 'btn btn-group-right btn-custom '.$rightActive]);
                    break;
            }
        }
    }
    ?>
</div>
