ALTER TABLE `gzh_patient_record` MODIFY `price` decimal(10,2) unsigned NULL COMMENT '诊疗费用';

ALTER TABLE `gzh_outpatient_package_template` CHANGE `medical_fee_clinic_id` `medical_fee_id` INT(10) UNSIGNED NOT NULL COMMENT '机构诊疗费配置ID';
