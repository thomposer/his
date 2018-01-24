<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
/* @var $this yii\web\View */
/* @var $model app\modules\charge\models\Charge */
use johnitvn\ajaxcrud\CrudAsset;
use yii\helpers\Url;
use rkit\yii2\plugins\ajaxform\Asset;
use yii\widgets\ActiveForm;
use yii\web\View;
Asset::register($this);
$this->title = '新增收费';
$this->params['breadcrumbs'][] = ['label' => '收费', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
        <?php $form = ActiveForm::begin([
                'method' => 'post',
                'id' => 'createMaterialForm',
                'options' =>  ['enctype' => 'multipart/form-data'],
            ]); 
        ?>    
            <?= $this->render('_createMaterial', [
               'model' => $model,
               'dataProvider' => $dataProvider,
               'list' => $list,
               'form' => $form,
               'update' => true,
               'materialTotal' => $materialTotal
            ]) ?>
        <?php ActiveForm::end(); ?>	
<?php 
$listJson = json_encode($list,true);
$materialTotalJson = json_encode($materialTotal,true);
$js = <<<JS
    var baseUrl = '$baseUrl';
    var materialList = $listJson;
    var materialTotal = $materialTotalJson;
     require([baseUrl+"/public/js/charge/updateMaterial.js"],function(main){
    		main.init();
     });
JS;

$this->registerJs($js,View::POS_END);

?>