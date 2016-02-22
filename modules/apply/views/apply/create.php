<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\apply\models\ApplyPermissionList */

$this->title = '申请权限';
$this->params['breadcrumbs'][] = ['label' => 'Apply Permission Lists', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="apply-permission-list-create">
    <?= $this->render('_form', [
        'model' => $model,
        'spot' => $spot,
       
    ]) ?>

</div>
