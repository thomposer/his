
CREATE TABLE `gzh_configure_clinic_union` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `parent_spot_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '机构id',
  `configure_id` int(10) unsigned NOT NULL COMMENT '机构-配置表的id',
  `spot_id` int(11) unsigned NOT NULL COMMENT '诊所id',
  `type` tinyint(3) unsigned NOT NULL COMMENT '类型(1-实验室,2-影像学,3-治疗,4-处方,7-其他,8-耗材)',
  `create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `parent_spot-configure` (`parent_spot_id`,`configure_id`,`type`),
  KEY `spot_id` (`spot_id`,`type`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='机构配置-诊所关联表';



--处方分类

UPDATE gzh_recipelist SET drug_type = 3 WHERE drug_type=11;

UPDATE gzh_recipelist SET drug_type = 0 WHERE drug_type=6;

--his发布时，不用发，因为已经提前发了
ALTER TABLE `gzh_appointment` ADD `openid` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'openid(微信公众号openid，clientid)' AFTER `appointment_cancel_reason`;
