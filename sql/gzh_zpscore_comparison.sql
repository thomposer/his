/*
Navicat MySQL Data Transfer

Source Server         : his(dev)
Source Server Version : 50628
Source Host           : 10.66.157.166:3306
Source Database       : d_easyhin_his

Target Server Type    : MYSQL
Target Server Version : 50628
File Encoding         : 65001

Date: 2017-03-02 15:40:16
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for gzh_zpscore_comparison
-- ----------------------------
DROP TABLE IF EXISTS `gzh_zpscore_comparison`;
CREATE TABLE `gzh_zpscore_comparison` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `zscore` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'z值',
  `pscore` varchar(10) NOT NULL DEFAULT '0' COMMENT 'p值',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `index_zscore` (`zscore`)
) ENGINE=InnoDB AUTO_INCREMENT=411 DEFAULT CHARSET=utf8 COMMENT='z评分和p评分对照表';

-- ----------------------------
-- Records of gzh_zpscore_comparison
-- ----------------------------
INSERT INTO `gzh_zpscore_comparison` VALUES ('1', '0.00', '0.0000', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('2', '0.01', '0.0040', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('3', '0.02', '0.0080', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('4', '0.03', '0.0120', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('5', '0.04', '0.0160', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('6', '0.05', '0.0199', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('7', '0.06', '0.0239', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('8', '0.07', '0.0279', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('9', '0.08', '0.0319', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('10', '0.09', '0.0359', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('11', '0.10', '0.0398', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('12', '0.11', '0.0438', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('13', '0.12', '0.0478', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('14', '0.13', '0.0517', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('15', '0.14', '0.0557', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('16', '0.15', '0.0596', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('17', '0.16', '0.0636', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('18', '0.17', '0.0675', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('19', '0.18', '0.0714', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('20', '0.19', '0.0753', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('21', '0.20', '0.0793', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('22', '0.21', '0.0832', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('23', '0.22', '0.0871', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('24', '0.23', '0.0910', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('25', '0.24', '0.0948', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('26', '0.25', '0.0987', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('27', '0.26', '0.1026', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('28', '0.27', '0.1064', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('29', '0.28', '0.1103', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('30', '0.29', '0.1141', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('31', '0.30', '0.1179', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('32', '0.31', '0.1217', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('33', '0.32', '0.1255', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('34', '0.33', '0.1293', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('35', '0.34', '0.1331', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('36', '0.35', '0.1368', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('37', '0.36', '0.1406', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('38', '0.37', '0.1443', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('39', '0.38', '0.1480', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('40', '0.39', '0.1517', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('41', '0.40', '0.1554', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('42', '0.41', '0.1591', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('43', '0.42', '0.1628', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('44', '0.43', '0.1664', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('45', '0.44', '0.1700', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('46', '0.45', '0.1736', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('47', '0.46', '0.1772', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('48', '0.47', '0.1808', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('49', '0.48', '0.1844', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('50', '0.49', '0.1879', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('51', '0.50', '0.1915', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('52', '0.51', '0.1950', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('53', '0.52', '0.1985', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('54', '0.53', '0.2019', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('55', '0.54', '0.2054', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('56', '0.55', '0.2088', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('57', '0.56', '0.2123', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('58', '0.57', '0.2157', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('59', '0.58', '0.2190', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('60', '0.59', '0.2224', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('61', '0.60', '0.2257', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('62', '0.61', '0.2291', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('63', '0.62', '0.2324', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('64', '0.63', '0.2357', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('65', '0.64', '0.2389', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('66', '0.65', '0.2422', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('67', '0.66', '0.2454', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('68', '0.67', '0.2486', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('69', '0.68', '0.2517', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('70', '0.69', '0.2549', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('71', '0.70', '0.2580', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('72', '0.71', '0.2611', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('73', '0.72', '0.2642', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('74', '0.73', '0.2673', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('75', '0.74', '0.2704', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('76', '0.75', '0.2734', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('77', '0.76', '0.2764', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('78', '0.77', '0.2794', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('79', '0.78', '0.2823', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('80', '0.79', '0.2852', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('81', '0.80', '0.2881', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('82', '0.81', '0.2910', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('83', '0.82', '0.2939', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('84', '0.83', '0.2967', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('85', '0.84', '0.2995', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('86', '0.85', '0.3023', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('87', '0.86', '0.3051', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('88', '0.87', '0.3078', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('89', '0.88', '0.3106', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('90', '0.89', '0.3133', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('91', '0.90', '0.3159', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('92', '0.91', '0.3186', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('93', '0.92', '0.3212', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('94', '0.93', '0.3238', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('95', '0.94', '0.3264', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('96', '0.95', '0.3289', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('97', '0.96', '0.3315', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('98', '0.97', '0.3340', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('99', '0.98', '0.3365', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('100', '0.99', '0.3389', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('101', '1.00', '0.3413', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('102', '1.01', '0.3438', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('103', '1.02', '0.3461', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('104', '1.03', '0.3485', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('105', '1.04', '0.3508', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('106', '1.05', '0.3531', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('107', '1.06', '0.3554', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('108', '1.07', '0.3577', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('109', '1.08', '0.3599', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('110', '1.09', '0.3621', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('111', '1.10', '0.3643', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('112', '1.11', '0.3665', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('113', '1.12', '0.3686', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('114', '1.13', '0.3708', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('115', '1.14', '0.3729', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('116', '1.15', '0.3749', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('117', '1.16', '0.3770', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('118', '1.17', '0.3790', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('119', '1.18', '0.3810', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('120', '1.19', '0.3830', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('121', '1.20', '0.3849', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('122', '1.21', '0.3869', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('123', '1.22', '0.3888', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('124', '1.23', '0.3907', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('125', '1.24', '0.3925', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('126', '1.25', '0.3944', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('127', '1.26', '0.3962', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('128', '1.27', '0.3980', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('129', '1.28', '0.3997', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('130', '1.29', '0.4015', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('131', '1.30', '0.4032', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('132', '1.31', '0.4049', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('133', '1.32', '0.4066', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('134', '1.33', '0.4082', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('135', '1.34', '0.4099', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('136', '1.35', '0.4115', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('137', '1.36', '0.4131', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('138', '1.37', '0.4147', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('139', '1.38', '0.4162', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('140', '1.39', '0.4177', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('141', '1.40', '0.4192', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('142', '1.41', '0.4207', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('143', '1.42', '0.4222', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('144', '1.43', '0.4236', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('145', '1.44', '0.4251', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('146', '1.45', '0.4265', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('147', '1.46', '0.4279', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('148', '1.47', '0.4292', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('149', '1.48', '0.4306', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('150', '1.49', '0.4319', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('151', '1.50', '0.4332', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('152', '1.51', '0.4345', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('153', '1.52', '0.4357', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('154', '1.53', '0.4370', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('155', '1.54', '0.4382', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('156', '1.55', '0.4394', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('157', '1.56', '0.4406', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('158', '1.57', '0.4418', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('159', '1.58', '0.4429', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('160', '1.59', '0.4441', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('161', '1.60', '0.4452', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('162', '1.61', '0.4463', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('163', '1.62', '0.4474', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('164', '1.63', '0.4484', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('165', '1.64', '0.4495', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('166', '1.65', '0.4505', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('167', '1.66', '0.4515', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('168', '1.67', '0.4525', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('169', '1.68', '0.4535', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('170', '1.69', '0.4545', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('171', '1.70', '0.4554', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('172', '1.71', '0.4564', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('173', '1.72', '0.4573', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('174', '1.73', '0.4582', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('175', '1.74', '0.4591', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('176', '1.75', '0.4599', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('177', '1.76', '0.4608', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('178', '1.77', '0.4616', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('179', '1.78', '0.4625', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('180', '1.79', '0.4633', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('181', '1.80', '0.4641', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('182', '1.81', '0.4649', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('183', '1.82', '0.4656', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('184', '1.83', '0.4664', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('185', '1.84', '0.4671', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('186', '1.85', '0.4678', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('187', '1.86', '0.4686', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('188', '1.87', '0.4693', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('189', '1.88', '0.4699', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('190', '1.89', '0.4706', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('191', '1.90', '0.4713', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('192', '1.91', '0.4719', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('193', '1.92', '0.4726', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('194', '1.93', '0.4732', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('195', '1.94', '0.4738', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('196', '1.95', '0.4744', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('197', '1.96', '0.4750', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('198', '1.97', '0.4756', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('199', '1.98', '0.4761', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('200', '1.99', '0.4767', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('201', '2.00', '0.4772', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('202', '2.01', '0.4778', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('203', '2.02', '0.4783', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('204', '2.03', '0.4788', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('205', '2.04', '0.4793', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('206', '2.05', '0.4798', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('207', '2.06', '0.4803', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('208', '2.07', '0.4808', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('209', '2.08', '0.4812', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('210', '2.09', '0.4817', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('211', '2.10', '0.4821', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('212', '2.11', '0.4826', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('213', '2.12', '0.4830', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('214', '2.13', '0.4834', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('215', '2.14', '0.4838', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('216', '2.15', '0.4842', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('217', '2.16', '0.4846', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('218', '2.17', '0.4850', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('219', '2.18', '0.4854', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('220', '2.19', '0.4857', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('221', '2.20', '0.4861', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('222', '2.21', '0.4864', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('223', '2.22', '0.4868', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('224', '2.23', '0.4871', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('225', '2.24', '0.4875', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('226', '2.25', '0.4878', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('227', '2.26', '0.4881', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('228', '2.27', '0.4884', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('229', '2.28', '0.4887', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('230', '2.29', '0.4890', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('231', '2.30', '0.4893', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('232', '2.31', '0.4896', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('233', '2.32', '0.4898', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('234', '2.33', '0.4901', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('235', '2.34', '0.4904', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('236', '2.35', '0.4906', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('237', '2.36', '0.4909', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('238', '2.37', '0.4911', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('239', '2.38', '0.4913', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('240', '2.39', '0.4916', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('241', '2.40', '0.4918', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('242', '2.41', '0.4920', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('243', '2.42', '0.4922', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('244', '2.43', '0.4925', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('245', '2.44', '0.4927', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('246', '2.45', '0.4929', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('247', '2.46', '0.4931', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('248', '2.47', '0.4932', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('249', '2.48', '0.4934', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('250', '2.49', '0.4936', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('251', '2.50', '0.4938', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('252', '2.51', '0.4940', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('253', '2.52', '0.4941', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('254', '2.53', '0.4943', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('255', '2.54', '0.4945', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('256', '2.55', '0.4946', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('257', '2.56', '0.4948', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('258', '2.57', '0.4949', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('259', '2.58', '0.4951', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('260', '2.59', '0.4952', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('261', '2.60', '0.4953', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('262', '2.61', '0.4955', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('263', '2.62', '0.4956', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('264', '2.63', '0.4957', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('265', '2.64', '0.4959', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('266', '2.65', '0.4960', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('267', '2.66', '0.4961', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('268', '2.67', '0.4962', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('269', '2.68', '0.4963', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('270', '2.69', '0.4964', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('271', '2.70', '0.4965', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('272', '2.71', '0.4966', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('273', '2.72', '0.4967', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('274', '2.73', '0.4968', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('275', '2.74', '0.4969', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('276', '2.75', '0.4970', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('277', '2.76', '0.4971', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('278', '2.77', '0.4972', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('279', '2.78', '0.4973', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('280', '2.79', '0.4974', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('281', '2.80', '0.4974', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('282', '2.81', '0.4975', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('283', '2.82', '0.4976', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('284', '2.83', '0.4977', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('285', '2.84', '0.4977', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('286', '2.85', '0.4978', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('287', '2.86', '0.4979', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('288', '2.87', '0.4979', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('289', '2.88', '0.4980', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('290', '2.89', '0.4981', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('291', '2.90', '0.4981', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('292', '2.91', '0.4982', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('293', '2.92', '0.4982', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('294', '2.93', '0.4983', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('295', '2.94', '0.4984', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('296', '2.95', '0.4984', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('297', '2.96', '0.4985', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('298', '2.97', '0.4985', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('299', '2.98', '0.4986', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('300', '2.99', '0.4986', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('301', '3.00', '0.4987', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('302', '3.01', '0.4987', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('303', '3.02', '0.4987', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('304', '3.03', '0.4988', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('305', '3.04', '0.4988', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('306', '3.05', '0.4989', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('307', '3.06', '0.4989', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('308', '3.07', '0.4989', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('309', '3.08', '0.4990', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('310', '3.09', '0.4990', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('311', '3.10', '0.4990', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('312', '3.11', '0.4991', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('313', '3.12', '0.4991', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('314', '3.13', '0.4991', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('315', '3.14', '0.4992', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('316', '3.15', '0.4992', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('317', '3.16', '0.4992', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('318', '3.17', '0.4992', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('319', '3.18', '0.4993', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('320', '3.19', '0.4993', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('321', '3.20', '0.4993', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('322', '3.21', '0.4993', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('323', '3.22', '0.4994', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('324', '3.23', '0.4994', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('325', '3.24', '0.4994', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('326', '3.25', '0.4994', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('327', '3.26', '0.4994', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('328', '3.27', '0.4995', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('329', '3.28', '0.4995', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('330', '3.29', '0.4995', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('331', '3.30', '0.4995', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('332', '3.31', '0.4995', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('333', '3.32', '0.4995', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('334', '3.33', '0.4996', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('335', '3.34', '0.4996', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('336', '3.35', '0.4996', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('337', '3.36', '0.4996', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('338', '3.37', '0.4996', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('339', '3.38', '0.4996', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('340', '3.39', '0.4997', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('341', '3.40', '0.4997', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('342', '3.41', '0.4997', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('343', '3.42', '0.4997', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('344', '3.43', '0.4997', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('345', '3.44', '0.4997', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('346', '3.45', '0.4997', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('347', '3.46', '0.4997', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('348', '3.47', '0.4997', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('349', '3.48', '0.4997', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('350', '3.49', '0.4998', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('351', '3.50', '0.4998', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('352', '3.51', '0.4998', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('353', '3.52', '0.4998', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('354', '3.53', '0.4998', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('355', '3.54', '0.4998', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('356', '3.55', '0.4998', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('357', '3.56', '0.4998', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('358', '3.57', '0.4998', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('359', '3.58', '0.4998', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('360', '3.59', '0.4998', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('361', '3.60', '0.4998', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('362', '3.61', '0.4998', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('363', '3.62', '0.4999', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('364', '3.63', '0.4999', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('365', '3.64', '0.4999', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('366', '3.65', '0.4999', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('367', '3.66', '0.4999', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('368', '3.67', '0.4999', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('369', '3.68', '0.4999', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('370', '3.69', '0.4999', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('371', '3.70', '0.4999', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('372', '3.71', '0.4999', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('373', '3.72', '0.4999', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('374', '3.73', '0.4999', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('375', '3.74', '0.4999', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('376', '3.75', '0.4999', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('377', '3.76', '0.4999', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('378', '3.77', '0.4999', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('379', '3.78', '0.4999', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('380', '3.79', '0.4999', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('381', '3.80', '0.4999', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('382', '3.81', '0.4999', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('383', '3.82', '0.4999', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('384', '3.83', '0.4999', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('385', '3.84', '0.4999', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('386', '3.85', '0.4999', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('387', '3.86', '0.4999', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('388', '3.87', '0.4999', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('389', '3.88', '0.4999', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('390', '3.89', '0.4999', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('391', '3.90', '0.5000', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('392', '3.91', '0.5000', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('393', '3.92', '0.5000', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('394', '3.93', '0.5000', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('395', '3.94', '0.5000', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('396', '3.95', '0.5000', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('397', '3.96', '0.5000', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('398', '3.97', '0.5000', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('399', '3.98', '0.5000', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('400', '3.99', '0.5000', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('401', '4.00', '0.5000', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('402', '4.01', '0.5000', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('403', '4.02', '0.5000', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('404', '4.03', '0.5000', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('405', '4.04', '0.5000', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('406', '4.05', '0.5000', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('407', '4.06', '0.5000', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('408', '4.07', '0.5000', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('409', '4.08', '0.5000', '0', '0');
INSERT INTO `gzh_zpscore_comparison` VALUES ('410', '4.09', '0.5000', '0', '0');
