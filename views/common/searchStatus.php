<?php
use yii\helpers\Url;
use yii\helpers\Html;
$requestUrl = Url::to([$this->params['requestUrl']]);
$queryParams = Yii::$app->request->queryParams;
$params = '';
$allActive = 'active';
$middleActive = '';
$centerActive = '';
$rightActive = '';
$cancelActive ='';
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
               }else if($v == 4){
                   $allActive = '';
                   $middleActive = '';
                   $centerActive = '';
                   $rightActive = '';
                   $cancelActive = 'active';
               }
            }
        }
    }
}
?>
<div class="btn-group" id="J-select-box">
    <?php
	   foreach ($buttons as $key => $v){
	       switch ($v['statusCode']){
	           case 0 :
	             echo Html::tag('span',Html::a($v['title'],$requestUrl.'?'.$params.$searchName.'['.$statusName.']='),['class' => 'btn  btn-group-left '.$allActive]);
	           break;
	           case 3 :
	             echo Html::tag('span',Html::a(Html::tag('i','',['class' => 'fa fa-circle red commonSearchStatus']).$v['title'].(isset($v['num'])?$v['num'].'人':''),$requestUrl.'?'.$params.$searchName.'['.$statusName.']='.$v['statusCode']),['class' => 'btn  btn-group-middle '.$middleActive]);
	           break;
	           case 2 :
	             echo Html::tag('span',Html::a(Html::tag('i','',['class' => 'fa fa-circle green commonSearchStatus']).$v['title'].(isset($v['num'])?$v['num'].'人':''),$requestUrl.'?'.$params.$searchName.'['.$statusName.']='.$v['statusCode']),['class' => 'btn  btn-group-center '.$centerActive]);
	           break;
	           case 1:
	             echo Html::tag('span',Html::a(Html::tag('i','',['class' => 'fa fa-circle end commonSearchStatus']).$v['title'].(isset($v['num'])?$v['num'].'人':''),$requestUrl.'?'.$params.$searchName.'['.$statusName.']='.$v['statusCode']),['class' => 'btn btn-group-right '.$rightActive]);
	           break;
	           case 4:
	               echo Html::tag('span',Html::a(Html::tag('i','',['class' => 'fa fa-circle grey commonSearchStatus']).$v['title'].(isset($v['num'])?$v['num'].'人':''),$requestUrl.'?'.$params.$searchName.'['.$statusName.']='.$v['statusCode']),['class' => 'btn btn-group-right '.$cancelActive]);
	           break;
	       }
	   }
	?>
</div>
