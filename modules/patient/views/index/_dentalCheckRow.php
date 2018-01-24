<?php
use yii\helpers\Html;
use app\modules\outpatient\models\DentalHistoryRelation;
$leftTop='';
$rightTop='';
$leftBottom='';
$rightBottom='';
$infoArr = explode(',',$dental['position']);
if(is_array($infoArr) && count($infoArr) == 4){
    $leftTop = $infoArr[0];
    $rightTop = $infoArr[1];
    $rightBottom = $infoArr[2];
    $leftBottom = $infoArr[3];
}
 if($dental['dental_disease']){
     $dentalCheckBox = 'dental-disease-check-box ';
 }else{
     $dentalCheckBox = 'dental-check-box ';
 }
?>
<div class=" <?php echo $dentalCheckBox;  if($showLine) { echo 'dashed-Bottom'; } ?>">
    <div class="dental-position">
        <div class="dental-left-top"><?= $leftTop ?></div>
        <div class="dental-right-top"><?= $rightTop ?></div>
        <div class="dental-left-bottom"><?= $leftBottom ?></div>
        <div class="dental-right-bottom"><?= $rightBottom ?></div>
        <?php if($dental['dental_disease']) :?>
            <div class="dental-disease">
                    病症：<?=DentalHistoryRelation::$dentalDisease[$dental['dental_disease']];  ?>
            </div>
        <?php endif; ?>
    </div>



    <div class="dental-desc ">
        <?= Html::encode($dental['content']) ?>
    </div>



</div>

