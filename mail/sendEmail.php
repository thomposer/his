<?php
use yii\helpers\Html;
$css = <<<CSS
     .table
{
border-collapse:collapse;
}
table,th, td
{
border: 1px solid black;
}
CSS;
echo "<style type='text/css'>";
echo $css;
echo "</style>";
/* @var $this yii\web\View */
/* @var $model app\modules\user\models\User */
    $type =[
        '1'=>"初诊",
        '2'=>"复诊"
    ];
    echo '<table style="border-collapse: collapse">';
    echo '<thead>';
    echo '<th>序号</th><th>患者姓名</th><th>手机号</th><th>预约类型</th><th>预约科室</th><th>预约医生</th><th>预约时间</th></tr>';
    echo '</thead>';
    echo '<tbody>';
    $num = 0;
    foreach($data as $key=>$value){
        $num = $key+1;
        echo '<tr><td>'.$num.'</td><td>'.Html::encode($value['username']).'</td><td>'.Html::encode($value['iphone']).'</td><td>'.$type[$value['type']].'</td><td>'.Html::encode($value['departmentName']).'</td><td>'.$value['doctorName'].'</td><td>'.date("Y-m-d H:i:s",$value['time']).'</td></tr>';
    }
    echo '</tbody>';
    echo '</table>';
?>

