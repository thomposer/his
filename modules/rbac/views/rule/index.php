<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\common\AutoLayout;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\rbac\models\search\RuleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Rules';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
    <?php $this->registerCssFile('@web/public/css/bootstrap/bootstrap.css') ?>
<?php $this->endBlock();?>
<?php $this->beginBlock('content')?>
<div class="rule-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Rule', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    

</div>
<?php $this->endBlock();?>
<?php AutoLayout::end();?>