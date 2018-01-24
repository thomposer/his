--医生服务类型价格配置
ALTER TABLE `gzh_user_appointment_config` ADD `price` decimal(10,2) unsigned DEFAULT NULL COMMENT '诊金' AFTER `spot_type_id`;

CREATE TABLE `gzh_user_price_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `spot_id` int(10) unsigned NOT NULL COMMENT '诊所id',
  `user_id` int(10) unsigned NOT NULL COMMENT '医生id',
  `price` decimal(10,2) unsigned DEFAULT NULL COMMENT '诊金',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '类型(1-方便门诊)',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `spot_id` (`spot_id`),
  KEY `spot_user_type` (`spot_id`,`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='医生-诊金';


ALTER TABLE `gzh_triage_info` CHANGE `pain_score` `pain_score` INT(11) UNSIGNED NULL DEFAULT NULL COMMENT '评估时间最大的疼痛评分';
ALTER TABLE `gzh_triage_info` CHANGE `fall_score` `fall_score` INT(11) UNSIGNED NULL DEFAULT NULL COMMENT '评分时间最大的跌倒评分';

ALTER TABLE `gzh_outpatient_package_template`
CHANGE COLUMN `medical_fee_id` `medical_fee_price`  decimal(10,2) unsigned  DEFAULT '0.00' COMMENT '诊疗费' AFTER `price`;


ALTER TABLE `gzh_outpatient_package_template`
DROP INDEX `medical_fee_clinic_id`;

-- 新增历史诊金字段
ALTER TABLE `gzh_patient_record` ADD `record_price` decimal(10,2) unsigned DEFAULT NULL COMMENT '历史诊金' AFTER `price`;

