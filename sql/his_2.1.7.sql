CREATE TABLE `gzh_cure_template` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `spot_id` int(10) unsigned NOT NULL COMMENT '诊所id',
  `name` varchar(64) NOT NULL DEFAULT '' COMMENT '模板名称',
  `template_type_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '模板分类id',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '2' COMMENT '分类类型:1-通用，2-个人',
  `user_id` int(10) unsigned NOT NULL COMMENT '创建人',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `spot_id` (`spot_id`) USING BTREE,
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='门诊-治疗模板配置表';

CREATE TABLE `gzh_cure_template_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `spot_id` int(10) unsigned NOT NULL COMMENT '诊所id',
  `clinic_cure_id` int(10) NOT NULL COMMENT '诊所治疗医嘱id',
  `cure_template_id` int(10) unsigned NOT NULL COMMENT '治疗模版配置id',
  `time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '次数',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '说明',
  `create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `spot_id` (`clinic_cure_id`,`spot_id`),
  KEY `cure_template_id` (`cure_template_id`),
  CONSTRAINT `gzh_cure_template_info_ibfk_1` FOREIGN KEY (`cure_template_id`) REFERENCES `gzh_cure_template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `gzh_cure_template_info_ibfk_2` FOREIGN KEY (`clinic_cure_id`) REFERENCES `gzh_curelist_clinic` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='门诊-治疗模板配置详情表';



-- 将其他配置的机构ID更改为诊所ID
ALTER TABLE gzh_material MODIFY `spot_id` int(10) unsigned NOT NULL COMMENT '诊所id';


ALTER TABLE `gzh_inspect_record` ADD `notice_status` TINYINT UNSIGNED NOT NULL DEFAULT '2' COMMENT '通知医生状态(1-已通知，2-未通知)' AFTER `inspect_finish_time`, ADD `notice_user_id` INT UNSIGNED NOT NULL DEFAULT '0' COMMENT '通知医生--操作人' AFTER `notice_status`, ADD `notice_time` INT UNSIGNED NOT NULL DEFAULT '0' COMMENT '点击通知医生-时间' AFTER `notice_user_id`, ADD `handle_status` TINYINT UNSIGNED NOT NULL DEFAULT '2' COMMENT '医生处理状态(1-已处理，2-未处理)' AFTER `notice_time`, ADD `handle_time` INT UNSIGNED NOT NULL DEFAULT '0' COMMENT '医生点击处理的时间' AFTER `handle_status`;



-- 历史数据处理
-- 将原有职称信息进行清除

UPDATE gzh_user SET position_title = 0;
-- 数据库 d_easyhin_his
-- 表的结构 `gzh_once_department`
-- 表的结构 `gzh_second_department`
ALTER TABLE `gzh_once_department` CHANGE `spot_id` `spot_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '机构id';
ALTER TABLE `gzh_second_department` CHANGE `spot_id` `spot_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '机构id';

-- 诊所下勾选的二级科室的表
CREATE TABLE `gzh_second_department_union` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `second_department_id` int(10) unsigned NOT NULL  COMMENT '二级科室id',
  `spot_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '诊所id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0',
  `update_time` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='诊所下二级科室关联表';

ALTER TABLE `gzh_spot_config` ADD `logo_img` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '诊所打印logo' AFTER `child_check`, ADD `spot_name` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '诊所名称' AFTER `logo_img`, ADD `pub_tel` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '通用电话' AFTER `spot_name`, ADD `label_tel` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '标签电话' AFTER `pub_tel`;

