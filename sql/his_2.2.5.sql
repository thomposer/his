-- 修改服务类型的备注
ALTER table  `gzh_organization_type` MODIFY `record_type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '病历类型(1-非专科,2-儿保,4-口腔初诊,5-口腔复诊,6-正畸初诊,7-正畸复诊)';
ALTER TABLE `gzh_dental_returnvisit_template` COMMENT = '诊所-口腔复诊模板' ROW_FORMAT = COMPACT;
DROP TABLE IF EXISTS `gzh_orthodontics_returnvisit_record`;
CREATE TABLE `gzh_orthodontics_returnvisit_record` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `spot_id` int(10) UNSIGNED NOT NULL COMMENT '诊所id',
  `record_id` int(10) unsigned NOT NULL COMMENT '就诊流水id',
  `returnvisit` text NOT NULL COMMENT '复诊',
  `check` text NOT NULL COMMENT '影像学检查',
  `treatment` text NOT NULL COMMENT '处理',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `record_id` (`spot_id`,`record_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='正畸复诊病历';

--
-- 表的结构 `gzh_orthodontics_first_record`
--

DROP TABLE IF EXISTS `gzh_orthodontics_first_record`;
CREATE TABLE `gzh_orthodontics_first_record` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `spot_id` int(10) UNSIGNED NOT NULL COMMENT '诊所ID',
  `record_id` int(10) UNSIGNED NOT NULL COMMENT '就诊记录ID',
  `chiefcomplaint` text NOT NULL COMMENT '主诉',
  `motivation` text NOT NULL COMMENT '动机',
  `historypresent` text NOT NULL COMMENT '现病史',
  `all_past_history` text NOT NULL COMMENT '全身既往史',
  `pastdraghistory` text NOT NULL COMMENT '过去用药史',
  `retention` varchar(125) NOT NULL DEFAULT '' COMMENT '滞留',
  `early_loss` varchar(125) NOT NULL DEFAULT '' COMMENT '早失',
  `bad_habits` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '不良习惯(1-无，2-有)',
  `bad_habits_abnormal` varchar(64) NOT NULL DEFAULT '' COMMENT '有不良习惯(1-吮指,2-咬唇,3-咬物,4-吐舌,5-吸颊,6-口呼吸,7-不良吞咽,8-其他)',
  `bad_habits_abnormal_other` varchar(64) NOT NULL DEFAULT '' COMMENT '有不良习惯--其他备注',
  `traumahistory` text NOT NULL COMMENT '外伤史',
  `feed` varchar(32) NOT NULL DEFAULT '' COMMENT '喂养方式(1-母乳，2-人工，3-混合)',
  `immediate` text NOT NULL COMMENT '直系三代亲属',
  `oral_function` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '口腔功能(1-无异常，2-有异常)',
  `oral_function_abnormal` varchar(64) NOT NULL DEFAULT '' COMMENT '口腔功能有异常(1-口呼吸，2-偏侧咀嚼（左），3-偏侧咀嚼（右）,4-不良吞咽,5-发音不清)',
  `mandibular_movement` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '下颌运动（1-正常，2-异常）',
  `mandibular_movement_abnormal` varchar(64) NOT NULL DEFAULT '' COMMENT '下颌运动异常',
  `mouth_open` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '张口度(1-正常，2-异常)',
  `mouth_open_abnormal` varchar(64) NOT NULL DEFAULT '' COMMENT '张口度异常备注',
  `left_temporomandibular_joint` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '左颞下颌关节（1-正常，2-异常）',
  `left_temporomandibular_joint_abnormal` varchar(64) NOT NULL DEFAULT '' COMMENT '左颞下颌关节异常(1-弹响,2-疼痛,3-其他)',
  `left_temporomandibular_joint_abnormal_other` varchar(64) NOT NULL DEFAULT '' COMMENT '左颞下颌关节异常-其他备注',
  `right_temporomandibular_joint` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '右颞下颌关节（1-正常，2-异常）',
  `right_temporomandibular_joint_abnormal` varchar(64) NOT NULL DEFAULT '' COMMENT '右颞下颌关节异常(1-弹响,2-疼痛,3-其他)',
  `right_temporomandibular_joint_abnormal_other` varchar(64) NOT NULL DEFAULT '' COMMENT '右颞下颌关节异常-其他备注',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='口腔正畸初诊病历';

-- --------------------------------------------------------

--
-- 表的结构 `gzh_orthodontics_first_record_examination`
--

DROP TABLE IF EXISTS `gzh_orthodontics_first_record_examination`;
CREATE TABLE `gzh_orthodontics_first_record_examination` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `spot_id` int(10) UNSIGNED NOT NULL COMMENT '诊所id',
  `record_id` int(10) UNSIGNED NOT NULL COMMENT '就诊流水ID',
  `hygiene` text NOT NULL COMMENT '口腔卫生',
  `periodontal` text NOT NULL COMMENT '牙周状况',
  `ulcer` text NOT NULL COMMENT '溃疡',
  `gums` text NOT NULL COMMENT '牙龈',
  `tonsil` text NOT NULL COMMENT '扁桃体',
  `frenum` text NOT NULL COMMENT '舌系带',
  `soft_palate` text NOT NULL COMMENT '软腭',
  `lip` text NOT NULL COMMENT '唇系带',
  `tongue` text NOT NULL COMMENT '舌体',
  `dentition` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '牙列式(1-恒牙列，2-乳牙列，3-混合牙列)',
  `arch_form` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '牙弓形态(1-尖圆形，2-卵圆形，3-方圆形)',
  `arch_coordination` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '牙弓协调性(1-协调，2-不协调)',
  `overbite_anterior_teeth` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '覆合前牙(1-正常，2-异常)',
  `overbite_anterior_teeth_abnormal` varchar(32) NOT NULL DEFAULT '' COMMENT '覆合前牙异常(1-开合，2-对刃，3-其他)',
  `overbite_anterior_teeth_other` varchar(64) NOT NULL DEFAULT '' COMMENT '覆合前牙异常-其他备注',
  `overbite_posterior_teeth` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '覆合后牙(1-正常，2-异常)',
  `overbite_posterior_teeth_abnormal` varchar(32) NOT NULL DEFAULT '' COMMENT '覆合后牙异常(1-开合，2-对刃，3-其他)',
  `overbite_posterior_teeth_other` varchar(64) NOT NULL DEFAULT '' COMMENT '覆合后牙异常-其他备注',
  `cover_anterior_teeth` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '覆盖前牙(1-正常，2-异常)',
  `cover_anterior_teeth_abnormal` varchar(32) NOT NULL DEFAULT '' COMMENT '覆盖前牙异常(1-深覆盖，2-反合，3-对刃)',
  `cover_posterior_teeth` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '覆盖后牙(1-正常，2-异常)',
  `cover_posterior_teeth_abnormal` varchar(32) NOT NULL DEFAULT '' COMMENT '覆盖后牙异常(1-反合，2-锁合，3-反锁合)',
  `left_canine` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '左侧尖牙(1-Ⅰ°,2- Ⅱ°,3-Ⅲ°,4-尖对尖)',
  `right_canine` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '右侧尖牙(1-Ⅰ°,2- Ⅱ°,3-Ⅲ°，4-尖对尖)',
  `left_molar` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '左侧磨牙(1-Ⅰ°,2- Ⅱ°,3-Ⅲ°,4-尖对尖)',
  `right_molar` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '右侧磨牙(1-Ⅰ°,2- Ⅱ°,3-Ⅲ°,4-尖对尖)',
  `midline_teeth` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '牙中线(1-左偏,2-右偏)',
  `midline_teeth_value` varchar(32) NOT NULL DEFAULT '' COMMENT '牙中线数值',
  `midline` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '面中线(1-左偏,2-右偏)',
  `midline_value` varchar(32) NOT NULL DEFAULT '' COMMENT '面中线数值',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='口腔正畸初诊病历关联口腔组织检查';

-- --------------------------------------------------------

--
-- 表的结构 `gzh_orthodontics_first_record_features`
--

DROP TABLE IF EXISTS `gzh_orthodontics_first_record_features`;
CREATE TABLE `gzh_orthodontics_first_record_features` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `spot_id` int(10) UNSIGNED NOT NULL COMMENT '诊所ID',
  `record_id` int(10) UNSIGNED NOT NULL COMMENT '就诊记录ID',
  `dental_age` varchar(32) NOT NULL DEFAULT '' COMMENT '牙龄',
  `bone_age` varchar(32) NOT NULL DEFAULT '' COMMENT '骨龄',
  `second_features` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '第二特征(1-无，2-有)',
  `frontal_type` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '正面型(1-短，2-均，3-长)',
  `symmetry` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '对称性(1-对称,2-左侧丰满,3-右侧丰满)',
  `abit` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '唇齿位(1-正常,2-闭唇不全,3-右侧丰满)',
  `face` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '脸型(1-方，2-圆，3-长)',
  `smile` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '微笑(1-正常，2-露龈，3-其他)',
  `smile_other` varchar(64) NOT NULL DEFAULT '' COMMENT '微笑-其他备注',
  `upper_lip` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '上唇(1-短，2-肥厚，3-菲薄，4-外翻)',
  `lower_lip` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '下唇(1-短，2-肥厚，3-菲薄，4-外翻)  ',
  `side` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '侧面型(1-凹面型，2-直面型，3-凸面型)',
  `nasolabial_angle` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '鼻唇角(1-大，2-小，3-正常)',
  `chin_lip` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '颏唇沟(1-深，2-浅，3-正常)',
  `mandibular_angle` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '下颌角(1-钝，2-锐，3-正常)',
  `upper_lip_position` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '上唇位(1-前，2-后，3-正常)',
  `lower_lip_position` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '下唇位(1-前，2-后，3-正常)',
  `chin_position` tinyint(4) NOT NULL DEFAULT '0' COMMENT '颏位(1-前，2-后，3-正常)',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='口腔正畸初诊病历关联全身状态与颜貌信息表';

-- --------------------------------------------------------

--
-- 表的结构 `gzh_orthodontics_first_record_model_check`
--

DROP TABLE IF EXISTS `gzh_orthodontics_first_record_model_check`;
CREATE TABLE `gzh_orthodontics_first_record_model_check` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `spot_id` int(10) UNSIGNED NOT NULL COMMENT '诊所ID',
  `record_id` int(10) UNSIGNED NOT NULL COMMENT '就诊流水ID',
  `crowded_maxillary` varchar(32) NOT NULL DEFAULT '' COMMENT '拥挤度上颌',
  `crowded_mandible` varchar(32) NOT NULL DEFAULT '' COMMENT '拥挤度下颌',
  `canine_maxillary` varchar(32) NOT NULL DEFAULT '' COMMENT '尖牙区上颌',
  `canine_mandible` varchar(32) NOT NULL DEFAULT '' COMMENT '尖牙区下颌',
  `molar_maxillary` varchar(32) NOT NULL DEFAULT '' COMMENT '磨牙区上颌',
  `molar_mandible` varchar(32) NOT NULL DEFAULT '' COMMENT '磨牙区下颌',
  `spee_curve` varchar(32) NOT NULL DEFAULT '' COMMENT 'spee曲线',
  `transversal_curve` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '横合曲线(1-陡，2-平，3-凹)',
  `bolton_nterior_teeth` varchar(64) NOT NULL DEFAULT '' COMMENT 'bolton前牙',
  `bolton_all_teeth` varchar(64) NOT NULL DEFAULT '' COMMENT 'bolton全牙',
  `examination` text NOT NULL COMMENT '影像学检查',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='口腔正畸初诊病历关联模型检查';

-- --------------------------------------------------------

--
-- 表的结构 `gzh_orthodontics_first_record_teeth_check`
--

DROP TABLE IF EXISTS `gzh_orthodontics_first_record_teeth_check`;
CREATE TABLE `gzh_orthodontics_first_record_teeth_check` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `spot_id` int(10) UNSIGNED NOT NULL COMMENT '诊所id',
  `record_id` int(10) UNSIGNED NOT NULL COMMENT '就诊流水ID',
  `dental_caries` varchar(64) NOT NULL DEFAULT '' COMMENT '龋齿',
  `reverse` varchar(64) NOT NULL DEFAULT '' COMMENT '扭转',
  `impacted` varchar(64) NOT NULL DEFAULT '' COMMENT '阻生',
  `ectopic` varchar(64) NOT NULL DEFAULT '' COMMENT '异位',
  `defect` varchar(64) NOT NULL DEFAULT '' COMMENT '缺失',
  `retention` varchar(64) NOT NULL DEFAULT '' COMMENT '滞留',
  `repair_body` varchar(64) NOT NULL DEFAULT '' COMMENT '修复体',
  `other` varchar(64) NOT NULL DEFAULT '' COMMENT '其他',
  `other_remark` varchar(255) NOT NULL DEFAULT '' COMMENT '其他-备注',
  `orthodontic_target` text NOT NULL COMMENT '矫治目标',
  `cure` text NOT NULL COMMENT '治疗计划',
  `special_risk` text NOT NULL COMMENT '特殊计划',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='口腔正畸初诊病历关联牙齿检查';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gzh_orthodontics_first_record`
--
ALTER TABLE `gzh_orthodontics_first_record`
  ADD PRIMARY KEY (`id`),
  ADD KEY `spot_id` (`spot_id`,`record_id`);

--
-- Indexes for table `gzh_orthodontics_first_record_examination`
--
ALTER TABLE `gzh_orthodontics_first_record_examination`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gzh_orthodontics_first_record_features`
--
ALTER TABLE `gzh_orthodontics_first_record_features`
  ADD PRIMARY KEY (`id`),
  ADD KEY `record_id` (`record_id`,`spot_id`);

--
-- Indexes for table `gzh_orthodontics_first_record_model_check`
--
ALTER TABLE `gzh_orthodontics_first_record_model_check`
  ADD PRIMARY KEY (`id`),
  ADD KEY `record_id` (`record_id`,`spot_id`);

--
-- Indexes for table `gzh_orthodontics_first_record_teeth_check`
--
ALTER TABLE `gzh_orthodontics_first_record_teeth_check`
  ADD PRIMARY KEY (`id`),
  ADD KEY `record_id` (`record_id`,`spot_id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `gzh_orthodontics_first_record`
--
ALTER TABLE `gzh_orthodontics_first_record`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=3;
--
-- 使用表AUTO_INCREMENT `gzh_orthodontics_first_record_examination`
--
ALTER TABLE `gzh_orthodontics_first_record_examination`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID';
--
-- 使用表AUTO_INCREMENT `gzh_orthodontics_first_record_features`
--
ALTER TABLE `gzh_orthodontics_first_record_features`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID';
--
-- 使用表AUTO_INCREMENT `gzh_orthodontics_first_record_model_check`
--
ALTER TABLE `gzh_orthodontics_first_record_model_check`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID';
--
-- 使用表AUTO_INCREMENT `gzh_orthodontics_first_record_teeth_check`
--
ALTER TABLE `gzh_orthodontics_first_record_teeth_check`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID';
