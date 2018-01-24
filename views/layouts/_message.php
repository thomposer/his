<?php 
    use yii\helpers\Html;
    use  yii\helpers\Url;
?>
<ul class="nav navbar-nav">
                  <li class="dropdown notifications-menu">
                      <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                          <i class="icon_nav_right icon_message"></i>
                              <?php 
                                if($messageNum > 99){
                                  echo '<span class="label label-danger num-label">···</span>';
                                }else if($messageNum <= 99 && $messageNum > 0){
                                    echo '<span class="label label-danger num-label">'.$messageNum.'</span>';
                                }
                              ?>
                      </a>
                      <ul class="dropdown-menu dropdown-background" >
                          <li class="header dropdown-header">你有<span class="header-num"> <?= $messageNum ?> </span>条信息未处理,请关注</li>
                          <li>
                              <!-- inner menu: contains the actual data -->
                              <ul class="menu message-content">
                              	  <?php if(!empty($messageList)):?>	
                                  <?php foreach ($messageList as $m):?>
                                      <li><!-- start message -->
                                          <a href="<?= Url::to(['@apiMessageSetStatus','id'=>$m['id']])?>" data-method = 'post' style='padding: 8px 14px 5px'>

                                              <div class="menu-left-type"><?= Html::encode($m['type']) ?>:<?= Html::encode($m['content']) ?></div>
                                              <div class="time-box">
                                                  <span class="menu-right-time"><?= date('m-d H:i',$m['create_time']) ?></span>
                                              </div>
                                              <p style="margin: 18px 0 0px;">
                                                  <span class="menu-buttom-content">患者：<span class="menu-buttom-patient"><?= Html::encode($m['username']) ?><?= $m['clinic_name']?'('.Html::encode($m['clinic_name']).')':'' ?></span></span>
                                              </p>
                                          </a>
                                      </li>
                                  <?php endforeach;?>
                                  <?php endif;?>
                              </ul>
                          </li>
                          <li class="footer dropdown-boottom">
                              <div class="menu-bottom-left">
<!--                                  <a data-confirm-message="此操作会覆盖当前设置，请谨慎操作！" data-delete="" data-confirm-title="系统提示" data-toggle="tooltip" role="modal-remote" data-request-method="post" data-pjax="1" aria-label="复制上周" title=""  data-url="--><?//= Url::to(['@apiMessageSetStatusAll']) ?><!--" data-method="post" class="decoration-underline">-->
<!--                                      <i class=" commonSearchStatus btn-background"></i>一键清除-->
<!--                                  </a>-->
								
                                  <?php 
                                  if($messageNum > 0){
                                      echo Html::a('一键清除', ['@apiMessageSetStatusAll'], [
                                          'class' => 'decoration-underline',
                                          'data' => [
                                              'confirm' => '是否清除未读消息?',
                                              'method' => 'post',
                                              'confirm-ok' => '是',
                                              'confirm-cancel' => '否'
                                          ],
                                          'delete' => true,
                                      ]);
                                  }
                                  ?>

                              </div>
                              <div class="menu-bottom-right">
                                  <a class="decoration-underline" href="#"><span class="menu-buttom-content">更多消息</span></a>
                              </div>
                          </li>
                      </ul>
                  </li>
            </ul>