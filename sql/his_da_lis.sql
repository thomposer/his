CREATE TABLE `gzh_inspect_item_external_union` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `company_id` int(10) unsigned NOT NULL COMMENT '0-迪安',
  `inspect_item_id` int(10) unsigned NOT NULL COMMENT '检验项目id',
  `external_id` varchar(64) NOT NULL DEFAULT '' COMMENT '外部项目id',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `company_id` (`company_id`),
  KEY `inspect_item_id` (`inspect_item_id`),
  KEY `external_id` (`external_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='检验项目映射表';