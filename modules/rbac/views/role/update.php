<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\rbac\models\Role */

$this->title = '更新角色: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Roles', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->name]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="role-update">

    
    <?= $this->render('_form', [
        'model' => $model,
        
        'permission_parent' => $permission_parent,
        'permission_child' =>$permission_child
    ]) ?>

</div>
