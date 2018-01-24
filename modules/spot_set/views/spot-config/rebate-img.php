<?php

use yii\grid\GridView;
use app\common\Common;
use yii\helpers\Html;
use dosamigos\datepicker\DatePicker;
use yii\widgets\ActiveForm;
use app\modules\triage\models\TriageInfo;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\DetailView;

$baseUrl=Yii::$app->request->baseUrl;
$type=Yii::$app->request->get('type');

?>

<?php if($id==1): ?>
<p>
    <img src="<?php echo $baseUrl;?>/public/img/user/outpatient-rebate.png" alt="" style="width: 557px;">
</p>
<?php elseif($id==2): ?>
<p>
    <img src="<?php echo $baseUrl;?>/public/img/user/inspect-rebate.png" alt="" style="width: 557px;">
</p>
<?php elseif($id==3): ?>
<p>
    <img src="<?php echo $baseUrl;?>/public/img/user/check-rebate.png" alt="" style="width: 557px;">
</p>
<?php elseif($id==4): ?>
<p>
    <img src="<?php echo $baseUrl;?>/public/img/user/cure-rebate.png" alt="" style="width: 557px;">
</p>
<?php elseif($id==5): ?>

<p>
    <?php if($type==1) { ?>
        <img src="<?php echo $baseUrl; ?>/public/img/user/recipe-rebate-1.png" alt="" style="width: 557px;">
        <?php

    }else if($type==2){ ?>
        <img src="<?php echo $baseUrl; ?>/public/img/user/recipe-rebate-2.jpg" alt="" style="width: 557px;">
        <?php
    }
    ?>
</p>

<?php elseif($id==6): ?>
<p>
    <img src="<?php echo $baseUrl;?>/public/img/user/charge-rebate.png" alt="" style="width: 557px;">
</p>
<?php endif ?>