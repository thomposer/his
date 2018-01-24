DROP TABLE IF EXISTS `gzh_doctor_room_union`;
CREATE TABLE `gzh_doctor_room_union` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `spot_id` int(10) UNSIGNED NOT NULL COMMENT '诊所ID',
  `doctor_id` int(10) UNSIGNED NOT NULL COMMENT '医生ID',
  `room_id` int(10) UNSIGNED NOT NULL COMMENT '诊室ID',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `spot_id` (`spot_id`),
  KEY `doctor_id` (`doctor_id`),
  KEY `room_id` (`room_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='医生诊室关联表';

-- 数据库 d_easyhin_his
-- 表的结构 `gzh_appointment`
--
ALTER TABLE gzh_appointment ADD `cancel_online` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否为线上取消预约（0-his系统取消，1-妈咪知道线上取消)' AFTER `appointment_cancel_operator`;


ALTER TABLE gzh_user_card DROP service_id;
ALTER TABLE gzh_user_card DROP service_left;


-- //初步诊断增加 确诊/疑诊
ALTER TABLE gzh_first_check ADD check_degree tinyint(4) unsigned NOT NULL DEFAULT 1 COMMENT '诊断程度[1/确诊 2/疑诊]';