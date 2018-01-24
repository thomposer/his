ALTER TABLE `gzh_tag` ADD `type` TINYINT UNSIGNED NOT NULL DEFAULT '0' COMMENT '分类(1-充值卡折扣标签，2-通用标签)' AFTER `status`;
ALTER TABLE `gzh_tag` DROP INDEX `index_spot_id`, ADD INDEX `index_spot_id` (`spot_id`, `status`, `type`) USING BTREE;
ALTER TABLE `gzh_tag` CHANGE `status` `status` TINYINT(4) UNSIGNED NOT NULL DEFAULT '1' COMMENT '标签状态[ 1-正常,2-停用]';


--- 修复历史数据
UPDATE gzh_tag SET type = 1;

ALTER TABLE `gzh_recipelist` DROP INDEX `spot_id`, ADD INDEX `spot_id` (`spot_id`, `tag_id`, `status`) USING BTREE;



DROP TABLE IF EXISTS `gzh_advice_tag_relation`;
CREATE TABLE `gzh_advice_tag_relation` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `spot_id` int(11) UNSIGNED NOT NULL COMMENT '机构id',
  `advice_id` int(10) UNSIGNED NOT NULL COMMENT '医嘱ID',
  `tag_id` int(11) UNSIGNED NOT NULL COMMENT '标签ID',
  `type` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '医嘱类型(1-实验室检查，2-影像学检查，3-治疗，4-处方，7-其他，8-医疗耗材，9-医嘱套餐)',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='医嘱关联标签表';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gzh_advice_tag_relation`
--
ALTER TABLE `gzh_advice_tag_relation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `spot_id` (`spot_id`,`advice_id`,`type`) USING BTREE,
  ADD KEY `tag_id` (`tag_id`,`spot_id`,`type`) USING BTREE;

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `gzh_advice_tag_relation`
--
ALTER TABLE `gzh_advice_tag_relation`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID';
--
-- 限制导出的表
--

--
-- 限制表 `gzh_advice_tag_relation`
--
ALTER TABLE `gzh_advice_tag_relation`
  ADD CONSTRAINT `gzh_advice_tag_relation_ibfk_1` FOREIGN KEY (`tag_id`) REFERENCES `gzh_tag` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;




-- 检验配置 增加外送机构的名称
ALTER TABLE gzh_inspect_clinic ADD deliver_organization tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '外送机构 1/金域 2/迪安 3/艾迪康' AFTER deliver;
ALTER TABLE gzh_inspect_record ADD deliver_organization tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '外送机构 1/金域 2/迪安 3/艾迪康' AFTER deliver;
-- 修复海德的外送为金域
UPDATE gzh_inspect_clinic SET deliver_organization=1 WHERE deliver=1 AND spot_id=59;
UPDATE gzh_inspect_record SET deliver_organization=1 WHERE deliver=1 AND spot_id=59;

