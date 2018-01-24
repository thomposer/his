--修改第三方平台ID注释
ALTER TABLE `gzh_third_platform`
MODIFY COLUMN `platform_id`  int(10) UNSIGNED NOT NULL COMMENT '第三方平台ID(1-妈咪知道,2-就医160)' AFTER `id`;

-- 增加牙位病症
ALTER TABLE gzh_dental_history_relation ADD dental_disease tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '牙位病症(1-龋齿/缺损,2-根尖/牙髓,3-缺失,4-其他)' AFTER content;


