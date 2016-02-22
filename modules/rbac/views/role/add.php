<?php
/* @var $this \yii\web\View */
/* @var $content string */

use app\assets\AppAsset;

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $model app\modules\rbac\models\Role */
/* @var $form yii\widgets\ActiveForm */
?>
<!DOCTYPE html>
<html>
<head>
    <title></title>
    <meta charset="UTF-8">
    <?php $this->registerCssFile('@web/../public/Css/bootstrap-responsive.css',['depends' => \app\assets\AppAsset::className()]) ?>
    <?php $this->registerJsFile('@web/../public/Js/jquery.js',['depends' => \app\assets\AppAsset::className()])?>
    <?php $this->registerJsFile('@web/../public/Js/ckform.js',['depends' => \app\assets\AppAsset::className()])?>
    <?php $this->registerJsFile('@web/../public/Js/common.js',['depends' => \app\assets\AppAsset::className()])?>
    <?php $this->registerJsFile('@web/../public/Js/roleadd.js',['depends' => \app\assets\AppAsset::className()])?>
<!--     <script type="text/javascript" src="../Js/jquery.js"></script> -->
<!--     <script type="text/javascript" src="../Js/jquery.sorted.js"></script> -->
<!--     <script type="text/javascript" src="../Js/bootstrap.js"></script> -->
<!--     <script type="text/javascript" src="../Js/ckform.js"></script> -->
<!--     <script type="text/javascript" src="../Js/common.js"></script> -->

    <style type="text/css">
        body {
            padding-bottom: 40px;
        }
        .sidebar-nav {
            padding: 9px 0;
        }

        @media (max-width: 980px) {
            /* Enable use of floated navbar text */
            .navbar-text.pull-right {
                float: none;
                padding-left: 5px;
                padding-right: 5px;
            }
        }


    </style>

</head>
<body>
<?php $from =ActiveForm::begin(); ?>
    <table class="table table-bordered table-hover definewidth m10">
        <tr>
            <td width="10%" class="tableleft">角色名称</td>
            <td><input type="text" class="form-control" name="name" placeholder="角色名字" style="width: 40%"></td>
        </tr>
<!--         <tr> -->
<!--             <td class="tableleft">状态</td> -->
<!--             <td> -->
<!--                 <input type="radio" name="status" value="1" checked/> 启用  <input type="radio" name="status" value="0"/> 禁用 -->
<!--             </td> -->
<!--         </tr> -->
        <tr>
            <td class="tableleft">站点权限</td>
            <td>
                <ul>
                    <li><label class='checkbox inline'><input type='checkbox' name='group' value='' />公共站点</label>
                    <ul>
                        <?php foreach ($permissions as $v):?>
                        <li><label class='checkbox inline'><input type='checkbox' name='child' value='<?php echo $v->data; ?>' /><?php echo $v->name; ?></label></li>
<!--                         <li><label class='checkbox inline'><input type='checkbox' name='child' value='' />热血传奇手游(mir)</label> -->
                        <?php endforeach;?>
                    </ul>
                    </li>
                </ul>
            </td>
        </tr>
        <tr>
            <td width="10%" class="tableleft">描述</td>
            <td><textarea class="form-control" rows="3" name="description" style="width: 40%"></textarea></td>           
        </tr>

        
        <tr>
            <td class="tableleft"></td>
            <td>
                <button type="submit" class="btn btn-primary" type="button">保存</button> &nbsp;&nbsp;<button type="button" class="btn btn-success" name="backid" id="backid">返回列表</button>
            </td>
        </tr>
    </table>
<?php ActiveForm::end(); ?>
</body>
</html>
