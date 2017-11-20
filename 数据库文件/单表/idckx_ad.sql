/*
Navicat MySQL Data Transfer

Source Server         : 本机
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : idckx

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2017-11-20 11:27:35
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for idckx_ad
-- ----------------------------
DROP TABLE IF EXISTS `idckx_ad`;
CREATE TABLE `idckx_ad` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ad_site_id` int(11) unsigned NOT NULL COMMENT '广告位置id',
  `ad_ader_id` int(11) DEFAULT NULL COMMENT '广告主id',
  `ad_type` varchar(20) DEFAULT NULL COMMENT '广告类型',
  `ad_name` varchar(50) DEFAULT NULL COMMENT '广告名称',
  `ad_pic_address` varchar(255) DEFAULT NULL COMMENT '广告图片地址',
  `ad_pic_height` varchar(5) DEFAULT NULL COMMENT '图片高度',
  `ad_pic_width` varchar(5) DEFAULT NULL COMMENT '图片宽度',
  `ad_intro` text COMMENT '广东简介',
  `ad_link` varchar(255) DEFAULT NULL COMMENT '链接地址',
  `ad_open_pattern` varchar(255) DEFAULT NULL COMMENT '打开方式',
  `ad_power` int(11) DEFAULT NULL COMMENT '权重',
  `ad_state` tinyint(4) DEFAULT NULL COMMENT '状态',
  `ad_overtime` datetime DEFAULT NULL COMMENT '过期时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of idckx_ad
-- ----------------------------
