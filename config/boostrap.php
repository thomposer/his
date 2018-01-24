<?php
use yii\helpers\Html;
/**
 * 定义文件目录路径别名
 */
Yii::setAlias('@RootPath', dirname(__DIR__));
Yii::setAlias('@PublicPath', "@RootPath/public");
Yii::setAlias('@JsPath', "@PublicPath/js");
Yii::setAlias('@CssPath', "@PublicPath/css");
Yii::setAlias('@ImgPath', "@PublicPath/img");
Yii::setAlias("@DepPath", "@RootPath/dep");
Yii::setAlias("@DepPath", "@RootPath/gulp");
Yii::setAlias("@DepPath", "@RootPath/node_modules");
Yii::setAlias('@loginCookieExpireTime', 3600*24*7);//登录信息cookie时间
Yii::setAlias('@loginSessionExpireTime', 3600*24);//登录信息session时间
Yii::setAlias('@cacheTime', 300);//缓存时间
Yii::setAlias('@cachePrintTime', 60);//过敏史缓存时间
Yii::setAlias('@ServicePath', '@RootPath/service');//service目录第三方类库
Yii::setAlias('@hisCommonSendAppointmentText', '/his/common/sendAppointmentText');//his的预约成功后短信通知地址
Yii::setAlias('@hisCommonSendAppointmentTimer', '/his/common/sendAppointmentTimer');//his定时发送预约短信通知地址
Yii::setAlias('@emailName', 'ehospital@easyhin.com');//发送邮件的邮箱名称


/**
 * 临时文件夹
 */
Yii::setAlias('@TempPath', '@RootPath/temp');
/**
 * 顶部tab组件
 */
Yii::setAlias('@contentTopTab', '//common/contentTopTab');
/**
 * 模板管理的左侧导航
 */
Yii::setAlias('@templateSidebar', '//common/templateSidebar');
/**
 * 机构收费项 二级导航
 */
Yii::setAlias('@spotChargeItemNav', '//common/spotChargeItemNav');
/**
 * 状态筛选组件
 */
Yii::setAlias('@searchStatus', '//common/searchStatus');
/**
 * 状态筛选组件(跳转链接型)
 */
Yii::setAlias('@searchStatusSkip', '//common/searchStatusSkip');
/**
 * 状态筛选组件通用型
 */
Yii::setAlias('@searchStatusCommon', '//common/searchStatusCommon');
/*
 * 医嘱执行中的 医生信息  诊断信息
 */
Yii::setAlias('@orderFillerInfo', '//common/orderFillerInfo');
/*
 * 医嘱执行中的 医生信息  诊断信息 过敏史信息打印
 */
Yii::setAlias('@orderFillerInfoPrint', '//common/orderFillerInfoPrint');
/**
 *
 */
Yii::setAlias('@fileUpload', '//common/fileUpload');

/**
 * 文件服务器目录
 */
Yii::setAlias('@filePath', '@PublicPath/assets');

//超级管理员账号
Yii::setAlias('@rootEmail', 'root@qq.com');
Yii::setAlias('@rootIphone', '15913196469');
/**
 * 定义访问路径别名
 */
//RBAC权限管理模块

Yii::setAlias('@rbacRole', '/rbac/role/index');
Yii::setAlias('@rbacRoleCreate', '/rbac/role/create');
Yii::setAlias('@rbacRoleUpdate', '/rbac/role/update');
Yii::setAlias('@rbacRoleDelete', '/rbac/role/delete');
Yii::setAlias('@rbacPermissionIndex', '/rbac/permission/index');
Yii::setAlias('@rbacPermissionCreate', '/rbac/permission/create');
Yii::setAlias('@rbacPermissionUpdate', '/rbac/permission/update');
Yii::setAlias('@rbacPermissionDelete', '/rbac/permission/delete');
Yii::setAlias('@rbacPermissionCreateCategory', '/rbac/permission/create-category');
Yii::setAlias('@rbacApplyIndex', '/rbac/apply/index');
Yii::setAlias('@rbacApplyPass', '/rbac/apply/pass');
Yii::setAlias('@rbacApplyDelete', '/rbac/apply/delete');
Yii::setAlias('@rbacApplyUpdate', '/rbac/apply/update');
//申请权限和添加站点模块
Yii::setAlias('@applyApplyCreate', '/apply/apply/create');
Yii::setAlias('@applyApplyIndex', '/apply/apply/index');
Yii::setAlias('@applyApplyWxcreate', '/apply/apply/wxcreate');
Yii::setAlias('@applyApplyRegister', '/apply/apply/register');

Yii::setAlias('@rbacRule', '/rbac/rule/index');

//站点选择模块
Yii::setAlias('@manage', '/manage');
Yii::setAlias('@manageDefaultIndex', '/manage/default/index');
Yii::setAlias('@manageIndex', '/manage/index/index');
Yii::setAlias('@manageSites', '/manage/sites/index');
Yii::setAlias('@manageSitesUpload', '/manage/sites/upload');//上传头像URI
Yii::setAlias('@manageSitesAppointmentTime', '/manage/sites/appointment-time');//获取科室可预约时间URI
Yii::setAlias('@manageLogout', '/manage/sites/logout');


//机构管理
Yii::setAlias('@spotOrganizationIndex', '/spot/organization/index');
Yii::setAlias('@spotOrganizationCreate', '/spot/organization/create');
Yii::setAlias('@spotOrganizationUpdate', '/spot/organization/update');
Yii::setAlias('@spotOrganizationDelete', '/spot/organization/delete');
Yii::setAlias('@spotOrganizationView', '/spot/organization/view');
Yii::setAlias('@spotRecipeListUpdateStatus', '/spot/recipe-list/update-status');
Yii::setAlias('@manageSitesSecondDepartmrnt', '/user/manage/department-select');//获取科室可预约时间URI
//诊所管理
Yii::setAlias('@spotIndexIndex', '/spot/index/index');
Yii::setAlias('@spotIndexCreate', '/spot/index/create');
Yii::setAlias('@spotIndexUpdate', '/spot/index/update');
Yii::setAlias('@spotIndexDelete', '/spot/index/delete');

//系统管理员角色别名
Yii::setAlias('@systemRole', 'systems'); //角色名
Yii::setAlias('@systemPermission', 'allpermissions'); //权限名
//站点角色前缀
Yii::setAlias('@spotPrefix', 'spot_');

Yii::setAlias('@TemplateIndex', '/template/index/index');
Yii::setAlias('@TemplateIndexCreate', '/template/index/create');

//模板管理模块
Yii::setAlias('@moduleAdminCreate', '/module/admin/create');
Yii::setAlias('@moduleAdminIndex', '/module/admin/index');
Yii::setAlias('@moduleAdminAdd', '/module/admin/add');
Yii::setAlias('@moduleAdminList', '/module/admin/list');
Yii::setAlias('@moduleAdminView', '/module/admin/view');
Yii::setAlias('@moduleAdminUpdate', '/module/admin/update'); //更新各站点的模块菜单权限
Yii::setAlias('@moduleAdminEdit', '/module/admin/edit'); //修改该模块的基本信息
Yii::setAlias('@moduleAdminCreateChildren','/module/admin/create-children');//添加子模块

Yii::setAlias('@moduleMenuIndex', '/module/menu/index');
Yii::setAlias('@moduleMenuUpdate', '/module/menu/update');
Yii::setAlias('@moduleMenuDelete', '/module/menu/delete');
Yii::setAlias('@moduleMenuCreate', '/module/menu/create');
Yii::setAlias('@moduleMenuSearch', '/module/menu/search');
Yii::setAlias('@moduleMenuFlush', '/module/menu/flush');

//错误页面路径
Yii::setAlias('@userDefaultError', '/user/default/error');
Yii::setAlias('@errorAbsoluteUrl', "/modules/site/views/default/error");

// 默认诊所模块命名
Yii::setAlias('@defaultSpotName', '_defaut_module_');
//机构模块命名
Yii::setAlias('@superSpotName', '_super_module_');

Yii::setAlias('@defaultTemplateUrl', '@RootPath/config/defaultTemplate.php');
Yii::setAlias('@superTemplateUrl', '@RootPath/config/initSuperPermission.php');
Yii::setAlias('@initDefaultRoleUrl', '@RootPath/config/initDefaultRole.php');

// 行为记录
Yii::setAlias('@behaviorRecordDelete', '/behavior/record/delete');
Yii::setAlias('@behaviorRecordIndex', '/behavior/record/index');
Yii::setAlias('@behaviorRecordView', '/behavior/record/view');
Yii::setAlias('@behaviorRecordDeleteMonth', '/behavior/record/delete-month');

//user模块
Yii::setAlias('@userIndexLogin', '/user/index/login');
Yii::setAlias('@userIndexLogout', '/user/index/logout');
Yii::setAlias('@userIndexRegister', '/user/index/register');
Yii::setAlias('@userIndexValidateCode', '/user/index/validate-code');//短信验证码校验

//验证注册邮件模版地址
Yii::setAlias('@checkEmail', 'checkEmail');
//重置密码url
Yii::setAlias('@userManageReset', '/user/manage/reset');
//重置密码邮件模版地址
Yii::setAlias('@resetEmail', 'resetEmail');
//注册邮件模板地址
Yii::setAlias('@registerEmail', 'registerEmail');
Yii::setAlias('@userIndexResetPassword', '/user/index/reset-password');
Yii::setAlias('@userManageEditPassword', '/user/manage/edit-password');
Yii::setAlias('@userManageIndex', '/user/manage/index');
Yii::setAlias('@userManageCreate', '/user/manage/create');
Yii::setAlias('@userManageUpdate', '/user/manage/update');
Yii::setAlias('@userManageDelete', '/user/manage/delete');
Yii::setAlias('@userManageView', '/user/manage/view');
Yii::setAlias('@userManageInfo', '/user/manage/info');
Yii::setAlias('@userIndexSendCode', '/user/index/send-code');//发送验证码

//链接失效页面
Yii::setAlias('@userIndexOverdue', '/user/index/overdue');

//预约模块
Yii::setAlias('@make_appointmentAppointmentIndex', '/make_appointment/appointment/index');
Yii::setAlias('@make_appointmentAppointmentAppointmentDetail', '/make_appointment/appointment/appointment-detail');
Yii::setAlias('@make_appointmentAppointmentList', '/make_appointment/appointment/list');
Yii::setAlias('@make_appointmentAppointmentCreate', '/make_appointment/appointment/create');
Yii::setAlias('@make_appointmentAppointmentUpdate', '/make_appointment/appointment/update');
Yii::setAlias('@make_appointmentAppointmentDelete', '/make_appointment/appointment/delete');
Yii::setAlias('@make_appointmentAppointmentView', '/make_appointment/appointment/view');
Yii::setAlias('@make_appointmentAppointmenDoctortConfig', '/make_appointment/appointment/doctor-config');
Yii::setAlias('@make_appointmentAppointmentCopyTimeConfig','/make_appointment/appointment/copy-time-config');//医生预约设置复制上周
Yii::setAlias('@make_appointmentAppointmentRoomConfig', '/make_appointment/appointment/room-config');
Yii::setAlias('@make_appointmentAppointmentSaveConfig', '/make_appointment/appointment/save-config');
Yii::setAlias('@make_appointmentAppointmentCopyConfig','/make_appointment/appointment/copy-config');//复制上周
Yii::setAlias('@make_appointmentAppointmentSaveTimeConfig', '/make_appointment/appointment/save-time-config');
Yii::setAlias('@make_appointmentAppointmentDetail', '/make_appointment/appointment/detail');
Yii::setAlias('@make_appointmentAppointmentCreatbyDoctor', '@RootPath/modules/make_appointment/views/appointment/creatby-doctor');//医生预约弹窗
Yii::setAlias('@make_appointmentAppointmentDoctorConfig', '/make_appointment/appointment/doctor-config');//预约权限设置
Yii::setAlias('@make_appointmentAppointmentTimeConfig', '/make_appointment/appointment/time-config');//预约时间设置
Yii::setAlias('@closeAppointment', '/make_appointment/appointment/close-appointment');//关闭预约弹窗
Yii::setAlias('@saveCloseAppointment', '/make_appointment/appointment/save-close-appointment');//关闭预约弹窗
Yii::setAlias('@showMaxAppointmentInfo', '@RootPath/modules/make_appointment/views/appointment/showMaxAppointmentInfo');//关闭预约弹窗
Yii::setAlias('@appointmentDetailView', '@RootPath/modules/make_appointment/views/appointment/appointment-detail');//关闭预约弹窗
Yii::setAlias('@appointmentTimeTemplate','/make_appointment/appointment/appointment-time-template');//预约时间模板
Yii::setAlias('@createAppointmentTimeTemplate','/make_appointment/appointment/create-time-template');//新建预约时间模板
Yii::setAlias('@updateAppointmentTimeTemplate','/make_appointment/appointment/update-time-template');//修改预约时间模板
Yii::setAlias('@deleteAppointmentTimeTemplate','/make_appointment/appointment/delete-time-template');//删除预约时间模板
Yii::setAlias('@appointmentTimeTemplateList','/api/appointment/appointment-time-template-list');//删除预约时间模板
Yii::setAlias('@popAppointmentTimeTemplateList', '@RootPath/modules/make_appointment/views/appointment/popAppointmentTimeTemplateList');//预约时间模板弹框选择

//报到模块
//报到记录
Yii::setAlias('@reportRecordIndex', '/report/record/index');
Yii::setAlias('@reportRecordCreate', '/report/record/create');
Yii::setAlias('@reportRecordUpdate', '/report/record/update');
Yii::setAlias('@reportRecordDelete', '/report/record/delete');
Yii::setAlias('@reportRecordClose', '/report/record/close');
Yii::setAlias('@reportRecordView', '/report/record/view');
Yii::setAlias('@reportRecordConfirmReport', '/report/record/confirm-report');
Yii::setAlias('@reportRecordConfirmReportView', '@RootPath/modules/report/views/record/_confirmReport');//关闭预约弹窗
//今日预约待报到
Yii::setAlias('@reportRecordAppointment', '/report/record/appointment');

//患者库模块api 通过患者姓名
Yii::setAlias('@patientPatientGetPatients', '/api/patient/get-patients');
//患者库模块api 通过电话号码
Yii::setAlias('@apiPatientGetIphone', '/api/patient/get-iphone');
Yii::setAlias('@apiPatientGetPatientRecord', '/api/patient/get-patient-record');//获取患者库详情-就诊记录信息
Yii::setAlias('@apiPatientGetFollowRecord', '/api/patient/get-follow-record');//获取患者库详情-随访记录信息
Yii::setAlias('@apiPatientGetChargeRecord', '/api/patient/get-charge-record');//获取患者库详情-收费信息
Yii::setAlias('@apiPatientGetAppointmentRecord', '/api/patient/get-appointment-record');//获取患者库详情-预约记录信息
Yii::setAlias('@apiPatientGetCardList', '/api/patient/get-card-list');//获取患者库详情-会员卡信息
//分诊模块
Yii::setAlias('@triageTriageIndex', '/triage/triage/index');
Yii::setAlias('@triageTriageInfo', '/triage/triage/info');
Yii::setAlias('@triageTriageModal', '/triage/triage/modal');
Yii::setAlias('@triageTriageDoctor', '/triage/triage/doctor');
Yii::setAlias('@triageTriageRoom', '/triage/triage/room');
Yii::setAlias('@triageTriageChosedoctor', '/triage/triage/chosedoctor');
Yii::setAlias('@triageTriageChoseroom', '/triage/triage/choseroom');
Yii::setAlias('@signMeasurementTemplate', '@RootPath/modules/triage/views/triage/_signMeasurement');//分诊关键体征数据模板
Yii::setAlias('@triageNursingRecordCreate', '/triage/nursing-record/create');
Yii::setAlias('@triageNursingRecordUpdate', '/triage/nursing-record/update');
Yii::setAlias('@triageNursingRecordDelete', '/triage/nursing-record/delete');
Yii::setAlias('@triageNursingRecordCareModal', '/triage/nursing-record/care-modal');
Yii::setAlias('@nursingRecordCreate', 'nursing-record/create');
Yii::setAlias('@nursingRecordUpdate', 'nursing-record/update');
Yii::setAlias('@nursingRecordDelete', 'nursing-record/delete');
Yii::setAlias('@nursingRecordCareModal', 'nursing-record/care-modal');
//机构管理
Yii::setAlias('@spotModuleList', '/spot/module/list');//添加模块


//api接口地址

Yii::setAlias('@apiTriageGetContent', '/api/triage/get-template-content');
Yii::setAlias('@apiAppointmentIndex', '/api/appointment/index');
Yii::setAlias('@apiGetDoctorAppointment', '/api/appointment/get-doctor-appointment');//医生工作台预约接口
Yii::setAlias('@apiAppointmentAppointmentConfig', '/api/appointment/appointment-config');
Yii::setAlias('@apiAppointmentDoctorConfig', '/api/appointment/doctor-config');  //医生预约信息
Yii::setAlias('@apiSchedulingDoctorSchedule', '/api/scheduling/doctor-schedule');//医生单个排班信息
Yii::setAlias('@apiSchedulingIndex', '/api/scheduling/index');//排班列表

Yii::setAlias('@apiSchedulingScheduleConf', '/api/scheduling/schedule-conf');//班次列表
Yii::setAlias('@scheduleSchedulingIndex', '/schedule/schedule/index');//排班页面
Yii::setAlias('@scheduleSchedulingAddScheduling', '/schedule/schedule/add-scheduling');//添加排班
Yii::setAlias('@apiAppointmentWorkstationIndex', '/api/workstation/index');//前台工作台预约人数统计
Yii::setAlias('@apiAppointmentWorkstationConf', '/api/workstation/appointment-conf');//排班列表
Yii::setAlias('@apiAppointmentWorkstationGetOrdersData', '/api/workstation/get-orders-data');//排班列表
Yii::setAlias('@appointmentAdd', '/schedule/schedule/add-appointment');//添加排班
Yii::setAlias('@apiSchedulingDoctorSchedule', '/api/scheduling/doctor-schedule');//医生单个排班信息
Yii::setAlias('@apiSchedulingDoctorSchedule', '/api/scheduling/doctor-schedule');//医生单个排班信息
Yii::setAlias('@apiAppointmentMessage','/api/appointment/message');//点击查看详情
Yii::setAlias('@apiAppointmentDoctorTimeList','/api/appointment/doctor-time-list');//看到医生最大可预约详情列表
Yii::setAlias('@apiAppointmentTimeConfig', '/api/appointment/time-config'); //医生可预约时间段设置
Yii::setAlias('@apiAppointmentDoctorInfo', '/api/appointment/doctor-info');//二级科室下可预约医生api
Yii::setAlias('@apiAppointmentDoctorTime', '/api/appointment/doctor-time');//获取医生可预约时间api
Yii::setAlias('@apiAppointmentCreatbyDoctor','/api/appointment/creatby-doctor');//点击医生查看详情
Yii::setAlias('@apiAppointmentGetAppointmentDetail','/api/appointment/get-appointment-detail');//查看预约详情
Yii::setAlias('@apiAppointmentGetDoctorDepartment', '/api/appointment/get-doctor-department');//获取医生所属二级科室列表api

Yii::setAlias('@apiPayIndex', '/api/pay/index');//支付宝异步通知接口
Yii::setAlias('@apiWechatCallbackNotify', '/api/callback/notify');//微信异步通知接口
Yii::setAlias('@apiPayRechargeIndex', '/api/pay/recharge-index');//充值卡支付宝充值 异步通知接口
Yii::setAlias('@apiCallbackRechargeNotify', '/api/callback/recharge-notify');//充值卡微信充值-异步通知接口
Yii::setAlias('@apiCallbackPackageCardNotify', '/api/callback/package-card-notify');//套餐卡微信充值-异步通知接口
Yii::setAlias('@apiPayPackageCard', '/api/pay/package-card');//套餐卡支付宝充值-异步通知接口


Yii::setAlias('@apiTypeConfigGetTypeTime', '/api/type-config/get-type-time');//获取预约服务类型时长

Yii::setAlias('@apiChargeCheck', '/api/charge/check');//轮询检查订单支付状态api
Yii::setAlias('@apiRechargeCheck', '/api/recharge/check');//轮询检查充值卡充值订单支付状态api
Yii::setAlias('@apiChargePrintInfo', '/api/charge/print-info');//获取收费打印详情接口
Yii::setAlias('@apiChargeCreateDiscount', '/api/charge/create-discount');//待收费-新增单项折扣api
Yii::setAlias('@apiChargeGetCardDiscountPrice', '/api/charge/get-card-discount-price');//获取充值卡优惠金额
Yii::setAlias('@chargeCreateDiscountView', '@RootPath/modules/charge/views/index/_createDiscount');//待收费-新增单项折扣视图文件地址
Yii::setAlias('@apiChargeUpdateMaterial', '/api/charge/update-material');//修改物资管理信息api
Yii::setAlias('@apiChargeChargeRecordLog', '/api/charge/charge-record-log');//修改物资管理信息api
Yii::setAlias('@chargeUpdateMaterialView', '@RootPath/modules/charge/views/index/update-material');//待收费-修改其他收费项目视图文件地址
Yii::setAlias('@apiChargeCheckMaterialRecordNum', '/api/charge/check-material-record-num');//检查待收费列表是否有非药品，同时检查库存是否足够
Yii::setAlias('@apiChargePrintList', '/api/charge/charge-print-list');//收费打印弹窗
Yii::setAlias('@apiChargeLogPrintData', '/api/charge/charge-log-print-data'); //收费流水打印数据
Yii::setAlias('@apiChargePrintData', '/api/charge/charge-print-data'); //收费打印数据
Yii::setAlias('@apiChargePackageRecord', '/api/charge/package-record'); //医嘱套餐弹窗
Yii::setAlias('@apiChargePackageCardCheck', '/api/charge/package-card-check');//轮询检查会员-套餐卡订单支付状态api
Yii::setAlias('@apiRechargeGetCardDiscountPrice', '/api/recharge/get-card-discount-price');//获取套餐卡支付---充值卡优惠金额


Yii::setAlias('@apiRechargeGetPhoneCardCategory','/api/recharge/get-phone-card-category');//获取用户的关联卡种

Yii::setAlias('@apiUploadIndex', '/api/upload/index');//多文件上传api接口
Yii::setAlias('@apiUploadDelete', '/api/upload/delete');//多文件上传api接口
Yii::setAlias('@apiInspectUpload', '/api/upload/inspect-upload');//实验室检查上传api接口
Yii::setAlias('@apiInspectDelete', '/api/upload/inspect-delete');//实验室检查上传api接口
Yii::setAlias('@apiMedicalUpload', '/api/upload/medical-upload');//实验室检查上传api接口
Yii::setAlias('@apiMedicalDelete', '/api/upload/medical-delete');//实验室检查上传api接口
Yii::setAlias('@apiFollowUpload', '/api/upload/follow-upload');//随访上传api接口
Yii::setAlias('@apiFollowDelete', '/api/upload/follow-delete');//随访上传api接口
Yii::setAlias('@apiFollowMessageUpload', '/api/upload/follow-message-upload');//随访对话消息上传api接口
Yii::setAlias('@apiFollowMessageDelete', '/api/upload/follow-message-delete');//随访上传api接口
Yii::setAlias('@apiUploadBoardUpload', '/api/upload/board-upload');//公告上传api接口
Yii::setAlias('@apiMessageSetStatus','/api/message/set-status');//消息中心状态修改
Yii::setAlias('@apiMessageSetStatusAll','/api/message/set-status-all');//消息中心一键清除状态修改
Yii::setAlias('@apiMessageInspectWarn','/api/message/inspect-warn');//消息中心 检验医嘱危机值报警
Yii::setAlias('@apiMessageInspectWarnInfo','/api/message/inspect-warn-info');//消息中心 全局检验医嘱危机值报警
Yii::setAlias('@inspectIndexWarnView','@RootPath/modules/inspect/views/index/_warn');//消息中心 检验医嘱危机值报警视图
Yii::setAlias('@apiChargeGenerateCode', '/api/charge/generate-code');//生成微信和支付宝二维码接口
Yii::setAlias('@apiChargeNewGenerateCode', '/api/charge/new-generate-code');//生成微信和支付宝二维码接口
Yii::setAlias('@layoutWarnView','@RootPath/views/layouts/_warn');//消息中心 全局检验医嘱危机值报警
Yii::setAlias('@hisapiFollowMessage', '/his/follow/message');//hisapi获取随访消息列表
Yii::setAlias('@hisapiFollowChat', '/his/follow/chat');//hisapi 发起随访消息
Yii::setAlias('@hisapiFollowBindInfo', '/his/follow/bindInfo');//hisapi获取绑定信息

Yii::setAlias('@apiMedicineDescriptionItem', '/api/medicine-description/item');//用药指南预览api接口
Yii::setAlias('@apiMedicineDescriptionView', '/api/medicine-description/view');//查看对应使用指征详情接口
Yii::setAlias('@apiUploadBoardDelete', '/api/upload/board-delete');//公告附件删除api接口
Yii::setAlias('@apiGrowthView', '/api/growth/view');//查看用户生长曲线详情
Yii::setAlias('@apiAppointmentGetAppointmentType', '/api/appointment/get-appointment-type');//获取医生拥有的预约服务


Yii::setAlias('@apiOutpatientInspectApplication', '/api/outpatient/inspect-application');//实验室检查申请单弹窗
Yii::setAlias('@apiOutpatientInspectApplicationPrint', '/api/outpatient/inspect-application-print');//实验室检查申请单打印
Yii::setAlias('@apiOutpatientInspectReportPrint', '/api/outpatient/inspect-report-print');//实验室检查报告打印 @apiOutpatientInspectReportPrint
Yii::setAlias('@apiOutpatientRecipePrint', '/api/outpatient/recipe-print');//护士工作台处方打印
Yii::setAlias('@apiOutpatientCurePrint', '/api/outpatient/cure-print');//护士工作台治疗打印
Yii::setAlias('@apiOutpatientNursingRecordPrinkInfo', '/api/outpatient/nursing-prink-info');//护理记录打印接口
Yii::setAlias('@apiOutpatientCheckApplication', '/api/outpatient/check-application');//影像学检查申请单弹窗
Yii::setAlias('@apiOutpatientCureApplication', '/api/outpatient/cure-application');//治疗申请单弹窗
Yii::setAlias('@apiOutpatientCheckApplicationPrint', '/api/outpatient/check-application-print');//影像学检查申请单打印
Yii::setAlias('@apiGetOrthodonticsReturnvisitRecord', '/api/outpatient/get-orthodontics-returnvisit-record');//打印正畸复诊

Yii::setAlias('@apiDataGetChartData', '/api/data/get-chart-data');//获取核心指标数据
Yii::setAlias('@apiOutpatientDoctorRecipeList', '/api/outpatient/doctor-recipe-list');//获取医生开的处方
Yii::setAlias('@apioutpatientDoctorCheckInspectList', '/api/outpatient/doctor-check-inspect-list');//获取医生开的影像学检查以及实验室检查

Yii::setAlias('@apiOutpatientMarkType','/api/outpatient/outpatient-mark-type');//获取选择了的牙位图的类型

Yii::setAlias('@apiTagSearch', '/api/tag/search');//医嘱标签搜索弹窗

Yii::setAlias('@apiSearchPackageTemplateInspect', '/api/search/package-template-inspect');//医嘱套餐-实验室检查搜索
Yii::setAlias('@apiSearchPackageTemplateCheck', '/api/search/package-template-check');//医嘱套餐-影像学检查搜索
Yii::setAlias('@apiSearchPackageTemplateCure', '/api/search/package-template-cure');//医嘱套餐-治疗搜索
Yii::setAlias('@apiSearchPackageTemplateRecipe', '/api/search/package-template-recipe');//医嘱套餐-处方搜索
Yii::setAlias('@apiSearchClinicConsumables', '/api/search/clinic-consumables');//医嘱套餐-医疗耗材搜索
Yii::setAlias('@apiSearchClinicMaterial', '/api/search/clinic-material');//医嘱套餐-其他搜索


Yii::setAlias('@spotAdviceTagSearch', '@RootPath/modules/spot/views/advice-tag/_adviceTagSearch');//医嘱标签搜索弹窗视图


//医生门诊
Yii::setAlias('@outpatientTeethImgSelectView', '@RootPath/modules/outpatient/views/outpatient/_teethImgSelect');//选择要打印的牙位图的视图
Yii::setAlias('@outpatientOutpatientIndex', '/outpatient/outpatient/index');
Yii::setAlias('@outpatientOutpatientDiagnosis', '/outpatient/outpatient/diagnosis');//接诊
Yii::setAlias('@outpatientOutpatientUpdate', '/outpatient/outpatient/update');//门诊-病历
Yii::setAlias('@outpatientOutpatientCureRecord', '/outpatient/outpatient/cure-record');//治疗api接口
Yii::setAlias('@outpatientOutpatientCheckRecord', '/outpatient/outpatient/check-record');//辅助检查api接口
Yii::setAlias('@outpatientOutpatientInspectRecord', '/outpatient/outpatient/inspect-record');//辅助检查api接口
Yii::setAlias('@outpatientOutpatientRecipeRecord', '/outpatient/outpatient/recipe-record');//门诊-处方api接口
Yii::setAlias('@outpatientOutpatientMaterialRecord', '/outpatient/outpatient/material-record');//门诊-其他非药品
Yii::setAlias('@outpatientOutpatientConsumablesRecord', '/outpatient/outpatient/consumables-record');//门诊-其他非药品
Yii::setAlias('@outpatientOutpatientReportRecord', '/outpatient/outpatient/report-record');//报告
Yii::setAlias('@outpatientOutpatientEnd', '/outpatient/outpatient/end');//结束就诊
Yii::setAlias('@caseTemplateFormTemplate', '@RootPath/modules/spot/views/case-template/_form');// 影像学报告模板
Yii::setAlias('@outpatientOutpatientRecordPrinkInfo', '/outpatient/outpatient/record-prink-info');//打印病历
Yii::setAlias('@outpatientOutpatientCurePrinkInfo', '/outpatient/outpatient/cure-prink-info');//打印治疗
Yii::setAlias('@outpatientOutpatientTeethPrint', '/outpatient/outpatient/teeth-print');//口腔治疗
Yii::setAlias('@outpatientOutpatientRecipePrinkInfo', '/outpatient/outpatient/recipe-prink-info');//打印处方
Yii::setAlias('@outpatientCheckRecipeApplicationView','@RootPath/modules/outpatient/views/outpatient/_checkRecipeApplication');//处方打印选择弹框
Yii::setAlias('@outpatientOutpatientMaterialPrinkInfo', '/outpatient/outpatient/material-prink-info');//打印其他
Yii::setAlias('@outpatientOutpatientConsumablesPrinkInfo', '/api/outpatient/consumables-prink-info');//打印医疗耗材
Yii::setAlias('@outpatientOutpatientChildPrinkInfo', '/outpatient/outpatient/child-prink-info');//打印儿童体检
Yii::setAlias('@outpatientOutpatientReportInspectPrinkInfo', '/outpatient/outpatient/report-inspect-prink-info');//打印实验室检擦报告
Yii::setAlias('@outpatientOutpatientReportCheckPrinkInfo', '/outpatient/outpatient/report-check-prink-info');//打印影像学检擦报告
Yii::setAlias('@outpatientOutpatientCreateTemplate', '/outpatient/outpatient/create-template');//门诊-存为病例模版
Yii::setAlias('@outpatientOutpatientCreateCaseTemplate', '/outpatient/outpatient/case-create-template');//门诊-新增个人病例模板
Yii::setAlias('@outpatientOutpatientCaseTemplate', '/outpatient/outpatient/case-template');//管理病例模版
Yii::setAlias('@outpatientOutpatientViewCaseTemplate', '/outpatient/outpatient/case-view-template');//查看病例模版
Yii::setAlias('@outpatientOutpatientDeleteCaseTemplate', '/outpatient/outpatient/case-delete-template');//查看病例模版
Yii::setAlias('@outpatientOutpatientRecipeTypeCreate', '/outpatient/outpatient/recipe-type-create');//查看处方分类模版
Yii::setAlias('@outpatientOutpatientRecipeTypeUpdate', '/outpatient/outpatient/recipe-type-create');//修改处方分类模版
Yii::setAlias('@outpatientOutpatientRecipeTypeDelete', '/outpatient/outpatient/recipe-type-delete');//删除处方分类模版
Yii::setAlias('@outpatientOutpatientInspectTemplateIndex', '/outpatient/outpatient/inspect-template-index');//检验医嘱模板列表
Yii::setAlias('@outpatientOutpatientInspectTemplateCreate', '/outpatient/outpatient/inspect-template-create');//新增检验医嘱模板
Yii::setAlias('@outpatientOutpatientInspectTemplateUpdate', '/outpatient/outpatient/inspect-template-update');//修改检验医嘱模板
Yii::setAlias('@outpatientOutpatientInspectTemplateDelete', '/outpatient/outpatient/inspect-template-delete');//删除检验医嘱模板
Yii::setAlias('@outpatientOutpatientCureTemplateIndex', '/outpatient/outpatient/cure-template-index');//检验医嘱模板列表
Yii::setAlias('@outpatientOutpatientCureTemplateCreate', '/outpatient/outpatient/cure-template-create');//新增治疗医嘱模板
Yii::setAlias('@outpatientOutpatientCureTemplateUpdate', '/outpatient/outpatient/cure-template-update');//修改治疗医嘱模板
Yii::setAlias('@outpatientOutpatientCureTemplateDelete', '/outpatient/outpatient/cure-template-delete');//删除治疗医嘱模板

Yii::setAlias('@outpatientOutpatientCheckTemplateIndex', '/outpatient/outpatient/check-template-index');//检查医嘱模板列表
Yii::setAlias('@outpatientOutpatientCheckTemplateCreate', '/outpatient/outpatient/check-template-create');//新增检查医嘱模板
Yii::setAlias('@outpatientOutpatientCheckTemplateUpdate', '/outpatient/outpatient/check-template-update');//修改检查医嘱模板
Yii::setAlias('@outpatientOutpatientCheckTemplateDelete', '/outpatient/outpatient/check-template-delete');//删除检查医嘱模板

Yii::setAlias('@outpatientInspectApplicationView', '@RootPath/modules/outpatient/views/outpatient/_inspectApplication');//实验室检查项选择弹窗
Yii::setAlias('@outpatientCheckApplicationView', '@RootPath/modules/outpatient/views/outpatient/_checkApplication');//影像学检查项选择弹窗
Yii::setAlias('@outpatientCureApplicationView', '@RootPath/modules/outpatient/views/outpatient/_cureApplication');//治疗选择弹窗
Yii::setAlias('@outpatientOutpatientRecipetemplateCreate', '/outpatient/outpatient/recipetemplate-create');//新增处方模板
Yii::setAlias('@outpatientOutpatientRecipetemplateIndex', '/outpatient/outpatient/recipetemplate-index');//处方模板列表

Yii::setAlias('@apiOutpatientGetRecipeTemplateInfo', '/api/outpatient/get-recipe-template-info');//获取处方模板信息
Yii::setAlias('@apiOutpatientGetInspectTemplateInfo', '/api/outpatient/get-inspect-template-info');//获取检验模板信息
Yii::setAlias('@apiOutpatientGetCheckTemplateInfo', '/api/outpatient/get-check-template-info');//获取检查模板信息
Yii::setAlias('@apiOutpatientGetCureTemplateInfo', '/api/outpatient/get-cure-template-info');//获取检验模板信息
Yii::setAlias('@apiOutpatientGetCheckCodeList', '/api/outpatient/get-check-code-list');//获取诊断代码
Yii::setAlias('@apiOutpatientGetDoctorRecordData', '/api/outpatient/get-doctor-record-data');//获取病历信息
Yii::setAlias('@apiOutpatientNursePrintList', '/api/outpatient/nurse-print-list');//护士工作台获取儿保打印弹窗
Yii::setAlias('@apiOutpatientGetChildRecordData', '/api/outpatient/get-child-record-data');//护士工作台获取儿保记录数据
Yii::setAlias('@apiOutpatientGetChildInfoData', '/api/outpatient/get-child-info-data');//医生门诊获取儿保信息
Yii::setAlias('@apiOutpatientGetCheckList', '/api/outpatient/get-check-list');//获取影像学检查医嘱



Yii::setAlias('@outpatientOutpatientRecipeBack', '/outpatient/outpatient/recipe-back');//医生退药弹窗
Yii::setAlias('@outpatientOutpatientRecipeBackView','@RootPath/modules/outpatient/views/outpatient/_recipeBack');//医生退药弹窗

Yii::setAlias('@outpatientOutpatientInspectBack', '/outpatient/outpatient/inspect-back');//检验医嘱取消
Yii::setAlias('@outpatientOutpatientInspectBackView','@RootPath/modules/outpatient/views/outpatient/_inspectBack');//检验医嘱取消弹窗

Yii::setAlias('@bornInfoModal','/outpatient/outpatient/born-info-modal');//门诊用户信息卡片出生信息弹框
Yii::setAlias('@bornInfoModalView','@RootPath/modules/outpatient/views/outpatient/_bornInfoForm');//门诊用户信息卡片出生信息弹框
Yii::setAlias('@bornInfoModalContentView','@RootPath/modules/patient/views/index/_childbirthPregnancy');

Yii::setAlias('@outpatientOutpatientUpdatePatientInfo','/outpatient/outpatient/update-patient-info');

Yii::setAlias('@outpatientInspectRecordFormView', '@RootPath/modules/outpatient/views/outpatient/_inspectRecordForm');//实验室检查项tab视图
Yii::setAlias('@apiOutpatientGetInspectRecord', '/api/outpatient/get-inspect-record');//实验室检查项tab记录
Yii::setAlias('@apiOutpatientGetCheckRecord', '/api/outpatient/get-check-record');//影像学检查项tab记录
Yii::setAlias('@outpatientCheckRecordFormView', '@RootPath/modules/outpatient/views/outpatient/_checkRecordForm');//影像学检查项tab视图
Yii::setAlias('@apiOutpatientGetCureRecord', '/api/outpatient/get-cure-record');//治疗项tab记录
Yii::setAlias('@outpatientCureRecordFormView', '@RootPath/modules/outpatient/views/outpatient/_cureRecordForm');//治疗tab视图
Yii::setAlias('@apiOutpatientGetRecipeRecord', '/api/outpatient/get-recipe-record');//处方tab记录
Yii::setAlias('@outpatientRecipeRecordFormView', '@RootPath/modules/outpatient/views/outpatient/_recipeRecordForm');//处方tab视图
Yii::setAlias('@apiOutpatientGetConsumablesRecord', '/api/outpatient/get-consumables-record');//医疗耗材tab记录
Yii::setAlias('@outpatientConsumablesRecordFormView', '@RootPath/modules/outpatient/views/outpatient/_consumablesRecordForm');//医疗耗材tab视图
Yii::setAlias('@apiOutpatientGetMaterialRecord', '/api/outpatient/get-material-record');//其他tab记录
Yii::setAlias('@outpatientMaterialRecordFormView', '@RootPath/modules/outpatient/views/outpatient/_materialRecordForm');//其他tab视图

Yii::setAlias('@apiOutpatientGetReportRecord', '/api/outpatient/get-report-record');//报告tab记录
Yii::setAlias('@outpatientReportRecordFormView', '@RootPath/modules/outpatient/views/outpatient/_report');//报告tab视图


//检验医嘱配置
Yii::setAlias('@spotInspectIndex', '/spot/inspect/index');

//机构管理
//实验室检查
Yii::setAlias('@spotChargeManageInspectIndex', '/spot/charge-manage/inspect-index');//检验医嘱配置
Yii::setAlias('@spotChargeManageInspectUpdate', '/spot/charge-manage/inspect-update');
Yii::setAlias('@spotChargeManageInspectCreate', '/spot/charge-manage/inspect-create');
Yii::setAlias('@spotChargeManageInspectDelete', '/spot/charge-manage/inspect-delete');
Yii::setAlias('@spotChargeManageInspectUnion', '/spot/charge-manage/inspect-union');//检验医嘱 关联项目
Yii::setAlias('@spotChargeManageInspectItem', '/spot/charge-manage/inspect-item');//检验医嘱 关联项目信息
Yii::setAlias('@spotChargeManageInspectSaveUnion', '/spot/charge-manage/inspect-save-union');//检验医嘱 关联项目保存
//检查医嘱项目
Yii::setAlias('@spotChargeManageItemIndex', '/spot/charge-manage/item-index');//检验医嘱配置
Yii::setAlias('@spotChargeManageItemUpdate', '/spot/charge-manage/item-update');
Yii::setAlias('@spotChargeManageItemCreate', '/spot/charge-manage/item-create');
Yii::setAlias('@spotChargeManageItemDelete', '/spot/charge-manage/item-delete');
//影像学检验
Yii::setAlias('@spotChargeManageCheckIndex', '/spot/charge-manage/check-index');//检验医嘱配置
Yii::setAlias('@spotChargeManageCheckUpdate', '/spot/charge-manage/check-update');
Yii::setAlias('@spotChargeManageCheckCreate', '/spot/charge-manage/check-create');
Yii::setAlias('@spotChargeManageCheckDelete', '/spot/charge-manage/check-delete');
//治疗医嘱 
Yii::setAlias('@spotChargeManageCureIndex', '/spot/charge-manage/cure-index');//检验医嘱配置
Yii::setAlias('@spotChargeManageCureUpdate', '/spot/charge-manage/cure-update');
Yii::setAlias('@spotChargeManageCureCreate', '/spot/charge-manage/cure-create');
Yii::setAlias('@spotChargeManageCureDelete', '/spot/charge-manage/cure-delete');
//处方医嘱
Yii::setAlias('@spotChargeManageRecipeIndex', '/spot/charge-manage/recipe-index');//检验医嘱配置
Yii::setAlias('@spotChargeManageRecipeUpdate', '/spot/charge-manage/recipe-update');
Yii::setAlias('@spotChargeManageRecipeCreate', '/spot/charge-manage/recipe-create');
Yii::setAlias('@spotChargeManageRecipeDelete', '/spot/charge-manage/recipe-delete');
//医疗耗材
Yii::setAlias('@spotChargeManageConsumablesIndex', '/spot/charge-manage/consumables-index');//检验医嘱配置
Yii::setAlias('@spotChargeManageConsumablesUpdate', '/spot/charge-manage/consumables-update');
Yii::setAlias('@spotChargeManageConsumablesCreate', '/spot/charge-manage/consumables-create');
Yii::setAlias('@spotChargeManageConsumablesDelete', '/spot/charge-manage/consumables-delete');
//诊金管理
Yii::setAlias('@spotChargeManageMedicalFeeIndex', '/spot/charge-manage/medical-fee-index');//检验医嘱配置
Yii::setAlias('@spotChargeManageMedicalFeeUpdate', '/spot/charge-manage/medical-fee-update');
Yii::setAlias('@spotChargeManageMedicalFeeCreate', '/spot/charge-manage/medical-fee-create');
Yii::setAlias('@spotChargeManageMedicalFeeDelete', '/spot/charge-manage/medical-fee-delete');

Yii::setAlias('@spotCureListDelete', '/spot/cure-list/delete');
Yii::setAlias('@spotCheckListCreate', '/spot/check-list/create');
Yii::setAlias('@spotOrdersListDelete', '/spot/tag/delete-union');//删除标签与医嘱关联关系
Yii::setAlias('@spotTagDelete', '/spot/tag/delete');//删除标签
Yii::setAlias('@spotCaseTemplateCreate', '/spot/case-template/create');
Yii::setAlias('@spotDepartmentManageOnceDepartmentUpdate', '/spot/department-manage/once-department-update');//机构下一级科室的修改
Yii::setAlias('@spotDepartmentManageSecondDepartmentSubclass', '/spot/department-manage/second-department-subclass');//机构下展开二级科室显示
Yii::setAlias('@spotDepartmentManageSecondDepartmentUpdate', '/spot/department-manage/second-department-update');//机构下展开二级科室修改
Yii::setAlias('@spotDepartmentManageSecondDepartmentDelete', '/spot/department-manage/second-department-delete');//机构下展开二级科室停用禁用


//修改诊断代码状态
Yii::setAlias('@spotCheckCodeUpdateStatus', '/spot/check-code/update-status');

//收费
Yii::setAlias('@chargeIndexIndex', '/charge/index/index');
Yii::setAlias('@chargeIndexTradeLog', '/charge/index/trade-log');//交易流水
Yii::setAlias('@chargeIndexCreateMaterial', '/charge/index/create-material');
Yii::setAlias('@chargePrintListView', '@RootPath/modules/charge/views/index/print-list');
Yii::setAlias('@chargeIndexViewChargeRecordLog', '/charge/index/view-charge-record-log');

//诊室整理
Yii::setAlias('@roomIndexFinish', '/room/index/finish');

//药房管理
Yii::setAlias('@pharmacyIndexIndex', '/pharmacy/index/index');//药房列表
Yii::setAlias('@pharmacyIndexDispense', '/pharmacy/index/dispense');//发药
Yii::setAlias('@pharmacyIndexComplete', '/pharmacy/index/complete');//发药 已完成
Yii::setAlias('@pharmacyIndexInboundIndex', '/stock/index/pharmacy-inbound-index');//入库管理
Yii::setAlias('@pharmacyIndexInboundDrugs', '/stock/index/pharmacy-inbound-drugs');//入库管理按照药品
Yii::setAlias('@pharmacyIndexInboundCreate', '/stock/index/pharmacy-inbound-create');//新增入库
Yii::setAlias('@pharmacyIndexInboundUpdate', '/stock/index/pharmacy-inbound-update');//编辑入库
Yii::setAlias('@pharmacyIndexInboundDelete', '/stock/index/pharmacy-inbound-delete');//删除入库
Yii::setAlias('@pharmacyIndexInboundApply', '/stock/index/pharmacy-inbound-apply');//批准入库
Yii::setAlias('@pharmacyIndexInboundView', '/stock/index/pharmacy-inbound-view');//查看入库信息
Yii::setAlias('@pharmacyIndexStockInfo', '/stock/index/pharmacy-stock-info');//库存管理
Yii::setAlias('@pharmacyIndexOutboundIndex', '/stock/index/pharmacy-outbound-index');//出库管理
Yii::setAlias('@pharmacyIndexOutboundCreate', '/stock/index/pharmacy-outbound-create');//新增出库
Yii::setAlias('@pharmacyIndexOutboundUpdate', '/stock/index/pharmacy-outbound-update');//编辑出库
Yii::setAlias('@pharmacyIndexOutboundDelete', '/stock/index/pharmacy-outbound-delete');//删除出库
Yii::setAlias('@pharmacyIndexOutboundApply', '/stock/index/pharmacy-outbound-apply');//审核出库
Yii::setAlias('@pharmacyIndexOutboundView', '/stock/index/pharmacy-outbound-view');//查看出库信息
Yii::setAlias('@pharmacyIndexPrintPharmacyForm', '@RootPath/modules/pharmacy/views/index/_printPharmacyForm');//处方打印模板
Yii::setAlias('@pharmacyIndexPrebatch', '/pharmacy/index/prebatch');//待退药
Yii::setAlias('@pharmacyIndexEndbatch', '/pharmacy/index/endbatch');//已退药
Yii::setAlias('@pharmacyIndexPrintLabel', '/pharmacy/index/print-label');//打印处方标签弹窗
Yii::setAlias('@pharmacyIndexPreserve', '/pharmacy/index/preserve');//保存用药须知
Yii::setAlias('@pharmacyIndexStockExportData', '/stock/index/stock-export-data');//导出处方数据



//非药品管理
Yii::setAlias('@materialIndexInboundIndex', '/stock/index/material-inbound-index');//入库管理
Yii::setAlias('@materialIndexInboundCreate', '/stock/index/material-inbound-create');//新增入库
Yii::setAlias('@materialIndexInboundUpdate', '/stock/index/material-inbound-update');//编辑入库
Yii::setAlias('@materialIndexInboundDelete', '/stock/index/material-inbound-delete');//删除入库
Yii::setAlias('@materialIndexInboundApply', '/stock/index/material-inbound-apply');//批准入库
Yii::setAlias('@materialIndexInboundView', '/stock/index/material-inbound-view');//查看入库信息
Yii::setAlias('@materialIndexStockInfo', '/stock/index/material-stock-info');//库存管理
Yii::setAlias('@materialIndexOutboundCreate', '/stock/index/material-outbound-create');//新增出库
Yii::setAlias('@materialIndexOutboundUpdate', '/stock/index/material-outbound-update');//编辑出库
Yii::setAlias('@materialIndexOutboundDelete', '/stock/index/material-outbound-delete');//删除出库
Yii::setAlias('@materialIndexOutboundApply', '/stock/index/material-outbound-apply');//审核出库
Yii::setAlias('@materialIndexOutboundView', '/stock/index/material-outbound-view');//查看出库信息
Yii::setAlias('@materialIndexOutboundIndex', '/stock/index/material-outbound-index');//出库管理

//医疗耗材库存管理
Yii::setAlias('@stockIndexConsumablesInboundIndex', '/stock/index/consumables-inbound-index');//入库管理
Yii::setAlias('@stockIndexConsumablesInboundCreate', '/stock/index/consumables-inbound-create');//新增入库
Yii::setAlias('@stockIndexConsumablesInboundUpdate', '/stock/index/consumables-inbound-update');//编辑入库
Yii::setAlias('@stockIndexConsumablesInboundDelete', '/stock/index/consumables-inbound-delete');//删除入库
Yii::setAlias('@stockIndexConsumablesInboundApply', '/stock/index/consumables-inbound-apply');//批准入库
Yii::setAlias('@stockIndexConsumablesInboundView', '/stock/index/consumables-inbound-view');//查看入库信息
Yii::setAlias('@stockIndexConsumablesStockInfo', '/stock/index/consumables-stock-info');//库存管理
Yii::setAlias('@stockIndexConsumablesOutboundCreate', '/stock/index/consumables-outbound-create');//新增出库
Yii::setAlias('@stockIndexConsumablesOutboundUpdate', '/stock/index/consumables-outbound-update');//编辑出库
Yii::setAlias('@stockIndexConsumablesOutboundDelete', '/stock/index/consumables-outbound-delete');//删除出库
Yii::setAlias('@stockIndexConsumablesOutboundApply', '/stock/index/consumables-outbound-apply');//审核出库
Yii::setAlias('@stockIndexConsumablesOutboundView', '/stock/index/consumables-outbound-view');//查看出库信息
Yii::setAlias('@stockIndexConsumablesOutboundIndex', '/stock/index/consumables-outbound-index');//出库管理





//治疗 医嘱执行
Yii::setAlias('@cureIndexIndex', '/cure/index/index');// 待治疗 今日病人
Yii::setAlias('@cureIndexCure', '/cure/index/cure');// 待治疗 按钮
Yii::setAlias('@cureIndexUnderCure', '/cure/index/under-cure');// 治疗中 按钮
Yii::setAlias('@cureIndexComplete', '/cure/index/complete');// 治疗完成
Yii::setAlias('@cureIndexPrintCureForm', '@RootPath/modules/cure/views/index/_printCureForm');//治疗打印模板


//检查 医嘱执行
Yii::setAlias('@checkIndexIndex', '/check/index/index');// 待 今日病人
Yii::setAlias('@checkIndexCure', '/check/index/check');// 待治疗 按钮
Yii::setAlias('@checkIndexUnderCure', '/check/index/under-check');// 治疗中 按钮
Yii::setAlias('@checkIndexComplete', '/check/index/complete');// 治疗完成
Yii::setAlias('@checkIndexPrintCheckForm', '@RootPath/modules/check/views/index/_printCheckForm');// 影像学检查打印模板


//患者库
Yii::setAlias('@patientIndexView', '/patient/index/view');// 查看患者信息
Yii::setAlias('@patientIndexIndex', '/patient/index/index');//患者库列表
Yii::setAlias('@patientIndexCreate', '/patient/index/create');//新增患者
Yii::setAlias('@medicalRecord', '/patient/index/medical-record');//病历弹窗
Yii::setAlias('@patientIndexInformation', '/patient/index/information');//患者库－查看报告
Yii::setAlias('@inspectReport', '@RootPath/modules/inspect/views/index/_inspectForm');// 实验室报告模板
Yii::setAlias('@checkReport', '@RootPath/modules/check/views/index/_checkForm');// 影像学报告模板
Yii::setAlias('@patientIndexMakeup', '/patient/index/makeup');//ump补录信息
Yii::setAlias('@patientIndexInformationView', '@RootPath/modules/patient/views/index/_information');//就诊信息记录视图
Yii::setAlias('@patientIndexFollowView', '@RootPath/modules/patient/views/index/_follow');//随访视图

Yii::setAlias('@patientIndexCardView', '@RootPath/modules/patient/views/index/_card');//会员卡视图
Yii::setAlias('@patientIndexAppointmentView', '@RootPath/modules/patient/views/index/_appointment');//预约视图
Yii::setAlias('@patientIndexChargeInfoView', '@RootPath/modules/patient/views/index/_chargeInfo');//收费信息视图


//实验室检查
Yii::setAlias('@pharmacyIndexOnInspect', '/inspect/index/on-inspect');// 待检验 按钮
Yii::setAlias('@inspectIndexUnderInspect', '/inspect/index/under-inspect');// 实验中 按钮
Yii::setAlias('@inspectIndexComplete', '/inspect/index/complete');// 实验完成 按钮
Yii::setAlias('@inspectIndexIndex', '/inspect/index/index');// 实验室 今日病人
Yii::setAlias('@inspectIndexPrintInspectForm', '@RootPath/modules/inspect/views/index/_printInspectForm');// 实验室检查打印模板
Yii::setAlias('@inspectIndexCancelInspect', '/inspect/index/cancel-inspect');// 实验取消 按钮



//系统概况
Yii::setAlias('@overviewIndexSpotView', '/overview/index/spot-view');//系统概况－查看诊所详情
Yii::setAlias('@overviewIndexDetail', '/overview/index/detail');
Yii::setAlias('@overviewIndexIndex', '/overview/index/index');// 诊所列表
Yii::setAlias('@overviewIndexList', '/overview/index/list');// 机构加诊所列表

//前台工作台
Yii::setAlias('@receptionIndexReception','/reception/index/reception');//所有医生预约信息
Yii::setAlias('@receptionIndexIndex','/reception/index/index');//所有人员排班信息
Yii::setAlias('@apiSecondDepartmentSelect','/api/second-department/department-select');//所有人员新增

/**
 * 诊所设置
 */
Yii::setAlias('@spot_setPaymentConfigPay','/spot_set/payment-config/pay');//支付宝配置地址
Yii::setAlias('@spot_setPaymentConfigPayView','/spot_set/payment-config/pay-view');//支付宝配置地址
Yii::setAlias('@spot_setPaymentConfigUpdate','/spot_set/payment-config/update');//微信支付配置地址
Yii::setAlias('@spot_setPaymentConfigQrcodeView','/spot_set/payment-config/qrcode-view');//微信支付二维码浏览地址
Yii::setAlias('@spot_setPaymentConfigDelete', '/spot_set/payment-config/delete');//删除支付配置
Yii::setAlias('@spot_setBoardPreview', '/spot_set/board/preview');//查看公告板
Yii::setAlias('@spot_setUserAppointmentConfigIndex', '/spot_set/appointment-type/user-appointment-index');//医生-服务关联配置
Yii::setAlias('@spot_setAppointmentTimeConfig', '/spot_set/appointment-type/index');//预约时间参数设置
Yii::setAlias('@spot_setCustomAppointment', '/spot_set/appointment-type/custom-appointment-index');//预约时间参数设置
Yii::setAlias('@roomDoctorRoomConfig', '/spot_set/room/doctor-room-config');//医生常用诊室配置
Yii::setAlias('@spotSetScheduleIndex', '/spot_set/schedule/index');//预约时间参数设置
Yii::setAlias('@spot_setRecipeListClinicUpdate', '/spot_set/recipe-list-clinic/update');
Yii::setAlias('@spot_setDepartmentManageSecondDepartmentSubclass', '/spot_set/department-manage/second-department-subclass');//诊所下二级科室的展开
Yii::setAlias('@spot_setOutpatientPackageTemplatePackageTemplateIndex', '/spot_set/outpatient-package-template/package-template-index');

//会员卡
Yii::setAlias('@cardCenterCardInfoById','/api/card/card-id-list');//根据会员卡物理ID获取会员卡信息
Yii::setAlias('@cardCenterCardInfoBySn','/api/card/card-sn-list');//根据会员卡16位卡ID获取会员信息
Yii::setAlias('@cardCenterCardInfoBySnOne','/api/card/card-sn-info');//根据会员卡16位卡ID获取卡是实体卡或是虚拟卡并返回卡信息
Yii::setAlias('@cardCenterActivateCard','/api/card/activate-card');//激活会员卡
Yii::setAlias('@careCenterCardIndex', '/recharge/index/card-index');//会员卡列表
Yii::setAlias('@cardCenterSyncApp', '/his/card/disableCardService');//会员卡列表
Yii::setAlias('@rechargeIndexCreatePackageCard', '/recharge/index/create-package-card');//新增套餐卡

//用药指南
Yii::setAlias('@medicineItemViewPath', '@RootPath/modules/medicine/views/item/view');//查看用药指南详情视图
Yii::setAlias('@medicineIndexIndex', '/medicine/index/index');//用药指南列表
Yii::setAlias('@medicineIndexCreate', '/medicine/index/create');//新增用药指南
Yii::setAlias('@medicineIndexUpdate', '/medicine/index/update');//编辑用药指南
Yii::setAlias('@medicineIndexDelete', '/medicine/index/delete');//删除用药指南
Yii::setAlias('@medicineIndexDeleteItem', '/medicine/index/delete-item');//删除当前指征记录

//医嘱套餐弹窗
Yii::setAlias('@packageRecordView', '@RootPath/modules/charge/views/index/_packageRecord');//查看医嘱套餐弹窗


//生长曲线
Yii::setAlias('@growthIndexViewPath', '@RootPath/modules/growth/views/index/view');//生长曲线详情
//卡中心
Yii::setAlias('@spotCardManageIndex', '/spot/card-manage/index');//服务卡管理
Yii::setAlias('@spotCardManagePackageCardIndex', '/spot/card-manage/package-card-index');//套餐卡配置
Yii::setAlias('@spotCardManagePackageCardCreate', '/spot/card-manage/package-card-create');//新建套餐卡
Yii::setAlias('@spotCardManagePackageCardUpdate', '/spot/card-manage/package-card-update');//修改套餐卡服务类型
Yii::setAlias('@spotCardManagePackageCardUpdateStatus', '/spot/card-manage/package-card-update-status');//更改套餐卡服务类型状态
Yii::setAlias('@spotCardManagePackageCardServiceIndex', '/spot/card-manage/package-card-service-index');//套餐卡服务类型配置
Yii::setAlias('@spotCardManagePackageCardServiceCreate', '/spot/card-manage/package-card-service-create');//新增套餐卡服务类型
Yii::setAlias('@spotCardManagePackageCardServiceUpdate', '/spot/card-manage/package-card-service-update');//修改套餐卡服务类型
Yii::setAlias('@spotCardManagePackageCardServiceUpdateStatus', '/spot/card-manage/package-card-service-update-status');//更改套餐卡服务类型状态
Yii::setAlias('@spotCardManageGroupIndex', '/spot/card-manage/group-index');//卡组配置
Yii::setAlias('@spotCardManageGroupCreate', '/spot/card-manage/group-create');//卡组新增
Yii::setAlias('@spotCardManageGroupUpdate', '/spot/card-manage/group-update');//卡组修改
Yii::setAlias('@spotCardManageCategoryUpdate', '/spot/card-manage/category-update');
Yii::setAlias('@spotCardManageGroupView', '/spot/card-manage/group-view');//卡组详情
Yii::setAlias('@spotCardManageCategoryView', '/spot/card-manage/category-view');//卡组详情
Yii::setAlias('@spotCardManageSubclass', '/spot/card-manage/subclass');
Yii::setAlias('@spotCardManageCategoryCreate', '/spot/card-manage/category-create');//卡种新增/停止
Yii::setAlias('@spotCardManageCategoryOperation', '/spot/card-manage/category-operation');//卡种发行/停止

//卡中心 诊所配置
Yii::setAlias('@spot_setCardManageGroupIndex', '/spot_set/card-manage/group-index');//卡组配置
Yii::setAlias('@spot_setCardManageCategoryUpdate', '/spot_set/card-manage/category-update');//卡种修改
Yii::setAlias('@spot_setCardManageGroupView', '/spot_set/card-manage/group-view');//卡组详情
Yii::setAlias('@spot_setCardManageCategoryView', '/spot_set/card-manage/category-view');//卡种详情
Yii::setAlias('@spot_setCardManageSubclass', '/spot_set/card-manage/subclass');//卡种二级结构

Yii::setAlias('@cardRechargeCreate', '/recharge/index/create');//新建充值卡
Yii::setAlias('@cardRechargeUpdate', '/recharge/index/update');// 修改充值卡
Yii::setAlias('@cardRechargePreview', '/recharge/index/preview');// 充值卡详情
Yii::setAlias('@cardRechargeFlow', '/recharge/index/flow');// 充值卡流水
Yii::setAlias('@cardRechargeHistoryIndex', '/recharge/index/history-index');//  充值卡 卡种变更记录
Yii::setAlias('@cardRechargeHistoryCreate', '/recharge/index/history-create');//  充值卡 变更卡种
Yii::setAlias('@rechargeIndexIndex', '/recharge/index/index');//  充值卡首页
Yii::setAlias('@cardIndexIndex', '/recharge/index/card-index');//  会员卡首页
Yii::setAlias('@rechargeIndexRecharge', '/recharge/index/recharge');//  给卡片充值
Yii::setAlias('@cardTopTabViewPath', '@RootPath/specialModules/recharge/views/index/_topTab');//充值卡会员卡tab
Yii::setAlias('@rechargeIndexPackageCard', '/recharge/index/package-card');// 套餐卡列表
Yii::setAlias('@rechargeIndexPackageCardDelete', '/recharge/index/package-card-delete');// 套餐卡启用停用
Yii::setAlias('@rechargeIndexPackageCardView', '/recharge/index/package-card-view');// 套餐卡查看
Yii::setAlias('@rechargeIndexPackageCardFlow', '/recharge/index/package-card-flow');// 套餐卡流水
Yii::setAlias('@rechargeIndexCardCreate', '/recharge/index/card-create');//服务卡详情
Yii::setAlias('@rechargeIndexCardCheck', '/recharge/index/card-check');//验证服务卡


Yii::setAlias('@apiDepartmentManageSpotSecondDepartmrntSubclass', '/api/department-manage/spot-second-department-subclass');//  机构下二级科室展开
Yii::setAlias('@apiDepartmentManageSpotsetSecondDepartmrntSubclass', '/api/department-manage/spotset-second-department-subclass');//  诊所下二级科室展开
Yii::setAlias('@spotSecondDepartmentSubclassViewPath', '@RootPath/modules/spot/views/second-department/_subclass');//机构下二级科室展开页面
Yii::setAlias('@spot_setSecondDepartmentSubclassViewPath', '@RootPath/modules/spot_set/views/department-manage/_subclass');//诊所下二级科室展开页面

//随访
Yii::setAlias('@followIndexIndex', '/follow/index/index');//  随诊列表
Yii::setAlias('@followIndexCreate', '/follow/index/create');//  新增随诊
Yii::setAlias('@followIndexUpdate', '/follow/index/update');//  编辑随诊
Yii::setAlias('@followIndexExecute', '/follow/index/execute');//  执行随诊
Yii::setAlias('@followIndexCancel', '/follow/index/cancel');//  取消随诊
Yii::setAlias('@followIndexView', '/follow/index/view');//  查看
Yii::setAlias('@followIndexDialogMessage', '/follow/index/dialog-message');//  对话消息
Yii::setAlias('@followIndexSendMessage', '/follow/index/send-message');//  对话消息

//护士工作台
Yii::setAlias('@nurseIndexIndex', '/nurse/index/index');
Yii::setAlias('@nursePrintListView', '@RootPath/modules/nurse/views/index/print-list');//护士工作台打印弹窗VIEW
Yii::setAlias('@nurseIndexCreateRecord', '/nurse/index/create-record');//方便门诊

/**************************************诊所设置收费项管理************************************************/
//实验室检查
Yii::setAlias('@spot_setChargeManageInspectClinicIndex', '/spot_set/charge-manage/inspect-clinic-index');
Yii::setAlias('@spot_setChargeManageInspectClinicView', '/spot_set/charge-manage/inspect-clinic-view');
Yii::setAlias('@spot_setChargeManageInspectClinicCreate', '/spot_set/charge-manage/inspect-clinic-create');
Yii::setAlias('@spot_setChargeManageInspectClinicDelete', '/spot_set/charge-manage/inspect-clinic-delete');
Yii::setAlias('@spot_setChargeManageInspectClinicUnion', '/spot_set/charge-manage/inspect-clinic-union');

//影像学检查
Yii::setAlias('@spot_setChargeManageCheckListClinicIndex', '/spot_set/charge-manage/check-list-clinic-index');
Yii::setAlias('@spot_setChargeManageCheckListClinicView', '/spot_set/charge-manage/check-list-clinic-view');
Yii::setAlias('@spot_setChargeManageCheckListClinicCreate', '/spot_set/charge-manage/check-list-clinic-create');
Yii::setAlias('@spot_setChargeManageCheckListClinicUpdate', '/spot_set/charge-manage/check-list-clinic-update');
Yii::setAlias('@spot_setChargeManageCheckListClinicDelete', '/spot_set/charge-manage/check-list-clinic-delete');

//治疗
Yii::setAlias('@spot_setChargeManageCureClinicIndex', '/spot_set/charge-manage/cure-clinic-index');
Yii::setAlias('@spot_setChargeManageCureClinicView', '/spot_set/charge-manage/cure-clinic-view');
Yii::setAlias('@spot_setChargeManageCureClinicCreate', '/spot_set/charge-manage/cure-clinic-create');
Yii::setAlias('@spot_setChargeManageCureClinicUpdate', '/spot_set/charge-manage/cure-clinic-update');
Yii::setAlias('@spot_setChargeManageCureClinicDelete', '/spot_set/charge-manage/cure-clinic-delete');

//处方
Yii::setAlias('@spot_setChargeManageRecipeClinicIndex', '/spot_set/charge-manage/recipe-clinic-index');
Yii::setAlias('@spot_setChargeManageRecipeClinicView', '/spot_set/charge-manage/recipe-clinic-view');
Yii::setAlias('@spot_setChargeManageRecipeClinicCreate', '/spot_set/charge-manage/recipe-clinic-create');
Yii::setAlias('@spot_setChargeManageRecipeClinicUpdate', '/spot_set/charge-manage/recipe-clinic-update');
Yii::setAlias('@spot_setChargeManageRecipeClinicDelete', '/spot_set/charge-manage/recipe-clinic-delete');

//耗材
Yii::setAlias('@spot_setChargeManageConsumablesClinicIndex', '/spot_set/charge-manage/consumables-clinic-index');
Yii::setAlias('@spot_setChargeManageConsumablesClinicView', '/spot_set/charge-manage/consumables-clinic-view');
Yii::setAlias('@spot_setChargeManageConsumablesClinicCreate', '/spot_set/charge-manage/consumables-clinic-create');
Yii::setAlias('@spot_setChargeManageConsumablesClinicUpdate', '/spot_set/charge-manage/consumables-clinic-update');
Yii::setAlias('@spot_setChargeManageConsumablesClinicDelete', '/spot_set/charge-manage/consumables-clinic-delete');

//其他
Yii::setAlias('@spot_setChargeManageMaterialIndex', '/spot_set/charge-manage/material-index');
Yii::setAlias('@spot_setChargeManageMaterialView', '/spot_set/charge-manage/material-view');
Yii::setAlias('@spot_setChargeManageMaterialCreate', '/spot_set/charge-manage/material-create');
Yii::setAlias('@spot_setChargeManageMaterialUpdate', '/spot_set/charge-manage/material-update');
Yii::setAlias('@spot_setChargeManageMaterialDelete', '/spot_set/charge-manage/material-delete');

//诊金
Yii::setAlias('@spot_setChargeManageMedicalFeeClinicIndex', '/spot_set/charge-manage/medical-fee-clinic-index');
Yii::setAlias('@spot_setChargeManageMedicalFeeClinicView', '/spot_set/charge-manage/medical-fee-clinic-view');
Yii::setAlias('@spot_setChargeManageMedicalFeeClinicCreate', '/spot_set/charge-manage/medical-fee-clinic-create');
Yii::setAlias('@spot_setChargeManageMedicalFeeClinicDelete', '/spot_set/charge-manage/medical-fee-clinic-delete');

/**************************************诊所设置收费项管理************************************************/

//分页
Yii::setAlias('@firstPageLabel', '首页');
Yii::setAlias('@prevPageLabel', Html::tag('span','<',['class' => 'prevPage']));
Yii::setAlias('@nextPageLabel', Html::tag('span','>',['class' => 'nextPage']));
Yii::setAlias('@lastPageLabel', '末页');
//版本号

Yii::setAlias('@versionNumber', '20180118');






