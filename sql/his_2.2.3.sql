DROP TABLE IF EXISTS `gzh_material_stock_deduction_record`;
CREATE TABLE `gzh_material_stock_deduction_record` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `spot_id` int(10) UNSIGNED NOT NULL COMMENT '诊所id',
  `record_id` int(10) unsigned NOT NULL COMMENT '就诊流水id',
  `outpatient_id` int(10) UNSIGNED NOT NULL COMMENT '其他类门诊id或chargeInfo表id',
  `material_id` int(10) UNSIGNED NOT NULL COMMENT '其他类管理表id',
  `stock_info_id` int(10) UNSIGNED NOT NULL COMMENT '其他类库存管理表id',
  `num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '扣减数量',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1-正常,2-无效)',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '类型(1-门诊,2-新增收费)',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `outpatient_id` (`spot_id`,`status`,`record_id`,`outpatient_id`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='其他类库存扣减记录表';


DROP TABLE IF EXISTS `gzh_consumables_stock_deduction_record`;
CREATE TABLE `gzh_consumables_stock_deduction_record` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `spot_id` int(10) UNSIGNED NOT NULL COMMENT '诊所id',
  `record_id` int(10) unsigned NOT NULL COMMENT '就诊流水id',
  `outpatient_id` int(10) UNSIGNED NOT NULL COMMENT '医疗耗材门诊id',
  `consumables_id` int(10) UNSIGNED NOT NULL COMMENT '医疗耗材管理表id(机构)',
  `stock_info_id` int(10) UNSIGNED NOT NULL COMMENT '医疗耗材库存管理表id',
  `num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '扣减数量',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1-正常,2-无效)',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `outpatient_id` (`spot_id`,`status`,`record_id`,`outpatient_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='医疗耗材库存扣减记录表';



ALTER TABLE `gzh_recipelist_clinic`
ADD COLUMN `shelves`  varchar(64) NOT NULL DEFAULT '' COMMENT '货架号' AFTER `address`,
ADD COLUMN `shelves_sort`  int(10) UNSIGNED NULL COMMENT '货架号排序' AFTER `shelves`;

ALTER TABLE `gzh_triage_info` CHANGE `bloodtype` `bloodtype` ENUM('','0','ABO','AB','O','B','A') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '血型';
