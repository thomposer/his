--
-- 表的结构 `gzh_dental_first_template`
--

DROP TABLE IF EXISTS `gzh_dental_first_template`;
CREATE TABLE `gzh_dental_first_template` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `spot_id` int(10) UNSIGNED NOT NULL COMMENT '诊所ID',
  `name` varchar(125) NOT NULL DEFAULT '' COMMENT '模板名称',
  `user_id` int(10) UNSIGNED NOT NULL COMMENT '用户ID',
  `type` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '类型(1-通用，2-个人)',
  `chiefcomplaint` text NOT NULL COMMENT '主诉',
  `historypresent` text NOT NULL COMMENT '现病史',
  `pasthistory` text NOT NULL COMMENT '既往病史',
  `oral_check` text NOT NULL COMMENT '口腔检查',
  `auxiliary_check` text NOT NULL COMMENT '辅助检查',
  `diagnosis` text NOT NULL COMMENT '诊断',
  `cure_plan` text NOT NULL COMMENT '治疗方案',
  `cure` text NOT NULL COMMENT '治疗',
  `advice` text NOT NULL COMMENT '医嘱',
  `remark` text NOT NULL COMMENT '备注',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='诊所-口腔初诊模板';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gzh_dental_first_template`
--
ALTER TABLE `gzh_dental_first_template`
  ADD PRIMARY KEY (`id`),
  ADD KEY `spot_id` (`spot_id`,`type`,`user_id`,`name`) USING BTREE,
  ADD KEY `spot_id_2` (`spot_id`,`create_time`,`type`) USING BTREE;

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `gzh_dental_first_template`
--
ALTER TABLE `gzh_dental_first_template`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- 表的结构 `gzh_dental_returnvisit_template`
--

DROP TABLE IF EXISTS `gzh_dental_returnvisit_template`;
CREATE TABLE `gzh_dental_returnvisit_template` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `spot_id` int(10) UNSIGNED NOT NULL COMMENT '诊所ID',
  `name` varchar(125) NOT NULL DEFAULT '' COMMENT '模板名称',
  `user_id` int(10) UNSIGNED NOT NULL COMMENT '用户ID',
  `type` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '类型(1-通用，2-个人)',
  `returnvisit` text NOT NULL COMMENT '复诊',
  `oral_check` text NOT NULL COMMENT '口腔检查',
  `auxiliary_check` text NOT NULL COMMENT '辅助检查',
  `diagnosis` text NOT NULL COMMENT '诊断',
  `cure_plan` text NOT NULL COMMENT '治疗方案',
  `cure` text NOT NULL COMMENT '治疗',
  `advice` text NOT NULL COMMENT '医嘱',
  `remark` text NOT NULL COMMENT '备注',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='诊所-口腔初诊模板';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gzh_dental_returnvisit_template`
--
ALTER TABLE `gzh_dental_returnvisit_template`
  ADD PRIMARY KEY (`id`),
  ADD KEY `spot_id` (`spot_id`,`type`,`user_id`,`name`) USING BTREE,
  ADD KEY `spot_id_2` (`spot_id`,`create_time`,`type`) USING BTREE;

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `gzh_dental_returnvisit_template`
--
ALTER TABLE `gzh_dental_returnvisit_template`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID';
  
ALTER TABLE `gzh_recipelist`
MODIFY COLUMN `remark`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '备注' AFTER `skin_test`;




-- 数据库 d_easyhin_his
ALTER TABLE `gzh_spot_config`ADD COLUMN `logo_shape`  tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '诊所logo形状，1-正方形，2-长方形' AFTER `logo_img`;





























































































