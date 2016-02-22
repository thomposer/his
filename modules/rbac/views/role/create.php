<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\rbac\models\Role */

$this->title = '创建角色';
$this->params['breadcrumbs'][] = ['label' => 'Roles', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="role-create">

    
    <?= $this->render('_form', [
        'model' => $model,
        'type'  => $type,
        'permission_parent' => $permission_parent,
        'permission_child'  => $permission_child
    ]) ?>

</div>
