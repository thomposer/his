
-- 卡种变更历史增加诊所ID
ALTER TABLE t_category_history ADD `f_spot_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '诊所ID' AFTER f_change_reason;

CREATE TABLE `t_card_discount_clinic` (
	`id` INT (10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`card_discount_id` INT (10) UNSIGNED NOT NULL COMMENT '机构配置的标签ID',
	`parent_spot_id` INT (10) UNSIGNED NOT NULL COMMENT '机构id',
	`spot_id` INT (10) UNSIGNED NOT NULL COMMENT '诊所id',
	`recharge_category_id` INT (10) UNSIGNED NOT NULL COMMENT '卡种id',
	`tag_id` INT (10) UNSIGNED NOT NULL COMMENT '标签id',
	`discount` DECIMAL (10, 2) UNSIGNED NOT NULL DEFAULT '100.00' COMMENT '折扣',
	`create_time` INT (10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
	`update_time` INT (10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间',
	PRIMARY KEY (`id`),
	KEY `recharge_category_id` (`recharge_category_id`),
	KEY `tag_id` (`tag_id`),
	KEY `spot_id` (`spot_id`),
	CONSTRAINT `t_card_discount_ibfk_2` FOREIGN KEY (`recharge_category_id`) REFERENCES `t_card_recharge_category` (`f_physical_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = INNODB DEFAULT CHARSET = utf8 COMMENT = '诊所卡种关联服务折扣表';


-- 修改标签状态的默认值
ALTER TABLE gzh_tag MODIFY `status` tinyint(4) unsigned NOT NULL DEFAULT 1 COMMENT '标签状态[ 1-正常,2-停用]';




-- 将之前配置的标本种类历史数据清空

UPDATE `gzh_inspect_clinic` SET `specimen_type` = '0';


ALTER TABLE `gzh_checklist_clinic` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE gzh_cure_template_info DROP FOREIGN KEY gzh_cure_template_info_ibfk_2;
ALTER TABLE `gzh_cure_template_info` CHANGE `clinic_cure_id` `clinic_cure_id` INT(10) UNSIGNED NOT NULL COMMENT '诊所治疗医嘱id';
ALTER TABLE `gzh_curelist_clinic` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT;


--
-- Database: `d_easyhin_his`
--

-- --------------------------------------------------------

--
-- 表的结构 `gzh_outpatient_package_check`
--

DROP TABLE IF EXISTS `gzh_outpatient_package_check`;
CREATE TABLE `gzh_outpatient_package_check` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'id',
  `spot_id` int(10) UNSIGNED NOT NULL COMMENT '诊所id',
  `outpatient_package_id` int(10) UNSIGNED NOT NULL COMMENT '医嘱模板套餐公共信息表id',
  `check_id` int(10) UNSIGNED NOT NULL COMMENT '诊所的检查医嘱列表id',
  `create_time` int(10) UNSIGNED NOT NULL,
  `update_time` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `gzh_outpatient_package_cure`
--

DROP TABLE IF EXISTS `gzh_outpatient_package_cure`;
CREATE TABLE `gzh_outpatient_package_cure` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `spot_id` int(10) UNSIGNED NOT NULL COMMENT '诊所id',
  `outpatient_package_id` int(10) UNSIGNED NOT NULL COMMENT '医嘱模板套餐公共信息表id',
  `cure_id` int(10) UNSIGNED NOT NULL COMMENT '诊所的治疗医嘱列表id',
  `time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '次数',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '说明',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='医嘱模板套餐--治疗表';

-- --------------------------------------------------------

--
-- 表的结构 `gzh_outpatient_package_inspect`
--

DROP TABLE IF EXISTS `gzh_outpatient_package_inspect`;
CREATE TABLE `gzh_outpatient_package_inspect` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `spot_id` int(11) NOT NULL COMMENT '诊所id',
  `outpatient_package_id` int(10) UNSIGNED NOT NULL COMMENT '医嘱模板套餐公共信息表id',
  `inspect_id` int(10) UNSIGNED NOT NULL COMMENT '诊所检验医嘱的id',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='医嘱模板套餐--实验室检查表';

-- --------------------------------------------------------

--
-- 表的结构 `gzh_outpatient_package_recipe`
--

DROP TABLE IF EXISTS `gzh_outpatient_package_recipe`;
CREATE TABLE `gzh_outpatient_package_recipe` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `spot_id` int(10) UNSIGNED NOT NULL COMMENT '机构id',
  `clinic_recipe_id` int(10) NOT NULL COMMENT '诊所处方配置id',
  `outpatient_package_id` int(10) UNSIGNED NOT NULL COMMENT '医嘱模板套餐公共信息表id',
  `dose` varchar(32) NOT NULL DEFAULT '' COMMENT '剂量',
  `dose_unit` int(10) UNSIGNED NOT NULL COMMENT '剂量单位',
  `used` tinyint(3) UNSIGNED NOT NULL COMMENT '用法',
  `frequency` tinyint(3) UNSIGNED NOT NULL COMMENT '用药频次',
  `day` int(10) UNSIGNED NOT NULL COMMENT '天数',
  `num` int(10) UNSIGNED NOT NULL COMMENT '数量',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '说明',
  `type` tinyint(3) UNSIGNED NOT NULL COMMENT '类型(1-本院,2-外购)',
  `skin_test_status` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否需要皮试(0-没，1-是,2-否)',
  `outpatient_package_cure_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '关联医嘱模板套餐--治疗表id(皮试)',
  `curelist_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '机构治疗配置表id(皮试)',
  `create_time` int(10) UNSIGNED NOT NULL COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='医嘱模板套餐-处方配置详情表';

-- --------------------------------------------------------

--
-- 表的结构 `gzh_outpatient_package_template`
--

DROP TABLE IF EXISTS `gzh_outpatient_package_template`;
CREATE TABLE `gzh_outpatient_package_template` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `spot_id` int(10) UNSIGNED NOT NULL COMMENT '诊所id',
  `name` varchar(64) NOT NULL DEFAULT '' COMMENT '模板名称',
  `type` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '类型(1-套餐，2-模板)',
  `price` decimal(10,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '价格',
  `medical_fee_clinic_id` INT UNSIGNED NOT NULL COMMENT '诊疗费配置id',
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建人',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='医嘱模板套餐公共信息表';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gzh_outpatient_package_check`
--
ALTER TABLE `gzh_outpatient_package_check`
  ADD PRIMARY KEY (`id`),
  ADD KEY `check_id` (`check_id`),
  ADD KEY `outpatient_package_id` (`outpatient_package_id`);

--
-- Indexes for table `gzh_outpatient_package_cure`
--
ALTER TABLE `gzh_outpatient_package_cure`
  ADD PRIMARY KEY (`id`),
  ADD KEY `spot_id` (`spot_id`),
  ADD KEY `cure_id` (`cure_id`),
  ADD KEY `outpatient_package_id` (`outpatient_package_id`);

--
-- Indexes for table `gzh_outpatient_package_inspect`
--
ALTER TABLE `gzh_outpatient_package_inspect`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inspect_id` (`inspect_id`),
  ADD KEY `outpatient_package_id` (`outpatient_package_id`);

--
-- Indexes for table `gzh_outpatient_package_recipe`
--
ALTER TABLE `gzh_outpatient_package_recipe`
  ADD PRIMARY KEY (`id`),
  ADD KEY `spot_id` (`spot_id`,`clinic_recipe_id`),
  ADD KEY `outpatient_package_id` (`outpatient_package_id`),
  ADD KEY `gzh_outpatient_package_recipe_ibfk_2` (`clinic_recipe_id`);

--
-- Indexes for table `gzh_outpatient_package_template`
--
ALTER TABLE `gzh_outpatient_package_template`
  ADD PRIMARY KEY (`id`),
  ADD KEY `spot_id` (`spot_id`);

ALTER TABLE `gzh_outpatient_package_template` ADD INDEX(`medical_fee_clinic_id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `gzh_outpatient_package_check`
--
ALTER TABLE `gzh_outpatient_package_check`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id';
--
-- 使用表AUTO_INCREMENT `gzh_outpatient_package_cure`
--
ALTER TABLE `gzh_outpatient_package_cure`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID';
--
-- 使用表AUTO_INCREMENT `gzh_outpatient_package_inspect`
--
ALTER TABLE `gzh_outpatient_package_inspect`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID';
--
-- 使用表AUTO_INCREMENT `gzh_outpatient_package_recipe`
--
ALTER TABLE `gzh_outpatient_package_recipe`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID';
--
-- 使用表AUTO_INCREMENT `gzh_outpatient_package_template`
--
ALTER TABLE `gzh_outpatient_package_template`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID';
--
-- 限制导出的表
--

--
-- 限制表 `gzh_outpatient_package_check`
--
ALTER TABLE `gzh_outpatient_package_check`
  ADD CONSTRAINT `gzh_outpatient_package_check_ibfk_1` FOREIGN KEY (`check_id`) REFERENCES `gzh_checklist_clinic` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `gzh_outpatient_package_check_ibfk_2` FOREIGN KEY (`outpatient_package_id`) REFERENCES `gzh_outpatient_package_template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 限制表 `gzh_outpatient_package_cure`
--
ALTER TABLE `gzh_outpatient_package_cure`
  ADD CONSTRAINT `gzh_outpatient_package_cure_ibfk_1` FOREIGN KEY (`cure_id`) REFERENCES `gzh_curelist_clinic` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `gzh_outpatient_package_cure_ibfk_2` FOREIGN KEY (`outpatient_package_id`) REFERENCES `gzh_outpatient_package_template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 限制表 `gzh_outpatient_package_inspect`
--
ALTER TABLE `gzh_outpatient_package_inspect`
  ADD CONSTRAINT `gzh_outpatient_package_inspect_ibfk_1` FOREIGN KEY (`inspect_id`) REFERENCES `gzh_inspect_clinic` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `gzh_outpatient_package_inspect_ibfk_2` FOREIGN KEY (`outpatient_package_id`) REFERENCES `gzh_outpatient_package_template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 限制表 `gzh_outpatient_package_recipe`
--
ALTER TABLE `gzh_outpatient_package_recipe`
  ADD CONSTRAINT `gzh_outpatient_package_recipe_ibfk_1` FOREIGN KEY (`outpatient_package_id`) REFERENCES `gzh_outpatient_package_template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `gzh_outpatient_package_recipe_ibfk_2` FOREIGN KEY (`clinic_recipe_id`) REFERENCES `gzh_recipelist_clinic` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;


DROP TABLE IF EXISTS `gzh_package_record`;
CREATE TABLE `gzh_package_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `record_id` int(10) unsigned NOT NULL COMMENT '流水id',
  `template_id` int(10) unsigned NOT NULL COMMENT '医嘱模板套餐公共信息表id',
  `spot_id` int(10) unsigned NOT NULL COMMENT '诊所id',
  `name` varchar(64) NOT NULL DEFAULT '' COMMENT '模板名称',
  `price` decimal(10,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '价格',
  `remarks` varchar(255) NOT NULL DEFAULT '' COMMENT '诊金说明',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `spot_record_union_key` (`spot_id`,`record_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='门诊-套餐信息表';

ALTER TABLE `gzh_patient_record` ADD `is_package` tinyint(3) unsigned NOT NULL DEFAULT '2' COMMENT '是否开了医嘱模板套餐（1-是，2-否）' AFTER `delete_status`;

ALTER TABLE `gzh_inspect_record` ADD `package_record_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '（0-默认 其他-医嘱模板套餐id）' AFTER `description`;
ALTER TABLE `gzh_check_record` ADD `package_record_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '（0-默认 其他-医嘱模板套餐id）' AFTER `check_finish_time`;
ALTER TABLE `gzh_cure_record` ADD `package_record_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '（0-默认 其他-医嘱模板套餐id）' AFTER `type`;
ALTER TABLE `gzh_recipe_record` ADD `package_record_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '（0-默认 其他-医嘱模板套餐id）' AFTER `high_risk`;




ALTER TABLE `gzh_charge_record_log` ADD `package_price` decimal(10,2) unsigned DEFAULT NULL COMMENT '医嘱套餐费用' AFTER `consumables_discount_price` ;
ALTER TABLE `gzh_charge_record_log` ADD `package_discount_price` decimal(10,2) unsigned DEFAULT NULL COMMENT '医嘱套餐优惠金额' AFTER `package_price` ;
