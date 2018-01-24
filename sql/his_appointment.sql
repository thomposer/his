ALTER TABLE `gzh_patient_record` CHANGE `type` `type` INT UNSIGNED NOT NULL DEFAULT '0' COMMENT '预约服务id';

ALTER TABLE `gzh_report` ADD `once_department_id` INT UNSIGNED NOT NULL DEFAULT '0' COMMENT '一级科室id' AFTER `record_id`, ADD `second_department_id` INT UNSIGNED NOT NULL DEFAULT '0' COMMENT '二级科室id' AFTER `once_department_id`, ADD `doctor_id` INT UNSIGNED NOT NULL DEFAULT '0' COMMENT '医生id' AFTER `second_department_id`;

ALTER TABLE `gzh_report` ADD `type` INT UNSIGNED NOT NULL DEFAULT '0' COMMENT '预约服务id' AFTER `doctor_id`;

ALTER TABLE `gzh_report` ADD `type_description` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '预约服务描述' AFTER `type`;


ALTER TABLE `gzh_spot_type` ADD COLUMN `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态[1正常 2停用]' AFTER `type`;
ALTER TABLE `gzh_spot_type` ADD COLUMN `is_delete` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态[0可删除 1不可删除]' AFTER `status`;


DROP TABLE IF EXISTS `gzh_user_appointment_config`;
CREATE TABLE `gzh_user_appointment_config` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '自增id',
  `spot_id` int(10) UNSIGNED NOT NULL COMMENT '诊所id',
  `user_id` int(10) UNSIGNED NOT NULL COMMENT '医生id',
  `spot_type_id` int(10) UNSIGNED NOT NULL COMMENT '预约类型id',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='医生-预约类型服务关联配置';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gzh_user_appointment_config`
--
ALTER TABLE `gzh_user_appointment_config`
  ADD PRIMARY KEY (`id`),
  ADD KEY `spot_id` (`spot_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `spot_type_id` (`spot_type_id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `gzh_user_appointment_config`
--
ALTER TABLE `gzh_user_appointment_config`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增id';
--
-- 限制导出的表
--

--
-- 限制表 `gzh_user_appointment_config`
--
ALTER TABLE `gzh_user_appointment_config`
  ADD CONSTRAINT `gzh_user_appointment_config_ibfk_1` FOREIGN KEY (`spot_type_id`) REFERENCES `gzh_spot_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;



ALTER TABLE `gzh_user_spot` ADD `create_time` INT UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间' AFTER `status`, ADD `update_time` INT UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间' AFTER `create_time`;
