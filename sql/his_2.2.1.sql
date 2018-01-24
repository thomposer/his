
ALTER TABLE `gzh_patient`
ADD COLUMN `mommyknows_account`  varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '妈咪知道账号' AFTER `nation`;

ALTER TABLE `gzh_stock_info`
ADD COLUMN `invoice_number`  varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '发票号' AFTER `num`;

ALTER TABLE `gzh_consumables_stock_info`
ADD COLUMN `invoice_number`  varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '发票号' AFTER `num`;


ALTER TABLE `gzh_material_stock_info`
ADD COLUMN `invoice_number`  varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '发票号' AFTER `num`;

