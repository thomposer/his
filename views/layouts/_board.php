<?php
use yii\helpers\Html;
use  yii\helpers\Url;
?>
<ul class="navbar-nav nav">
    <li class="dropdown notifications-menu">
        <a href="<?= Url::to(['@spot_setBoardPreview']) ?>">
            <div class="icon_nav_right icon_board"></div>
        </a>
    </li>
</ul>