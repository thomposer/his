CREATE TABLE `gzh_check_template` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='门诊-检查模板配置表';

CREATE TABLE `gzh_check_template_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `spot_id` int(10) unsigned NOT NULL COMMENT '诊所id',
  `clinic_check_id` int(10) unsigned NOT NULL COMMENT '诊所检查医嘱id',
  `check_template_id` int(10) unsigned NOT NULL COMMENT '检查模版配置id',
  `create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `spot_id` (`clinic_check_id`,`spot_id`),
  KEY `check_template_id` (`check_template_id`),
  CONSTRAINT `gzh_check_template_info_ibfk_1` FOREIGN KEY (`check_template_id`) REFERENCES `gzh_check_template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `gzh_check_template_info_ibfk_2` FOREIGN KEY (`clinic_check_id`) REFERENCES `gzh_checklist_clinic` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='门诊-检查模板配置详情表';
-- 疼痛/跌倒评分 
CREATE TABLE `gzh_child_assessment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `spot_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '诊所ID',
  `record_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '就诊流水ID',
  `score` int(10) unsigned DEFAULT NULL,
  `assesment_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评估时间',
  `remark` varchar(60) NOT NULL DEFAULT '' COMMENT '备注',
  `type` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '评估类型[1-疼痛 2-跌倒]',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_rid_sid` (`record_id`,`spot_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='患者疼痛/跌倒评估';


-- 同步复诊医嘱，备注
UPDATE gzh_dental_history SET advice = returnvisit_advice WHERE type = 2;
UPDATE gzh_dental_history SET remarks = returnvisit_remarks WHERE type = 2;
-- 删除复诊医嘱，备注字段
ALTER TABLE gzh_dental_history DROP COLUMN returnvisit_advice;
ALTER TABLE gzh_dental_history DROP COLUMN returnvisit_remarks;
/**
添加分诊信息表的备注字段
 */
ALTER TABLE `gzh_triage_info`
ADD COLUMN `remark`  varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '备注' AFTER `fall_score`;

