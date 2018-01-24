
DROP TABLE IF EXISTS `gzh_third_platform`;
CREATE TABLE `gzh_third_platform` (
  `id` int(10) UNSIGNED NOT NULL,
  `platform_id` int(10) UNSIGNED NOT NULL COMMENT '第三方平台ID',
  `spot_id` int(10) UNSIGNED NOT NULL COMMENT '诊所ID',
  `spot_type_id` int(10) UNSIGNED NOT NULL COMMENT '服务类型ID',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gzh_third_platform`
--
ALTER TABLE `gzh_third_platform`
  ADD PRIMARY KEY (`id`),
  ADD KEY `spot_id` (`spot_id`,`spot_type_id`) USING BTREE;

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `gzh_third_platform`
--
ALTER TABLE `gzh_third_platform`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
  
ALTER TABLE gzh_first_check ADD check_degree tinyint(4) unsigned NOT NULL DEFAULT 1 COMMENT '诊断程度[1/确诊 2/疑诊]';



-- 卡中心d_easyhin_card_center
ALTER TABLE t_card_manage MODIFY `f_card_desc` varchar(500) NOT NULL DEFAULT '' COMMENT '卡描述';


DROP TABLE IF EXISTS gzh_package_card_service ;
CREATE TABLE gzh_package_card_service(
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `spot_id` int(11) unsigned NOT NULL COMMENT '机构id',
  `name` varchar(64) NOT NULL DEFAULT '' COMMENT '名称',
  `status` tinyint unsigned NOT NULL DEFAULT '2' COMMENT '状态(1-正常,2-停用)',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `spot_id` (`spot_id`)
)ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='套餐卡服务类型配置表';


DROP TABLE IF EXISTS gzh_package_card ;
CREATE TABLE gzh_package_card(
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `spot_id` int(11) unsigned NOT NULL COMMENT '机构id',
  `name` varchar(64) NOT NULL DEFAULT '' COMMENT '套餐名称',
  `product_name` varchar(64) NOT NULL DEFAULT '' COMMENT '商品名称',
  `meta` varchar(64) NOT NULL DEFAULT '' COMMENT '拼音码',
  `validity_period` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '有效期',
  `price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '零售价',
  `default_price` decimal(10,2) unsigned DEFAULT NULL COMMENT '成本价',
  `content` text NOT NULL COMMENT '套餐内容',
  `remarks` text NOT NULL COMMENT '备注内容',
  `status` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '状态(1-正常,2-停用)',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `spot_id` (`spot_id`)
)ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='套餐卡配置表';

DROP TABLE IF EXISTS gzh_package_service_union ;
CREATE TABLE `gzh_package_service_union` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `spot_id` int(11) unsigned NOT NULL COMMENT '机构id',
  `package_card_id` int(11) unsigned NOT NULL COMMENT '套餐卡id',
  `package_card_service_id` int(11) unsigned NOT NULL COMMENT '套餐卡服务类型id',
  `time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '次数',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `spot_id` (`spot_id`),
  KEY `package_card_id` (`package_card_id`),
  KEY `package_card_service_id` (`package_card_service_id`),
  CONSTRAINT `gzh_package_service_union_ibfk_1` FOREIGN KEY (`package_card_id`) REFERENCES `gzh_package_card` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `gzh_package_service_union_ibfk_2` FOREIGN KEY (`package_card_service_id`) REFERENCES `gzh_package_card_service` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='套餐卡与服务关联表';



DROP TABLE IF EXISTS `gzh_membership_package_card`;
CREATE TABLE `gzh_membership_package_card` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `spot_id` int(10) UNSIGNED NOT NULL COMMENT '诊所id',
  `package_card_id` int(10) UNSIGNED NOT NULL COMMENT '卡中心-套餐卡id',
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '状态(1-正常，2-停用)',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会员卡-套餐卡配置表';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gzh_membership_package_card`
--
ALTER TABLE `gzh_membership_package_card`
  ADD PRIMARY KEY (`id`),
  ADD KEY `package_card_id` (`package_card_id`),
  ADD KEY `spot_id` (`spot_id`,`status`) USING BTREE;

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `gzh_membership_package_card`
--
ALTER TABLE `gzh_membership_package_card`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID';



DROP TABLE IF EXISTS `gzh_membership_package_card_union`;
CREATE TABLE `gzh_membership_package_card_union` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `spot_id` int(10) UNSIGNED NOT NULL COMMENT '诊所id',
  `membership_package_card_id` int(10) UNSIGNED NOT NULL COMMENT '会员-套餐卡id',
  `patient_id` int(10) UNSIGNED NOT NULL COMMENT '患者id',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会员卡-套餐卡关联患者表';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gzh_membership_package_card_union`
--
ALTER TABLE `gzh_membership_package_card_union`
  ADD PRIMARY KEY (`id`),
  ADD KEY `spot_id` (`spot_id`),
  ADD KEY `membership_package_card_id` (`membership_package_card_id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `gzh_membership_package_card_union`
--
ALTER TABLE `gzh_membership_package_card_union`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID';
--
-- 限制导出的表
--

--
-- 限制表 `gzh_membership_package_card_union`
--
ALTER TABLE `gzh_membership_package_card_union`
  ADD CONSTRAINT `gzh_membership_package_card_union_ibfk_1` FOREIGN KEY (`membership_package_card_id`) REFERENCES `gzh_membership_package_card` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;



CREATE TABLE `gzh_card_order` (
  `id` int(11) UNSIGNED NOT NULL,
  `card_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '卡ID',
  `spot_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '诊所ID',
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '操作人ID',
  `type` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '支付方式(1-现金,2-刷卡,3-微信支付,4-支付宝支付,5-会员卡)',
  `out_trade_no` varchar(64) NOT NULL DEFAULT '' COMMENT '订单号',
  `subject` varchar(255) NOT NULL DEFAULT '' COMMENT '订单名称',
  `total_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '支付总金额(本次交易支付的订单金额，单位为人民币（元）)',
  `income` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '实收金额',
  `status` tinyint(4) UNSIGNED NOT NULL DEFAULT '1' COMMENT '订单支付状态[ 1-待支付,2-支付成功,3-支付失败 ,4-已退款,5-已过期]',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单表';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gzh_order`
--
ALTER TABLE `gzh_card_order`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `out_trade_no` (`out_trade_no`) USING BTREE,
  ADD KEY `type` (`type`),
  ADD KEY `card_id` (`spot_id`,`card_id`),
  ADD KEY `total_amount` (`total_amount`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `gzh_order`
--
ALTER TABLE `gzh_card_order`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;  




DROP TABLE IF EXISTS `gzh_membership_package_card_service`;
CREATE TABLE `gzh_membership_package_card_service` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `spot_id` int(10) UNSIGNED NOT NULL COMMENT '诊所id',
  `membership_package_card_id` int(10) UNSIGNED NOT NULL COMMENT '会员卡-套餐卡配置表id',
  `package_card_service_id` int(10) UNSIGNED NOT NULL COMMENT '套餐卡服务类型配置表id',
  `total_time` int(10) UNSIGNED NOT NULL COMMENT '总次数',
  `remain_time` int(10) UNSIGNED NOT NULL COMMENT '剩余次数',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gzh_membership_package_card_service`
--
ALTER TABLE `gzh_membership_package_card_service`
  ADD PRIMARY KEY (`id`),
  ADD KEY `membership_package_card_id` (`membership_package_card_id`),
  ADD KEY `package_card_service_id` (`package_card_service_id`),
  ADD KEY `spot_id` (`spot_id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `gzh_membership_package_card_service`
--
ALTER TABLE `gzh_membership_package_card_service`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID';
--
-- 限制导出的表
--

--
-- 限制表 `gzh_membership_package_card_service`
--
ALTER TABLE `gzh_membership_package_card_service`
  ADD CONSTRAINT `gzh_membership_package_card_service_ibfk_1` FOREIGN KEY (`membership_package_card_id`) REFERENCES `gzh_membership_package_card` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `gzh_membership_package_card_service_ibfk_2` FOREIGN KEY (`package_card_service_id`) REFERENCES `gzh_package_card_service` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
  


DROP TABLE IF EXISTS `gzh_membership_package_card_flow`;
CREATE TABLE `gzh_membership_package_card_flow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `spot_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '诊所ID(来源渠道)',
  `membership_package_card_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户套餐卡ID',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '操作用户ID',
  `flow_item` varchar(255) NOT NULL DEFAULT '' COMMENT '交易项',
  `patient_id` int(10) NOT NULL DEFAULT '0' COMMENT '交易用户',
  `transaction_type` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '交易类型(1消费／2购买／3消费退还)',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '流水金额（元）',
  `username` varchar(32) NOT NULL DEFAULT '' COMMENT '操作用户名',
  `pay_type` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '支付方式 1现金/2刷卡/3微信/4支付宝/5会员卡',
  `operate_origin` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '操作渠道(1门诊收费/2手动登记/3新增收费/4套餐卡购买)',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `charge_record_id` int(10) unsigned  NOT NULL DEFAULT '0' COMMENT '收费记录id',
  `charge_record_log_id` int(10) unsigned  NOT NULL DEFAULT '0' COMMENT '收费交易流水id',	
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_membership_package_card_id` (`membership_package_card_id`),
  KEY `idx_patient_id` (`patient_id`),
  KEY `idx_spot_id` (`spot_id`),
  KEY `idx_charge_record_id` (`charge_record_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会员--套餐卡流水表';


ALTER TABLE gzh_payment_log DROP FOREIGN KEY gzh_payment_log_ibfk_1;


DROP TABLE IF EXISTS `gzh_membership_package_card_flow_service`;
CREATE TABLE `gzh_membership_package_card_flow_service` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '自增ID',
  `spot_id` int(10) UNSIGNED NOT NULL COMMENT '诊所id',
  `flow_id` int(10) UNSIGNED NOT NULL COMMENT '会员--套餐卡流水ID',
  `package_card_service_id` int(10) UNSIGNED NOT NULL COMMENT '套餐卡服务类型配置表id',
  `time` int(11) NOT NULL DEFAULT '0' COMMENT '消费／回退次数',
  `create_time` int(10) UNSIGNED NOT NULL COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会员--套餐卡流水关联服务使用表';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gzh_membership_package_card_flow_service`
--
ALTER TABLE `gzh_membership_package_card_flow_service`
  ADD PRIMARY KEY (`id`),
  ADD KEY `flow_id` (`flow_id`,`spot_id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `gzh_membership_package_card_flow_service`
--
ALTER TABLE `gzh_membership_package_card_flow_service`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增ID';
