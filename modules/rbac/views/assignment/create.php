<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\rbac\models\Assignment */

$this->title = 'Create Assignment';
$this->params['breadcrumbs'][] = ['label' => 'Assignments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="assignment-create">


    <?= $this->render('_form', [
        'model' => $model,
        'userlist' => $userlist,
        'roles' => $roles
    ]) ?>

</div>
