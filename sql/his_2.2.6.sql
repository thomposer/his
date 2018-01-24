
-- 修改支付方式的描述
ALTER TABLE t_card_flow MODIFY `f_pay_type` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '1现金/2刷卡/3微信/4支付宝/5美团';

ALTER TABLE gzh_charge_record MODIFY `type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '支付方式(1-现金,2-刷卡,3-微信支付,4-支付宝支付,6-充值卡,7-服务卡,8-套餐卡,9-美团)';

ALTER TABLE gzh_charge_record_log MODIFY `pay_type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '支付方式(1-现金,2-刷卡,3-微信支付,4-支付宝支付,6-充值卡,7-服务卡,8-套餐卡,9-美团)';

ALTER TABLE gzh_membership_package_card_flow MODIFY `pay_type` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '支付方式 1现金/2刷卡/3微信/4支付宝/5会员卡/6美团';

-- 增加处方编号
ALTER TABLE gzh_patient_record ADD recipe_number varchar(20) NOT NULL DEFAULT '' COMMENT '处方号' AFTER is_package;

--默认为口腔初诊
update gzh_report set record_type = 4 where record_type = 3;