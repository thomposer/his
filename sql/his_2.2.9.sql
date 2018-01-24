--处方打印设置
ALTER TABLE `gzh_spot_config` ADD `recipe_rebate` TINYINT NOT NULL DEFAULT '2' COMMENT '处方打印设置(1-A4打印,2-A5打印)';

ALTER TABLE `gzh_recipe_record` ADD `drug_type` TINYINT UNSIGNED NOT NULL DEFAULT '0' COMMENT '药品分类' AFTER `package_record_id`;


ALTER TABLE `gzh_report` DROP INDEX `record_id`, ADD UNIQUE `record_id` (`record_id`, `spot_id`) USING BTREE;


ALTER TABLE `gzh_triage_info` DROP INDEX `record_id`, ADD UNIQUE `record_id` (`record_id`, `spot_id`) USING BTREE;

