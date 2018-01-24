<?php 
    use yii\helpers\Html;
    use  yii\helpers\Url;
use app\modules\user\models\User;
use app\modules\patient\models\Patient;
?>
<ul class="nav navbar-nav">
                  <li class="dropdown notifications-menu">
                      <a href="#" class="dropdown-toggle medical-title-color" data-toggle="dropdown">
                          <i class="fa fa-list-alt medical-icon"></i>今日病历未填写
                              <?php 
                                if($medicalNum > 99){
                                  echo '<span class="label label-danger num-label">···</span>';
                                }else if($medicalNum <= 99 && $medicalNum > 0){
                                    echo '<span class="label label-danger num-label left-num-label">'.$medicalNum.'</span>';
                                }
                              ?>
                      </a>
                      <ul class="dropdown-menu dropdown-background medical-ul" >
                          <li class="header dropdown-header">你有<span class="header-num"> <?= $medicalNum ?> </span>条病历未填写，请关注</li>
                          <li>
                              <!-- inner menu: contains the actual data -->
                              <ul class="menu message-content">
                              	  <?php if(!empty($medicalList)):?>	
                                  <?php foreach ($medicalList as $m):?>
                                      <li><!-- start message -->
                                          <div class = "medical-start-div" style='padding: 8px 14px 5px'>

                                              <p style="margin: 5px 0 8px;">
                                                  <span ><?= Html::encode($m['username']).'<span style = "color: #99A3B1;letter-spacing: 0;">（'.User::$getSex[$m['sex']].' '.Patient::dateDiffage($m['birthday'],time()).'）</span>' ?></span>
                                                  <?= Html::a('接诊',$m['url'],['class' => 'medical-outpatient']) ?>
                                              </p>
                                              
                                          </div>
                                      </li>
                                  <?php endforeach;?>
                                  <?php endif;?>
                              </ul>
                          </li>
                      </ul>
                  </li>
            </ul>