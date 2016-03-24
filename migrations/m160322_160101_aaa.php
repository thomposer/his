<?php

use yii\db\Schema;

class m160322_160101_aaa extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%patient}}', [
            'id' => Schema::TYPE_PK,
            'user_name' => Schema::TYPE_STRING . "(64) NOT NULL DEFAULT '' COMMENT '患者名称' ",
            'sex' => Schema::TYPE_SMALLINT . "(3) NOT NULL COMMENT '性别(1-男,2-女)' ",
            'birthday' => Schema::TYPE_DATETIME . " NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '出生日期' ",
            'nation' => Schema::TYPE_STRING . "(32) NOT NULL DEFAULT '' COMMENT '民族/国籍' ",
            'marriage' => Schema::TYPE_SMALLINT . "(3) NOT NULL DEFAULT 1 COMMENT '婚姻状况(1-未婚,2-已婚,3-离异,4-丧偶)' ",
            'occupation' => Schema::TYPE_STRING . "(64) NOT NULL DEFAULT '' COMMENT '职业' ",
            'province' => Schema::TYPE_STRING . "(32) NOT NULL DEFAULT '' COMMENT '省份' ",
            'city' => Schema::TYPE_STRING . "(32) NOT NULL DEFAULT '' COMMENT '城市' ",
            'area' => Schema::TYPE_STRING . "(32) NOT NULL DEFAULT '' COMMENT '区县' ",
            'detail_address' => Schema::TYPE_STRING . "(64) NOT NULL DEFAULT '' COMMENT '家庭详细地址' ",
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%patient}}');
    }
}
