
-- //初步诊断增加 确诊/疑诊
-- ALTER TABLE gzh_first_check ADD check_degree tinyint(4) unsigned NOT NULL DEFAULT 1 COMMENT '诊断程度[1/确诊 2/疑诊]';  下个迭代再发 已经移至下个迭代