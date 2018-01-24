<?php

//左侧菜单缓存id前缀
Yii::setAlias('@menuLayout', 'menuLayoutHis_');
//系统管理员菜单缓存id前缀
Yii::setAlias('@systemMenu', 'systemMenuHis_');
//普通角色菜单缓存id前缀
Yii::setAlias('@commonRoleMenu', 'commonRoleMenuHis_');
//普通角色权限列表缓存id前缀
Yii::setAlias('@commonAllPerm', 'commonAllPermHis_');
//机构下诊所列表缓存前缀
Yii::setAlias('@spotList', 'spotListHis_');

//护士工作台缓存前缀
Yii::setAlias('@nurseId', 'nurseHis_');


//医生预约时间缓存前缀
Yii::setAlias('@doctorTime', 'doctorTime_');
//微信支付诊所配置信息前缀
Yii::setAlias('@wechatSpotId', 'wechatSpotId_');
//微信支付配置前缀
Yii::setAlias('@wxPayConfig', 'wxPayConfig_');
//微信支付收费项前缀
Yii::setAlias('@wxPayChargeItem', 'wxPayChargeItem_');
//获取开单医生信息的缓存前缀
Yii::setAlias('@billingDoctor', 'billingDoctor_');
//机构名称前缀
Yii::setAlias('@parentSpotName', 'parentSpotName_');
//机构代码前缀
Yii::setAlias('@parentSpotCode', 'parentSpotCode_');

//诊所代码前缀
Yii::setAlias('@spot', 'spot_');
//诊所名称前缀
Yii::setAlias('@spotName', 'spotName_');
//诊所logo图
Yii::setAlias('@spotIcon', 'spotIcon_');

//生长曲线
Yii::setAlias('@growthData', 'growthDataCacheKey');

//普通消息提醒key
Yii::setAlias('@messageCenter', 'messageCenter_');
//病历未填写消息缓存key
Yii::setAlias('@messageMedical', 'messageMedical_');

//医生实验室检查告警提示缓存key
Yii::setAlias('@doctorWarning', 'doctorWarning_');

//医生门诊 待出报告缓存key
Yii::setAlias('@pendingReportNum', 'pendingReportNum');

//医生门诊 全部报告已出时间缓存key
Yii::setAlias('@allReportTime', 'allReportTime');

//医生门诊 已出报告缓存key
Yii::setAlias('@madeReportNum', 'madeReportNum');

//微信支付套餐卡收费项目前缀
Yii::setAlias('@wxPayPackageCardItem', 'wxPayPackageCardItem_');
