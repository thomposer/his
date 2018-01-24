<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/23
 * Time: 21:57
 */
use yii\helpers\Html;
use app\modules\triage\models\NursingRecord;
use app\modules\patient\models\Patient;
$nurseAttributeLabels = (new NursingRecord())->attributeLabels();
?>
<table id="w0" class="table detail-view nurse-record-<?= $patient_info['recordId'] ?> hidden">
    <tbody>
    <?php
//     $nurseRecord = NursingRecord::getNurseRecord($patient_info['recordId']);
    $nurseRecord = $nurseRecordData[$patient_info['recordId']];
    if(!empty($nurseRecord)){
        $nurseRecordContent = '';
        foreach($nurseRecord as $key =>$value){
            $nurseRecordContent .= "<tr>";
            $nurseRecordContent .=  " <th>".Html::encode($value['name'])."</th>" ;
            $nurseRecordContent .=  "<td>";
            $nurseRecordContent .=  "<div>".$nurseAttributeLabels['executor']."：".Html::encode($value['executor'])."</div>";
            $value['execute_time'] !=0?$executeTime = date("Y-m-d H:i",$value['execute_time']):$executeTime="";
            $nurseRecordContent .=  "<div>".$nurseAttributeLabels['execute_time']."：".$executeTime."</div>";
            $nurseRecordContent .=  "<div>护理内容："."</div>";
            $nurseRecordContent .=  "<div ><textarea  style='min-height: 100px; border: none;' class='form-control' disabled>".Html::encode($value['content'])."</textarea></div>";
            $nurseRecordContent .=  "</td>";
            $nurseRecordContent .=  "</tr>";
        }

    }else{
        $nurseRecordContent = '';
        $nurseRecordContent .= "<tr><td class='no-result'>暂无护理记录</td></tr>";
    }
    echo $nurseRecordContent;
    ?>
    </tbody>
</table>

