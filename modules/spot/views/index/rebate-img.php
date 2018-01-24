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

?>

<?php if($id==1): ?>
<p>
    <img src="/public/img/user/outpatient-rebate.png" alt="" style="width: 557px;">
</p>
<?php elseif($id==2): ?>
<p>
    <img src="/public/img/user/inspect-rebate.png" alt="" style="width: 557px;">
</p>
<?php elseif($id==3): ?>
<p>
    <img src="/public/img/user/check-rebate.png" alt="" style="width: 557px;">
</p>
<?php elseif($id==4): ?>
<p>
    <img src="/public/img/user/cure-rebate.png" alt="" style="width: 557px;">
</p>
<?php elseif($id==5): ?>
<p>
    <img src="/public/img/user/recipe-rebate.png" alt="" style="width: 557px;">
</p>
<?php elseif($id==6): ?>
<p>
    <img src="/public/img/user/charge-rebate.png" alt="" style="width: 557px;">
</p>
<?php endif ?>