<?php
use yii\helpers\Html;
use app\modules\spot\models\RecipeList;
?>

<table id="w0 " class="table table-striped table-bordered">
    <tbody>
    <?php  if(!empty($packageRecord['inspect'])):?>
        <tr>
            <td>检验医嘱</td>
            <td>
                <?php
                $inspectText = '';
                foreach($packageRecord['inspect'] as $key => $value){
                    $inspectText .= '<div>'.Html::encode($value['name']).'</div>';
                }
                echo $inspectText;
                ?>
            </td>
        </tr>
    <?php endif?>
    <?php  if(!empty($packageRecord['check'])):?>
        <tr>
            <td>检查医嘱</td>
            <td>
                <?php
                $checkText = '';
                foreach($packageRecord['check'] as $key => $value){
                    $checkText .= '<div>'.Html::encode($value['name']).'</div>';
                }
                echo $checkText;
                ?>
            </td>
        </tr>
    <?php endif?>
    <?php  if(!empty($packageRecord['cure'])):?>
        <tr>
            <td>治疗医嘱</td>
            <td>
                <?php
                $cureText = '';
                foreach($packageRecord['cure'] as $key => $value){
                    $cureText .= '<div>'.Html::encode($value['name']).$value['time'].Html::encode($value['unit']).'</div>';
                }
                echo $cureText;
                ?>
            </td>
        </tr>
    <?php endif?>
    <?php  if(!empty($packageRecord['recipe'])):?>
        <tr>
            <td>处方医嘱</td>
            <td>
                <?php
                $recipeText = '';
                foreach($packageRecord['recipe'] as $key => $value){
                    $specification = '';
                    $value['specification'] != '' && $specification = '（'.Html::encode($value['specification']).'）';
                    $recipeText .= '<div>'.Html::encode($value['name']).$specification.$value['num'].Html::encode(RecipeList::$getUnit[$value['unit']]).'</div>';
                }
                echo $recipeText;
                ?>
            </td>
        </tr>
    <?php endif?>
    </tbody>
</table>