/*
Navicat MySQL Data Transfer

Source Server         : liuyibu
Source Server Version : 50617
Source Host           : 192.168.10.9:3306
Source Database       : liuyibu

Target Server Type    : MYSQL
Target Server Version : 50617
File Encoding         : 65001

Date: 2016-05-03 10:58:23
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `address`
-- ----------------------------
DROP TABLE IF EXISTS `address`;
CREATE TABLE `address` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `address` varchar(150) NOT NULL,
  `phone` char(15) NOT NULL,
  `consignee` varchar(70) NOT NULL,
  `default` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '1默认地址0未默认',
  `status` tinyint(3) unsigned NOT NULL COMMENT '0冻结1正常',
  `sex` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1男2女',
  `door_num` varchar(255) NOT NULL COMMENT '门牌号',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of address
-- ----------------------------
INSERT INTO `address` VALUES ('1', '1', '陕西省西安火炬路东新世纪广场', '188888148409', '大明', '1', '1', '2', '205室');
INSERT INTO `address` VALUES ('2', '1', '陕西西安碑林区火炬路东新世纪广场', '18888148409', '飞', '0', '1', '1', '250室');
INSERT INTO `address` VALUES ('3', '1', '陕西省西安火炬路东新世纪广场', '188888148409', '小明', '0', '1', '1', '250室');
INSERT INTO `address` VALUES ('4', '1', '陕西省西安火炬路东新世纪广场', '188888148409', '小明', '0', '1', '1', '250室');
INSERT INTO `address` VALUES ('5', '1', '陕西省西安火炬路东新世纪广场', '188888148409', '小明', '0', '1', '1', '250室');
INSERT INTO `address` VALUES ('6', '1', '陕西省西安火炬路东新世纪广场', '188888148409', '小明', '0', '1', '1', '250室');
INSERT INTO `address` VALUES ('7', '1', '陕西省西安火炬路东新世纪广场', '188888148409', '小明', '0', '1', '1', '205室');
INSERT INTO `address` VALUES ('8', '1', '陕西省西安火炬路东新世纪广场', '188888148409', '小明', '0', '1', '1', '205室');
INSERT INTO `address` VALUES ('9', '1', '陕西省西安火炬路东新世纪广场', '188888148409', '小明', '0', '1', '1', '205室');
INSERT INTO `address` VALUES ('10', '1', '陕西省西安火炬路东新世纪广场', '188888148409', '小明', '0', '1', '1', '205室');
INSERT INTO `address` VALUES ('11', '1', '陕西省西安火炬路东新世纪广场', '188888148409', '小明', '0', '1', '1', '205室');
INSERT INTO `address` VALUES ('12', '1', '陕西省西安火炬路东新世纪广场', '188888148409', '小明', '0', '1', '1', '205室');
INSERT INTO `address` VALUES ('13', '1', '陕西省西安火炬路东新世纪广场', '188888148409', '小明', '0', '1', '1', '205室');
INSERT INTO `address` VALUES ('14', '1', '陕西省西安火炬路东新世纪广场', '188888148409', '小明', '0', '1', '1', '205室');
INSERT INTO `address` VALUES ('16', '1', '陕西省西安火炬路东新世纪广场', '188888148409', '小明', '0', '1', '1', '205室');
INSERT INTO `address` VALUES ('17', '6', '黄埔花园', '17791376024', '刘备', '0', '1', '1', '1308');
INSERT INTO `address` VALUES ('18', '1', '陕西省西安火炬路东新世纪广场', '188888148409', '小明', '0', '1', '1', '205室');
INSERT INTO `address` VALUES ('21', '6', '普天小区', '12345678901', '张飞', '1', '1', '1', '7号楼301室');
INSERT INTO `address` VALUES ('23', '1', '陕西省西安火炬路东新世纪广场', '188888148409', '小明', '0', '1', '1', '205室');
INSERT INTO `address` VALUES ('26', '1', '陕西省西安火炬路东新世纪广场', '188888148409', '小明', '0', '1', '1', '205室');
INSERT INTO `address` VALUES ('27', '1', '陕西省西安火炬路东新世纪广场', '188888148409', '小明', '0', '1', '1', '205室');
INSERT INTO `address` VALUES ('28', '1', '陕西省西安火炬路东新世纪广场', '188888148409', '小明', '0', '1', '1', '205室');
INSERT INTO `address` VALUES ('32', '1', '陕西省西安火炬路东新世纪广场', '188888148409', '小明', '0', '1', '1', '205室');
INSERT INTO `address` VALUES ('34', '11', '陕西省西安火炬路东新世纪广场', '18710962367', '大明', '1', '1', '2', '205室');
INSERT INTO `address` VALUES ('37', '1', '陕西省西安火炬路东新世纪广场', '188888148409', '小明', '0', '1', '1', '205室');
INSERT INTO `address` VALUES ('38', '11', '点击选择', '81881213', 'may contain', '0', '1', '1', 'DDD off to the new York');
INSERT INTO `address` VALUES ('39', '6', '普天小区', '18837294050', '关羽', '0', '1', '1', '长安3号');
INSERT INTO `address` VALUES ('40', '6', '普天小区', '17793850692', '赵子龙', '0', '1', '1', '18楼709室');
INSERT INTO `address` VALUES ('41', '6', '普天小区', '82839481', '马超', '0', '1', '1', '1107');
INSERT INTO `address` VALUES ('42', '11', '点击选择', 'read', 'boys', '0', '1', '1', 'boys');
INSERT INTO `address` VALUES ('46', '11', '点击选择', '18710962367', 'boys', '0', '1', '2', 'boys and');

-- ----------------------------
-- Table structure for `admin`
-- ----------------------------
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) NOT NULL COMMENT '店铺id',
  `username` varchar(50) NOT NULL COMMENT '用户名',
  `password` varchar(32) NOT NULL COMMENT '密码',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0冻结  1禁用',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1总后台 2商家后台',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of admin
-- ----------------------------
INSERT INTO `admin` VALUES ('1', '0', 'admin', 'e10adc3949ba59abbe56e057f20f883e', '1', '1');
INSERT INTO `admin` VALUES ('9', '3', 'xiaozhang', '25d55ad283aa400af464c76d713c07ad', '1', '2');

-- ----------------------------
-- Table structure for `area`
-- ----------------------------
DROP TABLE IF EXISTS `area`;
CREATE TABLE `area` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `this_id` int(11) NOT NULL,
  `name` varchar(20) CHARACTER SET gbk NOT NULL,
  `pid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3145 DEFAULT CHARSET=gb2312;

-- ----------------------------
-- Records of area
-- ----------------------------
INSERT INTO `area` VALUES ('1', '110101', '东城区', '110100');
INSERT INTO `area` VALUES ('2', '110102', '西城区', '110100');
INSERT INTO `area` VALUES ('3', '110103', '崇文区', '110100');
INSERT INTO `area` VALUES ('4', '110104', '宣武区', '110100');
INSERT INTO `area` VALUES ('5', '110105', '朝阳区', '110100');
INSERT INTO `area` VALUES ('6', '110106', '丰台区', '110100');
INSERT INTO `area` VALUES ('7', '110107', '石景山区', '110100');
INSERT INTO `area` VALUES ('8', '110108', '海淀区', '110100');
INSERT INTO `area` VALUES ('9', '110109', '门头沟区', '110100');
INSERT INTO `area` VALUES ('10', '110111', '房山区', '110100');
INSERT INTO `area` VALUES ('11', '110112', '通州区', '110100');
INSERT INTO `area` VALUES ('12', '110113', '顺义区', '110100');
INSERT INTO `area` VALUES ('13', '110114', '昌平区', '110100');
INSERT INTO `area` VALUES ('14', '110115', '大兴区', '110100');
INSERT INTO `area` VALUES ('15', '110116', '怀柔区', '110100');
INSERT INTO `area` VALUES ('16', '110117', '平谷区', '110100');
INSERT INTO `area` VALUES ('17', '110228', '密云县', '110200');
INSERT INTO `area` VALUES ('18', '110229', '延庆县', '110200');
INSERT INTO `area` VALUES ('19', '120101', '和平区', '120100');
INSERT INTO `area` VALUES ('20', '120102', '河东区', '120100');
INSERT INTO `area` VALUES ('21', '120103', '河西区', '120100');
INSERT INTO `area` VALUES ('22', '120104', '南开区', '120100');
INSERT INTO `area` VALUES ('23', '120105', '河北区', '120100');
INSERT INTO `area` VALUES ('24', '120106', '红桥区', '120100');
INSERT INTO `area` VALUES ('25', '120107', '塘沽区', '120100');
INSERT INTO `area` VALUES ('26', '120108', '汉沽区', '120100');
INSERT INTO `area` VALUES ('27', '120109', '大港区', '120100');
INSERT INTO `area` VALUES ('28', '120110', '东丽区', '120100');
INSERT INTO `area` VALUES ('29', '120111', '西青区', '120100');
INSERT INTO `area` VALUES ('30', '120112', '津南区', '120100');
INSERT INTO `area` VALUES ('31', '120113', '北辰区', '120100');
INSERT INTO `area` VALUES ('32', '120114', '武清区', '120100');
INSERT INTO `area` VALUES ('33', '120115', '宝坻区', '120100');
INSERT INTO `area` VALUES ('34', '120221', '宁河县', '120200');
INSERT INTO `area` VALUES ('35', '120223', '静海县', '120200');
INSERT INTO `area` VALUES ('36', '120225', '蓟　县', '120200');
INSERT INTO `area` VALUES ('37', '130101', '市辖区', '130100');
INSERT INTO `area` VALUES ('38', '130102', '长安区', '130100');
INSERT INTO `area` VALUES ('39', '130103', '桥东区', '130100');
INSERT INTO `area` VALUES ('40', '130104', '桥西区', '130100');
INSERT INTO `area` VALUES ('41', '130105', '新华区', '130100');
INSERT INTO `area` VALUES ('42', '130107', '井陉矿区', '130100');
INSERT INTO `area` VALUES ('43', '130108', '裕华区', '130100');
INSERT INTO `area` VALUES ('44', '130121', '井陉县', '130100');
INSERT INTO `area` VALUES ('45', '130123', '正定县', '130100');
INSERT INTO `area` VALUES ('46', '130124', '栾城县', '130100');
INSERT INTO `area` VALUES ('47', '130125', '行唐县', '130100');
INSERT INTO `area` VALUES ('48', '130126', '灵寿县', '130100');
INSERT INTO `area` VALUES ('49', '130127', '高邑县', '130100');
INSERT INTO `area` VALUES ('50', '130128', '深泽县', '130100');
INSERT INTO `area` VALUES ('51', '130129', '赞皇县', '130100');
INSERT INTO `area` VALUES ('52', '130130', '无极县', '130100');
INSERT INTO `area` VALUES ('53', '130131', '平山县', '130100');
INSERT INTO `area` VALUES ('54', '130132', '元氏县', '130100');
INSERT INTO `area` VALUES ('55', '130133', '赵　县', '130100');
INSERT INTO `area` VALUES ('56', '130181', '辛集市', '130100');
INSERT INTO `area` VALUES ('57', '130182', '藁城市', '130100');
INSERT INTO `area` VALUES ('58', '130183', '晋州市', '130100');
INSERT INTO `area` VALUES ('59', '130184', '新乐市', '130100');
INSERT INTO `area` VALUES ('60', '130185', '鹿泉市', '130100');
INSERT INTO `area` VALUES ('61', '130201', '市辖区', '130200');
INSERT INTO `area` VALUES ('62', '130202', '路南区', '130200');
INSERT INTO `area` VALUES ('63', '130203', '路北区', '130200');
INSERT INTO `area` VALUES ('64', '130204', '古冶区', '130200');
INSERT INTO `area` VALUES ('65', '130205', '开平区', '130200');
INSERT INTO `area` VALUES ('66', '130207', '丰南区', '130200');
INSERT INTO `area` VALUES ('67', '130208', '丰润区', '130200');
INSERT INTO `area` VALUES ('68', '130223', '滦　县', '130200');
INSERT INTO `area` VALUES ('69', '130224', '滦南县', '130200');
INSERT INTO `area` VALUES ('70', '130225', '乐亭县', '130200');
INSERT INTO `area` VALUES ('71', '130227', '迁西县', '130200');
INSERT INTO `area` VALUES ('72', '130229', '玉田县', '130200');
INSERT INTO `area` VALUES ('73', '130230', '唐海县', '130200');
INSERT INTO `area` VALUES ('74', '130281', '遵化市', '130200');
INSERT INTO `area` VALUES ('75', '130283', '迁安市', '130200');
INSERT INTO `area` VALUES ('76', '130301', '市辖区', '130300');
INSERT INTO `area` VALUES ('77', '130302', '海港区', '130300');
INSERT INTO `area` VALUES ('78', '130303', '山海关区', '130300');
INSERT INTO `area` VALUES ('79', '130304', '北戴河区', '130300');
INSERT INTO `area` VALUES ('80', '130321', '青龙满族自治县', '130300');
INSERT INTO `area` VALUES ('81', '130322', '昌黎县', '130300');
INSERT INTO `area` VALUES ('82', '130323', '抚宁县', '130300');
INSERT INTO `area` VALUES ('83', '130324', '卢龙县', '130300');
INSERT INTO `area` VALUES ('84', '130401', '市辖区', '130400');
INSERT INTO `area` VALUES ('85', '130402', '邯山区', '130400');
INSERT INTO `area` VALUES ('86', '130403', '丛台区', '130400');
INSERT INTO `area` VALUES ('87', '130404', '复兴区', '130400');
INSERT INTO `area` VALUES ('88', '130406', '峰峰矿区', '130400');
INSERT INTO `area` VALUES ('89', '130421', '邯郸县', '130400');
INSERT INTO `area` VALUES ('90', '130423', '临漳县', '130400');
INSERT INTO `area` VALUES ('91', '130424', '成安县', '130400');
INSERT INTO `area` VALUES ('92', '130425', '大名县', '130400');
INSERT INTO `area` VALUES ('93', '130426', '涉　县', '130400');
INSERT INTO `area` VALUES ('94', '130427', '磁　县', '130400');
INSERT INTO `area` VALUES ('95', '130428', '肥乡县', '130400');
INSERT INTO `area` VALUES ('96', '130429', '永年县', '130400');
INSERT INTO `area` VALUES ('97', '130430', '邱　县', '130400');
INSERT INTO `area` VALUES ('98', '130431', '鸡泽县', '130400');
INSERT INTO `area` VALUES ('99', '130432', '广平县', '130400');
INSERT INTO `area` VALUES ('100', '130433', '馆陶县', '130400');
INSERT INTO `area` VALUES ('101', '130434', '魏　县', '130400');
INSERT INTO `area` VALUES ('102', '130435', '曲周县', '130400');
INSERT INTO `area` VALUES ('103', '130481', '武安市', '130400');
INSERT INTO `area` VALUES ('104', '130501', '市辖区', '130500');
INSERT INTO `area` VALUES ('105', '130502', '桥东区', '130500');
INSERT INTO `area` VALUES ('106', '130503', '桥西区', '130500');
INSERT INTO `area` VALUES ('107', '130521', '邢台县', '130500');
INSERT INTO `area` VALUES ('108', '130522', '临城县', '130500');
INSERT INTO `area` VALUES ('109', '130523', '内丘县', '130500');
INSERT INTO `area` VALUES ('110', '130524', '柏乡县', '130500');
INSERT INTO `area` VALUES ('111', '130525', '隆尧县', '130500');
INSERT INTO `area` VALUES ('112', '130526', '任　县', '130500');
INSERT INTO `area` VALUES ('113', '130527', '南和县', '130500');
INSERT INTO `area` VALUES ('114', '130528', '宁晋县', '130500');
INSERT INTO `area` VALUES ('115', '130529', '巨鹿县', '130500');
INSERT INTO `area` VALUES ('116', '130530', '新河县', '130500');
INSERT INTO `area` VALUES ('117', '130531', '广宗县', '130500');
INSERT INTO `area` VALUES ('118', '130532', '平乡县', '130500');
INSERT INTO `area` VALUES ('119', '130533', '威　县', '130500');
INSERT INTO `area` VALUES ('120', '130534', '清河县', '130500');
INSERT INTO `area` VALUES ('121', '130535', '临西县', '130500');
INSERT INTO `area` VALUES ('122', '130581', '南宫市', '130500');
INSERT INTO `area` VALUES ('123', '130582', '沙河市', '130500');
INSERT INTO `area` VALUES ('124', '130601', '市辖区', '130600');
INSERT INTO `area` VALUES ('125', '130602', '新市区', '130600');
INSERT INTO `area` VALUES ('126', '130603', '北市区', '130600');
INSERT INTO `area` VALUES ('127', '130604', '南市区', '130600');
INSERT INTO `area` VALUES ('128', '130621', '满城县', '130600');
INSERT INTO `area` VALUES ('129', '130622', '清苑县', '130600');
INSERT INTO `area` VALUES ('130', '130623', '涞水县', '130600');
INSERT INTO `area` VALUES ('131', '130624', '阜平县', '130600');
INSERT INTO `area` VALUES ('132', '130625', '徐水县', '130600');
INSERT INTO `area` VALUES ('133', '130626', '定兴县', '130600');
INSERT INTO `area` VALUES ('134', '130627', '唐　县', '130600');
INSERT INTO `area` VALUES ('135', '130628', '高阳县', '130600');
INSERT INTO `area` VALUES ('136', '130629', '容城县', '130600');
INSERT INTO `area` VALUES ('137', '130630', '涞源县', '130600');
INSERT INTO `area` VALUES ('138', '130631', '望都县', '130600');
INSERT INTO `area` VALUES ('139', '130632', '安新县', '130600');
INSERT INTO `area` VALUES ('140', '130633', '易　县', '130600');
INSERT INTO `area` VALUES ('141', '130634', '曲阳县', '130600');
INSERT INTO `area` VALUES ('142', '130635', '蠡　县', '130600');
INSERT INTO `area` VALUES ('143', '130636', '顺平县', '130600');
INSERT INTO `area` VALUES ('144', '130637', '博野县', '130600');
INSERT INTO `area` VALUES ('145', '130638', '雄　县', '130600');
INSERT INTO `area` VALUES ('146', '130681', '涿州市', '130600');
INSERT INTO `area` VALUES ('147', '130682', '定州市', '130600');
INSERT INTO `area` VALUES ('148', '130683', '安国市', '130600');
INSERT INTO `area` VALUES ('149', '130684', '高碑店市', '130600');
INSERT INTO `area` VALUES ('150', '130701', '市辖区', '130700');
INSERT INTO `area` VALUES ('151', '130702', '桥东区', '130700');
INSERT INTO `area` VALUES ('152', '130703', '桥西区', '130700');
INSERT INTO `area` VALUES ('153', '130705', '宣化区', '130700');
INSERT INTO `area` VALUES ('154', '130706', '下花园区', '130700');
INSERT INTO `area` VALUES ('155', '130721', '宣化县', '130700');
INSERT INTO `area` VALUES ('156', '130722', '张北县', '130700');
INSERT INTO `area` VALUES ('157', '130723', '康保县', '130700');
INSERT INTO `area` VALUES ('158', '130724', '沽源县', '130700');
INSERT INTO `area` VALUES ('159', '130725', '尚义县', '130700');
INSERT INTO `area` VALUES ('160', '130726', '蔚　县', '130700');
INSERT INTO `area` VALUES ('161', '130727', '阳原县', '130700');
INSERT INTO `area` VALUES ('162', '130728', '怀安县', '130700');
INSERT INTO `area` VALUES ('163', '130729', '万全县', '130700');
INSERT INTO `area` VALUES ('164', '130730', '怀来县', '130700');
INSERT INTO `area` VALUES ('165', '130731', '涿鹿县', '130700');
INSERT INTO `area` VALUES ('166', '130732', '赤城县', '130700');
INSERT INTO `area` VALUES ('167', '130733', '崇礼县', '130700');
INSERT INTO `area` VALUES ('168', '130801', '市辖区', '130800');
INSERT INTO `area` VALUES ('169', '130802', '双桥区', '130800');
INSERT INTO `area` VALUES ('170', '130803', '双滦区', '130800');
INSERT INTO `area` VALUES ('171', '130804', '鹰手营子矿区', '130800');
INSERT INTO `area` VALUES ('172', '130821', '承德县', '130800');
INSERT INTO `area` VALUES ('173', '130822', '兴隆县', '130800');
INSERT INTO `area` VALUES ('174', '130823', '平泉县', '130800');
INSERT INTO `area` VALUES ('175', '130824', '滦平县', '130800');
INSERT INTO `area` VALUES ('176', '130825', '隆化县', '130800');
INSERT INTO `area` VALUES ('177', '130826', '丰宁满族自治县', '130800');
INSERT INTO `area` VALUES ('178', '130827', '宽城满族自治县', '130800');
INSERT INTO `area` VALUES ('179', '130828', '围场满族蒙古族自治县', '130800');
INSERT INTO `area` VALUES ('180', '130901', '市辖区', '130900');
INSERT INTO `area` VALUES ('181', '130902', '新华区', '130900');
INSERT INTO `area` VALUES ('182', '130903', '运河区', '130900');
INSERT INTO `area` VALUES ('183', '130921', '沧　县', '130900');
INSERT INTO `area` VALUES ('184', '130922', '青　县', '130900');
INSERT INTO `area` VALUES ('185', '130923', '东光县', '130900');
INSERT INTO `area` VALUES ('186', '130924', '海兴县', '130900');
INSERT INTO `area` VALUES ('187', '130925', '盐山县', '130900');
INSERT INTO `area` VALUES ('188', '130926', '肃宁县', '130900');
INSERT INTO `area` VALUES ('189', '130927', '南皮县', '130900');
INSERT INTO `area` VALUES ('190', '130928', '吴桥县', '130900');
INSERT INTO `area` VALUES ('191', '130929', '献　县', '130900');
INSERT INTO `area` VALUES ('192', '130930', '孟村回族自治县', '130900');
INSERT INTO `area` VALUES ('193', '130981', '泊头市', '130900');
INSERT INTO `area` VALUES ('194', '130982', '任丘市', '130900');
INSERT INTO `area` VALUES ('195', '130983', '黄骅市', '130900');
INSERT INTO `area` VALUES ('196', '130984', '河间市', '130900');
INSERT INTO `area` VALUES ('197', '131001', '市辖区', '131000');
INSERT INTO `area` VALUES ('198', '131002', '安次区', '131000');
INSERT INTO `area` VALUES ('199', '131003', '广阳区', '131000');
INSERT INTO `area` VALUES ('200', '131022', '固安县', '131000');
INSERT INTO `area` VALUES ('201', '131023', '永清县', '131000');
INSERT INTO `area` VALUES ('202', '131024', '香河县', '131000');
INSERT INTO `area` VALUES ('203', '131025', '大城县', '131000');
INSERT INTO `area` VALUES ('204', '131026', '文安县', '131000');
INSERT INTO `area` VALUES ('205', '131028', '大厂回族自治县', '131000');
INSERT INTO `area` VALUES ('206', '131081', '霸州市', '131000');
INSERT INTO `area` VALUES ('207', '131082', '三河市', '131000');
INSERT INTO `area` VALUES ('208', '131101', '市辖区', '131100');
INSERT INTO `area` VALUES ('209', '131102', '桃城区', '131100');
INSERT INTO `area` VALUES ('210', '131121', '枣强县', '131100');
INSERT INTO `area` VALUES ('211', '131122', '武邑县', '131100');
INSERT INTO `area` VALUES ('212', '131123', '武强县', '131100');
INSERT INTO `area` VALUES ('213', '131124', '饶阳县', '131100');
INSERT INTO `area` VALUES ('214', '131125', '安平县', '131100');
INSERT INTO `area` VALUES ('215', '131126', '故城县', '131100');
INSERT INTO `area` VALUES ('216', '131127', '景　县', '131100');
INSERT INTO `area` VALUES ('217', '131128', '阜城县', '131100');
INSERT INTO `area` VALUES ('218', '131181', '冀州市', '131100');
INSERT INTO `area` VALUES ('219', '131182', '深州市', '131100');
INSERT INTO `area` VALUES ('220', '140101', '市辖区', '140100');
INSERT INTO `area` VALUES ('221', '140105', '小店区', '140100');
INSERT INTO `area` VALUES ('222', '140106', '迎泽区', '140100');
INSERT INTO `area` VALUES ('223', '140107', '杏花岭区', '140100');
INSERT INTO `area` VALUES ('224', '140108', '尖草坪区', '140100');
INSERT INTO `area` VALUES ('225', '140109', '万柏林区', '140100');
INSERT INTO `area` VALUES ('226', '140110', '晋源区', '140100');
INSERT INTO `area` VALUES ('227', '140121', '清徐县', '140100');
INSERT INTO `area` VALUES ('228', '140122', '阳曲县', '140100');
INSERT INTO `area` VALUES ('229', '140123', '娄烦县', '140100');
INSERT INTO `area` VALUES ('230', '140181', '古交市', '140100');
INSERT INTO `area` VALUES ('231', '140201', '市辖区', '140200');
INSERT INTO `area` VALUES ('232', '140202', '城　区', '140200');
INSERT INTO `area` VALUES ('233', '140203', '矿　区', '140200');
INSERT INTO `area` VALUES ('234', '140211', '南郊区', '140200');
INSERT INTO `area` VALUES ('235', '140212', '新荣区', '140200');
INSERT INTO `area` VALUES ('236', '140221', '阳高县', '140200');
INSERT INTO `area` VALUES ('237', '140222', '天镇县', '140200');
INSERT INTO `area` VALUES ('238', '140223', '广灵县', '140200');
INSERT INTO `area` VALUES ('239', '140224', '灵丘县', '140200');
INSERT INTO `area` VALUES ('240', '140225', '浑源县', '140200');
INSERT INTO `area` VALUES ('241', '140226', '左云县', '140200');
INSERT INTO `area` VALUES ('242', '140227', '大同县', '140200');
INSERT INTO `area` VALUES ('243', '140301', '市辖区', '140300');
INSERT INTO `area` VALUES ('244', '140302', '城　区', '140300');
INSERT INTO `area` VALUES ('245', '140303', '矿　区', '140300');
INSERT INTO `area` VALUES ('246', '140311', '郊　区', '140300');
INSERT INTO `area` VALUES ('247', '140321', '平定县', '140300');
INSERT INTO `area` VALUES ('248', '140322', '盂　县', '140300');
INSERT INTO `area` VALUES ('249', '140401', '市辖区', '140400');
INSERT INTO `area` VALUES ('250', '140402', '城　区', '140400');
INSERT INTO `area` VALUES ('251', '140411', '郊　区', '140400');
INSERT INTO `area` VALUES ('252', '140421', '长治县', '140400');
INSERT INTO `area` VALUES ('253', '140423', '襄垣县', '140400');
INSERT INTO `area` VALUES ('254', '140424', '屯留县', '140400');
INSERT INTO `area` VALUES ('255', '140425', '平顺县', '140400');
INSERT INTO `area` VALUES ('256', '140426', '黎城县', '140400');
INSERT INTO `area` VALUES ('257', '140427', '壶关县', '140400');
INSERT INTO `area` VALUES ('258', '140428', '长子县', '140400');
INSERT INTO `area` VALUES ('259', '140429', '武乡县', '140400');
INSERT INTO `area` VALUES ('260', '140430', '沁　县', '140400');
INSERT INTO `area` VALUES ('261', '140431', '沁源县', '140400');
INSERT INTO `area` VALUES ('262', '140481', '潞城市', '140400');
INSERT INTO `area` VALUES ('263', '140501', '市辖区', '140500');
INSERT INTO `area` VALUES ('264', '140502', '城　区', '140500');
INSERT INTO `area` VALUES ('265', '140521', '沁水县', '140500');
INSERT INTO `area` VALUES ('266', '140522', '阳城县', '140500');
INSERT INTO `area` VALUES ('267', '140524', '陵川县', '140500');
INSERT INTO `area` VALUES ('268', '140525', '泽州县', '140500');
INSERT INTO `area` VALUES ('269', '140581', '高平市', '140500');
INSERT INTO `area` VALUES ('270', '140601', '市辖区', '140600');
INSERT INTO `area` VALUES ('271', '140602', '朔城区', '140600');
INSERT INTO `area` VALUES ('272', '140603', '平鲁区', '140600');
INSERT INTO `area` VALUES ('273', '140621', '山阴县', '140600');
INSERT INTO `area` VALUES ('274', '140622', '应　县', '140600');
INSERT INTO `area` VALUES ('275', '140623', '右玉县', '140600');
INSERT INTO `area` VALUES ('276', '140624', '怀仁县', '140600');
INSERT INTO `area` VALUES ('277', '140701', '市辖区', '140700');
INSERT INTO `area` VALUES ('278', '140702', '榆次区', '140700');
INSERT INTO `area` VALUES ('279', '140721', '榆社县', '140700');
INSERT INTO `area` VALUES ('280', '140722', '左权县', '140700');
INSERT INTO `area` VALUES ('281', '140723', '和顺县', '140700');
INSERT INTO `area` VALUES ('282', '140724', '昔阳县', '140700');
INSERT INTO `area` VALUES ('283', '140725', '寿阳县', '140700');
INSERT INTO `area` VALUES ('284', '140726', '太谷县', '140700');
INSERT INTO `area` VALUES ('285', '140727', '祁　县', '140700');
INSERT INTO `area` VALUES ('286', '140728', '平遥县', '140700');
INSERT INTO `area` VALUES ('287', '140729', '灵石县', '140700');
INSERT INTO `area` VALUES ('288', '140781', '介休市', '140700');
INSERT INTO `area` VALUES ('289', '140801', '市辖区', '140800');
INSERT INTO `area` VALUES ('290', '140802', '盐湖区', '140800');
INSERT INTO `area` VALUES ('291', '140821', '临猗县', '140800');
INSERT INTO `area` VALUES ('292', '140822', '万荣县', '140800');
INSERT INTO `area` VALUES ('293', '140823', '闻喜县', '140800');
INSERT INTO `area` VALUES ('294', '140824', '稷山县', '140800');
INSERT INTO `area` VALUES ('295', '140825', '新绛县', '140800');
INSERT INTO `area` VALUES ('296', '140826', '绛　县', '140800');
INSERT INTO `area` VALUES ('297', '140827', '垣曲县', '140800');
INSERT INTO `area` VALUES ('298', '140828', '夏　县', '140800');
INSERT INTO `area` VALUES ('299', '140829', '平陆县', '140800');
INSERT INTO `area` VALUES ('300', '140830', '芮城县', '140800');
INSERT INTO `area` VALUES ('301', '140881', '永济市', '140800');
INSERT INTO `area` VALUES ('302', '140882', '河津市', '140800');
INSERT INTO `area` VALUES ('303', '140901', '市辖区', '140900');
INSERT INTO `area` VALUES ('304', '140902', '忻府区', '140900');
INSERT INTO `area` VALUES ('305', '140921', '定襄县', '140900');
INSERT INTO `area` VALUES ('306', '140922', '五台县', '140900');
INSERT INTO `area` VALUES ('307', '140923', '代　县', '140900');
INSERT INTO `area` VALUES ('308', '140924', '繁峙县', '140900');
INSERT INTO `area` VALUES ('309', '140925', '宁武县', '140900');
INSERT INTO `area` VALUES ('310', '140926', '静乐县', '140900');
INSERT INTO `area` VALUES ('311', '140927', '神池县', '140900');
INSERT INTO `area` VALUES ('312', '140928', '五寨县', '140900');
INSERT INTO `area` VALUES ('313', '140929', '岢岚县', '140900');
INSERT INTO `area` VALUES ('314', '140930', '河曲县', '140900');
INSERT INTO `area` VALUES ('315', '140931', '保德县', '140900');
INSERT INTO `area` VALUES ('316', '140932', '偏关县', '140900');
INSERT INTO `area` VALUES ('317', '140981', '原平市', '140900');
INSERT INTO `area` VALUES ('318', '141001', '市辖区', '141000');
INSERT INTO `area` VALUES ('319', '141002', '尧都区', '141000');
INSERT INTO `area` VALUES ('320', '141021', '曲沃县', '141000');
INSERT INTO `area` VALUES ('321', '141022', '翼城县', '141000');
INSERT INTO `area` VALUES ('322', '141023', '襄汾县', '141000');
INSERT INTO `area` VALUES ('323', '141024', '洪洞县', '141000');
INSERT INTO `area` VALUES ('324', '141025', '古　县', '141000');
INSERT INTO `area` VALUES ('325', '141026', '安泽县', '141000');
INSERT INTO `area` VALUES ('326', '141027', '浮山县', '141000');
INSERT INTO `area` VALUES ('327', '141028', '吉　县', '141000');
INSERT INTO `area` VALUES ('328', '141029', '乡宁县', '141000');
INSERT INTO `area` VALUES ('329', '141030', '大宁县', '141000');
INSERT INTO `area` VALUES ('330', '141031', '隰　县', '141000');
INSERT INTO `area` VALUES ('331', '141032', '永和县', '141000');
INSERT INTO `area` VALUES ('332', '141033', '蒲　县', '141000');
INSERT INTO `area` VALUES ('333', '141034', '汾西县', '141000');
INSERT INTO `area` VALUES ('334', '141081', '侯马市', '141000');
INSERT INTO `area` VALUES ('335', '141082', '霍州市', '141000');
INSERT INTO `area` VALUES ('336', '141101', '市辖区', '141100');
INSERT INTO `area` VALUES ('337', '141102', '离石区', '141100');
INSERT INTO `area` VALUES ('338', '141121', '文水县', '141100');
INSERT INTO `area` VALUES ('339', '141122', '交城县', '141100');
INSERT INTO `area` VALUES ('340', '141123', '兴　县', '141100');
INSERT INTO `area` VALUES ('341', '141124', '临　县', '141100');
INSERT INTO `area` VALUES ('342', '141125', '柳林县', '141100');
INSERT INTO `area` VALUES ('343', '141126', '石楼县', '141100');
INSERT INTO `area` VALUES ('344', '141127', '岚　县', '141100');
INSERT INTO `area` VALUES ('345', '141128', '方山县', '141100');
INSERT INTO `area` VALUES ('346', '141129', '中阳县', '141100');
INSERT INTO `area` VALUES ('347', '141130', '交口县', '141100');
INSERT INTO `area` VALUES ('348', '141181', '孝义市', '141100');
INSERT INTO `area` VALUES ('349', '141182', '汾阳市', '141100');
INSERT INTO `area` VALUES ('350', '150101', '市辖区', '150100');
INSERT INTO `area` VALUES ('351', '150102', '新城区', '150100');
INSERT INTO `area` VALUES ('352', '150103', '回民区', '150100');
INSERT INTO `area` VALUES ('353', '150104', '玉泉区', '150100');
INSERT INTO `area` VALUES ('354', '150105', '赛罕区', '150100');
INSERT INTO `area` VALUES ('355', '150121', '土默特左旗', '150100');
INSERT INTO `area` VALUES ('356', '150122', '托克托县', '150100');
INSERT INTO `area` VALUES ('357', '150123', '和林格尔县', '150100');
INSERT INTO `area` VALUES ('358', '150124', '清水河县', '150100');
INSERT INTO `area` VALUES ('359', '150125', '武川县', '150100');
INSERT INTO `area` VALUES ('360', '150201', '市辖区', '150200');
INSERT INTO `area` VALUES ('361', '150202', '东河区', '150200');
INSERT INTO `area` VALUES ('362', '150203', '昆都仑区', '150200');
INSERT INTO `area` VALUES ('363', '150204', '青山区', '150200');
INSERT INTO `area` VALUES ('364', '150205', '石拐区', '150200');
INSERT INTO `area` VALUES ('365', '150206', '白云矿区', '150200');
INSERT INTO `area` VALUES ('366', '150207', '九原区', '150200');
INSERT INTO `area` VALUES ('367', '150221', '土默特右旗', '150200');
INSERT INTO `area` VALUES ('368', '150222', '固阳县', '150200');
INSERT INTO `area` VALUES ('369', '150223', '达尔罕茂明安联合旗', '150200');
INSERT INTO `area` VALUES ('370', '150301', '市辖区', '150300');
INSERT INTO `area` VALUES ('371', '150302', '海勃湾区', '150300');
INSERT INTO `area` VALUES ('372', '150303', '海南区', '150300');
INSERT INTO `area` VALUES ('373', '150304', '乌达区', '150300');
INSERT INTO `area` VALUES ('374', '150401', '市辖区', '150400');
INSERT INTO `area` VALUES ('375', '150402', '红山区', '150400');
INSERT INTO `area` VALUES ('376', '150403', '元宝山区', '150400');
INSERT INTO `area` VALUES ('377', '150404', '松山区', '150400');
INSERT INTO `area` VALUES ('378', '150421', '阿鲁科尔沁旗', '150400');
INSERT INTO `area` VALUES ('379', '150422', '巴林左旗', '150400');
INSERT INTO `area` VALUES ('380', '150423', '巴林右旗', '150400');
INSERT INTO `area` VALUES ('381', '150424', '林西县', '150400');
INSERT INTO `area` VALUES ('382', '150425', '克什克腾旗', '150400');
INSERT INTO `area` VALUES ('383', '150426', '翁牛特旗', '150400');
INSERT INTO `area` VALUES ('384', '150428', '喀喇沁旗', '150400');
INSERT INTO `area` VALUES ('385', '150429', '宁城县', '150400');
INSERT INTO `area` VALUES ('386', '150430', '敖汉旗', '150400');
INSERT INTO `area` VALUES ('387', '150501', '市辖区', '150500');
INSERT INTO `area` VALUES ('388', '150502', '科尔沁区', '150500');
INSERT INTO `area` VALUES ('389', '150521', '科尔沁左翼中旗', '150500');
INSERT INTO `area` VALUES ('390', '150522', '科尔沁左翼后旗', '150500');
INSERT INTO `area` VALUES ('391', '150523', '开鲁县', '150500');
INSERT INTO `area` VALUES ('392', '150524', '库伦旗', '150500');
INSERT INTO `area` VALUES ('393', '150525', '奈曼旗', '150500');
INSERT INTO `area` VALUES ('394', '150526', '扎鲁特旗', '150500');
INSERT INTO `area` VALUES ('395', '150581', '霍林郭勒市', '150500');
INSERT INTO `area` VALUES ('396', '150602', '东胜区', '150600');
INSERT INTO `area` VALUES ('397', '150621', '达拉特旗', '150600');
INSERT INTO `area` VALUES ('398', '150622', '准格尔旗', '150600');
INSERT INTO `area` VALUES ('399', '150623', '鄂托克前旗', '150600');
INSERT INTO `area` VALUES ('400', '150624', '鄂托克旗', '150600');
INSERT INTO `area` VALUES ('401', '150625', '杭锦旗', '150600');
INSERT INTO `area` VALUES ('402', '150626', '乌审旗', '150600');
INSERT INTO `area` VALUES ('403', '150627', '伊金霍洛旗', '150600');
INSERT INTO `area` VALUES ('404', '150701', '市辖区', '150700');
INSERT INTO `area` VALUES ('405', '150702', '海拉尔区', '150700');
INSERT INTO `area` VALUES ('406', '150721', '阿荣旗', '150700');
INSERT INTO `area` VALUES ('407', '150722', '莫力达瓦达斡尔族自治旗', '150700');
INSERT INTO `area` VALUES ('408', '150723', '鄂伦春自治旗', '150700');
INSERT INTO `area` VALUES ('409', '150724', '鄂温克族自治旗', '150700');
INSERT INTO `area` VALUES ('410', '150725', '陈巴尔虎旗', '150700');
INSERT INTO `area` VALUES ('411', '150726', '新巴尔虎左旗', '150700');
INSERT INTO `area` VALUES ('412', '150727', '新巴尔虎右旗', '150700');
INSERT INTO `area` VALUES ('413', '150781', '满洲里市', '150700');
INSERT INTO `area` VALUES ('414', '150782', '牙克石市', '150700');
INSERT INTO `area` VALUES ('415', '150783', '扎兰屯市', '150700');
INSERT INTO `area` VALUES ('416', '150784', '额尔古纳市', '150700');
INSERT INTO `area` VALUES ('417', '150785', '根河市', '150700');
INSERT INTO `area` VALUES ('418', '150801', '市辖区', '150800');
INSERT INTO `area` VALUES ('419', '150802', '临河区', '150800');
INSERT INTO `area` VALUES ('420', '150821', '五原县', '150800');
INSERT INTO `area` VALUES ('421', '150822', '磴口县', '150800');
INSERT INTO `area` VALUES ('422', '150823', '乌拉特前旗', '150800');
INSERT INTO `area` VALUES ('423', '150824', '乌拉特中旗', '150800');
INSERT INTO `area` VALUES ('424', '150825', '乌拉特后旗', '150800');
INSERT INTO `area` VALUES ('425', '150826', '杭锦后旗', '150800');
INSERT INTO `area` VALUES ('426', '150901', '市辖区', '150900');
INSERT INTO `area` VALUES ('427', '150902', '集宁区', '150900');
INSERT INTO `area` VALUES ('428', '150921', '卓资县', '150900');
INSERT INTO `area` VALUES ('429', '150922', '化德县', '150900');
INSERT INTO `area` VALUES ('430', '150923', '商都县', '150900');
INSERT INTO `area` VALUES ('431', '150924', '兴和县', '150900');
INSERT INTO `area` VALUES ('432', '150925', '凉城县', '150900');
INSERT INTO `area` VALUES ('433', '150926', '察哈尔右翼前旗', '150900');
INSERT INTO `area` VALUES ('434', '150927', '察哈尔右翼中旗', '150900');
INSERT INTO `area` VALUES ('435', '150928', '察哈尔右翼后旗', '150900');
INSERT INTO `area` VALUES ('436', '150929', '四子王旗', '150900');
INSERT INTO `area` VALUES ('437', '150981', '丰镇市', '150900');
INSERT INTO `area` VALUES ('438', '152201', '乌兰浩特市', '152200');
INSERT INTO `area` VALUES ('439', '152202', '阿尔山市', '152200');
INSERT INTO `area` VALUES ('440', '152221', '科尔沁右翼前旗', '152200');
INSERT INTO `area` VALUES ('441', '152222', '科尔沁右翼中旗', '152200');
INSERT INTO `area` VALUES ('442', '152223', '扎赉特旗', '152200');
INSERT INTO `area` VALUES ('443', '152224', '突泉县', '152200');
INSERT INTO `area` VALUES ('444', '152501', '二连浩特市', '152500');
INSERT INTO `area` VALUES ('445', '152502', '锡林浩特市', '152500');
INSERT INTO `area` VALUES ('446', '152522', '阿巴嘎旗', '152500');
INSERT INTO `area` VALUES ('447', '152523', '苏尼特左旗', '152500');
INSERT INTO `area` VALUES ('448', '152524', '苏尼特右旗', '152500');
INSERT INTO `area` VALUES ('449', '152525', '东乌珠穆沁旗', '152500');
INSERT INTO `area` VALUES ('450', '152526', '西乌珠穆沁旗', '152500');
INSERT INTO `area` VALUES ('451', '152527', '太仆寺旗', '152500');
INSERT INTO `area` VALUES ('452', '152528', '镶黄旗', '152500');
INSERT INTO `area` VALUES ('453', '152529', '正镶白旗', '152500');
INSERT INTO `area` VALUES ('454', '152530', '正蓝旗', '152500');
INSERT INTO `area` VALUES ('455', '152531', '多伦县', '152500');
INSERT INTO `area` VALUES ('456', '152921', '阿拉善左旗', '152900');
INSERT INTO `area` VALUES ('457', '152922', '阿拉善右旗', '152900');
INSERT INTO `area` VALUES ('458', '152923', '额济纳旗', '152900');
INSERT INTO `area` VALUES ('459', '210101', '市辖区', '210100');
INSERT INTO `area` VALUES ('460', '210102', '和平区', '210100');
INSERT INTO `area` VALUES ('461', '210103', '沈河区', '210100');
INSERT INTO `area` VALUES ('462', '210104', '大东区', '210100');
INSERT INTO `area` VALUES ('463', '210105', '皇姑区', '210100');
INSERT INTO `area` VALUES ('464', '210106', '铁西区', '210100');
INSERT INTO `area` VALUES ('465', '210111', '苏家屯区', '210100');
INSERT INTO `area` VALUES ('466', '210112', '东陵区', '210100');
INSERT INTO `area` VALUES ('467', '210113', '新城子区', '210100');
INSERT INTO `area` VALUES ('468', '210114', '于洪区', '210100');
INSERT INTO `area` VALUES ('469', '210122', '辽中县', '210100');
INSERT INTO `area` VALUES ('470', '210123', '康平县', '210100');
INSERT INTO `area` VALUES ('471', '210124', '法库县', '210100');
INSERT INTO `area` VALUES ('472', '210181', '新民市', '210100');
INSERT INTO `area` VALUES ('473', '210201', '市辖区', '210200');
INSERT INTO `area` VALUES ('474', '210202', '中山区', '210200');
INSERT INTO `area` VALUES ('475', '210203', '西岗区', '210200');
INSERT INTO `area` VALUES ('476', '210204', '沙河口区', '210200');
INSERT INTO `area` VALUES ('477', '210211', '甘井子区', '210200');
INSERT INTO `area` VALUES ('478', '210212', '旅顺口区', '210200');
INSERT INTO `area` VALUES ('479', '210213', '金州区', '210200');
INSERT INTO `area` VALUES ('480', '210224', '长海县', '210200');
INSERT INTO `area` VALUES ('481', '210281', '瓦房店市', '210200');
INSERT INTO `area` VALUES ('482', '210282', '普兰店市', '210200');
INSERT INTO `area` VALUES ('483', '210283', '庄河市', '210200');
INSERT INTO `area` VALUES ('484', '210301', '市辖区', '210300');
INSERT INTO `area` VALUES ('485', '210302', '铁东区', '210300');
INSERT INTO `area` VALUES ('486', '210303', '铁西区', '210300');
INSERT INTO `area` VALUES ('487', '210304', '立山区', '210300');
INSERT INTO `area` VALUES ('488', '210311', '千山区', '210300');
INSERT INTO `area` VALUES ('489', '210321', '台安县', '210300');
INSERT INTO `area` VALUES ('490', '210323', '岫岩满族自治县', '210300');
INSERT INTO `area` VALUES ('491', '210381', '海城市', '210300');
INSERT INTO `area` VALUES ('492', '210401', '市辖区', '210400');
INSERT INTO `area` VALUES ('493', '210402', '新抚区', '210400');
INSERT INTO `area` VALUES ('494', '210403', '东洲区', '210400');
INSERT INTO `area` VALUES ('495', '210404', '望花区', '210400');
INSERT INTO `area` VALUES ('496', '210411', '顺城区', '210400');
INSERT INTO `area` VALUES ('497', '210421', '抚顺县', '210400');
INSERT INTO `area` VALUES ('498', '210422', '新宾满族自治县', '210400');
INSERT INTO `area` VALUES ('499', '210423', '清原满族自治县', '210400');
INSERT INTO `area` VALUES ('500', '210501', '市辖区', '210500');
INSERT INTO `area` VALUES ('501', '210502', '平山区', '210500');
INSERT INTO `area` VALUES ('502', '210503', '溪湖区', '210500');
INSERT INTO `area` VALUES ('503', '210504', '明山区', '210500');
INSERT INTO `area` VALUES ('504', '210505', '南芬区', '210500');
INSERT INTO `area` VALUES ('505', '210521', '本溪满族自治县', '210500');
INSERT INTO `area` VALUES ('506', '210522', '桓仁满族自治县', '210500');
INSERT INTO `area` VALUES ('507', '210601', '市辖区', '210600');
INSERT INTO `area` VALUES ('508', '210602', '元宝区', '210600');
INSERT INTO `area` VALUES ('509', '210603', '振兴区', '210600');
INSERT INTO `area` VALUES ('510', '210604', '振安区', '210600');
INSERT INTO `area` VALUES ('511', '210624', '宽甸满族自治县', '210600');
INSERT INTO `area` VALUES ('512', '210681', '东港市', '210600');
INSERT INTO `area` VALUES ('513', '210682', '凤城市', '210600');
INSERT INTO `area` VALUES ('514', '210701', '市辖区', '210700');
INSERT INTO `area` VALUES ('515', '210702', '古塔区', '210700');
INSERT INTO `area` VALUES ('516', '210703', '凌河区', '210700');
INSERT INTO `area` VALUES ('517', '210711', '太和区', '210700');
INSERT INTO `area` VALUES ('518', '210726', '黑山县', '210700');
INSERT INTO `area` VALUES ('519', '210727', '义　县', '210700');
INSERT INTO `area` VALUES ('520', '210781', '凌海市', '210700');
INSERT INTO `area` VALUES ('521', '210782', '北宁市', '210700');
INSERT INTO `area` VALUES ('522', '210801', '市辖区', '210800');
INSERT INTO `area` VALUES ('523', '210802', '站前区', '210800');
INSERT INTO `area` VALUES ('524', '210803', '西市区', '210800');
INSERT INTO `area` VALUES ('525', '210804', '鲅鱼圈区', '210800');
INSERT INTO `area` VALUES ('526', '210811', '老边区', '210800');
INSERT INTO `area` VALUES ('527', '210881', '盖州市', '210800');
INSERT INTO `area` VALUES ('528', '210882', '大石桥市', '210800');
INSERT INTO `area` VALUES ('529', '210901', '市辖区', '210900');
INSERT INTO `area` VALUES ('530', '210902', '海州区', '210900');
INSERT INTO `area` VALUES ('531', '210903', '新邱区', '210900');
INSERT INTO `area` VALUES ('532', '210904', '太平区', '210900');
INSERT INTO `area` VALUES ('533', '210905', '清河门区', '210900');
INSERT INTO `area` VALUES ('534', '210911', '细河区', '210900');
INSERT INTO `area` VALUES ('535', '210921', '阜新蒙古族自治县', '210900');
INSERT INTO `area` VALUES ('536', '210922', '彰武县', '210900');
INSERT INTO `area` VALUES ('537', '211001', '市辖区', '211000');
INSERT INTO `area` VALUES ('538', '211002', '白塔区', '211000');
INSERT INTO `area` VALUES ('539', '211003', '文圣区', '211000');
INSERT INTO `area` VALUES ('540', '211004', '宏伟区', '211000');
INSERT INTO `area` VALUES ('541', '211005', '弓长岭区', '211000');
INSERT INTO `area` VALUES ('542', '211011', '太子河区', '211000');
INSERT INTO `area` VALUES ('543', '211021', '辽阳县', '211000');
INSERT INTO `area` VALUES ('544', '211081', '灯塔市', '211000');
INSERT INTO `area` VALUES ('545', '211101', '市辖区', '211100');
INSERT INTO `area` VALUES ('546', '211102', '双台子区', '211100');
INSERT INTO `area` VALUES ('547', '211103', '兴隆台区', '211100');
INSERT INTO `area` VALUES ('548', '211121', '大洼县', '211100');
INSERT INTO `area` VALUES ('549', '211122', '盘山县', '211100');
INSERT INTO `area` VALUES ('550', '211201', '市辖区', '211200');
INSERT INTO `area` VALUES ('551', '211202', '银州区', '211200');
INSERT INTO `area` VALUES ('552', '211204', '清河区', '211200');
INSERT INTO `area` VALUES ('553', '211221', '铁岭县', '211200');
INSERT INTO `area` VALUES ('554', '211223', '西丰县', '211200');
INSERT INTO `area` VALUES ('555', '211224', '昌图县', '211200');
INSERT INTO `area` VALUES ('556', '211281', '调兵山市', '211200');
INSERT INTO `area` VALUES ('557', '211282', '开原市', '211200');
INSERT INTO `area` VALUES ('558', '211301', '市辖区', '211300');
INSERT INTO `area` VALUES ('559', '211302', '双塔区', '211300');
INSERT INTO `area` VALUES ('560', '211303', '龙城区', '211300');
INSERT INTO `area` VALUES ('561', '211321', '朝阳县', '211300');
INSERT INTO `area` VALUES ('562', '211322', '建平县', '211300');
INSERT INTO `area` VALUES ('563', '211324', '喀喇沁左翼蒙古族自治县', '211300');
INSERT INTO `area` VALUES ('564', '211381', '北票市', '211300');
INSERT INTO `area` VALUES ('565', '211382', '凌源市', '211300');
INSERT INTO `area` VALUES ('566', '211401', '市辖区', '211400');
INSERT INTO `area` VALUES ('567', '211402', '连山区', '211400');
INSERT INTO `area` VALUES ('568', '211403', '龙港区', '211400');
INSERT INTO `area` VALUES ('569', '211404', '南票区', '211400');
INSERT INTO `area` VALUES ('570', '211421', '绥中县', '211400');
INSERT INTO `area` VALUES ('571', '211422', '建昌县', '211400');
INSERT INTO `area` VALUES ('572', '211481', '兴城市', '211400');
INSERT INTO `area` VALUES ('573', '220101', '市辖区', '220100');
INSERT INTO `area` VALUES ('574', '220102', '南关区', '220100');
INSERT INTO `area` VALUES ('575', '220103', '宽城区', '220100');
INSERT INTO `area` VALUES ('576', '220104', '朝阳区', '220100');
INSERT INTO `area` VALUES ('577', '220105', '二道区', '220100');
INSERT INTO `area` VALUES ('578', '220106', '绿园区', '220100');
INSERT INTO `area` VALUES ('579', '220112', '双阳区', '220100');
INSERT INTO `area` VALUES ('580', '220122', '农安县', '220100');
INSERT INTO `area` VALUES ('581', '220181', '九台市', '220100');
INSERT INTO `area` VALUES ('582', '220182', '榆树市', '220100');
INSERT INTO `area` VALUES ('583', '220183', '德惠市', '220100');
INSERT INTO `area` VALUES ('584', '220201', '市辖区', '220200');
INSERT INTO `area` VALUES ('585', '220202', '昌邑区', '220200');
INSERT INTO `area` VALUES ('586', '220203', '龙潭区', '220200');
INSERT INTO `area` VALUES ('587', '220204', '船营区', '220200');
INSERT INTO `area` VALUES ('588', '220211', '丰满区', '220200');
INSERT INTO `area` VALUES ('589', '220221', '永吉县', '220200');
INSERT INTO `area` VALUES ('590', '220281', '蛟河市', '220200');
INSERT INTO `area` VALUES ('591', '220282', '桦甸市', '220200');
INSERT INTO `area` VALUES ('592', '220283', '舒兰市', '220200');
INSERT INTO `area` VALUES ('593', '220284', '磐石市', '220200');
INSERT INTO `area` VALUES ('594', '220301', '市辖区', '220300');
INSERT INTO `area` VALUES ('595', '220302', '铁西区', '220300');
INSERT INTO `area` VALUES ('596', '220303', '铁东区', '220300');
INSERT INTO `area` VALUES ('597', '220322', '梨树县', '220300');
INSERT INTO `area` VALUES ('598', '220323', '伊通满族自治县', '220300');
INSERT INTO `area` VALUES ('599', '220381', '公主岭市', '220300');
INSERT INTO `area` VALUES ('600', '220382', '双辽市', '220300');
INSERT INTO `area` VALUES ('601', '220401', '市辖区', '220400');
INSERT INTO `area` VALUES ('602', '220402', '龙山区', '220400');
INSERT INTO `area` VALUES ('603', '220403', '西安区', '220400');
INSERT INTO `area` VALUES ('604', '220421', '东丰县', '220400');
INSERT INTO `area` VALUES ('605', '220422', '东辽县', '220400');
INSERT INTO `area` VALUES ('606', '220501', '市辖区', '220500');
INSERT INTO `area` VALUES ('607', '220502', '东昌区', '220500');
INSERT INTO `area` VALUES ('608', '220503', '二道江区', '220500');
INSERT INTO `area` VALUES ('609', '220521', '通化县', '220500');
INSERT INTO `area` VALUES ('610', '220523', '辉南县', '220500');
INSERT INTO `area` VALUES ('611', '220524', '柳河县', '220500');
INSERT INTO `area` VALUES ('612', '220581', '梅河口市', '220500');
INSERT INTO `area` VALUES ('613', '220582', '集安市', '220500');
INSERT INTO `area` VALUES ('614', '220601', '市辖区', '220600');
INSERT INTO `area` VALUES ('615', '220602', '八道江区', '220600');
INSERT INTO `area` VALUES ('616', '220621', '抚松县', '220600');
INSERT INTO `area` VALUES ('617', '220622', '靖宇县', '220600');
INSERT INTO `area` VALUES ('618', '220623', '长白朝鲜族自治县', '220600');
INSERT INTO `area` VALUES ('619', '220625', '江源县', '220600');
INSERT INTO `area` VALUES ('620', '220681', '临江市', '220600');
INSERT INTO `area` VALUES ('621', '220701', '市辖区', '220700');
INSERT INTO `area` VALUES ('622', '220702', '宁江区', '220700');
INSERT INTO `area` VALUES ('623', '220721', '前郭尔罗斯蒙古族自治县', '220700');
INSERT INTO `area` VALUES ('624', '220722', '长岭县', '220700');
INSERT INTO `area` VALUES ('625', '220723', '乾安县', '220700');
INSERT INTO `area` VALUES ('626', '220724', '扶余县', '220700');
INSERT INTO `area` VALUES ('627', '220801', '市辖区', '220800');
INSERT INTO `area` VALUES ('628', '220802', '洮北区', '220800');
INSERT INTO `area` VALUES ('629', '220821', '镇赉县', '220800');
INSERT INTO `area` VALUES ('630', '220822', '通榆县', '220800');
INSERT INTO `area` VALUES ('631', '220881', '洮南市', '220800');
INSERT INTO `area` VALUES ('632', '220882', '大安市', '220800');
INSERT INTO `area` VALUES ('633', '222401', '延吉市', '222400');
INSERT INTO `area` VALUES ('634', '222402', '图们市', '222400');
INSERT INTO `area` VALUES ('635', '222403', '敦化市', '222400');
INSERT INTO `area` VALUES ('636', '222404', '珲春市', '222400');
INSERT INTO `area` VALUES ('637', '222405', '龙井市', '222400');
INSERT INTO `area` VALUES ('638', '222406', '和龙市', '222400');
INSERT INTO `area` VALUES ('639', '222424', '汪清县', '222400');
INSERT INTO `area` VALUES ('640', '222426', '安图县', '222400');
INSERT INTO `area` VALUES ('641', '230101', '市辖区', '230100');
INSERT INTO `area` VALUES ('642', '230102', '道里区', '230100');
INSERT INTO `area` VALUES ('643', '230103', '南岗区', '230100');
INSERT INTO `area` VALUES ('644', '230104', '道外区', '230100');
INSERT INTO `area` VALUES ('645', '230106', '香坊区', '230100');
INSERT INTO `area` VALUES ('646', '230107', '动力区', '230100');
INSERT INTO `area` VALUES ('647', '230108', '平房区', '230100');
INSERT INTO `area` VALUES ('648', '230109', '松北区', '230100');
INSERT INTO `area` VALUES ('649', '230111', '呼兰区', '230100');
INSERT INTO `area` VALUES ('650', '230123', '依兰县', '230100');
INSERT INTO `area` VALUES ('651', '230124', '方正县', '230100');
INSERT INTO `area` VALUES ('652', '230125', '宾　县', '230100');
INSERT INTO `area` VALUES ('653', '230126', '巴彦县', '230100');
INSERT INTO `area` VALUES ('654', '230127', '木兰县', '230100');
INSERT INTO `area` VALUES ('655', '230128', '通河县', '230100');
INSERT INTO `area` VALUES ('656', '230129', '延寿县', '230100');
INSERT INTO `area` VALUES ('657', '230181', '阿城市', '230100');
INSERT INTO `area` VALUES ('658', '230182', '双城市', '230100');
INSERT INTO `area` VALUES ('659', '230183', '尚志市', '230100');
INSERT INTO `area` VALUES ('660', '230184', '五常市', '230100');
INSERT INTO `area` VALUES ('661', '230201', '市辖区', '230200');
INSERT INTO `area` VALUES ('662', '230202', '龙沙区', '230200');
INSERT INTO `area` VALUES ('663', '230203', '建华区', '230200');
INSERT INTO `area` VALUES ('664', '230204', '铁锋区', '230200');
INSERT INTO `area` VALUES ('665', '230205', '昂昂溪区', '230200');
INSERT INTO `area` VALUES ('666', '230206', '富拉尔基区', '230200');
INSERT INTO `area` VALUES ('667', '230207', '碾子山区', '230200');
INSERT INTO `area` VALUES ('668', '230208', '梅里斯达斡尔族区', '230200');
INSERT INTO `area` VALUES ('669', '230221', '龙江县', '230200');
INSERT INTO `area` VALUES ('670', '230223', '依安县', '230200');
INSERT INTO `area` VALUES ('671', '230224', '泰来县', '230200');
INSERT INTO `area` VALUES ('672', '230225', '甘南县', '230200');
INSERT INTO `area` VALUES ('673', '230227', '富裕县', '230200');
INSERT INTO `area` VALUES ('674', '230229', '克山县', '230200');
INSERT INTO `area` VALUES ('675', '230230', '克东县', '230200');
INSERT INTO `area` VALUES ('676', '230231', '拜泉县', '230200');
INSERT INTO `area` VALUES ('677', '230281', '讷河市', '230200');
INSERT INTO `area` VALUES ('678', '230301', '市辖区', '230300');
INSERT INTO `area` VALUES ('679', '230302', '鸡冠区', '230300');
INSERT INTO `area` VALUES ('680', '230303', '恒山区', '230300');
INSERT INTO `area` VALUES ('681', '230304', '滴道区', '230300');
INSERT INTO `area` VALUES ('682', '230305', '梨树区', '230300');
INSERT INTO `area` VALUES ('683', '230306', '城子河区', '230300');
INSERT INTO `area` VALUES ('684', '230307', '麻山区', '230300');
INSERT INTO `area` VALUES ('685', '230321', '鸡东县', '230300');
INSERT INTO `area` VALUES ('686', '230381', '虎林市', '230300');
INSERT INTO `area` VALUES ('687', '230382', '密山市', '230300');
INSERT INTO `area` VALUES ('688', '230401', '市辖区', '230400');
INSERT INTO `area` VALUES ('689', '230402', '向阳区', '230400');
INSERT INTO `area` VALUES ('690', '230403', '工农区', '230400');
INSERT INTO `area` VALUES ('691', '230404', '南山区', '230400');
INSERT INTO `area` VALUES ('692', '230405', '兴安区', '230400');
INSERT INTO `area` VALUES ('693', '230406', '东山区', '230400');
INSERT INTO `area` VALUES ('694', '230407', '兴山区', '230400');
INSERT INTO `area` VALUES ('695', '230421', '萝北县', '230400');
INSERT INTO `area` VALUES ('696', '230422', '绥滨县', '230400');
INSERT INTO `area` VALUES ('697', '230501', '市辖区', '230500');
INSERT INTO `area` VALUES ('698', '230502', '尖山区', '230500');
INSERT INTO `area` VALUES ('699', '230503', '岭东区', '230500');
INSERT INTO `area` VALUES ('700', '230505', '四方台区', '230500');
INSERT INTO `area` VALUES ('701', '230506', '宝山区', '230500');
INSERT INTO `area` VALUES ('702', '230521', '集贤县', '230500');
INSERT INTO `area` VALUES ('703', '230522', '友谊县', '230500');
INSERT INTO `area` VALUES ('704', '230523', '宝清县', '230500');
INSERT INTO `area` VALUES ('705', '230524', '饶河县', '230500');
INSERT INTO `area` VALUES ('706', '230601', '市辖区', '230600');
INSERT INTO `area` VALUES ('707', '230602', '萨尔图区', '230600');
INSERT INTO `area` VALUES ('708', '230603', '龙凤区', '230600');
INSERT INTO `area` VALUES ('709', '230604', '让胡路区', '230600');
INSERT INTO `area` VALUES ('710', '230605', '红岗区', '230600');
INSERT INTO `area` VALUES ('711', '230606', '大同区', '230600');
INSERT INTO `area` VALUES ('712', '230621', '肇州县', '230600');
INSERT INTO `area` VALUES ('713', '230622', '肇源县', '230600');
INSERT INTO `area` VALUES ('714', '230623', '林甸县', '230600');
INSERT INTO `area` VALUES ('715', '230624', '杜尔伯特蒙古族自治县', '230600');
INSERT INTO `area` VALUES ('716', '230701', '市辖区', '230700');
INSERT INTO `area` VALUES ('717', '230702', '伊春区', '230700');
INSERT INTO `area` VALUES ('718', '230703', '南岔区', '230700');
INSERT INTO `area` VALUES ('719', '230704', '友好区', '230700');
INSERT INTO `area` VALUES ('720', '230705', '西林区', '230700');
INSERT INTO `area` VALUES ('721', '230706', '翠峦区', '230700');
INSERT INTO `area` VALUES ('722', '230707', '新青区', '230700');
INSERT INTO `area` VALUES ('723', '230708', '美溪区', '230700');
INSERT INTO `area` VALUES ('724', '230709', '金山屯区', '230700');
INSERT INTO `area` VALUES ('725', '230710', '五营区', '230700');
INSERT INTO `area` VALUES ('726', '230711', '乌马河区', '230700');
INSERT INTO `area` VALUES ('727', '230712', '汤旺河区', '230700');
INSERT INTO `area` VALUES ('728', '230713', '带岭区', '230700');
INSERT INTO `area` VALUES ('729', '230714', '乌伊岭区', '230700');
INSERT INTO `area` VALUES ('730', '230715', '红星区', '230700');
INSERT INTO `area` VALUES ('731', '230716', '上甘岭区', '230700');
INSERT INTO `area` VALUES ('732', '230722', '嘉荫县', '230700');
INSERT INTO `area` VALUES ('733', '230781', '铁力市', '230700');
INSERT INTO `area` VALUES ('734', '230801', '市辖区', '230800');
INSERT INTO `area` VALUES ('735', '230802', '永红区', '230800');
INSERT INTO `area` VALUES ('736', '230803', '向阳区', '230800');
INSERT INTO `area` VALUES ('737', '230804', '前进区', '230800');
INSERT INTO `area` VALUES ('738', '230805', '东风区', '230800');
INSERT INTO `area` VALUES ('739', '230811', '郊　区', '230800');
INSERT INTO `area` VALUES ('740', '230822', '桦南县', '230800');
INSERT INTO `area` VALUES ('741', '230826', '桦川县', '230800');
INSERT INTO `area` VALUES ('742', '230828', '汤原县', '230800');
INSERT INTO `area` VALUES ('743', '230833', '抚远县', '230800');
INSERT INTO `area` VALUES ('744', '230881', '同江市', '230800');
INSERT INTO `area` VALUES ('745', '230882', '富锦市', '230800');
INSERT INTO `area` VALUES ('746', '230901', '市辖区', '230900');
INSERT INTO `area` VALUES ('747', '230902', '新兴区', '230900');
INSERT INTO `area` VALUES ('748', '230903', '桃山区', '230900');
INSERT INTO `area` VALUES ('749', '230904', '茄子河区', '230900');
INSERT INTO `area` VALUES ('750', '230921', '勃利县', '230900');
INSERT INTO `area` VALUES ('751', '231001', '市辖区', '231000');
INSERT INTO `area` VALUES ('752', '231002', '东安区', '231000');
INSERT INTO `area` VALUES ('753', '231003', '阳明区', '231000');
INSERT INTO `area` VALUES ('754', '231004', '爱民区', '231000');
INSERT INTO `area` VALUES ('755', '231005', '西安区', '231000');
INSERT INTO `area` VALUES ('756', '231024', '东宁县', '231000');
INSERT INTO `area` VALUES ('757', '231025', '林口县', '231000');
INSERT INTO `area` VALUES ('758', '231081', '绥芬河市', '231000');
INSERT INTO `area` VALUES ('759', '231083', '海林市', '231000');
INSERT INTO `area` VALUES ('760', '231084', '宁安市', '231000');
INSERT INTO `area` VALUES ('761', '231085', '穆棱市', '231000');
INSERT INTO `area` VALUES ('762', '231101', '市辖区', '231100');
INSERT INTO `area` VALUES ('763', '231102', '爱辉区', '231100');
INSERT INTO `area` VALUES ('764', '231121', '嫩江县', '231100');
INSERT INTO `area` VALUES ('765', '231123', '逊克县', '231100');
INSERT INTO `area` VALUES ('766', '231124', '孙吴县', '231100');
INSERT INTO `area` VALUES ('767', '231181', '北安市', '231100');
INSERT INTO `area` VALUES ('768', '231182', '五大连池市', '231100');
INSERT INTO `area` VALUES ('769', '231201', '市辖区', '231200');
INSERT INTO `area` VALUES ('770', '231202', '北林区', '231200');
INSERT INTO `area` VALUES ('771', '231221', '望奎县', '231200');
INSERT INTO `area` VALUES ('772', '231222', '兰西县', '231200');
INSERT INTO `area` VALUES ('773', '231223', '青冈县', '231200');
INSERT INTO `area` VALUES ('774', '231224', '庆安县', '231200');
INSERT INTO `area` VALUES ('775', '231225', '明水县', '231200');
INSERT INTO `area` VALUES ('776', '231226', '绥棱县', '231200');
INSERT INTO `area` VALUES ('777', '231281', '安达市', '231200');
INSERT INTO `area` VALUES ('778', '231282', '肇东市', '231200');
INSERT INTO `area` VALUES ('779', '231283', '海伦市', '231200');
INSERT INTO `area` VALUES ('780', '232721', '呼玛县', '232700');
INSERT INTO `area` VALUES ('781', '232722', '塔河县', '232700');
INSERT INTO `area` VALUES ('782', '232723', '漠河县', '232700');
INSERT INTO `area` VALUES ('783', '310101', '黄浦区', '310100');
INSERT INTO `area` VALUES ('784', '310103', '卢湾区', '310100');
INSERT INTO `area` VALUES ('785', '310104', '徐汇区', '310100');
INSERT INTO `area` VALUES ('786', '310105', '长宁区', '310100');
INSERT INTO `area` VALUES ('787', '310106', '静安区', '310100');
INSERT INTO `area` VALUES ('788', '310107', '普陀区', '310100');
INSERT INTO `area` VALUES ('789', '310108', '闸北区', '310100');
INSERT INTO `area` VALUES ('790', '310109', '虹口区', '310100');
INSERT INTO `area` VALUES ('791', '310110', '杨浦区', '310100');
INSERT INTO `area` VALUES ('792', '310112', '闵行区', '310100');
INSERT INTO `area` VALUES ('793', '310113', '宝山区', '310100');
INSERT INTO `area` VALUES ('794', '310114', '嘉定区', '310100');
INSERT INTO `area` VALUES ('795', '310115', '浦东新区', '310100');
INSERT INTO `area` VALUES ('796', '310116', '金山区', '310100');
INSERT INTO `area` VALUES ('797', '310117', '松江区', '310100');
INSERT INTO `area` VALUES ('798', '310118', '青浦区', '310100');
INSERT INTO `area` VALUES ('799', '310119', '南汇区', '310100');
INSERT INTO `area` VALUES ('800', '310120', '奉贤区', '310100');
INSERT INTO `area` VALUES ('801', '310230', '崇明县', '310200');
INSERT INTO `area` VALUES ('802', '320101', '市辖区', '320100');
INSERT INTO `area` VALUES ('803', '320102', '玄武区', '320100');
INSERT INTO `area` VALUES ('804', '320103', '白下区', '320100');
INSERT INTO `area` VALUES ('805', '320104', '秦淮区', '320100');
INSERT INTO `area` VALUES ('806', '320105', '建邺区', '320100');
INSERT INTO `area` VALUES ('807', '320106', '鼓楼区', '320100');
INSERT INTO `area` VALUES ('808', '320107', '下关区', '320100');
INSERT INTO `area` VALUES ('809', '320111', '浦口区', '320100');
INSERT INTO `area` VALUES ('810', '320113', '栖霞区', '320100');
INSERT INTO `area` VALUES ('811', '320114', '雨花台区', '320100');
INSERT INTO `area` VALUES ('812', '320115', '江宁区', '320100');
INSERT INTO `area` VALUES ('813', '320116', '六合区', '320100');
INSERT INTO `area` VALUES ('814', '320124', '溧水县', '320100');
INSERT INTO `area` VALUES ('815', '320125', '高淳县', '320100');
INSERT INTO `area` VALUES ('816', '320201', '市辖区', '320200');
INSERT INTO `area` VALUES ('817', '320202', '崇安区', '320200');
INSERT INTO `area` VALUES ('818', '320203', '南长区', '320200');
INSERT INTO `area` VALUES ('819', '320204', '北塘区', '320200');
INSERT INTO `area` VALUES ('820', '320205', '锡山区', '320200');
INSERT INTO `area` VALUES ('821', '320206', '惠山区', '320200');
INSERT INTO `area` VALUES ('822', '320211', '滨湖区', '320200');
INSERT INTO `area` VALUES ('823', '320281', '江阴市', '320200');
INSERT INTO `area` VALUES ('824', '320282', '宜兴市', '320200');
INSERT INTO `area` VALUES ('825', '320301', '市辖区', '320300');
INSERT INTO `area` VALUES ('826', '320302', '鼓楼区', '320300');
INSERT INTO `area` VALUES ('827', '320303', '云龙区', '320300');
INSERT INTO `area` VALUES ('828', '320304', '九里区', '320300');
INSERT INTO `area` VALUES ('829', '320305', '贾汪区', '320300');
INSERT INTO `area` VALUES ('830', '320311', '泉山区', '320300');
INSERT INTO `area` VALUES ('831', '320321', '丰　县', '320300');
INSERT INTO `area` VALUES ('832', '320322', '沛　县', '320300');
INSERT INTO `area` VALUES ('833', '320323', '铜山县', '320300');
INSERT INTO `area` VALUES ('834', '320324', '睢宁县', '320300');
INSERT INTO `area` VALUES ('835', '320381', '新沂市', '320300');
INSERT INTO `area` VALUES ('836', '320382', '邳州市', '320300');
INSERT INTO `area` VALUES ('837', '320401', '市辖区', '320400');
INSERT INTO `area` VALUES ('838', '320402', '天宁区', '320400');
INSERT INTO `area` VALUES ('839', '320404', '钟楼区', '320400');
INSERT INTO `area` VALUES ('840', '320405', '戚墅堰区', '320400');
INSERT INTO `area` VALUES ('841', '320411', '新北区', '320400');
INSERT INTO `area` VALUES ('842', '320412', '武进区', '320400');
INSERT INTO `area` VALUES ('843', '320481', '溧阳市', '320400');
INSERT INTO `area` VALUES ('844', '320482', '金坛市', '320400');
INSERT INTO `area` VALUES ('845', '320501', '市辖区', '320500');
INSERT INTO `area` VALUES ('846', '320502', '沧浪区', '320500');
INSERT INTO `area` VALUES ('847', '320503', '平江区', '320500');
INSERT INTO `area` VALUES ('848', '320504', '金阊区', '320500');
INSERT INTO `area` VALUES ('849', '320505', '虎丘区', '320500');
INSERT INTO `area` VALUES ('850', '320506', '吴中区', '320500');
INSERT INTO `area` VALUES ('851', '320507', '相城区', '320500');
INSERT INTO `area` VALUES ('852', '320581', '常熟市', '320500');
INSERT INTO `area` VALUES ('853', '320582', '张家港市', '320500');
INSERT INTO `area` VALUES ('854', '320583', '昆山市', '320500');
INSERT INTO `area` VALUES ('855', '320584', '吴江市', '320500');
INSERT INTO `area` VALUES ('856', '320585', '太仓市', '320500');
INSERT INTO `area` VALUES ('857', '320601', '市辖区', '320600');
INSERT INTO `area` VALUES ('858', '320602', '崇川区', '320600');
INSERT INTO `area` VALUES ('859', '320611', '港闸区', '320600');
INSERT INTO `area` VALUES ('860', '320621', '海安县', '320600');
INSERT INTO `area` VALUES ('861', '320623', '如东县', '320600');
INSERT INTO `area` VALUES ('862', '320681', '启东市', '320600');
INSERT INTO `area` VALUES ('863', '320682', '如皋市', '320600');
INSERT INTO `area` VALUES ('864', '320683', '通州市', '320600');
INSERT INTO `area` VALUES ('865', '320684', '海门市', '320600');
INSERT INTO `area` VALUES ('866', '320701', '市辖区', '320700');
INSERT INTO `area` VALUES ('867', '320703', '连云区', '320700');
INSERT INTO `area` VALUES ('868', '320705', '新浦区', '320700');
INSERT INTO `area` VALUES ('869', '320706', '海州区', '320700');
INSERT INTO `area` VALUES ('870', '320721', '赣榆县', '320700');
INSERT INTO `area` VALUES ('871', '320722', '东海县', '320700');
INSERT INTO `area` VALUES ('872', '320723', '灌云县', '320700');
INSERT INTO `area` VALUES ('873', '320724', '灌南县', '320700');
INSERT INTO `area` VALUES ('874', '320801', '市辖区', '320800');
INSERT INTO `area` VALUES ('875', '320802', '清河区', '320800');
INSERT INTO `area` VALUES ('876', '320803', '楚州区', '320800');
INSERT INTO `area` VALUES ('877', '320804', '淮阴区', '320800');
INSERT INTO `area` VALUES ('878', '320811', '清浦区', '320800');
INSERT INTO `area` VALUES ('879', '320826', '涟水县', '320800');
INSERT INTO `area` VALUES ('880', '320829', '洪泽县', '320800');
INSERT INTO `area` VALUES ('881', '320830', '盱眙县', '320800');
INSERT INTO `area` VALUES ('882', '320831', '金湖县', '320800');
INSERT INTO `area` VALUES ('883', '320901', '市辖区', '320900');
INSERT INTO `area` VALUES ('884', '320902', '亭湖区', '320900');
INSERT INTO `area` VALUES ('885', '320903', '盐都区', '320900');
INSERT INTO `area` VALUES ('886', '320921', '响水县', '320900');
INSERT INTO `area` VALUES ('887', '320922', '滨海县', '320900');
INSERT INTO `area` VALUES ('888', '320923', '阜宁县', '320900');
INSERT INTO `area` VALUES ('889', '320924', '射阳县', '320900');
INSERT INTO `area` VALUES ('890', '320925', '建湖县', '320900');
INSERT INTO `area` VALUES ('891', '320981', '东台市', '320900');
INSERT INTO `area` VALUES ('892', '320982', '大丰市', '320900');
INSERT INTO `area` VALUES ('893', '321001', '市辖区', '321000');
INSERT INTO `area` VALUES ('894', '321002', '广陵区', '321000');
INSERT INTO `area` VALUES ('895', '321003', '邗江区', '321000');
INSERT INTO `area` VALUES ('896', '321011', '郊　区', '321000');
INSERT INTO `area` VALUES ('897', '321023', '宝应县', '321000');
INSERT INTO `area` VALUES ('898', '321081', '仪征市', '321000');
INSERT INTO `area` VALUES ('899', '321084', '高邮市', '321000');
INSERT INTO `area` VALUES ('900', '321088', '江都市', '321000');
INSERT INTO `area` VALUES ('901', '321101', '市辖区', '321100');
INSERT INTO `area` VALUES ('902', '321102', '京口区', '321100');
INSERT INTO `area` VALUES ('903', '321111', '润州区', '321100');
INSERT INTO `area` VALUES ('904', '321112', '丹徒区', '321100');
INSERT INTO `area` VALUES ('905', '321181', '丹阳市', '321100');
INSERT INTO `area` VALUES ('906', '321182', '扬中市', '321100');
INSERT INTO `area` VALUES ('907', '321183', '句容市', '321100');
INSERT INTO `area` VALUES ('908', '321201', '市辖区', '321200');
INSERT INTO `area` VALUES ('909', '321202', '海陵区', '321200');
INSERT INTO `area` VALUES ('910', '321203', '高港区', '321200');
INSERT INTO `area` VALUES ('911', '321281', '兴化市', '321200');
INSERT INTO `area` VALUES ('912', '321282', '靖江市', '321200');
INSERT INTO `area` VALUES ('913', '321283', '泰兴市', '321200');
INSERT INTO `area` VALUES ('914', '321284', '姜堰市', '321200');
INSERT INTO `area` VALUES ('915', '321301', '市辖区', '321300');
INSERT INTO `area` VALUES ('916', '321302', '宿城区', '321300');
INSERT INTO `area` VALUES ('917', '321311', '宿豫区', '321300');
INSERT INTO `area` VALUES ('918', '321322', '沭阳县', '321300');
INSERT INTO `area` VALUES ('919', '321323', '泗阳县', '321300');
INSERT INTO `area` VALUES ('920', '321324', '泗洪县', '321300');
INSERT INTO `area` VALUES ('921', '330101', '市辖区', '330100');
INSERT INTO `area` VALUES ('922', '330102', '上城区', '330100');
INSERT INTO `area` VALUES ('923', '330103', '下城区', '330100');
INSERT INTO `area` VALUES ('924', '330104', '江干区', '330100');
INSERT INTO `area` VALUES ('925', '330105', '拱墅区', '330100');
INSERT INTO `area` VALUES ('926', '330106', '西湖区', '330100');
INSERT INTO `area` VALUES ('927', '330108', '滨江区', '330100');
INSERT INTO `area` VALUES ('928', '330109', '萧山区', '330100');
INSERT INTO `area` VALUES ('929', '330110', '余杭区', '330100');
INSERT INTO `area` VALUES ('930', '330122', '桐庐县', '330100');
INSERT INTO `area` VALUES ('931', '330127', '淳安县', '330100');
INSERT INTO `area` VALUES ('932', '330182', '建德市', '330100');
INSERT INTO `area` VALUES ('933', '330183', '富阳市', '330100');
INSERT INTO `area` VALUES ('934', '330185', '临安市', '330100');
INSERT INTO `area` VALUES ('935', '330201', '市辖区', '330200');
INSERT INTO `area` VALUES ('936', '330203', '海曙区', '330200');
INSERT INTO `area` VALUES ('937', '330204', '江东区', '330200');
INSERT INTO `area` VALUES ('938', '330205', '江北区', '330200');
INSERT INTO `area` VALUES ('939', '330206', '北仑区', '330200');
INSERT INTO `area` VALUES ('940', '330211', '镇海区', '330200');
INSERT INTO `area` VALUES ('941', '330212', '鄞州区', '330200');
INSERT INTO `area` VALUES ('942', '330225', '象山县', '330200');
INSERT INTO `area` VALUES ('943', '330226', '宁海县', '330200');
INSERT INTO `area` VALUES ('944', '330281', '余姚市', '330200');
INSERT INTO `area` VALUES ('945', '330282', '慈溪市', '330200');
INSERT INTO `area` VALUES ('946', '330283', '奉化市', '330200');
INSERT INTO `area` VALUES ('947', '330301', '市辖区', '330300');
INSERT INTO `area` VALUES ('948', '330302', '鹿城区', '330300');
INSERT INTO `area` VALUES ('949', '330303', '龙湾区', '330300');
INSERT INTO `area` VALUES ('950', '330304', '瓯海区', '330300');
INSERT INTO `area` VALUES ('951', '330322', '洞头县', '330300');
INSERT INTO `area` VALUES ('952', '330324', '永嘉县', '330300');
INSERT INTO `area` VALUES ('953', '330326', '平阳县', '330300');
INSERT INTO `area` VALUES ('954', '330327', '苍南县', '330300');
INSERT INTO `area` VALUES ('955', '330328', '文成县', '330300');
INSERT INTO `area` VALUES ('956', '330329', '泰顺县', '330300');
INSERT INTO `area` VALUES ('957', '330381', '瑞安市', '330300');
INSERT INTO `area` VALUES ('958', '330382', '乐清市', '330300');
INSERT INTO `area` VALUES ('959', '330401', '市辖区', '330400');
INSERT INTO `area` VALUES ('960', '330402', '秀城区', '330400');
INSERT INTO `area` VALUES ('961', '330411', '秀洲区', '330400');
INSERT INTO `area` VALUES ('962', '330421', '嘉善县', '330400');
INSERT INTO `area` VALUES ('963', '330424', '海盐县', '330400');
INSERT INTO `area` VALUES ('964', '330481', '海宁市', '330400');
INSERT INTO `area` VALUES ('965', '330482', '平湖市', '330400');
INSERT INTO `area` VALUES ('966', '330483', '桐乡市', '330400');
INSERT INTO `area` VALUES ('967', '330501', '市辖区', '330500');
INSERT INTO `area` VALUES ('968', '330502', '吴兴区', '330500');
INSERT INTO `area` VALUES ('969', '330503', '南浔区', '330500');
INSERT INTO `area` VALUES ('970', '330521', '德清县', '330500');
INSERT INTO `area` VALUES ('971', '330522', '长兴县', '330500');
INSERT INTO `area` VALUES ('972', '330523', '安吉县', '330500');
INSERT INTO `area` VALUES ('973', '330601', '市辖区', '330600');
INSERT INTO `area` VALUES ('974', '330602', '越城区', '330600');
INSERT INTO `area` VALUES ('975', '330621', '绍兴县', '330600');
INSERT INTO `area` VALUES ('976', '330624', '新昌县', '330600');
INSERT INTO `area` VALUES ('977', '330681', '诸暨市', '330600');
INSERT INTO `area` VALUES ('978', '330682', '上虞市', '330600');
INSERT INTO `area` VALUES ('979', '330683', '嵊州市', '330600');
INSERT INTO `area` VALUES ('980', '330701', '市辖区', '330700');
INSERT INTO `area` VALUES ('981', '330702', '婺城区', '330700');
INSERT INTO `area` VALUES ('982', '330703', '金东区', '330700');
INSERT INTO `area` VALUES ('983', '330723', '武义县', '330700');
INSERT INTO `area` VALUES ('984', '330726', '浦江县', '330700');
INSERT INTO `area` VALUES ('985', '330727', '磐安县', '330700');
INSERT INTO `area` VALUES ('986', '330781', '兰溪市', '330700');
INSERT INTO `area` VALUES ('987', '330782', '义乌市', '330700');
INSERT INTO `area` VALUES ('988', '330783', '东阳市', '330700');
INSERT INTO `area` VALUES ('989', '330784', '永康市', '330700');
INSERT INTO `area` VALUES ('990', '330801', '市辖区', '330800');
INSERT INTO `area` VALUES ('991', '330802', '柯城区', '330800');
INSERT INTO `area` VALUES ('992', '330803', '衢江区', '330800');
INSERT INTO `area` VALUES ('993', '330822', '常山县', '330800');
INSERT INTO `area` VALUES ('994', '330824', '开化县', '330800');
INSERT INTO `area` VALUES ('995', '330825', '龙游县', '330800');
INSERT INTO `area` VALUES ('996', '330881', '江山市', '330800');
INSERT INTO `area` VALUES ('997', '330901', '市辖区', '330900');
INSERT INTO `area` VALUES ('998', '330902', '定海区', '330900');
INSERT INTO `area` VALUES ('999', '330903', '普陀区', '330900');
INSERT INTO `area` VALUES ('1000', '330921', '岱山县', '330900');
INSERT INTO `area` VALUES ('1001', '330922', '嵊泗县', '330900');
INSERT INTO `area` VALUES ('1002', '331001', '市辖区', '331000');
INSERT INTO `area` VALUES ('1003', '331002', '椒江区', '331000');
INSERT INTO `area` VALUES ('1004', '331003', '黄岩区', '331000');
INSERT INTO `area` VALUES ('1005', '331004', '路桥区', '331000');
INSERT INTO `area` VALUES ('1006', '331021', '玉环县', '331000');
INSERT INTO `area` VALUES ('1007', '331022', '三门县', '331000');
INSERT INTO `area` VALUES ('1008', '331023', '天台县', '331000');
INSERT INTO `area` VALUES ('1009', '331024', '仙居县', '331000');
INSERT INTO `area` VALUES ('1010', '331081', '温岭市', '331000');
INSERT INTO `area` VALUES ('1011', '331082', '临海市', '331000');
INSERT INTO `area` VALUES ('1012', '331101', '市辖区', '331100');
INSERT INTO `area` VALUES ('1013', '331102', '莲都区', '331100');
INSERT INTO `area` VALUES ('1014', '331121', '青田县', '331100');
INSERT INTO `area` VALUES ('1015', '331122', '缙云县', '331100');
INSERT INTO `area` VALUES ('1016', '331123', '遂昌县', '331100');
INSERT INTO `area` VALUES ('1017', '331124', '松阳县', '331100');
INSERT INTO `area` VALUES ('1018', '331125', '云和县', '331100');
INSERT INTO `area` VALUES ('1019', '331126', '庆元县', '331100');
INSERT INTO `area` VALUES ('1020', '331127', '景宁畲族自治县', '331100');
INSERT INTO `area` VALUES ('1021', '331181', '龙泉市', '331100');
INSERT INTO `area` VALUES ('1022', '340101', '市辖区', '340100');
INSERT INTO `area` VALUES ('1023', '340102', '瑶海区', '340100');
INSERT INTO `area` VALUES ('1024', '340103', '庐阳区', '340100');
INSERT INTO `area` VALUES ('1025', '340104', '蜀山区', '340100');
INSERT INTO `area` VALUES ('1026', '340111', '包河区', '340100');
INSERT INTO `area` VALUES ('1027', '340121', '长丰县', '340100');
INSERT INTO `area` VALUES ('1028', '340122', '肥东县', '340100');
INSERT INTO `area` VALUES ('1029', '340123', '肥西县', '340100');
INSERT INTO `area` VALUES ('1030', '340201', '市辖区', '340200');
INSERT INTO `area` VALUES ('1031', '340202', '镜湖区', '340200');
INSERT INTO `area` VALUES ('1032', '340203', '马塘区', '340200');
INSERT INTO `area` VALUES ('1033', '340204', '新芜区', '340200');
INSERT INTO `area` VALUES ('1034', '340207', '鸠江区', '340200');
INSERT INTO `area` VALUES ('1035', '340221', '芜湖县', '340200');
INSERT INTO `area` VALUES ('1036', '340222', '繁昌县', '340200');
INSERT INTO `area` VALUES ('1037', '340223', '南陵县', '340200');
INSERT INTO `area` VALUES ('1038', '340301', '市辖区', '340300');
INSERT INTO `area` VALUES ('1039', '340302', '龙子湖区', '340300');
INSERT INTO `area` VALUES ('1040', '340303', '蚌山区', '340300');
INSERT INTO `area` VALUES ('1041', '340304', '禹会区', '340300');
INSERT INTO `area` VALUES ('1042', '340311', '淮上区', '340300');
INSERT INTO `area` VALUES ('1043', '340321', '怀远县', '340300');
INSERT INTO `area` VALUES ('1044', '340322', '五河县', '340300');
INSERT INTO `area` VALUES ('1045', '340323', '固镇县', '340300');
INSERT INTO `area` VALUES ('1046', '340401', '市辖区', '340400');
INSERT INTO `area` VALUES ('1047', '340402', '大通区', '340400');
INSERT INTO `area` VALUES ('1048', '340403', '田家庵区', '340400');
INSERT INTO `area` VALUES ('1049', '340404', '谢家集区', '340400');
INSERT INTO `area` VALUES ('1050', '340405', '八公山区', '340400');
INSERT INTO `area` VALUES ('1051', '340406', '潘集区', '340400');
INSERT INTO `area` VALUES ('1052', '340421', '凤台县', '340400');
INSERT INTO `area` VALUES ('1053', '340501', '市辖区', '340500');
INSERT INTO `area` VALUES ('1054', '340502', '金家庄区', '340500');
INSERT INTO `area` VALUES ('1055', '340503', '花山区', '340500');
INSERT INTO `area` VALUES ('1056', '340504', '雨山区', '340500');
INSERT INTO `area` VALUES ('1057', '340521', '当涂县', '340500');
INSERT INTO `area` VALUES ('1058', '340601', '市辖区', '340600');
INSERT INTO `area` VALUES ('1059', '340602', '杜集区', '340600');
INSERT INTO `area` VALUES ('1060', '340603', '相山区', '340600');
INSERT INTO `area` VALUES ('1061', '340604', '烈山区', '340600');
INSERT INTO `area` VALUES ('1062', '340621', '濉溪县', '340600');
INSERT INTO `area` VALUES ('1063', '340701', '市辖区', '340700');
INSERT INTO `area` VALUES ('1064', '340702', '铜官山区', '340700');
INSERT INTO `area` VALUES ('1065', '340703', '狮子山区', '340700');
INSERT INTO `area` VALUES ('1066', '340711', '郊　区', '340700');
INSERT INTO `area` VALUES ('1067', '340721', '铜陵县', '340700');
INSERT INTO `area` VALUES ('1068', '340801', '市辖区', '340800');
INSERT INTO `area` VALUES ('1069', '340802', '迎江区', '340800');
INSERT INTO `area` VALUES ('1070', '340803', '大观区', '340800');
INSERT INTO `area` VALUES ('1071', '340811', '郊　区', '340800');
INSERT INTO `area` VALUES ('1072', '340822', '怀宁县', '340800');
INSERT INTO `area` VALUES ('1073', '340823', '枞阳县', '340800');
INSERT INTO `area` VALUES ('1074', '340824', '潜山县', '340800');
INSERT INTO `area` VALUES ('1075', '340825', '太湖县', '340800');
INSERT INTO `area` VALUES ('1076', '340826', '宿松县', '340800');
INSERT INTO `area` VALUES ('1077', '340827', '望江县', '340800');
INSERT INTO `area` VALUES ('1078', '340828', '岳西县', '340800');
INSERT INTO `area` VALUES ('1079', '340881', '桐城市', '340800');
INSERT INTO `area` VALUES ('1080', '341001', '市辖区', '341000');
INSERT INTO `area` VALUES ('1081', '341002', '屯溪区', '341000');
INSERT INTO `area` VALUES ('1082', '341003', '黄山区', '341000');
INSERT INTO `area` VALUES ('1083', '341004', '徽州区', '341000');
INSERT INTO `area` VALUES ('1084', '341021', '歙　县', '341000');
INSERT INTO `area` VALUES ('1085', '341022', '休宁县', '341000');
INSERT INTO `area` VALUES ('1086', '341023', '黟　县', '341000');
INSERT INTO `area` VALUES ('1087', '341024', '祁门县', '341000');
INSERT INTO `area` VALUES ('1088', '341101', '市辖区', '341100');
INSERT INTO `area` VALUES ('1089', '341102', '琅琊区', '341100');
INSERT INTO `area` VALUES ('1090', '341103', '南谯区', '341100');
INSERT INTO `area` VALUES ('1091', '341122', '来安县', '341100');
INSERT INTO `area` VALUES ('1092', '341124', '全椒县', '341100');
INSERT INTO `area` VALUES ('1093', '341125', '定远县', '341100');
INSERT INTO `area` VALUES ('1094', '341126', '凤阳县', '341100');
INSERT INTO `area` VALUES ('1095', '341181', '天长市', '341100');
INSERT INTO `area` VALUES ('1096', '341182', '明光市', '341100');
INSERT INTO `area` VALUES ('1097', '341201', '市辖区', '341200');
INSERT INTO `area` VALUES ('1098', '341202', '颍州区', '341200');
INSERT INTO `area` VALUES ('1099', '341203', '颍东区', '341200');
INSERT INTO `area` VALUES ('1100', '341204', '颍泉区', '341200');
INSERT INTO `area` VALUES ('1101', '341221', '临泉县', '341200');
INSERT INTO `area` VALUES ('1102', '341222', '太和县', '341200');
INSERT INTO `area` VALUES ('1103', '341225', '阜南县', '341200');
INSERT INTO `area` VALUES ('1104', '341226', '颍上县', '341200');
INSERT INTO `area` VALUES ('1105', '341282', '界首市', '341200');
INSERT INTO `area` VALUES ('1106', '341301', '市辖区', '341300');
INSERT INTO `area` VALUES ('1107', '341302', '墉桥区', '341300');
INSERT INTO `area` VALUES ('1108', '341321', '砀山县', '341300');
INSERT INTO `area` VALUES ('1109', '341322', '萧　县', '341300');
INSERT INTO `area` VALUES ('1110', '341323', '灵璧县', '341300');
INSERT INTO `area` VALUES ('1111', '341324', '泗　县', '341300');
INSERT INTO `area` VALUES ('1112', '341401', '市辖区', '341400');
INSERT INTO `area` VALUES ('1113', '341402', '居巢区', '341400');
INSERT INTO `area` VALUES ('1114', '341421', '庐江县', '341400');
INSERT INTO `area` VALUES ('1115', '341422', '无为县', '341400');
INSERT INTO `area` VALUES ('1116', '341423', '含山县', '341400');
INSERT INTO `area` VALUES ('1117', '341424', '和　县', '341400');
INSERT INTO `area` VALUES ('1118', '341501', '市辖区', '341500');
INSERT INTO `area` VALUES ('1119', '341502', '金安区', '341500');
INSERT INTO `area` VALUES ('1120', '341503', '裕安区', '341500');
INSERT INTO `area` VALUES ('1121', '341521', '寿　县', '341500');
INSERT INTO `area` VALUES ('1122', '341522', '霍邱县', '341500');
INSERT INTO `area` VALUES ('1123', '341523', '舒城县', '341500');
INSERT INTO `area` VALUES ('1124', '341524', '金寨县', '341500');
INSERT INTO `area` VALUES ('1125', '341525', '霍山县', '341500');
INSERT INTO `area` VALUES ('1126', '341601', '市辖区', '341600');
INSERT INTO `area` VALUES ('1127', '341602', '谯城区', '341600');
INSERT INTO `area` VALUES ('1128', '341621', '涡阳县', '341600');
INSERT INTO `area` VALUES ('1129', '341622', '蒙城县', '341600');
INSERT INTO `area` VALUES ('1130', '341623', '利辛县', '341600');
INSERT INTO `area` VALUES ('1131', '341701', '市辖区', '341700');
INSERT INTO `area` VALUES ('1132', '341702', '贵池区', '341700');
INSERT INTO `area` VALUES ('1133', '341721', '东至县', '341700');
INSERT INTO `area` VALUES ('1134', '341722', '石台县', '341700');
INSERT INTO `area` VALUES ('1135', '341723', '青阳县', '341700');
INSERT INTO `area` VALUES ('1136', '341801', '市辖区', '341800');
INSERT INTO `area` VALUES ('1137', '341802', '宣州区', '341800');
INSERT INTO `area` VALUES ('1138', '341821', '郎溪县', '341800');
INSERT INTO `area` VALUES ('1139', '341822', '广德县', '341800');
INSERT INTO `area` VALUES ('1140', '341823', '泾　县', '341800');
INSERT INTO `area` VALUES ('1141', '341824', '绩溪县', '341800');
INSERT INTO `area` VALUES ('1142', '341825', '旌德县', '341800');
INSERT INTO `area` VALUES ('1143', '341881', '宁国市', '341800');
INSERT INTO `area` VALUES ('1144', '350101', '市辖区', '350100');
INSERT INTO `area` VALUES ('1145', '350102', '鼓楼区', '350100');
INSERT INTO `area` VALUES ('1146', '350103', '台江区', '350100');
INSERT INTO `area` VALUES ('1147', '350104', '仓山区', '350100');
INSERT INTO `area` VALUES ('1148', '350105', '马尾区', '350100');
INSERT INTO `area` VALUES ('1149', '350111', '晋安区', '350100');
INSERT INTO `area` VALUES ('1150', '350121', '闽侯县', '350100');
INSERT INTO `area` VALUES ('1151', '350122', '连江县', '350100');
INSERT INTO `area` VALUES ('1152', '350123', '罗源县', '350100');
INSERT INTO `area` VALUES ('1153', '350124', '闽清县', '350100');
INSERT INTO `area` VALUES ('1154', '350125', '永泰县', '350100');
INSERT INTO `area` VALUES ('1155', '350128', '平潭县', '350100');
INSERT INTO `area` VALUES ('1156', '350181', '福清市', '350100');
INSERT INTO `area` VALUES ('1157', '350182', '长乐市', '350100');
INSERT INTO `area` VALUES ('1158', '350201', '市辖区', '350200');
INSERT INTO `area` VALUES ('1159', '350203', '思明区', '350200');
INSERT INTO `area` VALUES ('1160', '350205', '海沧区', '350200');
INSERT INTO `area` VALUES ('1161', '350206', '湖里区', '350200');
INSERT INTO `area` VALUES ('1162', '350211', '集美区', '350200');
INSERT INTO `area` VALUES ('1163', '350212', '同安区', '350200');
INSERT INTO `area` VALUES ('1164', '350213', '翔安区', '350200');
INSERT INTO `area` VALUES ('1165', '350301', '市辖区', '350300');
INSERT INTO `area` VALUES ('1166', '350302', '城厢区', '350300');
INSERT INTO `area` VALUES ('1167', '350303', '涵江区', '350300');
INSERT INTO `area` VALUES ('1168', '350304', '荔城区', '350300');
INSERT INTO `area` VALUES ('1169', '350305', '秀屿区', '350300');
INSERT INTO `area` VALUES ('1170', '350322', '仙游县', '350300');
INSERT INTO `area` VALUES ('1171', '350401', '市辖区', '350400');
INSERT INTO `area` VALUES ('1172', '350402', '梅列区', '350400');
INSERT INTO `area` VALUES ('1173', '350403', '三元区', '350400');
INSERT INTO `area` VALUES ('1174', '350421', '明溪县', '350400');
INSERT INTO `area` VALUES ('1175', '350423', '清流县', '350400');
INSERT INTO `area` VALUES ('1176', '350424', '宁化县', '350400');
INSERT INTO `area` VALUES ('1177', '350425', '大田县', '350400');
INSERT INTO `area` VALUES ('1178', '350426', '尤溪县', '350400');
INSERT INTO `area` VALUES ('1179', '350427', '沙　县', '350400');
INSERT INTO `area` VALUES ('1180', '350428', '将乐县', '350400');
INSERT INTO `area` VALUES ('1181', '350429', '泰宁县', '350400');
INSERT INTO `area` VALUES ('1182', '350430', '建宁县', '350400');
INSERT INTO `area` VALUES ('1183', '350481', '永安市', '350400');
INSERT INTO `area` VALUES ('1184', '350501', '市辖区', '350500');
INSERT INTO `area` VALUES ('1185', '350502', '鲤城区', '350500');
INSERT INTO `area` VALUES ('1186', '350503', '丰泽区', '350500');
INSERT INTO `area` VALUES ('1187', '350504', '洛江区', '350500');
INSERT INTO `area` VALUES ('1188', '350505', '泉港区', '350500');
INSERT INTO `area` VALUES ('1189', '350521', '惠安县', '350500');
INSERT INTO `area` VALUES ('1190', '350524', '安溪县', '350500');
INSERT INTO `area` VALUES ('1191', '350525', '永春县', '350500');
INSERT INTO `area` VALUES ('1192', '350526', '德化县', '350500');
INSERT INTO `area` VALUES ('1193', '350527', '金门县', '350500');
INSERT INTO `area` VALUES ('1194', '350581', '石狮市', '350500');
INSERT INTO `area` VALUES ('1195', '350582', '晋江市', '350500');
INSERT INTO `area` VALUES ('1196', '350583', '南安市', '350500');
INSERT INTO `area` VALUES ('1197', '350601', '市辖区', '350600');
INSERT INTO `area` VALUES ('1198', '350602', '芗城区', '350600');
INSERT INTO `area` VALUES ('1199', '350603', '龙文区', '350600');
INSERT INTO `area` VALUES ('1200', '350622', '云霄县', '350600');
INSERT INTO `area` VALUES ('1201', '350623', '漳浦县', '350600');
INSERT INTO `area` VALUES ('1202', '350624', '诏安县', '350600');
INSERT INTO `area` VALUES ('1203', '350625', '长泰县', '350600');
INSERT INTO `area` VALUES ('1204', '350626', '东山县', '350600');
INSERT INTO `area` VALUES ('1205', '350627', '南靖县', '350600');
INSERT INTO `area` VALUES ('1206', '350628', '平和县', '350600');
INSERT INTO `area` VALUES ('1207', '350629', '华安县', '350600');
INSERT INTO `area` VALUES ('1208', '350681', '龙海市', '350600');
INSERT INTO `area` VALUES ('1209', '350701', '市辖区', '350700');
INSERT INTO `area` VALUES ('1210', '350702', '延平区', '350700');
INSERT INTO `area` VALUES ('1211', '350721', '顺昌县', '350700');
INSERT INTO `area` VALUES ('1212', '350722', '浦城县', '350700');
INSERT INTO `area` VALUES ('1213', '350723', '光泽县', '350700');
INSERT INTO `area` VALUES ('1214', '350724', '松溪县', '350700');
INSERT INTO `area` VALUES ('1215', '350725', '政和县', '350700');
INSERT INTO `area` VALUES ('1216', '350781', '邵武市', '350700');
INSERT INTO `area` VALUES ('1217', '350782', '武夷山市', '350700');
INSERT INTO `area` VALUES ('1218', '350783', '建瓯市', '350700');
INSERT INTO `area` VALUES ('1219', '350784', '建阳市', '350700');
INSERT INTO `area` VALUES ('1220', '350801', '市辖区', '350800');
INSERT INTO `area` VALUES ('1221', '350802', '新罗区', '350800');
INSERT INTO `area` VALUES ('1222', '350821', '长汀县', '350800');
INSERT INTO `area` VALUES ('1223', '350822', '永定县', '350800');
INSERT INTO `area` VALUES ('1224', '350823', '上杭县', '350800');
INSERT INTO `area` VALUES ('1225', '350824', '武平县', '350800');
INSERT INTO `area` VALUES ('1226', '350825', '连城县', '350800');
INSERT INTO `area` VALUES ('1227', '350881', '漳平市', '350800');
INSERT INTO `area` VALUES ('1228', '350901', '市辖区', '350900');
INSERT INTO `area` VALUES ('1229', '350902', '蕉城区', '350900');
INSERT INTO `area` VALUES ('1230', '350921', '霞浦县', '350900');
INSERT INTO `area` VALUES ('1231', '350922', '古田县', '350900');
INSERT INTO `area` VALUES ('1232', '350923', '屏南县', '350900');
INSERT INTO `area` VALUES ('1233', '350924', '寿宁县', '350900');
INSERT INTO `area` VALUES ('1234', '350925', '周宁县', '350900');
INSERT INTO `area` VALUES ('1235', '350926', '柘荣县', '350900');
INSERT INTO `area` VALUES ('1236', '350981', '福安市', '350900');
INSERT INTO `area` VALUES ('1237', '350982', '福鼎市', '350900');
INSERT INTO `area` VALUES ('1238', '360101', '市辖区', '360100');
INSERT INTO `area` VALUES ('1239', '360102', '东湖区', '360100');
INSERT INTO `area` VALUES ('1240', '360103', '西湖区', '360100');
INSERT INTO `area` VALUES ('1241', '360104', '青云谱区', '360100');
INSERT INTO `area` VALUES ('1242', '360105', '湾里区', '360100');
INSERT INTO `area` VALUES ('1243', '360111', '青山湖区', '360100');
INSERT INTO `area` VALUES ('1244', '360121', '南昌县', '360100');
INSERT INTO `area` VALUES ('1245', '360122', '新建县', '360100');
INSERT INTO `area` VALUES ('1246', '360123', '安义县', '360100');
INSERT INTO `area` VALUES ('1247', '360124', '进贤县', '360100');
INSERT INTO `area` VALUES ('1248', '360201', '市辖区', '360200');
INSERT INTO `area` VALUES ('1249', '360202', '昌江区', '360200');
INSERT INTO `area` VALUES ('1250', '360203', '珠山区', '360200');
INSERT INTO `area` VALUES ('1251', '360222', '浮梁县', '360200');
INSERT INTO `area` VALUES ('1252', '360281', '乐平市', '360200');
INSERT INTO `area` VALUES ('1253', '360301', '市辖区', '360300');
INSERT INTO `area` VALUES ('1254', '360302', '安源区', '360300');
INSERT INTO `area` VALUES ('1255', '360313', '湘东区', '360300');
INSERT INTO `area` VALUES ('1256', '360321', '莲花县', '360300');
INSERT INTO `area` VALUES ('1257', '360322', '上栗县', '360300');
INSERT INTO `area` VALUES ('1258', '360323', '芦溪县', '360300');
INSERT INTO `area` VALUES ('1259', '360401', '市辖区', '360400');
INSERT INTO `area` VALUES ('1260', '360402', '庐山区', '360400');
INSERT INTO `area` VALUES ('1261', '360403', '浔阳区', '360400');
INSERT INTO `area` VALUES ('1262', '360421', '九江县', '360400');
INSERT INTO `area` VALUES ('1263', '360423', '武宁县', '360400');
INSERT INTO `area` VALUES ('1264', '360424', '修水县', '360400');
INSERT INTO `area` VALUES ('1265', '360425', '永修县', '360400');
INSERT INTO `area` VALUES ('1266', '360426', '德安县', '360400');
INSERT INTO `area` VALUES ('1267', '360427', '星子县', '360400');
INSERT INTO `area` VALUES ('1268', '360428', '都昌县', '360400');
INSERT INTO `area` VALUES ('1269', '360429', '湖口县', '360400');
INSERT INTO `area` VALUES ('1270', '360430', '彭泽县', '360400');
INSERT INTO `area` VALUES ('1271', '360481', '瑞昌市', '360400');
INSERT INTO `area` VALUES ('1272', '360501', '市辖区', '360500');
INSERT INTO `area` VALUES ('1273', '360502', '渝水区', '360500');
INSERT INTO `area` VALUES ('1274', '360521', '分宜县', '360500');
INSERT INTO `area` VALUES ('1275', '360601', '市辖区', '360600');
INSERT INTO `area` VALUES ('1276', '360602', '月湖区', '360600');
INSERT INTO `area` VALUES ('1277', '360622', '余江县', '360600');
INSERT INTO `area` VALUES ('1278', '360681', '贵溪市', '360600');
INSERT INTO `area` VALUES ('1279', '360701', '市辖区', '360700');
INSERT INTO `area` VALUES ('1280', '360702', '章贡区', '360700');
INSERT INTO `area` VALUES ('1281', '360721', '赣　县', '360700');
INSERT INTO `area` VALUES ('1282', '360722', '信丰县', '360700');
INSERT INTO `area` VALUES ('1283', '360723', '大余县', '360700');
INSERT INTO `area` VALUES ('1284', '360724', '上犹县', '360700');
INSERT INTO `area` VALUES ('1285', '360725', '崇义县', '360700');
INSERT INTO `area` VALUES ('1286', '360726', '安远县', '360700');
INSERT INTO `area` VALUES ('1287', '360727', '龙南县', '360700');
INSERT INTO `area` VALUES ('1288', '360728', '定南县', '360700');
INSERT INTO `area` VALUES ('1289', '360729', '全南县', '360700');
INSERT INTO `area` VALUES ('1290', '360730', '宁都县', '360700');
INSERT INTO `area` VALUES ('1291', '360731', '于都县', '360700');
INSERT INTO `area` VALUES ('1292', '360732', '兴国县', '360700');
INSERT INTO `area` VALUES ('1293', '360733', '会昌县', '360700');
INSERT INTO `area` VALUES ('1294', '360734', '寻乌县', '360700');
INSERT INTO `area` VALUES ('1295', '360735', '石城县', '360700');
INSERT INTO `area` VALUES ('1296', '360781', '瑞金市', '360700');
INSERT INTO `area` VALUES ('1297', '360782', '南康市', '360700');
INSERT INTO `area` VALUES ('1298', '360801', '市辖区', '360800');
INSERT INTO `area` VALUES ('1299', '360802', '吉州区', '360800');
INSERT INTO `area` VALUES ('1300', '360803', '青原区', '360800');
INSERT INTO `area` VALUES ('1301', '360821', '吉安县', '360800');
INSERT INTO `area` VALUES ('1302', '360822', '吉水县', '360800');
INSERT INTO `area` VALUES ('1303', '360823', '峡江县', '360800');
INSERT INTO `area` VALUES ('1304', '360824', '新干县', '360800');
INSERT INTO `area` VALUES ('1305', '360825', '永丰县', '360800');
INSERT INTO `area` VALUES ('1306', '360826', '泰和县', '360800');
INSERT INTO `area` VALUES ('1307', '360827', '遂川县', '360800');
INSERT INTO `area` VALUES ('1308', '360828', '万安县', '360800');
INSERT INTO `area` VALUES ('1309', '360829', '安福县', '360800');
INSERT INTO `area` VALUES ('1310', '360830', '永新县', '360800');
INSERT INTO `area` VALUES ('1311', '360881', '井冈山市', '360800');
INSERT INTO `area` VALUES ('1312', '360901', '市辖区', '360900');
INSERT INTO `area` VALUES ('1313', '360902', '袁州区', '360900');
INSERT INTO `area` VALUES ('1314', '360921', '奉新县', '360900');
INSERT INTO `area` VALUES ('1315', '360922', '万载县', '360900');
INSERT INTO `area` VALUES ('1316', '360923', '上高县', '360900');
INSERT INTO `area` VALUES ('1317', '360924', '宜丰县', '360900');
INSERT INTO `area` VALUES ('1318', '360925', '靖安县', '360900');
INSERT INTO `area` VALUES ('1319', '360926', '铜鼓县', '360900');
INSERT INTO `area` VALUES ('1320', '360981', '丰城市', '360900');
INSERT INTO `area` VALUES ('1321', '360982', '樟树市', '360900');
INSERT INTO `area` VALUES ('1322', '360983', '高安市', '360900');
INSERT INTO `area` VALUES ('1323', '361001', '市辖区', '361000');
INSERT INTO `area` VALUES ('1324', '361002', '临川区', '361000');
INSERT INTO `area` VALUES ('1325', '361021', '南城县', '361000');
INSERT INTO `area` VALUES ('1326', '361022', '黎川县', '361000');
INSERT INTO `area` VALUES ('1327', '361023', '南丰县', '361000');
INSERT INTO `area` VALUES ('1328', '361024', '崇仁县', '361000');
INSERT INTO `area` VALUES ('1329', '361025', '乐安县', '361000');
INSERT INTO `area` VALUES ('1330', '361026', '宜黄县', '361000');
INSERT INTO `area` VALUES ('1331', '361027', '金溪县', '361000');
INSERT INTO `area` VALUES ('1332', '361028', '资溪县', '361000');
INSERT INTO `area` VALUES ('1333', '361029', '东乡县', '361000');
INSERT INTO `area` VALUES ('1334', '361030', '广昌县', '361000');
INSERT INTO `area` VALUES ('1335', '361101', '市辖区', '361100');
INSERT INTO `area` VALUES ('1336', '361102', '信州区', '361100');
INSERT INTO `area` VALUES ('1337', '361121', '上饶县', '361100');
INSERT INTO `area` VALUES ('1338', '361122', '广丰县', '361100');
INSERT INTO `area` VALUES ('1339', '361123', '玉山县', '361100');
INSERT INTO `area` VALUES ('1340', '361124', '铅山县', '361100');
INSERT INTO `area` VALUES ('1341', '361125', '横峰县', '361100');
INSERT INTO `area` VALUES ('1342', '361126', '弋阳县', '361100');
INSERT INTO `area` VALUES ('1343', '361127', '余干县', '361100');
INSERT INTO `area` VALUES ('1344', '361128', '鄱阳县', '361100');
INSERT INTO `area` VALUES ('1345', '361129', '万年县', '361100');
INSERT INTO `area` VALUES ('1346', '361130', '婺源县', '361100');
INSERT INTO `area` VALUES ('1347', '361181', '德兴市', '361100');
INSERT INTO `area` VALUES ('1348', '370101', '市辖区', '370100');
INSERT INTO `area` VALUES ('1349', '370102', '历下区', '370100');
INSERT INTO `area` VALUES ('1350', '370103', '市中区', '370100');
INSERT INTO `area` VALUES ('1351', '370104', '槐荫区', '370100');
INSERT INTO `area` VALUES ('1352', '370105', '天桥区', '370100');
INSERT INTO `area` VALUES ('1353', '370112', '历城区', '370100');
INSERT INTO `area` VALUES ('1354', '370113', '长清区', '370100');
INSERT INTO `area` VALUES ('1355', '370124', '平阴县', '370100');
INSERT INTO `area` VALUES ('1356', '370125', '济阳县', '370100');
INSERT INTO `area` VALUES ('1357', '370126', '商河县', '370100');
INSERT INTO `area` VALUES ('1358', '370181', '章丘市', '370100');
INSERT INTO `area` VALUES ('1359', '370201', '市辖区', '370200');
INSERT INTO `area` VALUES ('1360', '370202', '市南区', '370200');
INSERT INTO `area` VALUES ('1361', '370203', '市北区', '370200');
INSERT INTO `area` VALUES ('1362', '370205', '四方区', '370200');
INSERT INTO `area` VALUES ('1363', '370211', '黄岛区', '370200');
INSERT INTO `area` VALUES ('1364', '370212', '崂山区', '370200');
INSERT INTO `area` VALUES ('1365', '370213', '李沧区', '370200');
INSERT INTO `area` VALUES ('1366', '370214', '城阳区', '370200');
INSERT INTO `area` VALUES ('1367', '370281', '胶州市', '370200');
INSERT INTO `area` VALUES ('1368', '370282', '即墨市', '370200');
INSERT INTO `area` VALUES ('1369', '370283', '平度市', '370200');
INSERT INTO `area` VALUES ('1370', '370284', '胶南市', '370200');
INSERT INTO `area` VALUES ('1371', '370285', '莱西市', '370200');
INSERT INTO `area` VALUES ('1372', '370301', '市辖区', '370300');
INSERT INTO `area` VALUES ('1373', '370302', '淄川区', '370300');
INSERT INTO `area` VALUES ('1374', '370303', '张店区', '370300');
INSERT INTO `area` VALUES ('1375', '370304', '博山区', '370300');
INSERT INTO `area` VALUES ('1376', '370305', '临淄区', '370300');
INSERT INTO `area` VALUES ('1377', '370306', '周村区', '370300');
INSERT INTO `area` VALUES ('1378', '370321', '桓台县', '370300');
INSERT INTO `area` VALUES ('1379', '370322', '高青县', '370300');
INSERT INTO `area` VALUES ('1380', '370323', '沂源县', '370300');
INSERT INTO `area` VALUES ('1381', '370401', '市辖区', '370400');
INSERT INTO `area` VALUES ('1382', '370402', '市中区', '370400');
INSERT INTO `area` VALUES ('1383', '370403', '薛城区', '370400');
INSERT INTO `area` VALUES ('1384', '370404', '峄城区', '370400');
INSERT INTO `area` VALUES ('1385', '370405', '台儿庄区', '370400');
INSERT INTO `area` VALUES ('1386', '370406', '山亭区', '370400');
INSERT INTO `area` VALUES ('1387', '370481', '滕州市', '370400');
INSERT INTO `area` VALUES ('1388', '370501', '市辖区', '370500');
INSERT INTO `area` VALUES ('1389', '370502', '东营区', '370500');
INSERT INTO `area` VALUES ('1390', '370503', '河口区', '370500');
INSERT INTO `area` VALUES ('1391', '370521', '垦利县', '370500');
INSERT INTO `area` VALUES ('1392', '370522', '利津县', '370500');
INSERT INTO `area` VALUES ('1393', '370523', '广饶县', '370500');
INSERT INTO `area` VALUES ('1394', '370601', '市辖区', '370600');
INSERT INTO `area` VALUES ('1395', '370602', '芝罘区', '370600');
INSERT INTO `area` VALUES ('1396', '370611', '福山区', '370600');
INSERT INTO `area` VALUES ('1397', '370612', '牟平区', '370600');
INSERT INTO `area` VALUES ('1398', '370613', '莱山区', '370600');
INSERT INTO `area` VALUES ('1399', '370634', '长岛县', '370600');
INSERT INTO `area` VALUES ('1400', '370681', '龙口市', '370600');
INSERT INTO `area` VALUES ('1401', '370682', '莱阳市', '370600');
INSERT INTO `area` VALUES ('1402', '370683', '莱州市', '370600');
INSERT INTO `area` VALUES ('1403', '370684', '蓬莱市', '370600');
INSERT INTO `area` VALUES ('1404', '370685', '招远市', '370600');
INSERT INTO `area` VALUES ('1405', '370686', '栖霞市', '370600');
INSERT INTO `area` VALUES ('1406', '370687', '海阳市', '370600');
INSERT INTO `area` VALUES ('1407', '370701', '市辖区', '370700');
INSERT INTO `area` VALUES ('1408', '370702', '潍城区', '370700');
INSERT INTO `area` VALUES ('1409', '370703', '寒亭区', '370700');
INSERT INTO `area` VALUES ('1410', '370704', '坊子区', '370700');
INSERT INTO `area` VALUES ('1411', '370705', '奎文区', '370700');
INSERT INTO `area` VALUES ('1412', '370724', '临朐县', '370700');
INSERT INTO `area` VALUES ('1413', '370725', '昌乐县', '370700');
INSERT INTO `area` VALUES ('1414', '370781', '青州市', '370700');
INSERT INTO `area` VALUES ('1415', '370782', '诸城市', '370700');
INSERT INTO `area` VALUES ('1416', '370783', '寿光市', '370700');
INSERT INTO `area` VALUES ('1417', '370784', '安丘市', '370700');
INSERT INTO `area` VALUES ('1418', '370785', '高密市', '370700');
INSERT INTO `area` VALUES ('1419', '370786', '昌邑市', '370700');
INSERT INTO `area` VALUES ('1420', '370801', '市辖区', '370800');
INSERT INTO `area` VALUES ('1421', '370802', '市中区', '370800');
INSERT INTO `area` VALUES ('1422', '370811', '任城区', '370800');
INSERT INTO `area` VALUES ('1423', '370826', '微山县', '370800');
INSERT INTO `area` VALUES ('1424', '370827', '鱼台县', '370800');
INSERT INTO `area` VALUES ('1425', '370828', '金乡县', '370800');
INSERT INTO `area` VALUES ('1426', '370829', '嘉祥县', '370800');
INSERT INTO `area` VALUES ('1427', '370830', '汶上县', '370800');
INSERT INTO `area` VALUES ('1428', '370831', '泗水县', '370800');
INSERT INTO `area` VALUES ('1429', '370832', '梁山县', '370800');
INSERT INTO `area` VALUES ('1430', '370881', '曲阜市', '370800');
INSERT INTO `area` VALUES ('1431', '370882', '兖州市', '370800');
INSERT INTO `area` VALUES ('1432', '370883', '邹城市', '370800');
INSERT INTO `area` VALUES ('1433', '370901', '市辖区', '370900');
INSERT INTO `area` VALUES ('1434', '370902', '泰山区', '370900');
INSERT INTO `area` VALUES ('1435', '370903', '岱岳区', '370900');
INSERT INTO `area` VALUES ('1436', '370921', '宁阳县', '370900');
INSERT INTO `area` VALUES ('1437', '370923', '东平县', '370900');
INSERT INTO `area` VALUES ('1438', '370982', '新泰市', '370900');
INSERT INTO `area` VALUES ('1439', '370983', '肥城市', '370900');
INSERT INTO `area` VALUES ('1440', '371001', '市辖区', '371000');
INSERT INTO `area` VALUES ('1441', '371002', '环翠区', '371000');
INSERT INTO `area` VALUES ('1442', '371081', '文登市', '371000');
INSERT INTO `area` VALUES ('1443', '371082', '荣成市', '371000');
INSERT INTO `area` VALUES ('1444', '371083', '乳山市', '371000');
INSERT INTO `area` VALUES ('1445', '371101', '市辖区', '371100');
INSERT INTO `area` VALUES ('1446', '371102', '东港区', '371100');
INSERT INTO `area` VALUES ('1447', '371103', '岚山区', '371100');
INSERT INTO `area` VALUES ('1448', '371121', '五莲县', '371100');
INSERT INTO `area` VALUES ('1449', '371122', '莒　县', '371100');
INSERT INTO `area` VALUES ('1450', '371201', '市辖区', '371200');
INSERT INTO `area` VALUES ('1451', '371202', '莱城区', '371200');
INSERT INTO `area` VALUES ('1452', '371203', '钢城区', '371200');
INSERT INTO `area` VALUES ('1453', '371301', '市辖区', '371300');
INSERT INTO `area` VALUES ('1454', '371302', '兰山区', '371300');
INSERT INTO `area` VALUES ('1455', '371311', '罗庄区', '371300');
INSERT INTO `area` VALUES ('1456', '371312', '河东区', '371300');
INSERT INTO `area` VALUES ('1457', '371321', '沂南县', '371300');
INSERT INTO `area` VALUES ('1458', '371322', '郯城县', '371300');
INSERT INTO `area` VALUES ('1459', '371323', '沂水县', '371300');
INSERT INTO `area` VALUES ('1460', '371324', '苍山县', '371300');
INSERT INTO `area` VALUES ('1461', '371325', '费　县', '371300');
INSERT INTO `area` VALUES ('1462', '371326', '平邑县', '371300');
INSERT INTO `area` VALUES ('1463', '371327', '莒南县', '371300');
INSERT INTO `area` VALUES ('1464', '371328', '蒙阴县', '371300');
INSERT INTO `area` VALUES ('1465', '371329', '临沭县', '371300');
INSERT INTO `area` VALUES ('1466', '371401', '市辖区', '371400');
INSERT INTO `area` VALUES ('1467', '371402', '德城区', '371400');
INSERT INTO `area` VALUES ('1468', '371421', '陵　县', '371400');
INSERT INTO `area` VALUES ('1469', '371422', '宁津县', '371400');
INSERT INTO `area` VALUES ('1470', '371423', '庆云县', '371400');
INSERT INTO `area` VALUES ('1471', '371424', '临邑县', '371400');
INSERT INTO `area` VALUES ('1472', '371425', '齐河县', '371400');
INSERT INTO `area` VALUES ('1473', '371426', '平原县', '371400');
INSERT INTO `area` VALUES ('1474', '371427', '夏津县', '371400');
INSERT INTO `area` VALUES ('1475', '371428', '武城县', '371400');
INSERT INTO `area` VALUES ('1476', '371481', '乐陵市', '371400');
INSERT INTO `area` VALUES ('1477', '371482', '禹城市', '371400');
INSERT INTO `area` VALUES ('1478', '371501', '市辖区', '371500');
INSERT INTO `area` VALUES ('1479', '371502', '东昌府区', '371500');
INSERT INTO `area` VALUES ('1480', '371521', '阳谷县', '371500');
INSERT INTO `area` VALUES ('1481', '371522', '莘　县', '371500');
INSERT INTO `area` VALUES ('1482', '371523', '茌平县', '371500');
INSERT INTO `area` VALUES ('1483', '371524', '东阿县', '371500');
INSERT INTO `area` VALUES ('1484', '371525', '冠　县', '371500');
INSERT INTO `area` VALUES ('1485', '371526', '高唐县', '371500');
INSERT INTO `area` VALUES ('1486', '371581', '临清市', '371500');
INSERT INTO `area` VALUES ('1487', '371601', '市辖区', '371600');
INSERT INTO `area` VALUES ('1488', '371602', '滨城区', '371600');
INSERT INTO `area` VALUES ('1489', '371621', '惠民县', '371600');
INSERT INTO `area` VALUES ('1490', '371622', '阳信县', '371600');
INSERT INTO `area` VALUES ('1491', '371623', '无棣县', '371600');
INSERT INTO `area` VALUES ('1492', '371624', '沾化县', '371600');
INSERT INTO `area` VALUES ('1493', '371625', '博兴县', '371600');
INSERT INTO `area` VALUES ('1494', '371626', '邹平县', '371600');
INSERT INTO `area` VALUES ('1495', '371701', '市辖区', '371700');
INSERT INTO `area` VALUES ('1496', '371702', '牡丹区', '371700');
INSERT INTO `area` VALUES ('1497', '371721', '曹　县', '371700');
INSERT INTO `area` VALUES ('1498', '371722', '单　县', '371700');
INSERT INTO `area` VALUES ('1499', '371723', '成武县', '371700');
INSERT INTO `area` VALUES ('1500', '371724', '巨野县', '371700');
INSERT INTO `area` VALUES ('1501', '371725', '郓城县', '371700');
INSERT INTO `area` VALUES ('1502', '371726', '鄄城县', '371700');
INSERT INTO `area` VALUES ('1503', '371727', '定陶县', '371700');
INSERT INTO `area` VALUES ('1504', '371728', '东明县', '371700');
INSERT INTO `area` VALUES ('1505', '410101', '市辖区', '410100');
INSERT INTO `area` VALUES ('1506', '410102', '中原区', '410100');
INSERT INTO `area` VALUES ('1507', '410103', '二七区', '410100');
INSERT INTO `area` VALUES ('1508', '410104', '管城回族区', '410100');
INSERT INTO `area` VALUES ('1509', '410105', '金水区', '410100');
INSERT INTO `area` VALUES ('1510', '410106', '上街区', '410100');
INSERT INTO `area` VALUES ('1511', '410108', '邙山区', '410100');
INSERT INTO `area` VALUES ('1512', '410122', '中牟县', '410100');
INSERT INTO `area` VALUES ('1513', '410181', '巩义市', '410100');
INSERT INTO `area` VALUES ('1514', '410182', '荥阳市', '410100');
INSERT INTO `area` VALUES ('1515', '410183', '新密市', '410100');
INSERT INTO `area` VALUES ('1516', '410184', '新郑市', '410100');
INSERT INTO `area` VALUES ('1517', '410185', '登封市', '410100');
INSERT INTO `area` VALUES ('1518', '410201', '市辖区', '410200');
INSERT INTO `area` VALUES ('1519', '410202', '龙亭区', '410200');
INSERT INTO `area` VALUES ('1520', '410203', '顺河回族区', '410200');
INSERT INTO `area` VALUES ('1521', '410204', '鼓楼区', '410200');
INSERT INTO `area` VALUES ('1522', '410205', '南关区', '410200');
INSERT INTO `area` VALUES ('1523', '410211', '郊　区', '410200');
INSERT INTO `area` VALUES ('1524', '410221', '杞　县', '410200');
INSERT INTO `area` VALUES ('1525', '410222', '通许县', '410200');
INSERT INTO `area` VALUES ('1526', '410223', '尉氏县', '410200');
INSERT INTO `area` VALUES ('1527', '410224', '开封县', '410200');
INSERT INTO `area` VALUES ('1528', '410225', '兰考县', '410200');
INSERT INTO `area` VALUES ('1529', '410301', '市辖区', '410300');
INSERT INTO `area` VALUES ('1530', '410302', '老城区', '410300');
INSERT INTO `area` VALUES ('1531', '410303', '西工区', '410300');
INSERT INTO `area` VALUES ('1532', '410304', '廛河回族区', '410300');
INSERT INTO `area` VALUES ('1533', '410305', '涧西区', '410300');
INSERT INTO `area` VALUES ('1534', '410306', '吉利区', '410300');
INSERT INTO `area` VALUES ('1535', '410307', '洛龙区', '410300');
INSERT INTO `area` VALUES ('1536', '410322', '孟津县', '410300');
INSERT INTO `area` VALUES ('1537', '410323', '新安县', '410300');
INSERT INTO `area` VALUES ('1538', '410324', '栾川县', '410300');
INSERT INTO `area` VALUES ('1539', '410325', '嵩　县', '410300');
INSERT INTO `area` VALUES ('1540', '410326', '汝阳县', '410300');
INSERT INTO `area` VALUES ('1541', '410327', '宜阳县', '410300');
INSERT INTO `area` VALUES ('1542', '410328', '洛宁县', '410300');
INSERT INTO `area` VALUES ('1543', '410329', '伊川县', '410300');
INSERT INTO `area` VALUES ('1544', '410381', '偃师市', '410300');
INSERT INTO `area` VALUES ('1545', '410401', '市辖区', '410400');
INSERT INTO `area` VALUES ('1546', '410402', '新华区', '410400');
INSERT INTO `area` VALUES ('1547', '410403', '卫东区', '410400');
INSERT INTO `area` VALUES ('1548', '410404', '石龙区', '410400');
INSERT INTO `area` VALUES ('1549', '410411', '湛河区', '410400');
INSERT INTO `area` VALUES ('1550', '410421', '宝丰县', '410400');
INSERT INTO `area` VALUES ('1551', '410422', '叶　县', '410400');
INSERT INTO `area` VALUES ('1552', '410423', '鲁山县', '410400');
INSERT INTO `area` VALUES ('1553', '410425', '郏　县', '410400');
INSERT INTO `area` VALUES ('1554', '410481', '舞钢市', '410400');
INSERT INTO `area` VALUES ('1555', '410482', '汝州市', '410400');
INSERT INTO `area` VALUES ('1556', '410501', '市辖区', '410500');
INSERT INTO `area` VALUES ('1557', '410502', '文峰区', '410500');
INSERT INTO `area` VALUES ('1558', '410503', '北关区', '410500');
INSERT INTO `area` VALUES ('1559', '410505', '殷都区', '410500');
INSERT INTO `area` VALUES ('1560', '410506', '龙安区', '410500');
INSERT INTO `area` VALUES ('1561', '410522', '安阳县', '410500');
INSERT INTO `area` VALUES ('1562', '410523', '汤阴县', '410500');
INSERT INTO `area` VALUES ('1563', '410526', '滑　县', '410500');
INSERT INTO `area` VALUES ('1564', '410527', '内黄县', '410500');
INSERT INTO `area` VALUES ('1565', '410581', '林州市', '410500');
INSERT INTO `area` VALUES ('1566', '410601', '市辖区', '410600');
INSERT INTO `area` VALUES ('1567', '410602', '鹤山区', '410600');
INSERT INTO `area` VALUES ('1568', '410603', '山城区', '410600');
INSERT INTO `area` VALUES ('1569', '410611', '淇滨区', '410600');
INSERT INTO `area` VALUES ('1570', '410621', '浚　县', '410600');
INSERT INTO `area` VALUES ('1571', '410622', '淇　县', '410600');
INSERT INTO `area` VALUES ('1572', '410701', '市辖区', '410700');
INSERT INTO `area` VALUES ('1573', '410702', '红旗区', '410700');
INSERT INTO `area` VALUES ('1574', '410703', '卫滨区', '410700');
INSERT INTO `area` VALUES ('1575', '410704', '凤泉区', '410700');
INSERT INTO `area` VALUES ('1576', '410711', '牧野区', '410700');
INSERT INTO `area` VALUES ('1577', '410721', '新乡县', '410700');
INSERT INTO `area` VALUES ('1578', '410724', '获嘉县', '410700');
INSERT INTO `area` VALUES ('1579', '410725', '原阳县', '410700');
INSERT INTO `area` VALUES ('1580', '410726', '延津县', '410700');
INSERT INTO `area` VALUES ('1581', '410727', '封丘县', '410700');
INSERT INTO `area` VALUES ('1582', '410728', '长垣县', '410700');
INSERT INTO `area` VALUES ('1583', '410781', '卫辉市', '410700');
INSERT INTO `area` VALUES ('1584', '410782', '辉县市', '410700');
INSERT INTO `area` VALUES ('1585', '410801', '市辖区', '410800');
INSERT INTO `area` VALUES ('1586', '410802', '解放区', '410800');
INSERT INTO `area` VALUES ('1587', '410803', '中站区', '410800');
INSERT INTO `area` VALUES ('1588', '410804', '马村区', '410800');
INSERT INTO `area` VALUES ('1589', '410811', '山阳区', '410800');
INSERT INTO `area` VALUES ('1590', '410821', '修武县', '410800');
INSERT INTO `area` VALUES ('1591', '410822', '博爱县', '410800');
INSERT INTO `area` VALUES ('1592', '410823', '武陟县', '410800');
INSERT INTO `area` VALUES ('1593', '410825', '温　县', '410800');
INSERT INTO `area` VALUES ('1594', '410881', '济源市', '410800');
INSERT INTO `area` VALUES ('1595', '410882', '沁阳市', '410800');
INSERT INTO `area` VALUES ('1596', '410883', '孟州市', '410800');
INSERT INTO `area` VALUES ('1597', '410901', '市辖区', '410900');
INSERT INTO `area` VALUES ('1598', '410902', '华龙区', '410900');
INSERT INTO `area` VALUES ('1599', '410922', '清丰县', '410900');
INSERT INTO `area` VALUES ('1600', '410923', '南乐县', '410900');
INSERT INTO `area` VALUES ('1601', '410926', '范　县', '410900');
INSERT INTO `area` VALUES ('1602', '410927', '台前县', '410900');
INSERT INTO `area` VALUES ('1603', '410928', '濮阳县', '410900');
INSERT INTO `area` VALUES ('1604', '411001', '市辖区', '411000');
INSERT INTO `area` VALUES ('1605', '411002', '魏都区', '411000');
INSERT INTO `area` VALUES ('1606', '411023', '许昌县', '411000');
INSERT INTO `area` VALUES ('1607', '411024', '鄢陵县', '411000');
INSERT INTO `area` VALUES ('1608', '411025', '襄城县', '411000');
INSERT INTO `area` VALUES ('1609', '411081', '禹州市', '411000');
INSERT INTO `area` VALUES ('1610', '411082', '长葛市', '411000');
INSERT INTO `area` VALUES ('1611', '411101', '市辖区', '411100');
INSERT INTO `area` VALUES ('1612', '411102', '源汇区', '411100');
INSERT INTO `area` VALUES ('1613', '411103', '郾城区', '411100');
INSERT INTO `area` VALUES ('1614', '411104', '召陵区', '411100');
INSERT INTO `area` VALUES ('1615', '411121', '舞阳县', '411100');
INSERT INTO `area` VALUES ('1616', '411122', '临颍县', '411100');
INSERT INTO `area` VALUES ('1617', '411201', '市辖区', '411200');
INSERT INTO `area` VALUES ('1618', '411202', '湖滨区', '411200');
INSERT INTO `area` VALUES ('1619', '411221', '渑池县', '411200');
INSERT INTO `area` VALUES ('1620', '411222', '陕　县', '411200');
INSERT INTO `area` VALUES ('1621', '411224', '卢氏县', '411200');
INSERT INTO `area` VALUES ('1622', '411281', '义马市', '411200');
INSERT INTO `area` VALUES ('1623', '411282', '灵宝市', '411200');
INSERT INTO `area` VALUES ('1624', '411301', '市辖区', '411300');
INSERT INTO `area` VALUES ('1625', '411302', '宛城区', '411300');
INSERT INTO `area` VALUES ('1626', '411303', '卧龙区', '411300');
INSERT INTO `area` VALUES ('1627', '411321', '南召县', '411300');
INSERT INTO `area` VALUES ('1628', '411322', '方城县', '411300');
INSERT INTO `area` VALUES ('1629', '411323', '西峡县', '411300');
INSERT INTO `area` VALUES ('1630', '411324', '镇平县', '411300');
INSERT INTO `area` VALUES ('1631', '411325', '内乡县', '411300');
INSERT INTO `area` VALUES ('1632', '411326', '淅川县', '411300');
INSERT INTO `area` VALUES ('1633', '411327', '社旗县', '411300');
INSERT INTO `area` VALUES ('1634', '411328', '唐河县', '411300');
INSERT INTO `area` VALUES ('1635', '411329', '新野县', '411300');
INSERT INTO `area` VALUES ('1636', '411330', '桐柏县', '411300');
INSERT INTO `area` VALUES ('1637', '411381', '邓州市', '411300');
INSERT INTO `area` VALUES ('1638', '411401', '市辖区', '411400');
INSERT INTO `area` VALUES ('1639', '411402', '梁园区', '411400');
INSERT INTO `area` VALUES ('1640', '411403', '睢阳区', '411400');
INSERT INTO `area` VALUES ('1641', '411421', '民权县', '411400');
INSERT INTO `area` VALUES ('1642', '411422', '睢　县', '411400');
INSERT INTO `area` VALUES ('1643', '411423', '宁陵县', '411400');
INSERT INTO `area` VALUES ('1644', '411424', '柘城县', '411400');
INSERT INTO `area` VALUES ('1645', '411425', '虞城县', '411400');
INSERT INTO `area` VALUES ('1646', '411426', '夏邑县', '411400');
INSERT INTO `area` VALUES ('1647', '411481', '永城市', '411400');
INSERT INTO `area` VALUES ('1648', '411501', '市辖区', '411500');
INSERT INTO `area` VALUES ('1649', '411502', '师河区', '411500');
INSERT INTO `area` VALUES ('1650', '411503', '平桥区', '411500');
INSERT INTO `area` VALUES ('1651', '411521', '罗山县', '411500');
INSERT INTO `area` VALUES ('1652', '411522', '光山县', '411500');
INSERT INTO `area` VALUES ('1653', '411523', '新　县', '411500');
INSERT INTO `area` VALUES ('1654', '411524', '商城县', '411500');
INSERT INTO `area` VALUES ('1655', '411525', '固始县', '411500');
INSERT INTO `area` VALUES ('1656', '411526', '潢川县', '411500');
INSERT INTO `area` VALUES ('1657', '411527', '淮滨县', '411500');
INSERT INTO `area` VALUES ('1658', '411528', '息　县', '411500');
INSERT INTO `area` VALUES ('1659', '411601', '市辖区', '411600');
INSERT INTO `area` VALUES ('1660', '411602', '川汇区', '411600');
INSERT INTO `area` VALUES ('1661', '411621', '扶沟县', '411600');
INSERT INTO `area` VALUES ('1662', '411622', '西华县', '411600');
INSERT INTO `area` VALUES ('1663', '411623', '商水县', '411600');
INSERT INTO `area` VALUES ('1664', '411624', '沈丘县', '411600');
INSERT INTO `area` VALUES ('1665', '411625', '郸城县', '411600');
INSERT INTO `area` VALUES ('1666', '411626', '淮阳县', '411600');
INSERT INTO `area` VALUES ('1667', '411627', '太康县', '411600');
INSERT INTO `area` VALUES ('1668', '411628', '鹿邑县', '411600');
INSERT INTO `area` VALUES ('1669', '411681', '项城市', '411600');
INSERT INTO `area` VALUES ('1670', '411701', '市辖区', '411700');
INSERT INTO `area` VALUES ('1671', '411702', '驿城区', '411700');
INSERT INTO `area` VALUES ('1672', '411721', '西平县', '411700');
INSERT INTO `area` VALUES ('1673', '411722', '上蔡县', '411700');
INSERT INTO `area` VALUES ('1674', '411723', '平舆县', '411700');
INSERT INTO `area` VALUES ('1675', '411724', '正阳县', '411700');
INSERT INTO `area` VALUES ('1676', '411725', '确山县', '411700');
INSERT INTO `area` VALUES ('1677', '411726', '泌阳县', '411700');
INSERT INTO `area` VALUES ('1678', '411727', '汝南县', '411700');
INSERT INTO `area` VALUES ('1679', '411728', '遂平县', '411700');
INSERT INTO `area` VALUES ('1680', '411729', '新蔡县', '411700');
INSERT INTO `area` VALUES ('1681', '420101', '市辖区', '420100');
INSERT INTO `area` VALUES ('1682', '420102', '江岸区', '420100');
INSERT INTO `area` VALUES ('1683', '420103', '江汉区', '420100');
INSERT INTO `area` VALUES ('1684', '420104', '乔口区', '420100');
INSERT INTO `area` VALUES ('1685', '420105', '汉阳区', '420100');
INSERT INTO `area` VALUES ('1686', '420106', '武昌区', '420100');
INSERT INTO `area` VALUES ('1687', '420107', '青山区', '420100');
INSERT INTO `area` VALUES ('1688', '420111', '洪山区', '420100');
INSERT INTO `area` VALUES ('1689', '420112', '东西湖区', '420100');
INSERT INTO `area` VALUES ('1690', '420113', '汉南区', '420100');
INSERT INTO `area` VALUES ('1691', '420114', '蔡甸区', '420100');
INSERT INTO `area` VALUES ('1692', '420115', '江夏区', '420100');
INSERT INTO `area` VALUES ('1693', '420116', '黄陂区', '420100');
INSERT INTO `area` VALUES ('1694', '420117', '新洲区', '420100');
INSERT INTO `area` VALUES ('1695', '420201', '市辖区', '420200');
INSERT INTO `area` VALUES ('1696', '420202', '黄石港区', '420200');
INSERT INTO `area` VALUES ('1697', '420203', '西塞山区', '420200');
INSERT INTO `area` VALUES ('1698', '420204', '下陆区', '420200');
INSERT INTO `area` VALUES ('1699', '420205', '铁山区', '420200');
INSERT INTO `area` VALUES ('1700', '420222', '阳新县', '420200');
INSERT INTO `area` VALUES ('1701', '420281', '大冶市', '420200');
INSERT INTO `area` VALUES ('1702', '420301', '市辖区', '420300');
INSERT INTO `area` VALUES ('1703', '420302', '茅箭区', '420300');
INSERT INTO `area` VALUES ('1704', '420303', '张湾区', '420300');
INSERT INTO `area` VALUES ('1705', '420321', '郧　县', '420300');
INSERT INTO `area` VALUES ('1706', '420322', '郧西县', '420300');
INSERT INTO `area` VALUES ('1707', '420323', '竹山县', '420300');
INSERT INTO `area` VALUES ('1708', '420324', '竹溪县', '420300');
INSERT INTO `area` VALUES ('1709', '420325', '房　县', '420300');
INSERT INTO `area` VALUES ('1710', '420381', '丹江口市', '420300');
INSERT INTO `area` VALUES ('1711', '420501', '市辖区', '420500');
INSERT INTO `area` VALUES ('1712', '420502', '西陵区', '420500');
INSERT INTO `area` VALUES ('1713', '420503', '伍家岗区', '420500');
INSERT INTO `area` VALUES ('1714', '420504', '点军区', '420500');
INSERT INTO `area` VALUES ('1715', '420505', '猇亭区', '420500');
INSERT INTO `area` VALUES ('1716', '420506', '夷陵区', '420500');
INSERT INTO `area` VALUES ('1717', '420525', '远安县', '420500');
INSERT INTO `area` VALUES ('1718', '420526', '兴山县', '420500');
INSERT INTO `area` VALUES ('1719', '420527', '秭归县', '420500');
INSERT INTO `area` VALUES ('1720', '420528', '长阳土家族自治县', '420500');
INSERT INTO `area` VALUES ('1721', '420529', '五峰土家族自治县', '420500');
INSERT INTO `area` VALUES ('1722', '420581', '宜都市', '420500');
INSERT INTO `area` VALUES ('1723', '420582', '当阳市', '420500');
INSERT INTO `area` VALUES ('1724', '420583', '枝江市', '420500');
INSERT INTO `area` VALUES ('1725', '420601', '市辖区', '420600');
INSERT INTO `area` VALUES ('1726', '420602', '襄城区', '420600');
INSERT INTO `area` VALUES ('1727', '420606', '樊城区', '420600');
INSERT INTO `area` VALUES ('1728', '420607', '襄阳区', '420600');
INSERT INTO `area` VALUES ('1729', '420624', '南漳县', '420600');
INSERT INTO `area` VALUES ('1730', '420625', '谷城县', '420600');
INSERT INTO `area` VALUES ('1731', '420626', '保康县', '420600');
INSERT INTO `area` VALUES ('1732', '420682', '老河口市', '420600');
INSERT INTO `area` VALUES ('1733', '420683', '枣阳市', '420600');
INSERT INTO `area` VALUES ('1734', '420684', '宜城市', '420600');
INSERT INTO `area` VALUES ('1735', '420701', '市辖区', '420700');
INSERT INTO `area` VALUES ('1736', '420702', '梁子湖区', '420700');
INSERT INTO `area` VALUES ('1737', '420703', '华容区', '420700');
INSERT INTO `area` VALUES ('1738', '420704', '鄂城区', '420700');
INSERT INTO `area` VALUES ('1739', '420801', '市辖区', '420800');
INSERT INTO `area` VALUES ('1740', '420802', '东宝区', '420800');
INSERT INTO `area` VALUES ('1741', '420804', '掇刀区', '420800');
INSERT INTO `area` VALUES ('1742', '420821', '京山县', '420800');
INSERT INTO `area` VALUES ('1743', '420822', '沙洋县', '420800');
INSERT INTO `area` VALUES ('1744', '420881', '钟祥市', '420800');
INSERT INTO `area` VALUES ('1745', '420901', '市辖区', '420900');
INSERT INTO `area` VALUES ('1746', '420902', '孝南区', '420900');
INSERT INTO `area` VALUES ('1747', '420921', '孝昌县', '420900');
INSERT INTO `area` VALUES ('1748', '420922', '大悟县', '420900');
INSERT INTO `area` VALUES ('1749', '420923', '云梦县', '420900');
INSERT INTO `area` VALUES ('1750', '420981', '应城市', '420900');
INSERT INTO `area` VALUES ('1751', '420982', '安陆市', '420900');
INSERT INTO `area` VALUES ('1752', '420984', '汉川市', '420900');
INSERT INTO `area` VALUES ('1753', '421001', '市辖区', '421000');
INSERT INTO `area` VALUES ('1754', '421002', '沙市区', '421000');
INSERT INTO `area` VALUES ('1755', '421003', '荆州区', '421000');
INSERT INTO `area` VALUES ('1756', '421022', '公安县', '421000');
INSERT INTO `area` VALUES ('1757', '421023', '监利县', '421000');
INSERT INTO `area` VALUES ('1758', '421024', '江陵县', '421000');
INSERT INTO `area` VALUES ('1759', '421081', '石首市', '421000');
INSERT INTO `area` VALUES ('1760', '421083', '洪湖市', '421000');
INSERT INTO `area` VALUES ('1761', '421087', '松滋市', '421000');
INSERT INTO `area` VALUES ('1762', '421101', '市辖区', '421100');
INSERT INTO `area` VALUES ('1763', '421102', '黄州区', '421100');
INSERT INTO `area` VALUES ('1764', '421121', '团风县', '421100');
INSERT INTO `area` VALUES ('1765', '421122', '红安县', '421100');
INSERT INTO `area` VALUES ('1766', '421123', '罗田县', '421100');
INSERT INTO `area` VALUES ('1767', '421124', '英山县', '421100');
INSERT INTO `area` VALUES ('1768', '421125', '浠水县', '421100');
INSERT INTO `area` VALUES ('1769', '421126', '蕲春县', '421100');
INSERT INTO `area` VALUES ('1770', '421127', '黄梅县', '421100');
INSERT INTO `area` VALUES ('1771', '421181', '麻城市', '421100');
INSERT INTO `area` VALUES ('1772', '421182', '武穴市', '421100');
INSERT INTO `area` VALUES ('1773', '421201', '市辖区', '421200');
INSERT INTO `area` VALUES ('1774', '421202', '咸安区', '421200');
INSERT INTO `area` VALUES ('1775', '421221', '嘉鱼县', '421200');
INSERT INTO `area` VALUES ('1776', '421222', '通城县', '421200');
INSERT INTO `area` VALUES ('1777', '421223', '崇阳县', '421200');
INSERT INTO `area` VALUES ('1778', '421224', '通山县', '421200');
INSERT INTO `area` VALUES ('1779', '421281', '赤壁市', '421200');
INSERT INTO `area` VALUES ('1780', '421301', '市辖区', '421300');
INSERT INTO `area` VALUES ('1781', '421302', '曾都区', '421300');
INSERT INTO `area` VALUES ('1782', '421381', '广水市', '421300');
INSERT INTO `area` VALUES ('1783', '422801', '恩施市', '422800');
INSERT INTO `area` VALUES ('1784', '422802', '利川市', '422800');
INSERT INTO `area` VALUES ('1785', '422822', '建始县', '422800');
INSERT INTO `area` VALUES ('1786', '422823', '巴东县', '422800');
INSERT INTO `area` VALUES ('1787', '422825', '宣恩县', '422800');
INSERT INTO `area` VALUES ('1788', '422826', '咸丰县', '422800');
INSERT INTO `area` VALUES ('1789', '422827', '来凤县', '422800');
INSERT INTO `area` VALUES ('1790', '422828', '鹤峰县', '422800');
INSERT INTO `area` VALUES ('1791', '429004', '仙桃市', '429000');
INSERT INTO `area` VALUES ('1792', '429005', '潜江市', '429000');
INSERT INTO `area` VALUES ('1793', '429006', '天门市', '429000');
INSERT INTO `area` VALUES ('1794', '429021', '神农架林区', '429000');
INSERT INTO `area` VALUES ('1795', '430101', '市辖区', '430100');
INSERT INTO `area` VALUES ('1796', '430102', '芙蓉区', '430100');
INSERT INTO `area` VALUES ('1797', '430103', '天心区', '430100');
INSERT INTO `area` VALUES ('1798', '430104', '岳麓区', '430100');
INSERT INTO `area` VALUES ('1799', '430105', '开福区', '430100');
INSERT INTO `area` VALUES ('1800', '430111', '雨花区', '430100');
INSERT INTO `area` VALUES ('1801', '430121', '长沙县', '430100');
INSERT INTO `area` VALUES ('1802', '430122', '望城县', '430100');
INSERT INTO `area` VALUES ('1803', '430124', '宁乡县', '430100');
INSERT INTO `area` VALUES ('1804', '430181', '浏阳市', '430100');
INSERT INTO `area` VALUES ('1805', '430201', '市辖区', '430200');
INSERT INTO `area` VALUES ('1806', '430202', '荷塘区', '430200');
INSERT INTO `area` VALUES ('1807', '430203', '芦淞区', '430200');
INSERT INTO `area` VALUES ('1808', '430204', '石峰区', '430200');
INSERT INTO `area` VALUES ('1809', '430211', '天元区', '430200');
INSERT INTO `area` VALUES ('1810', '430221', '株洲县', '430200');
INSERT INTO `area` VALUES ('1811', '430223', '攸　县', '430200');
INSERT INTO `area` VALUES ('1812', '430224', '茶陵县', '430200');
INSERT INTO `area` VALUES ('1813', '430225', '炎陵县', '430200');
INSERT INTO `area` VALUES ('1814', '430281', '醴陵市', '430200');
INSERT INTO `area` VALUES ('1815', '430301', '市辖区', '430300');
INSERT INTO `area` VALUES ('1816', '430302', '雨湖区', '430300');
INSERT INTO `area` VALUES ('1817', '430304', '岳塘区', '430300');
INSERT INTO `area` VALUES ('1818', '430321', '湘潭县', '430300');
INSERT INTO `area` VALUES ('1819', '430381', '湘乡市', '430300');
INSERT INTO `area` VALUES ('1820', '430382', '韶山市', '430300');
INSERT INTO `area` VALUES ('1821', '430401', '市辖区', '430400');
INSERT INTO `area` VALUES ('1822', '430405', '珠晖区', '430400');
INSERT INTO `area` VALUES ('1823', '430406', '雁峰区', '430400');
INSERT INTO `area` VALUES ('1824', '430407', '石鼓区', '430400');
INSERT INTO `area` VALUES ('1825', '430408', '蒸湘区', '430400');
INSERT INTO `area` VALUES ('1826', '430412', '南岳区', '430400');
INSERT INTO `area` VALUES ('1827', '430421', '衡阳县', '430400');
INSERT INTO `area` VALUES ('1828', '430422', '衡南县', '430400');
INSERT INTO `area` VALUES ('1829', '430423', '衡山县', '430400');
INSERT INTO `area` VALUES ('1830', '430424', '衡东县', '430400');
INSERT INTO `area` VALUES ('1831', '430426', '祁东县', '430400');
INSERT INTO `area` VALUES ('1832', '430481', '耒阳市', '430400');
INSERT INTO `area` VALUES ('1833', '430482', '常宁市', '430400');
INSERT INTO `area` VALUES ('1834', '430501', '市辖区', '430500');
INSERT INTO `area` VALUES ('1835', '430502', '双清区', '430500');
INSERT INTO `area` VALUES ('1836', '430503', '大祥区', '430500');
INSERT INTO `area` VALUES ('1837', '430511', '北塔区', '430500');
INSERT INTO `area` VALUES ('1838', '430521', '邵东县', '430500');
INSERT INTO `area` VALUES ('1839', '430522', '新邵县', '430500');
INSERT INTO `area` VALUES ('1840', '430523', '邵阳县', '430500');
INSERT INTO `area` VALUES ('1841', '430524', '隆回县', '430500');
INSERT INTO `area` VALUES ('1842', '430525', '洞口县', '430500');
INSERT INTO `area` VALUES ('1843', '430527', '绥宁县', '430500');
INSERT INTO `area` VALUES ('1844', '430528', '新宁县', '430500');
INSERT INTO `area` VALUES ('1845', '430529', '城步苗族自治县', '430500');
INSERT INTO `area` VALUES ('1846', '430581', '武冈市', '430500');
INSERT INTO `area` VALUES ('1847', '430601', '市辖区', '430600');
INSERT INTO `area` VALUES ('1848', '430602', '岳阳楼区', '430600');
INSERT INTO `area` VALUES ('1849', '430603', '云溪区', '430600');
INSERT INTO `area` VALUES ('1850', '430611', '君山区', '430600');
INSERT INTO `area` VALUES ('1851', '430621', '岳阳县', '430600');
INSERT INTO `area` VALUES ('1852', '430623', '华容县', '430600');
INSERT INTO `area` VALUES ('1853', '430624', '湘阴县', '430600');
INSERT INTO `area` VALUES ('1854', '430626', '平江县', '430600');
INSERT INTO `area` VALUES ('1855', '430681', '汨罗市', '430600');
INSERT INTO `area` VALUES ('1856', '430682', '临湘市', '430600');
INSERT INTO `area` VALUES ('1857', '430701', '市辖区', '430700');
INSERT INTO `area` VALUES ('1858', '430702', '武陵区', '430700');
INSERT INTO `area` VALUES ('1859', '430703', '鼎城区', '430700');
INSERT INTO `area` VALUES ('1860', '430721', '安乡县', '430700');
INSERT INTO `area` VALUES ('1861', '430722', '汉寿县', '430700');
INSERT INTO `area` VALUES ('1862', '430723', '澧　县', '430700');
INSERT INTO `area` VALUES ('1863', '430724', '临澧县', '430700');
INSERT INTO `area` VALUES ('1864', '430725', '桃源县', '430700');
INSERT INTO `area` VALUES ('1865', '430726', '石门县', '430700');
INSERT INTO `area` VALUES ('1866', '430781', '津市市', '430700');
INSERT INTO `area` VALUES ('1867', '430801', '市辖区', '430800');
INSERT INTO `area` VALUES ('1868', '430802', '永定区', '430800');
INSERT INTO `area` VALUES ('1869', '430811', '武陵源区', '430800');
INSERT INTO `area` VALUES ('1870', '430821', '慈利县', '430800');
INSERT INTO `area` VALUES ('1871', '430822', '桑植县', '430800');
INSERT INTO `area` VALUES ('1872', '430901', '市辖区', '430900');
INSERT INTO `area` VALUES ('1873', '430902', '资阳区', '430900');
INSERT INTO `area` VALUES ('1874', '430903', '赫山区', '430900');
INSERT INTO `area` VALUES ('1875', '430921', '南　县', '430900');
INSERT INTO `area` VALUES ('1876', '430922', '桃江县', '430900');
INSERT INTO `area` VALUES ('1877', '430923', '安化县', '430900');
INSERT INTO `area` VALUES ('1878', '430981', '沅江市', '430900');
INSERT INTO `area` VALUES ('1879', '431001', '市辖区', '431000');
INSERT INTO `area` VALUES ('1880', '431002', '北湖区', '431000');
INSERT INTO `area` VALUES ('1881', '431003', '苏仙区', '431000');
INSERT INTO `area` VALUES ('1882', '431021', '桂阳县', '431000');
INSERT INTO `area` VALUES ('1883', '431022', '宜章县', '431000');
INSERT INTO `area` VALUES ('1884', '431023', '永兴县', '431000');
INSERT INTO `area` VALUES ('1885', '431024', '嘉禾县', '431000');
INSERT INTO `area` VALUES ('1886', '431025', '临武县', '431000');
INSERT INTO `area` VALUES ('1887', '431026', '汝城县', '431000');
INSERT INTO `area` VALUES ('1888', '431027', '桂东县', '431000');
INSERT INTO `area` VALUES ('1889', '431028', '安仁县', '431000');
INSERT INTO `area` VALUES ('1890', '431081', '资兴市', '431000');
INSERT INTO `area` VALUES ('1891', '431101', '市辖区', '431100');
INSERT INTO `area` VALUES ('1892', '431102', '芝山区', '431100');
INSERT INTO `area` VALUES ('1893', '431103', '冷水滩区', '431100');
INSERT INTO `area` VALUES ('1894', '431121', '祁阳县', '431100');
INSERT INTO `area` VALUES ('1895', '431122', '东安县', '431100');
INSERT INTO `area` VALUES ('1896', '431123', '双牌县', '431100');
INSERT INTO `area` VALUES ('1897', '431124', '道　县', '431100');
INSERT INTO `area` VALUES ('1898', '431125', '江永县', '431100');
INSERT INTO `area` VALUES ('1899', '431126', '宁远县', '431100');
INSERT INTO `area` VALUES ('1900', '431127', '蓝山县', '431100');
INSERT INTO `area` VALUES ('1901', '431128', '新田县', '431100');
INSERT INTO `area` VALUES ('1902', '431129', '江华瑶族自治县', '431100');
INSERT INTO `area` VALUES ('1903', '431201', '市辖区', '431200');
INSERT INTO `area` VALUES ('1904', '431202', '鹤城区', '431200');
INSERT INTO `area` VALUES ('1905', '431221', '中方县', '431200');
INSERT INTO `area` VALUES ('1906', '431222', '沅陵县', '431200');
INSERT INTO `area` VALUES ('1907', '431223', '辰溪县', '431200');
INSERT INTO `area` VALUES ('1908', '431224', '溆浦县', '431200');
INSERT INTO `area` VALUES ('1909', '431225', '会同县', '431200');
INSERT INTO `area` VALUES ('1910', '431226', '麻阳苗族自治县', '431200');
INSERT INTO `area` VALUES ('1911', '431227', '新晃侗族自治县', '431200');
INSERT INTO `area` VALUES ('1912', '431228', '芷江侗族自治县', '431200');
INSERT INTO `area` VALUES ('1913', '431229', '靖州苗族侗族自治县', '431200');
INSERT INTO `area` VALUES ('1914', '431230', '通道侗族自治县', '431200');
INSERT INTO `area` VALUES ('1915', '431281', '洪江市', '431200');
INSERT INTO `area` VALUES ('1916', '431301', '市辖区', '431300');
INSERT INTO `area` VALUES ('1917', '431302', '娄星区', '431300');
INSERT INTO `area` VALUES ('1918', '431321', '双峰县', '431300');
INSERT INTO `area` VALUES ('1919', '431322', '新化县', '431300');
INSERT INTO `area` VALUES ('1920', '431381', '冷水江市', '431300');
INSERT INTO `area` VALUES ('1921', '431382', '涟源市', '431300');
INSERT INTO `area` VALUES ('1922', '433101', '吉首市', '433100');
INSERT INTO `area` VALUES ('1923', '433122', '泸溪县', '433100');
INSERT INTO `area` VALUES ('1924', '433123', '凤凰县', '433100');
INSERT INTO `area` VALUES ('1925', '433124', '花垣县', '433100');
INSERT INTO `area` VALUES ('1926', '433125', '保靖县', '433100');
INSERT INTO `area` VALUES ('1927', '433126', '古丈县', '433100');
INSERT INTO `area` VALUES ('1928', '433127', '永顺县', '433100');
INSERT INTO `area` VALUES ('1929', '433130', '龙山县', '433100');
INSERT INTO `area` VALUES ('1930', '440101', '市辖区', '440100');
INSERT INTO `area` VALUES ('1931', '440102', '东山区', '440100');
INSERT INTO `area` VALUES ('1932', '440103', '荔湾区', '440100');
INSERT INTO `area` VALUES ('1933', '440104', '越秀区', '440100');
INSERT INTO `area` VALUES ('1934', '440105', '海珠区', '440100');
INSERT INTO `area` VALUES ('1935', '440106', '天河区', '440100');
INSERT INTO `area` VALUES ('1936', '440107', '芳村区', '440100');
INSERT INTO `area` VALUES ('1937', '440111', '白云区', '440100');
INSERT INTO `area` VALUES ('1938', '440112', '黄埔区', '440100');
INSERT INTO `area` VALUES ('1939', '440113', '番禺区', '440100');
INSERT INTO `area` VALUES ('1940', '440114', '花都区', '440100');
INSERT INTO `area` VALUES ('1941', '440183', '增城市', '440100');
INSERT INTO `area` VALUES ('1942', '440184', '从化市', '440100');
INSERT INTO `area` VALUES ('1943', '440201', '市辖区', '440200');
INSERT INTO `area` VALUES ('1944', '440203', '武江区', '440200');
INSERT INTO `area` VALUES ('1945', '440204', '浈江区', '440200');
INSERT INTO `area` VALUES ('1946', '440205', '曲江区', '440200');
INSERT INTO `area` VALUES ('1947', '440222', '始兴县', '440200');
INSERT INTO `area` VALUES ('1948', '440224', '仁化县', '440200');
INSERT INTO `area` VALUES ('1949', '440229', '翁源县', '440200');
INSERT INTO `area` VALUES ('1950', '440232', '乳源瑶族自治县', '440200');
INSERT INTO `area` VALUES ('1951', '440233', '新丰县', '440200');
INSERT INTO `area` VALUES ('1952', '440281', '乐昌市', '440200');
INSERT INTO `area` VALUES ('1953', '440282', '南雄市', '440200');
INSERT INTO `area` VALUES ('1954', '440301', '市辖区', '440300');
INSERT INTO `area` VALUES ('1955', '440303', '罗湖区', '440300');
INSERT INTO `area` VALUES ('1956', '440304', '福田区', '440300');
INSERT INTO `area` VALUES ('1957', '440305', '南山区', '440300');
INSERT INTO `area` VALUES ('1958', '440306', '宝安区', '440300');
INSERT INTO `area` VALUES ('1959', '440307', '龙岗区', '440300');
INSERT INTO `area` VALUES ('1960', '440308', '盐田区', '440300');
INSERT INTO `area` VALUES ('1961', '440401', '市辖区', '440400');
INSERT INTO `area` VALUES ('1962', '440402', '香洲区', '440400');
INSERT INTO `area` VALUES ('1963', '440403', '斗门区', '440400');
INSERT INTO `area` VALUES ('1964', '440404', '金湾区', '440400');
INSERT INTO `area` VALUES ('1965', '440501', '市辖区', '440500');
INSERT INTO `area` VALUES ('1966', '440507', '龙湖区', '440500');
INSERT INTO `area` VALUES ('1967', '440511', '金平区', '440500');
INSERT INTO `area` VALUES ('1968', '440512', '濠江区', '440500');
INSERT INTO `area` VALUES ('1969', '440513', '潮阳区', '440500');
INSERT INTO `area` VALUES ('1970', '440514', '潮南区', '440500');
INSERT INTO `area` VALUES ('1971', '440515', '澄海区', '440500');
INSERT INTO `area` VALUES ('1972', '440523', '南澳县', '440500');
INSERT INTO `area` VALUES ('1973', '440601', '市辖区', '440600');
INSERT INTO `area` VALUES ('1974', '440604', '禅城区', '440600');
INSERT INTO `area` VALUES ('1975', '440605', '南海区', '440600');
INSERT INTO `area` VALUES ('1976', '440606', '顺德区', '440600');
INSERT INTO `area` VALUES ('1977', '440607', '三水区', '440600');
INSERT INTO `area` VALUES ('1978', '440608', '高明区', '440600');
INSERT INTO `area` VALUES ('1979', '440701', '市辖区', '440700');
INSERT INTO `area` VALUES ('1980', '440703', '蓬江区', '440700');
INSERT INTO `area` VALUES ('1981', '440704', '江海区', '440700');
INSERT INTO `area` VALUES ('1982', '440705', '新会区', '440700');
INSERT INTO `area` VALUES ('1983', '440781', '台山市', '440700');
INSERT INTO `area` VALUES ('1984', '440783', '开平市', '440700');
INSERT INTO `area` VALUES ('1985', '440784', '鹤山市', '440700');
INSERT INTO `area` VALUES ('1986', '440785', '恩平市', '440700');
INSERT INTO `area` VALUES ('1987', '440801', '市辖区', '440800');
INSERT INTO `area` VALUES ('1988', '440802', '赤坎区', '440800');
INSERT INTO `area` VALUES ('1989', '440803', '霞山区', '440800');
INSERT INTO `area` VALUES ('1990', '440804', '坡头区', '440800');
INSERT INTO `area` VALUES ('1991', '440811', '麻章区', '440800');
INSERT INTO `area` VALUES ('1992', '440823', '遂溪县', '440800');
INSERT INTO `area` VALUES ('1993', '440825', '徐闻县', '440800');
INSERT INTO `area` VALUES ('1994', '440881', '廉江市', '440800');
INSERT INTO `area` VALUES ('1995', '440882', '雷州市', '440800');
INSERT INTO `area` VALUES ('1996', '440883', '吴川市', '440800');
INSERT INTO `area` VALUES ('1997', '440901', '市辖区', '440900');
INSERT INTO `area` VALUES ('1998', '440902', '茂南区', '440900');
INSERT INTO `area` VALUES ('1999', '440903', '茂港区', '440900');
INSERT INTO `area` VALUES ('2000', '440923', '电白县', '440900');
INSERT INTO `area` VALUES ('2001', '440981', '高州市', '440900');
INSERT INTO `area` VALUES ('2002', '440982', '化州市', '440900');
INSERT INTO `area` VALUES ('2003', '440983', '信宜市', '440900');
INSERT INTO `area` VALUES ('2004', '441201', '市辖区', '441200');
INSERT INTO `area` VALUES ('2005', '441202', '端州区', '441200');
INSERT INTO `area` VALUES ('2006', '441203', '鼎湖区', '441200');
INSERT INTO `area` VALUES ('2007', '441223', '广宁县', '441200');
INSERT INTO `area` VALUES ('2008', '441224', '怀集县', '441200');
INSERT INTO `area` VALUES ('2009', '441225', '封开县', '441200');
INSERT INTO `area` VALUES ('2010', '441226', '德庆县', '441200');
INSERT INTO `area` VALUES ('2011', '441283', '高要市', '441200');
INSERT INTO `area` VALUES ('2012', '441284', '四会市', '441200');
INSERT INTO `area` VALUES ('2013', '441301', '市辖区', '441300');
INSERT INTO `area` VALUES ('2014', '441302', '惠城区', '441300');
INSERT INTO `area` VALUES ('2015', '441303', '惠阳区', '441300');
INSERT INTO `area` VALUES ('2016', '441322', '博罗县', '441300');
INSERT INTO `area` VALUES ('2017', '441323', '惠东县', '441300');
INSERT INTO `area` VALUES ('2018', '441324', '龙门县', '441300');
INSERT INTO `area` VALUES ('2019', '441401', '市辖区', '441400');
INSERT INTO `area` VALUES ('2020', '441402', '梅江区', '441400');
INSERT INTO `area` VALUES ('2021', '441421', '梅　县', '441400');
INSERT INTO `area` VALUES ('2022', '441422', '大埔县', '441400');
INSERT INTO `area` VALUES ('2023', '441423', '丰顺县', '441400');
INSERT INTO `area` VALUES ('2024', '441424', '五华县', '441400');
INSERT INTO `area` VALUES ('2025', '441426', '平远县', '441400');
INSERT INTO `area` VALUES ('2026', '441427', '蕉岭县', '441400');
INSERT INTO `area` VALUES ('2027', '441481', '兴宁市', '441400');
INSERT INTO `area` VALUES ('2028', '441501', '市辖区', '441500');
INSERT INTO `area` VALUES ('2029', '441502', '城　区', '441500');
INSERT INTO `area` VALUES ('2030', '441521', '海丰县', '441500');
INSERT INTO `area` VALUES ('2031', '441523', '陆河县', '441500');
INSERT INTO `area` VALUES ('2032', '441581', '陆丰市', '441500');
INSERT INTO `area` VALUES ('2033', '441601', '市辖区', '441600');
INSERT INTO `area` VALUES ('2034', '441602', '源城区', '441600');
INSERT INTO `area` VALUES ('2035', '441621', '紫金县', '441600');
INSERT INTO `area` VALUES ('2036', '441622', '龙川县', '441600');
INSERT INTO `area` VALUES ('2037', '441623', '连平县', '441600');
INSERT INTO `area` VALUES ('2038', '441624', '和平县', '441600');
INSERT INTO `area` VALUES ('2039', '441625', '东源县', '441600');
INSERT INTO `area` VALUES ('2040', '441701', '市辖区', '441700');
INSERT INTO `area` VALUES ('2041', '441702', '江城区', '441700');
INSERT INTO `area` VALUES ('2042', '441721', '阳西县', '441700');
INSERT INTO `area` VALUES ('2043', '441723', '阳东县', '441700');
INSERT INTO `area` VALUES ('2044', '441781', '阳春市', '441700');
INSERT INTO `area` VALUES ('2045', '441801', '市辖区', '441800');
INSERT INTO `area` VALUES ('2046', '441802', '清城区', '441800');
INSERT INTO `area` VALUES ('2047', '441821', '佛冈县', '441800');
INSERT INTO `area` VALUES ('2048', '441823', '阳山县', '441800');
INSERT INTO `area` VALUES ('2049', '441825', '连山壮族瑶族自治县', '441800');
INSERT INTO `area` VALUES ('2050', '441826', '连南瑶族自治县', '441800');
INSERT INTO `area` VALUES ('2051', '441827', '清新县', '441800');
INSERT INTO `area` VALUES ('2052', '441881', '英德市', '441800');
INSERT INTO `area` VALUES ('2053', '441882', '连州市', '441800');
INSERT INTO `area` VALUES ('2054', '445101', '市辖区', '445100');
INSERT INTO `area` VALUES ('2055', '445102', '湘桥区', '445100');
INSERT INTO `area` VALUES ('2056', '445121', '潮安县', '445100');
INSERT INTO `area` VALUES ('2057', '445122', '饶平县', '445100');
INSERT INTO `area` VALUES ('2058', '445201', '市辖区', '445200');
INSERT INTO `area` VALUES ('2059', '445202', '榕城区', '445200');
INSERT INTO `area` VALUES ('2060', '445221', '揭东县', '445200');
INSERT INTO `area` VALUES ('2061', '445222', '揭西县', '445200');
INSERT INTO `area` VALUES ('2062', '445224', '惠来县', '445200');
INSERT INTO `area` VALUES ('2063', '445281', '普宁市', '445200');
INSERT INTO `area` VALUES ('2064', '445301', '市辖区', '445300');
INSERT INTO `area` VALUES ('2065', '445302', '云城区', '445300');
INSERT INTO `area` VALUES ('2066', '445321', '新兴县', '445300');
INSERT INTO `area` VALUES ('2067', '445322', '郁南县', '445300');
INSERT INTO `area` VALUES ('2068', '445323', '云安县', '445300');
INSERT INTO `area` VALUES ('2069', '445381', '罗定市', '445300');
INSERT INTO `area` VALUES ('2070', '450101', '市辖区', '450100');
INSERT INTO `area` VALUES ('2071', '450102', '兴宁区', '450100');
INSERT INTO `area` VALUES ('2072', '450103', '青秀区', '450100');
INSERT INTO `area` VALUES ('2073', '450105', '江南区', '450100');
INSERT INTO `area` VALUES ('2074', '450107', '西乡塘区', '450100');
INSERT INTO `area` VALUES ('2075', '450108', '良庆区', '450100');
INSERT INTO `area` VALUES ('2076', '450109', '邕宁区', '450100');
INSERT INTO `area` VALUES ('2077', '450122', '武鸣县', '450100');
INSERT INTO `area` VALUES ('2078', '450123', '隆安县', '450100');
INSERT INTO `area` VALUES ('2079', '450124', '马山县', '450100');
INSERT INTO `area` VALUES ('2080', '450125', '上林县', '450100');
INSERT INTO `area` VALUES ('2081', '450126', '宾阳县', '450100');
INSERT INTO `area` VALUES ('2082', '450127', '横　县', '450100');
INSERT INTO `area` VALUES ('2083', '450201', '市辖区', '450200');
INSERT INTO `area` VALUES ('2084', '450202', '城中区', '450200');
INSERT INTO `area` VALUES ('2085', '450203', '鱼峰区', '450200');
INSERT INTO `area` VALUES ('2086', '450204', '柳南区', '450200');
INSERT INTO `area` VALUES ('2087', '450205', '柳北区', '450200');
INSERT INTO `area` VALUES ('2088', '450221', '柳江县', '450200');
INSERT INTO `area` VALUES ('2089', '450222', '柳城县', '450200');
INSERT INTO `area` VALUES ('2090', '450223', '鹿寨县', '450200');
INSERT INTO `area` VALUES ('2091', '450224', '融安县', '450200');
INSERT INTO `area` VALUES ('2092', '450225', '融水苗族自治县', '450200');
INSERT INTO `area` VALUES ('2093', '450226', '三江侗族自治县', '450200');
INSERT INTO `area` VALUES ('2094', '450301', '市辖区', '450300');
INSERT INTO `area` VALUES ('2095', '450302', '秀峰区', '450300');
INSERT INTO `area` VALUES ('2096', '450303', '叠彩区', '450300');
INSERT INTO `area` VALUES ('2097', '450304', '象山区', '450300');
INSERT INTO `area` VALUES ('2098', '450305', '七星区', '450300');
INSERT INTO `area` VALUES ('2099', '450311', '雁山区', '450300');
INSERT INTO `area` VALUES ('2100', '450321', '阳朔县', '450300');
INSERT INTO `area` VALUES ('2101', '450322', '临桂县', '450300');
INSERT INTO `area` VALUES ('2102', '450323', '灵川县', '450300');
INSERT INTO `area` VALUES ('2103', '450324', '全州县', '450300');
INSERT INTO `area` VALUES ('2104', '450325', '兴安县', '450300');
INSERT INTO `area` VALUES ('2105', '450326', '永福县', '450300');
INSERT INTO `area` VALUES ('2106', '450327', '灌阳县', '450300');
INSERT INTO `area` VALUES ('2107', '450328', '龙胜各族自治县', '450300');
INSERT INTO `area` VALUES ('2108', '450329', '资源县', '450300');
INSERT INTO `area` VALUES ('2109', '450330', '平乐县', '450300');
INSERT INTO `area` VALUES ('2110', '450331', '荔蒲县', '450300');
INSERT INTO `area` VALUES ('2111', '450332', '恭城瑶族自治县', '450300');
INSERT INTO `area` VALUES ('2112', '450401', '市辖区', '450400');
INSERT INTO `area` VALUES ('2113', '450403', '万秀区', '450400');
INSERT INTO `area` VALUES ('2114', '450404', '蝶山区', '450400');
INSERT INTO `area` VALUES ('2115', '450405', '长洲区', '450400');
INSERT INTO `area` VALUES ('2116', '450421', '苍梧县', '450400');
INSERT INTO `area` VALUES ('2117', '450422', '藤　县', '450400');
INSERT INTO `area` VALUES ('2118', '450423', '蒙山县', '450400');
INSERT INTO `area` VALUES ('2119', '450481', '岑溪市', '450400');
INSERT INTO `area` VALUES ('2120', '450501', '市辖区', '450500');
INSERT INTO `area` VALUES ('2121', '450502', '海城区', '450500');
INSERT INTO `area` VALUES ('2122', '450503', '银海区', '450500');
INSERT INTO `area` VALUES ('2123', '450512', '铁山港区', '450500');
INSERT INTO `area` VALUES ('2124', '450521', '合浦县', '450500');
INSERT INTO `area` VALUES ('2125', '450601', '市辖区', '450600');
INSERT INTO `area` VALUES ('2126', '450602', '港口区', '450600');
INSERT INTO `area` VALUES ('2127', '450603', '防城区', '450600');
INSERT INTO `area` VALUES ('2128', '450621', '上思县', '450600');
INSERT INTO `area` VALUES ('2129', '450681', '东兴市', '450600');
INSERT INTO `area` VALUES ('2130', '450701', '市辖区', '450700');
INSERT INTO `area` VALUES ('2131', '450702', '钦南区', '450700');
INSERT INTO `area` VALUES ('2132', '450703', '钦北区', '450700');
INSERT INTO `area` VALUES ('2133', '450721', '灵山县', '450700');
INSERT INTO `area` VALUES ('2134', '450722', '浦北县', '450700');
INSERT INTO `area` VALUES ('2135', '450801', '市辖区', '450800');
INSERT INTO `area` VALUES ('2136', '450802', '港北区', '450800');
INSERT INTO `area` VALUES ('2137', '450803', '港南区', '450800');
INSERT INTO `area` VALUES ('2138', '450804', '覃塘区', '450800');
INSERT INTO `area` VALUES ('2139', '450821', '平南县', '450800');
INSERT INTO `area` VALUES ('2140', '450881', '桂平市', '450800');
INSERT INTO `area` VALUES ('2141', '450901', '市辖区', '450900');
INSERT INTO `area` VALUES ('2142', '450902', '玉州区', '450900');
INSERT INTO `area` VALUES ('2143', '450921', '容　县', '450900');
INSERT INTO `area` VALUES ('2144', '450922', '陆川县', '450900');
INSERT INTO `area` VALUES ('2145', '450923', '博白县', '450900');
INSERT INTO `area` VALUES ('2146', '450924', '兴业县', '450900');
INSERT INTO `area` VALUES ('2147', '450981', '北流市', '450900');
INSERT INTO `area` VALUES ('2148', '451001', '市辖区', '451000');
INSERT INTO `area` VALUES ('2149', '451002', '右江区', '451000');
INSERT INTO `area` VALUES ('2150', '451021', '田阳县', '451000');
INSERT INTO `area` VALUES ('2151', '451022', '田东县', '451000');
INSERT INTO `area` VALUES ('2152', '451023', '平果县', '451000');
INSERT INTO `area` VALUES ('2153', '451024', '德保县', '451000');
INSERT INTO `area` VALUES ('2154', '451025', '靖西县', '451000');
INSERT INTO `area` VALUES ('2155', '451026', '那坡县', '451000');
INSERT INTO `area` VALUES ('2156', '451027', '凌云县', '451000');
INSERT INTO `area` VALUES ('2157', '451028', '乐业县', '451000');
INSERT INTO `area` VALUES ('2158', '451029', '田林县', '451000');
INSERT INTO `area` VALUES ('2159', '451030', '西林县', '451000');
INSERT INTO `area` VALUES ('2160', '451031', '隆林各族自治县', '451000');
INSERT INTO `area` VALUES ('2161', '451101', '市辖区', '451100');
INSERT INTO `area` VALUES ('2162', '451102', '八步区', '451100');
INSERT INTO `area` VALUES ('2163', '451121', '昭平县', '451100');
INSERT INTO `area` VALUES ('2164', '451122', '钟山县', '451100');
INSERT INTO `area` VALUES ('2165', '451123', '富川瑶族自治县', '451100');
INSERT INTO `area` VALUES ('2166', '451201', '市辖区', '451200');
INSERT INTO `area` VALUES ('2167', '451202', '金城江区', '451200');
INSERT INTO `area` VALUES ('2168', '451221', '南丹县', '451200');
INSERT INTO `area` VALUES ('2169', '451222', '天峨县', '451200');
INSERT INTO `area` VALUES ('2170', '451223', '凤山县', '451200');
INSERT INTO `area` VALUES ('2171', '451224', '东兰县', '451200');
INSERT INTO `area` VALUES ('2172', '451225', '罗城仫佬族自治县', '451200');
INSERT INTO `area` VALUES ('2173', '451226', '环江毛南族自治县', '451200');
INSERT INTO `area` VALUES ('2174', '451227', '巴马瑶族自治县', '451200');
INSERT INTO `area` VALUES ('2175', '451228', '都安瑶族自治县', '451200');
INSERT INTO `area` VALUES ('2176', '451229', '大化瑶族自治县', '451200');
INSERT INTO `area` VALUES ('2177', '451281', '宜州市', '451200');
INSERT INTO `area` VALUES ('2178', '451301', '市辖区', '451300');
INSERT INTO `area` VALUES ('2179', '451302', '兴宾区', '451300');
INSERT INTO `area` VALUES ('2180', '451321', '忻城县', '451300');
INSERT INTO `area` VALUES ('2181', '451322', '象州县', '451300');
INSERT INTO `area` VALUES ('2182', '451323', '武宣县', '451300');
INSERT INTO `area` VALUES ('2183', '451324', '金秀瑶族自治县', '451300');
INSERT INTO `area` VALUES ('2184', '451381', '合山市', '451300');
INSERT INTO `area` VALUES ('2185', '451401', '市辖区', '451400');
INSERT INTO `area` VALUES ('2186', '451402', '江洲区', '451400');
INSERT INTO `area` VALUES ('2187', '451421', '扶绥县', '451400');
INSERT INTO `area` VALUES ('2188', '451422', '宁明县', '451400');
INSERT INTO `area` VALUES ('2189', '451423', '龙州县', '451400');
INSERT INTO `area` VALUES ('2190', '451424', '大新县', '451400');
INSERT INTO `area` VALUES ('2191', '451425', '天等县', '451400');
INSERT INTO `area` VALUES ('2192', '451481', '凭祥市', '451400');
INSERT INTO `area` VALUES ('2193', '460101', '市辖区', '460100');
INSERT INTO `area` VALUES ('2194', '460105', '秀英区', '460100');
INSERT INTO `area` VALUES ('2195', '460106', '龙华区', '460100');
INSERT INTO `area` VALUES ('2196', '460107', '琼山区', '460100');
INSERT INTO `area` VALUES ('2197', '460108', '美兰区', '460100');
INSERT INTO `area` VALUES ('2198', '460201', '市辖区', '460200');
INSERT INTO `area` VALUES ('2199', '469001', '五指山市', '469000');
INSERT INTO `area` VALUES ('2200', '469002', '琼海市', '469000');
INSERT INTO `area` VALUES ('2201', '469003', '儋州市', '469000');
INSERT INTO `area` VALUES ('2202', '469005', '文昌市', '469000');
INSERT INTO `area` VALUES ('2203', '469006', '万宁市', '469000');
INSERT INTO `area` VALUES ('2204', '469007', '东方市', '469000');
INSERT INTO `area` VALUES ('2205', '469025', '定安县', '469000');
INSERT INTO `area` VALUES ('2206', '469026', '屯昌县', '469000');
INSERT INTO `area` VALUES ('2207', '469027', '澄迈县', '469000');
INSERT INTO `area` VALUES ('2208', '469028', '临高县', '469000');
INSERT INTO `area` VALUES ('2209', '469030', '白沙黎族自治县', '469000');
INSERT INTO `area` VALUES ('2210', '469031', '昌江黎族自治县', '469000');
INSERT INTO `area` VALUES ('2211', '469033', '乐东黎族自治县', '469000');
INSERT INTO `area` VALUES ('2212', '469034', '陵水黎族自治县', '469000');
INSERT INTO `area` VALUES ('2213', '469035', '保亭黎族苗族自治县', '469000');
INSERT INTO `area` VALUES ('2214', '469036', '琼中黎族苗族自治县', '469000');
INSERT INTO `area` VALUES ('2215', '469037', '西沙群岛', '469000');
INSERT INTO `area` VALUES ('2216', '469038', '南沙群岛', '469000');
INSERT INTO `area` VALUES ('2217', '469039', '中沙群岛的岛礁及其海域', '469000');
INSERT INTO `area` VALUES ('2218', '500101', '万州区', '500100');
INSERT INTO `area` VALUES ('2219', '500102', '涪陵区', '500100');
INSERT INTO `area` VALUES ('2220', '500103', '渝中区', '500100');
INSERT INTO `area` VALUES ('2221', '500104', '大渡口区', '500100');
INSERT INTO `area` VALUES ('2222', '500105', '江北区', '500100');
INSERT INTO `area` VALUES ('2223', '500106', '沙坪坝区', '500100');
INSERT INTO `area` VALUES ('2224', '500107', '九龙坡区', '500100');
INSERT INTO `area` VALUES ('2225', '500108', '南岸区', '500100');
INSERT INTO `area` VALUES ('2226', '500109', '北碚区', '500100');
INSERT INTO `area` VALUES ('2227', '500110', '万盛区', '500100');
INSERT INTO `area` VALUES ('2228', '500111', '双桥区', '500100');
INSERT INTO `area` VALUES ('2229', '500112', '渝北区', '500100');
INSERT INTO `area` VALUES ('2230', '500113', '巴南区', '500100');
INSERT INTO `area` VALUES ('2231', '500114', '黔江区', '500100');
INSERT INTO `area` VALUES ('2232', '500115', '长寿区', '500100');
INSERT INTO `area` VALUES ('2233', '500222', '綦江县', '500200');
INSERT INTO `area` VALUES ('2234', '500223', '潼南县', '500200');
INSERT INTO `area` VALUES ('2235', '500224', '铜梁县', '500200');
INSERT INTO `area` VALUES ('2236', '500225', '大足县', '500200');
INSERT INTO `area` VALUES ('2237', '500226', '荣昌县', '500200');
INSERT INTO `area` VALUES ('2238', '500227', '璧山县', '500200');
INSERT INTO `area` VALUES ('2239', '500228', '梁平县', '500200');
INSERT INTO `area` VALUES ('2240', '500229', '城口县', '500200');
INSERT INTO `area` VALUES ('2241', '500230', '丰都县', '500200');
INSERT INTO `area` VALUES ('2242', '500231', '垫江县', '500200');
INSERT INTO `area` VALUES ('2243', '500232', '武隆县', '500200');
INSERT INTO `area` VALUES ('2244', '500233', '忠　县', '500200');
INSERT INTO `area` VALUES ('2245', '500234', '开　县', '500200');
INSERT INTO `area` VALUES ('2246', '500235', '云阳县', '500200');
INSERT INTO `area` VALUES ('2247', '500236', '奉节县', '500200');
INSERT INTO `area` VALUES ('2248', '500237', '巫山县', '500200');
INSERT INTO `area` VALUES ('2249', '500238', '巫溪县', '500200');
INSERT INTO `area` VALUES ('2250', '500240', '石柱土家族自治县', '500200');
INSERT INTO `area` VALUES ('2251', '500241', '秀山土家族苗族自治县', '500200');
INSERT INTO `area` VALUES ('2252', '500242', '酉阳土家族苗族自治县', '500200');
INSERT INTO `area` VALUES ('2253', '500243', '彭水苗族土家族自治县', '500200');
INSERT INTO `area` VALUES ('2254', '500381', '江津市', '500300');
INSERT INTO `area` VALUES ('2255', '500382', '合川市', '500300');
INSERT INTO `area` VALUES ('2256', '500383', '永川市', '500300');
INSERT INTO `area` VALUES ('2257', '500384', '南川市', '500300');
INSERT INTO `area` VALUES ('2258', '510101', '市辖区', '510100');
INSERT INTO `area` VALUES ('2259', '510104', '锦江区', '510100');
INSERT INTO `area` VALUES ('2260', '510105', '青羊区', '510100');
INSERT INTO `area` VALUES ('2261', '510106', '金牛区', '510100');
INSERT INTO `area` VALUES ('2262', '510107', '武侯区', '510100');
INSERT INTO `area` VALUES ('2263', '510108', '成华区', '510100');
INSERT INTO `area` VALUES ('2264', '510112', '龙泉驿区', '510100');
INSERT INTO `area` VALUES ('2265', '510113', '青白江区', '510100');
INSERT INTO `area` VALUES ('2266', '510114', '新都区', '510100');
INSERT INTO `area` VALUES ('2267', '510115', '温江区', '510100');
INSERT INTO `area` VALUES ('2268', '510121', '金堂县', '510100');
INSERT INTO `area` VALUES ('2269', '510122', '双流县', '510100');
INSERT INTO `area` VALUES ('2270', '510124', '郫　县', '510100');
INSERT INTO `area` VALUES ('2271', '510129', '大邑县', '510100');
INSERT INTO `area` VALUES ('2272', '510131', '蒲江县', '510100');
INSERT INTO `area` VALUES ('2273', '510132', '新津县', '510100');
INSERT INTO `area` VALUES ('2274', '510181', '都江堰市', '510100');
INSERT INTO `area` VALUES ('2275', '510182', '彭州市', '510100');
INSERT INTO `area` VALUES ('2276', '510183', '邛崃市', '510100');
INSERT INTO `area` VALUES ('2277', '510184', '崇州市', '510100');
INSERT INTO `area` VALUES ('2278', '510301', '市辖区', '510300');
INSERT INTO `area` VALUES ('2279', '510302', '自流井区', '510300');
INSERT INTO `area` VALUES ('2280', '510303', '贡井区', '510300');
INSERT INTO `area` VALUES ('2281', '510304', '大安区', '510300');
INSERT INTO `area` VALUES ('2282', '510311', '沿滩区', '510300');
INSERT INTO `area` VALUES ('2283', '510321', '荣　县', '510300');
INSERT INTO `area` VALUES ('2284', '510322', '富顺县', '510300');
INSERT INTO `area` VALUES ('2285', '510401', '市辖区', '510400');
INSERT INTO `area` VALUES ('2286', '510402', '东　区', '510400');
INSERT INTO `area` VALUES ('2287', '510403', '西　区', '510400');
INSERT INTO `area` VALUES ('2288', '510411', '仁和区', '510400');
INSERT INTO `area` VALUES ('2289', '510421', '米易县', '510400');
INSERT INTO `area` VALUES ('2290', '510422', '盐边县', '510400');
INSERT INTO `area` VALUES ('2291', '510501', '市辖区', '510500');
INSERT INTO `area` VALUES ('2292', '510502', '江阳区', '510500');
INSERT INTO `area` VALUES ('2293', '510503', '纳溪区', '510500');
INSERT INTO `area` VALUES ('2294', '510504', '龙马潭区', '510500');
INSERT INTO `area` VALUES ('2295', '510521', '泸　县', '510500');
INSERT INTO `area` VALUES ('2296', '510522', '合江县', '510500');
INSERT INTO `area` VALUES ('2297', '510524', '叙永县', '510500');
INSERT INTO `area` VALUES ('2298', '510525', '古蔺县', '510500');
INSERT INTO `area` VALUES ('2299', '510601', '市辖区', '510600');
INSERT INTO `area` VALUES ('2300', '510603', '旌阳区', '510600');
INSERT INTO `area` VALUES ('2301', '510623', '中江县', '510600');
INSERT INTO `area` VALUES ('2302', '510626', '罗江县', '510600');
INSERT INTO `area` VALUES ('2303', '510681', '广汉市', '510600');
INSERT INTO `area` VALUES ('2304', '510682', '什邡市', '510600');
INSERT INTO `area` VALUES ('2305', '510683', '绵竹市', '510600');
INSERT INTO `area` VALUES ('2306', '510701', '市辖区', '510700');
INSERT INTO `area` VALUES ('2307', '510703', '涪城区', '510700');
INSERT INTO `area` VALUES ('2308', '510704', '游仙区', '510700');
INSERT INTO `area` VALUES ('2309', '510722', '三台县', '510700');
INSERT INTO `area` VALUES ('2310', '510723', '盐亭县', '510700');
INSERT INTO `area` VALUES ('2311', '510724', '安　县', '510700');
INSERT INTO `area` VALUES ('2312', '510725', '梓潼县', '510700');
INSERT INTO `area` VALUES ('2313', '510726', '北川羌族自治县', '510700');
INSERT INTO `area` VALUES ('2314', '510727', '平武县', '510700');
INSERT INTO `area` VALUES ('2315', '510781', '江油市', '510700');
INSERT INTO `area` VALUES ('2316', '510801', '市辖区', '510800');
INSERT INTO `area` VALUES ('2317', '510802', '市中区', '510800');
INSERT INTO `area` VALUES ('2318', '510811', '元坝区', '510800');
INSERT INTO `area` VALUES ('2319', '510812', '朝天区', '510800');
INSERT INTO `area` VALUES ('2320', '510821', '旺苍县', '510800');
INSERT INTO `area` VALUES ('2321', '510822', '青川县', '510800');
INSERT INTO `area` VALUES ('2322', '510823', '剑阁县', '510800');
INSERT INTO `area` VALUES ('2323', '510824', '苍溪县', '510800');
INSERT INTO `area` VALUES ('2324', '510901', '市辖区', '510900');
INSERT INTO `area` VALUES ('2325', '510903', '船山区', '510900');
INSERT INTO `area` VALUES ('2326', '510904', '安居区', '510900');
INSERT INTO `area` VALUES ('2327', '510921', '蓬溪县', '510900');
INSERT INTO `area` VALUES ('2328', '510922', '射洪县', '510900');
INSERT INTO `area` VALUES ('2329', '510923', '大英县', '510900');
INSERT INTO `area` VALUES ('2330', '511001', '市辖区', '511000');
INSERT INTO `area` VALUES ('2331', '511002', '市中区', '511000');
INSERT INTO `area` VALUES ('2332', '511011', '东兴区', '511000');
INSERT INTO `area` VALUES ('2333', '511024', '威远县', '511000');
INSERT INTO `area` VALUES ('2334', '511025', '资中县', '511000');
INSERT INTO `area` VALUES ('2335', '511028', '隆昌县', '511000');
INSERT INTO `area` VALUES ('2336', '511101', '市辖区', '511100');
INSERT INTO `area` VALUES ('2337', '511102', '市中区', '511100');
INSERT INTO `area` VALUES ('2338', '511111', '沙湾区', '511100');
INSERT INTO `area` VALUES ('2339', '511112', '五通桥区', '511100');
INSERT INTO `area` VALUES ('2340', '511113', '金口河区', '511100');
INSERT INTO `area` VALUES ('2341', '511123', '犍为县', '511100');
INSERT INTO `area` VALUES ('2342', '511124', '井研县', '511100');
INSERT INTO `area` VALUES ('2343', '511126', '夹江县', '511100');
INSERT INTO `area` VALUES ('2344', '511129', '沐川县', '511100');
INSERT INTO `area` VALUES ('2345', '511132', '峨边彝族自治县', '511100');
INSERT INTO `area` VALUES ('2346', '511133', '马边彝族自治县', '511100');
INSERT INTO `area` VALUES ('2347', '511181', '峨眉山市', '511100');
INSERT INTO `area` VALUES ('2348', '511301', '市辖区', '511300');
INSERT INTO `area` VALUES ('2349', '511302', '顺庆区', '511300');
INSERT INTO `area` VALUES ('2350', '511303', '高坪区', '511300');
INSERT INTO `area` VALUES ('2351', '511304', '嘉陵区', '511300');
INSERT INTO `area` VALUES ('2352', '511321', '南部县', '511300');
INSERT INTO `area` VALUES ('2353', '511322', '营山县', '511300');
INSERT INTO `area` VALUES ('2354', '511323', '蓬安县', '511300');
INSERT INTO `area` VALUES ('2355', '511324', '仪陇县', '511300');
INSERT INTO `area` VALUES ('2356', '511325', '西充县', '511300');
INSERT INTO `area` VALUES ('2357', '511381', '阆中市', '511300');
INSERT INTO `area` VALUES ('2358', '511401', '市辖区', '511400');
INSERT INTO `area` VALUES ('2359', '511402', '东坡区', '511400');
INSERT INTO `area` VALUES ('2360', '511421', '仁寿县', '511400');
INSERT INTO `area` VALUES ('2361', '511422', '彭山县', '511400');
INSERT INTO `area` VALUES ('2362', '511423', '洪雅县', '511400');
INSERT INTO `area` VALUES ('2363', '511424', '丹棱县', '511400');
INSERT INTO `area` VALUES ('2364', '511425', '青神县', '511400');
INSERT INTO `area` VALUES ('2365', '511501', '市辖区', '511500');
INSERT INTO `area` VALUES ('2366', '511502', '翠屏区', '511500');
INSERT INTO `area` VALUES ('2367', '511521', '宜宾县', '511500');
INSERT INTO `area` VALUES ('2368', '511522', '南溪县', '511500');
INSERT INTO `area` VALUES ('2369', '511523', '江安县', '511500');
INSERT INTO `area` VALUES ('2370', '511524', '长宁县', '511500');
INSERT INTO `area` VALUES ('2371', '511525', '高　县', '511500');
INSERT INTO `area` VALUES ('2372', '511526', '珙　县', '511500');
INSERT INTO `area` VALUES ('2373', '511527', '筠连县', '511500');
INSERT INTO `area` VALUES ('2374', '511528', '兴文县', '511500');
INSERT INTO `area` VALUES ('2375', '511529', '屏山县', '511500');
INSERT INTO `area` VALUES ('2376', '511601', '市辖区', '511600');
INSERT INTO `area` VALUES ('2377', '511602', '广安区', '511600');
INSERT INTO `area` VALUES ('2378', '511621', '岳池县', '511600');
INSERT INTO `area` VALUES ('2379', '511622', '武胜县', '511600');
INSERT INTO `area` VALUES ('2380', '511623', '邻水县', '511600');
INSERT INTO `area` VALUES ('2381', '511681', '华莹市', '511600');
INSERT INTO `area` VALUES ('2382', '511701', '市辖区', '511700');
INSERT INTO `area` VALUES ('2383', '511702', '通川区', '511700');
INSERT INTO `area` VALUES ('2384', '511721', '达　县', '511700');
INSERT INTO `area` VALUES ('2385', '511722', '宣汉县', '511700');
INSERT INTO `area` VALUES ('2386', '511723', '开江县', '511700');
INSERT INTO `area` VALUES ('2387', '511724', '大竹县', '511700');
INSERT INTO `area` VALUES ('2388', '511725', '渠　县', '511700');
INSERT INTO `area` VALUES ('2389', '511781', '万源市', '511700');
INSERT INTO `area` VALUES ('2390', '511801', '市辖区', '511800');
INSERT INTO `area` VALUES ('2391', '511802', '雨城区', '511800');
INSERT INTO `area` VALUES ('2392', '511821', '名山县', '511800');
INSERT INTO `area` VALUES ('2393', '511822', '荥经县', '511800');
INSERT INTO `area` VALUES ('2394', '511823', '汉源县', '511800');
INSERT INTO `area` VALUES ('2395', '511824', '石棉县', '511800');
INSERT INTO `area` VALUES ('2396', '511825', '天全县', '511800');
INSERT INTO `area` VALUES ('2397', '511826', '芦山县', '511800');
INSERT INTO `area` VALUES ('2398', '511827', '宝兴县', '511800');
INSERT INTO `area` VALUES ('2399', '511901', '市辖区', '511900');
INSERT INTO `area` VALUES ('2400', '511902', '巴州区', '511900');
INSERT INTO `area` VALUES ('2401', '511921', '通江县', '511900');
INSERT INTO `area` VALUES ('2402', '511922', '南江县', '511900');
INSERT INTO `area` VALUES ('2403', '511923', '平昌县', '511900');
INSERT INTO `area` VALUES ('2404', '512001', '市辖区', '512000');
INSERT INTO `area` VALUES ('2405', '512002', '雁江区', '512000');
INSERT INTO `area` VALUES ('2406', '512021', '安岳县', '512000');
INSERT INTO `area` VALUES ('2407', '512022', '乐至县', '512000');
INSERT INTO `area` VALUES ('2408', '512081', '简阳市', '512000');
INSERT INTO `area` VALUES ('2409', '513221', '汶川县', '513200');
INSERT INTO `area` VALUES ('2410', '513222', '理　县', '513200');
INSERT INTO `area` VALUES ('2411', '513223', '茂　县', '513200');
INSERT INTO `area` VALUES ('2412', '513224', '松潘县', '513200');
INSERT INTO `area` VALUES ('2413', '513225', '九寨沟县', '513200');
INSERT INTO `area` VALUES ('2414', '513226', '金川县', '513200');
INSERT INTO `area` VALUES ('2415', '513227', '小金县', '513200');
INSERT INTO `area` VALUES ('2416', '513228', '黑水县', '513200');
INSERT INTO `area` VALUES ('2417', '513229', '马尔康县', '513200');
INSERT INTO `area` VALUES ('2418', '513230', '壤塘县', '513200');
INSERT INTO `area` VALUES ('2419', '513231', '阿坝县', '513200');
INSERT INTO `area` VALUES ('2420', '513232', '若尔盖县', '513200');
INSERT INTO `area` VALUES ('2421', '513233', '红原县', '513200');
INSERT INTO `area` VALUES ('2422', '513321', '康定县', '513300');
INSERT INTO `area` VALUES ('2423', '513322', '泸定县', '513300');
INSERT INTO `area` VALUES ('2424', '513323', '丹巴县', '513300');
INSERT INTO `area` VALUES ('2425', '513324', '九龙县', '513300');
INSERT INTO `area` VALUES ('2426', '513325', '雅江县', '513300');
INSERT INTO `area` VALUES ('2427', '513326', '道孚县', '513300');
INSERT INTO `area` VALUES ('2428', '513327', '炉霍县', '513300');
INSERT INTO `area` VALUES ('2429', '513328', '甘孜县', '513300');
INSERT INTO `area` VALUES ('2430', '513329', '新龙县', '513300');
INSERT INTO `area` VALUES ('2431', '513330', '德格县', '513300');
INSERT INTO `area` VALUES ('2432', '513331', '白玉县', '513300');
INSERT INTO `area` VALUES ('2433', '513332', '石渠县', '513300');
INSERT INTO `area` VALUES ('2434', '513333', '色达县', '513300');
INSERT INTO `area` VALUES ('2435', '513334', '理塘县', '513300');
INSERT INTO `area` VALUES ('2436', '513335', '巴塘县', '513300');
INSERT INTO `area` VALUES ('2437', '513336', '乡城县', '513300');
INSERT INTO `area` VALUES ('2438', '513337', '稻城县', '513300');
INSERT INTO `area` VALUES ('2439', '513338', '得荣县', '513300');
INSERT INTO `area` VALUES ('2440', '513401', '西昌市', '513400');
INSERT INTO `area` VALUES ('2441', '513422', '木里藏族自治县', '513400');
INSERT INTO `area` VALUES ('2442', '513423', '盐源县', '513400');
INSERT INTO `area` VALUES ('2443', '513424', '德昌县', '513400');
INSERT INTO `area` VALUES ('2444', '513425', '会理县', '513400');
INSERT INTO `area` VALUES ('2445', '513426', '会东县', '513400');
INSERT INTO `area` VALUES ('2446', '513427', '宁南县', '513400');
INSERT INTO `area` VALUES ('2447', '513428', '普格县', '513400');
INSERT INTO `area` VALUES ('2448', '513429', '布拖县', '513400');
INSERT INTO `area` VALUES ('2449', '513430', '金阳县', '513400');
INSERT INTO `area` VALUES ('2450', '513431', '昭觉县', '513400');
INSERT INTO `area` VALUES ('2451', '513432', '喜德县', '513400');
INSERT INTO `area` VALUES ('2452', '513433', '冕宁县', '513400');
INSERT INTO `area` VALUES ('2453', '513434', '越西县', '513400');
INSERT INTO `area` VALUES ('2454', '513435', '甘洛县', '513400');
INSERT INTO `area` VALUES ('2455', '513436', '美姑县', '513400');
INSERT INTO `area` VALUES ('2456', '513437', '雷波县', '513400');
INSERT INTO `area` VALUES ('2457', '520101', '市辖区', '520100');
INSERT INTO `area` VALUES ('2458', '520102', '南明区', '520100');
INSERT INTO `area` VALUES ('2459', '520103', '云岩区', '520100');
INSERT INTO `area` VALUES ('2460', '520111', '花溪区', '520100');
INSERT INTO `area` VALUES ('2461', '520112', '乌当区', '520100');
INSERT INTO `area` VALUES ('2462', '520113', '白云区', '520100');
INSERT INTO `area` VALUES ('2463', '520114', '小河区', '520100');
INSERT INTO `area` VALUES ('2464', '520121', '开阳县', '520100');
INSERT INTO `area` VALUES ('2465', '520122', '息烽县', '520100');
INSERT INTO `area` VALUES ('2466', '520123', '修文县', '520100');
INSERT INTO `area` VALUES ('2467', '520181', '清镇市', '520100');
INSERT INTO `area` VALUES ('2468', '520201', '钟山区', '520200');
INSERT INTO `area` VALUES ('2469', '520203', '六枝特区', '520200');
INSERT INTO `area` VALUES ('2470', '520221', '水城县', '520200');
INSERT INTO `area` VALUES ('2471', '520222', '盘　县', '520200');
INSERT INTO `area` VALUES ('2472', '520301', '市辖区', '520300');
INSERT INTO `area` VALUES ('2473', '520302', '红花岗区', '520300');
INSERT INTO `area` VALUES ('2474', '520303', '汇川区', '520300');
INSERT INTO `area` VALUES ('2475', '520321', '遵义县', '520300');
INSERT INTO `area` VALUES ('2476', '520322', '桐梓县', '520300');
INSERT INTO `area` VALUES ('2477', '520323', '绥阳县', '520300');
INSERT INTO `area` VALUES ('2478', '520324', '正安县', '520300');
INSERT INTO `area` VALUES ('2479', '520325', '道真仡佬族苗族自治县', '520300');
INSERT INTO `area` VALUES ('2480', '520326', '务川仡佬族苗族自治县', '520300');
INSERT INTO `area` VALUES ('2481', '520327', '凤冈县', '520300');
INSERT INTO `area` VALUES ('2482', '520328', '湄潭县', '520300');
INSERT INTO `area` VALUES ('2483', '520329', '余庆县', '520300');
INSERT INTO `area` VALUES ('2484', '520330', '习水县', '520300');
INSERT INTO `area` VALUES ('2485', '520381', '赤水市', '520300');
INSERT INTO `area` VALUES ('2486', '520382', '仁怀市', '520300');
INSERT INTO `area` VALUES ('2487', '520401', '市辖区', '520400');
INSERT INTO `area` VALUES ('2488', '520402', '西秀区', '520400');
INSERT INTO `area` VALUES ('2489', '520421', '平坝县', '520400');
INSERT INTO `area` VALUES ('2490', '520422', '普定县', '520400');
INSERT INTO `area` VALUES ('2491', '520423', '镇宁布依族苗族自治县', '520400');
INSERT INTO `area` VALUES ('2492', '520424', '关岭布依族苗族自治县', '520400');
INSERT INTO `area` VALUES ('2493', '520425', '紫云苗族布依族自治县', '520400');
INSERT INTO `area` VALUES ('2494', '522201', '铜仁市', '522200');
INSERT INTO `area` VALUES ('2495', '522222', '江口县', '522200');
INSERT INTO `area` VALUES ('2496', '522223', '玉屏侗族自治县', '522200');
INSERT INTO `area` VALUES ('2497', '522224', '石阡县', '522200');
INSERT INTO `area` VALUES ('2498', '522225', '思南县', '522200');
INSERT INTO `area` VALUES ('2499', '522226', '印江土家族苗族自治县', '522200');
INSERT INTO `area` VALUES ('2500', '522227', '德江县', '522200');
INSERT INTO `area` VALUES ('2501', '522228', '沿河土家族自治县', '522200');
INSERT INTO `area` VALUES ('2502', '522229', '松桃苗族自治县', '522200');
INSERT INTO `area` VALUES ('2503', '522230', '万山特区', '522200');
INSERT INTO `area` VALUES ('2504', '522301', '兴义市', '522300');
INSERT INTO `area` VALUES ('2505', '522322', '兴仁县', '522300');
INSERT INTO `area` VALUES ('2506', '522323', '普安县', '522300');
INSERT INTO `area` VALUES ('2507', '522324', '晴隆县', '522300');
INSERT INTO `area` VALUES ('2508', '522325', '贞丰县', '522300');
INSERT INTO `area` VALUES ('2509', '522326', '望谟县', '522300');
INSERT INTO `area` VALUES ('2510', '522327', '册亨县', '522300');
INSERT INTO `area` VALUES ('2511', '522328', '安龙县', '522300');
INSERT INTO `area` VALUES ('2512', '522401', '毕节市', '522400');
INSERT INTO `area` VALUES ('2513', '522422', '大方县', '522400');
INSERT INTO `area` VALUES ('2514', '522423', '黔西县', '522400');
INSERT INTO `area` VALUES ('2515', '522424', '金沙县', '522400');
INSERT INTO `area` VALUES ('2516', '522425', '织金县', '522400');
INSERT INTO `area` VALUES ('2517', '522426', '纳雍县', '522400');
INSERT INTO `area` VALUES ('2518', '522427', '威宁彝族回族苗族自治县', '522400');
INSERT INTO `area` VALUES ('2519', '522428', '赫章县', '522400');
INSERT INTO `area` VALUES ('2520', '522601', '凯里市', '522600');
INSERT INTO `area` VALUES ('2521', '522622', '黄平县', '522600');
INSERT INTO `area` VALUES ('2522', '522623', '施秉县', '522600');
INSERT INTO `area` VALUES ('2523', '522624', '三穗县', '522600');
INSERT INTO `area` VALUES ('2524', '522625', '镇远县', '522600');
INSERT INTO `area` VALUES ('2525', '522626', '岑巩县', '522600');
INSERT INTO `area` VALUES ('2526', '522627', '天柱县', '522600');
INSERT INTO `area` VALUES ('2527', '522628', '锦屏县', '522600');
INSERT INTO `area` VALUES ('2528', '522629', '剑河县', '522600');
INSERT INTO `area` VALUES ('2529', '522630', '台江县', '522600');
INSERT INTO `area` VALUES ('2530', '522631', '黎平县', '522600');
INSERT INTO `area` VALUES ('2531', '522632', '榕江县', '522600');
INSERT INTO `area` VALUES ('2532', '522633', '从江县', '522600');
INSERT INTO `area` VALUES ('2533', '522634', '雷山县', '522600');
INSERT INTO `area` VALUES ('2534', '522635', '麻江县', '522600');
INSERT INTO `area` VALUES ('2535', '522636', '丹寨县', '522600');
INSERT INTO `area` VALUES ('2536', '522701', '都匀市', '522700');
INSERT INTO `area` VALUES ('2537', '522702', '福泉市', '522700');
INSERT INTO `area` VALUES ('2538', '522722', '荔波县', '522700');
INSERT INTO `area` VALUES ('2539', '522723', '贵定县', '522700');
INSERT INTO `area` VALUES ('2540', '522725', '瓮安县', '522700');
INSERT INTO `area` VALUES ('2541', '522726', '独山县', '522700');
INSERT INTO `area` VALUES ('2542', '522727', '平塘县', '522700');
INSERT INTO `area` VALUES ('2543', '522728', '罗甸县', '522700');
INSERT INTO `area` VALUES ('2544', '522729', '长顺县', '522700');
INSERT INTO `area` VALUES ('2545', '522730', '龙里县', '522700');
INSERT INTO `area` VALUES ('2546', '522731', '惠水县', '522700');
INSERT INTO `area` VALUES ('2547', '522732', '三都水族自治县', '522700');
INSERT INTO `area` VALUES ('2548', '530101', '市辖区', '530100');
INSERT INTO `area` VALUES ('2549', '530102', '五华区', '530100');
INSERT INTO `area` VALUES ('2550', '530103', '盘龙区', '530100');
INSERT INTO `area` VALUES ('2551', '530111', '官渡区', '530100');
INSERT INTO `area` VALUES ('2552', '530112', '西山区', '530100');
INSERT INTO `area` VALUES ('2553', '530113', '东川区', '530100');
INSERT INTO `area` VALUES ('2554', '530121', '呈贡县', '530100');
INSERT INTO `area` VALUES ('2555', '530122', '晋宁县', '530100');
INSERT INTO `area` VALUES ('2556', '530124', '富民县', '530100');
INSERT INTO `area` VALUES ('2557', '530125', '宜良县', '530100');
INSERT INTO `area` VALUES ('2558', '530126', '石林彝族自治县', '530100');
INSERT INTO `area` VALUES ('2559', '530127', '嵩明县', '530100');
INSERT INTO `area` VALUES ('2560', '530128', '禄劝彝族苗族自治县', '530100');
INSERT INTO `area` VALUES ('2561', '530129', '寻甸回族彝族自治县', '530100');
INSERT INTO `area` VALUES ('2562', '530181', '安宁市', '530100');
INSERT INTO `area` VALUES ('2563', '530301', '市辖区', '530300');
INSERT INTO `area` VALUES ('2564', '530302', '麒麟区', '530300');
INSERT INTO `area` VALUES ('2565', '530321', '马龙县', '530300');
INSERT INTO `area` VALUES ('2566', '530322', '陆良县', '530300');
INSERT INTO `area` VALUES ('2567', '530323', '师宗县', '530300');
INSERT INTO `area` VALUES ('2568', '530324', '罗平县', '530300');
INSERT INTO `area` VALUES ('2569', '530325', '富源县', '530300');
INSERT INTO `area` VALUES ('2570', '530326', '会泽县', '530300');
INSERT INTO `area` VALUES ('2571', '530328', '沾益县', '530300');
INSERT INTO `area` VALUES ('2572', '530381', '宣威市', '530300');
INSERT INTO `area` VALUES ('2573', '530401', '市辖区', '530400');
INSERT INTO `area` VALUES ('2574', '530402', '红塔区', '530400');
INSERT INTO `area` VALUES ('2575', '530421', '江川县', '530400');
INSERT INTO `area` VALUES ('2576', '530422', '澄江县', '530400');
INSERT INTO `area` VALUES ('2577', '530423', '通海县', '530400');
INSERT INTO `area` VALUES ('2578', '530424', '华宁县', '530400');
INSERT INTO `area` VALUES ('2579', '530425', '易门县', '530400');
INSERT INTO `area` VALUES ('2580', '530426', '峨山彝族自治县', '530400');
INSERT INTO `area` VALUES ('2581', '530427', '新平彝族傣族自治县', '530400');
INSERT INTO `area` VALUES ('2582', '530428', '元江哈尼族彝族傣族自治县', '530400');
INSERT INTO `area` VALUES ('2583', '530501', '市辖区', '530500');
INSERT INTO `area` VALUES ('2584', '530502', '隆阳区', '530500');
INSERT INTO `area` VALUES ('2585', '530521', '施甸县', '530500');
INSERT INTO `area` VALUES ('2586', '530522', '腾冲县', '530500');
INSERT INTO `area` VALUES ('2587', '530523', '龙陵县', '530500');
INSERT INTO `area` VALUES ('2588', '530524', '昌宁县', '530500');
INSERT INTO `area` VALUES ('2589', '530601', '市辖区', '530600');
INSERT INTO `area` VALUES ('2590', '530602', '昭阳区', '530600');
INSERT INTO `area` VALUES ('2591', '530621', '鲁甸县', '530600');
INSERT INTO `area` VALUES ('2592', '530622', '巧家县', '530600');
INSERT INTO `area` VALUES ('2593', '530623', '盐津县', '530600');
INSERT INTO `area` VALUES ('2594', '530624', '大关县', '530600');
INSERT INTO `area` VALUES ('2595', '530625', '永善县', '530600');
INSERT INTO `area` VALUES ('2596', '530626', '绥江县', '530600');
INSERT INTO `area` VALUES ('2597', '530627', '镇雄县', '530600');
INSERT INTO `area` VALUES ('2598', '530628', '彝良县', '530600');
INSERT INTO `area` VALUES ('2599', '530629', '威信县', '530600');
INSERT INTO `area` VALUES ('2600', '530630', '水富县', '530600');
INSERT INTO `area` VALUES ('2601', '530701', '市辖区', '530700');
INSERT INTO `area` VALUES ('2602', '530702', '古城区', '530700');
INSERT INTO `area` VALUES ('2603', '530721', '玉龙纳西族自治县', '530700');
INSERT INTO `area` VALUES ('2604', '530722', '永胜县', '530700');
INSERT INTO `area` VALUES ('2605', '530723', '华坪县', '530700');
INSERT INTO `area` VALUES ('2606', '530724', '宁蒗彝族自治县', '530700');
INSERT INTO `area` VALUES ('2607', '530801', '市辖区', '530800');
INSERT INTO `area` VALUES ('2608', '530802', '翠云区', '530800');
INSERT INTO `area` VALUES ('2609', '530821', '普洱哈尼族彝族自治县', '530800');
INSERT INTO `area` VALUES ('2610', '530822', '墨江哈尼族自治县', '530800');
INSERT INTO `area` VALUES ('2611', '530823', '景东彝族自治县', '530800');
INSERT INTO `area` VALUES ('2612', '530824', '景谷傣族彝族自治县', '530800');
INSERT INTO `area` VALUES ('2613', '530825', '镇沅彝族哈尼族拉祜族自治县', '530800');
INSERT INTO `area` VALUES ('2614', '530826', '江城哈尼族彝族自治县', '530800');
INSERT INTO `area` VALUES ('2615', '530827', '孟连傣族拉祜族佤族自治县', '530800');
INSERT INTO `area` VALUES ('2616', '530828', '澜沧拉祜族自治县', '530800');
INSERT INTO `area` VALUES ('2617', '530829', '西盟佤族自治县', '530800');
INSERT INTO `area` VALUES ('2618', '530901', '市辖区', '530900');
INSERT INTO `area` VALUES ('2619', '530902', '临翔区', '530900');
INSERT INTO `area` VALUES ('2620', '530921', '凤庆县', '530900');
INSERT INTO `area` VALUES ('2621', '530922', '云　县', '530900');
INSERT INTO `area` VALUES ('2622', '530923', '永德县', '530900');
INSERT INTO `area` VALUES ('2623', '530924', '镇康县', '530900');
INSERT INTO `area` VALUES ('2624', '530925', '双江拉祜族佤族布朗族傣族自治县', '530900');
INSERT INTO `area` VALUES ('2625', '530926', '耿马傣族佤族自治县', '530900');
INSERT INTO `area` VALUES ('2626', '530927', '沧源佤族自治县', '530900');
INSERT INTO `area` VALUES ('2627', '532301', '楚雄市', '532300');
INSERT INTO `area` VALUES ('2628', '532322', '双柏县', '532300');
INSERT INTO `area` VALUES ('2629', '532323', '牟定县', '532300');
INSERT INTO `area` VALUES ('2630', '532324', '南华县', '532300');
INSERT INTO `area` VALUES ('2631', '532325', '姚安县', '532300');
INSERT INTO `area` VALUES ('2632', '532326', '大姚县', '532300');
INSERT INTO `area` VALUES ('2633', '532327', '永仁县', '532300');
INSERT INTO `area` VALUES ('2634', '532328', '元谋县', '532300');
INSERT INTO `area` VALUES ('2635', '532329', '武定县', '532300');
INSERT INTO `area` VALUES ('2636', '532331', '禄丰县', '532300');
INSERT INTO `area` VALUES ('2637', '532501', '个旧市', '532500');
INSERT INTO `area` VALUES ('2638', '532502', '开远市', '532500');
INSERT INTO `area` VALUES ('2639', '532522', '蒙自县', '532500');
INSERT INTO `area` VALUES ('2640', '532523', '屏边苗族自治县', '532500');
INSERT INTO `area` VALUES ('2641', '532524', '建水县', '532500');
INSERT INTO `area` VALUES ('2642', '532525', '石屏县', '532500');
INSERT INTO `area` VALUES ('2643', '532526', '弥勒县', '532500');
INSERT INTO `area` VALUES ('2644', '532527', '泸西县', '532500');
INSERT INTO `area` VALUES ('2645', '532528', '元阳县', '532500');
INSERT INTO `area` VALUES ('2646', '532529', '红河县', '532500');
INSERT INTO `area` VALUES ('2647', '532530', '金平苗族瑶族傣族自治县', '532500');
INSERT INTO `area` VALUES ('2648', '532531', '绿春县', '532500');
INSERT INTO `area` VALUES ('2649', '532532', '河口瑶族自治县', '532500');
INSERT INTO `area` VALUES ('2650', '532621', '文山县', '532600');
INSERT INTO `area` VALUES ('2651', '532622', '砚山县', '532600');
INSERT INTO `area` VALUES ('2652', '532623', '西畴县', '532600');
INSERT INTO `area` VALUES ('2653', '532624', '麻栗坡县', '532600');
INSERT INTO `area` VALUES ('2654', '532625', '马关县', '532600');
INSERT INTO `area` VALUES ('2655', '532626', '丘北县', '532600');
INSERT INTO `area` VALUES ('2656', '532627', '广南县', '532600');
INSERT INTO `area` VALUES ('2657', '532628', '富宁县', '532600');
INSERT INTO `area` VALUES ('2658', '532801', '景洪市', '532800');
INSERT INTO `area` VALUES ('2659', '532822', '勐海县', '532800');
INSERT INTO `area` VALUES ('2660', '532823', '勐腊县', '532800');
INSERT INTO `area` VALUES ('2661', '532901', '大理市', '532900');
INSERT INTO `area` VALUES ('2662', '532922', '漾濞彝族自治县', '532900');
INSERT INTO `area` VALUES ('2663', '532923', '祥云县', '532900');
INSERT INTO `area` VALUES ('2664', '532924', '宾川县', '532900');
INSERT INTO `area` VALUES ('2665', '532925', '弥渡县', '532900');
INSERT INTO `area` VALUES ('2666', '532926', '南涧彝族自治县', '532900');
INSERT INTO `area` VALUES ('2667', '532927', '巍山彝族回族自治县', '532900');
INSERT INTO `area` VALUES ('2668', '532928', '永平县', '532900');
INSERT INTO `area` VALUES ('2669', '532929', '云龙县', '532900');
INSERT INTO `area` VALUES ('2670', '532930', '洱源县', '532900');
INSERT INTO `area` VALUES ('2671', '532931', '剑川县', '532900');
INSERT INTO `area` VALUES ('2672', '532932', '鹤庆县', '532900');
INSERT INTO `area` VALUES ('2673', '533102', '瑞丽市', '533100');
INSERT INTO `area` VALUES ('2674', '533103', '潞西市', '533100');
INSERT INTO `area` VALUES ('2675', '533122', '梁河县', '533100');
INSERT INTO `area` VALUES ('2676', '533123', '盈江县', '533100');
INSERT INTO `area` VALUES ('2677', '533124', '陇川县', '533100');
INSERT INTO `area` VALUES ('2678', '533321', '泸水县', '533300');
INSERT INTO `area` VALUES ('2679', '533323', '福贡县', '533300');
INSERT INTO `area` VALUES ('2680', '533324', '贡山独龙族怒族自治县', '533300');
INSERT INTO `area` VALUES ('2681', '533325', '兰坪白族普米族自治县', '533300');
INSERT INTO `area` VALUES ('2682', '533421', '香格里拉县', '533400');
INSERT INTO `area` VALUES ('2683', '533422', '德钦县', '533400');
INSERT INTO `area` VALUES ('2684', '533423', '维西傈僳族自治县', '533400');
INSERT INTO `area` VALUES ('2685', '540101', '市辖区', '540100');
INSERT INTO `area` VALUES ('2686', '540102', '城关区', '540100');
INSERT INTO `area` VALUES ('2687', '540121', '林周县', '540100');
INSERT INTO `area` VALUES ('2688', '540122', '当雄县', '540100');
INSERT INTO `area` VALUES ('2689', '540123', '尼木县', '540100');
INSERT INTO `area` VALUES ('2690', '540124', '曲水县', '540100');
INSERT INTO `area` VALUES ('2691', '540125', '堆龙德庆县', '540100');
INSERT INTO `area` VALUES ('2692', '540126', '达孜县', '540100');
INSERT INTO `area` VALUES ('2693', '540127', '墨竹工卡县', '540100');
INSERT INTO `area` VALUES ('2694', '542121', '昌都县', '542100');
INSERT INTO `area` VALUES ('2695', '542122', '江达县', '542100');
INSERT INTO `area` VALUES ('2696', '542123', '贡觉县', '542100');
INSERT INTO `area` VALUES ('2697', '542124', '类乌齐县', '542100');
INSERT INTO `area` VALUES ('2698', '542125', '丁青县', '542100');
INSERT INTO `area` VALUES ('2699', '542126', '察雅县', '542100');
INSERT INTO `area` VALUES ('2700', '542127', '八宿县', '542100');
INSERT INTO `area` VALUES ('2701', '542128', '左贡县', '542100');
INSERT INTO `area` VALUES ('2702', '542129', '芒康县', '542100');
INSERT INTO `area` VALUES ('2703', '542132', '洛隆县', '542100');
INSERT INTO `area` VALUES ('2704', '542133', '边坝县', '542100');
INSERT INTO `area` VALUES ('2705', '542221', '乃东县', '542200');
INSERT INTO `area` VALUES ('2706', '542222', '扎囊县', '542200');
INSERT INTO `area` VALUES ('2707', '542223', '贡嘎县', '542200');
INSERT INTO `area` VALUES ('2708', '542224', '桑日县', '542200');
INSERT INTO `area` VALUES ('2709', '542225', '琼结县', '542200');
INSERT INTO `area` VALUES ('2710', '542226', '曲松县', '542200');
INSERT INTO `area` VALUES ('2711', '542227', '措美县', '542200');
INSERT INTO `area` VALUES ('2712', '542228', '洛扎县', '542200');
INSERT INTO `area` VALUES ('2713', '542229', '加查县', '542200');
INSERT INTO `area` VALUES ('2714', '542231', '隆子县', '542200');
INSERT INTO `area` VALUES ('2715', '542232', '错那县', '542200');
INSERT INTO `area` VALUES ('2716', '542233', '浪卡子县', '542200');
INSERT INTO `area` VALUES ('2717', '542301', '日喀则市', '542300');
INSERT INTO `area` VALUES ('2718', '542322', '南木林县', '542300');
INSERT INTO `area` VALUES ('2719', '542323', '江孜县', '542300');
INSERT INTO `area` VALUES ('2720', '542324', '定日县', '542300');
INSERT INTO `area` VALUES ('2721', '542325', '萨迦县', '542300');
INSERT INTO `area` VALUES ('2722', '542326', '拉孜县', '542300');
INSERT INTO `area` VALUES ('2723', '542327', '昂仁县', '542300');
INSERT INTO `area` VALUES ('2724', '542328', '谢通门县', '542300');
INSERT INTO `area` VALUES ('2725', '542329', '白朗县', '542300');
INSERT INTO `area` VALUES ('2726', '542330', '仁布县', '542300');
INSERT INTO `area` VALUES ('2727', '542331', '康马县', '542300');
INSERT INTO `area` VALUES ('2728', '542332', '定结县', '542300');
INSERT INTO `area` VALUES ('2729', '542333', '仲巴县', '542300');
INSERT INTO `area` VALUES ('2730', '542334', '亚东县', '542300');
INSERT INTO `area` VALUES ('2731', '542335', '吉隆县', '542300');
INSERT INTO `area` VALUES ('2732', '542336', '聂拉木县', '542300');
INSERT INTO `area` VALUES ('2733', '542337', '萨嘎县', '542300');
INSERT INTO `area` VALUES ('2734', '542338', '岗巴县', '542300');
INSERT INTO `area` VALUES ('2735', '542421', '那曲县', '542400');
INSERT INTO `area` VALUES ('2736', '542422', '嘉黎县', '542400');
INSERT INTO `area` VALUES ('2737', '542423', '比如县', '542400');
INSERT INTO `area` VALUES ('2738', '542424', '聂荣县', '542400');
INSERT INTO `area` VALUES ('2739', '542425', '安多县', '542400');
INSERT INTO `area` VALUES ('2740', '542426', '申扎县', '542400');
INSERT INTO `area` VALUES ('2741', '542427', '索　县', '542400');
INSERT INTO `area` VALUES ('2742', '542428', '班戈县', '542400');
INSERT INTO `area` VALUES ('2743', '542429', '巴青县', '542400');
INSERT INTO `area` VALUES ('2744', '542430', '尼玛县', '542400');
INSERT INTO `area` VALUES ('2745', '542521', '普兰县', '542500');
INSERT INTO `area` VALUES ('2746', '542522', '札达县', '542500');
INSERT INTO `area` VALUES ('2747', '542523', '噶尔县', '542500');
INSERT INTO `area` VALUES ('2748', '542524', '日土县', '542500');
INSERT INTO `area` VALUES ('2749', '542525', '革吉县', '542500');
INSERT INTO `area` VALUES ('2750', '542526', '改则县', '542500');
INSERT INTO `area` VALUES ('2751', '542527', '措勤县', '542500');
INSERT INTO `area` VALUES ('2752', '542621', '林芝县', '542600');
INSERT INTO `area` VALUES ('2753', '542622', '工布江达县', '542600');
INSERT INTO `area` VALUES ('2754', '542623', '米林县', '542600');
INSERT INTO `area` VALUES ('2755', '542624', '墨脱县', '542600');
INSERT INTO `area` VALUES ('2756', '542625', '波密县', '542600');
INSERT INTO `area` VALUES ('2757', '542626', '察隅县', '542600');
INSERT INTO `area` VALUES ('2758', '542627', '朗　县', '542600');
INSERT INTO `area` VALUES ('2759', '610101', '市辖区', '610100');
INSERT INTO `area` VALUES ('2760', '610102', '新城区', '610100');
INSERT INTO `area` VALUES ('2761', '610103', '碑林区', '610100');
INSERT INTO `area` VALUES ('2762', '610104', '莲湖区', '610100');
INSERT INTO `area` VALUES ('2763', '610111', '灞桥区', '610100');
INSERT INTO `area` VALUES ('2764', '610112', '未央区', '610100');
INSERT INTO `area` VALUES ('2765', '610113', '雁塔区', '610100');
INSERT INTO `area` VALUES ('2766', '610114', '阎良区', '610100');
INSERT INTO `area` VALUES ('2767', '610115', '临潼区', '610100');
INSERT INTO `area` VALUES ('2768', '610116', '长安区', '610100');
INSERT INTO `area` VALUES ('2769', '610122', '蓝田县', '610100');
INSERT INTO `area` VALUES ('2770', '610124', '周至县', '610100');
INSERT INTO `area` VALUES ('2771', '610125', '户　县', '610100');
INSERT INTO `area` VALUES ('2772', '610126', '高陵县', '610100');
INSERT INTO `area` VALUES ('2773', '610201', '市辖区', '610200');
INSERT INTO `area` VALUES ('2774', '610202', '王益区', '610200');
INSERT INTO `area` VALUES ('2775', '610203', '印台区', '610200');
INSERT INTO `area` VALUES ('2776', '610204', '耀州区', '610200');
INSERT INTO `area` VALUES ('2777', '610222', '宜君县', '610200');
INSERT INTO `area` VALUES ('2778', '610301', '市辖区', '610300');
INSERT INTO `area` VALUES ('2779', '610302', '渭滨区', '610300');
INSERT INTO `area` VALUES ('2780', '610303', '金台区', '610300');
INSERT INTO `area` VALUES ('2781', '610304', '陈仓区', '610300');
INSERT INTO `area` VALUES ('2782', '610322', '凤翔县', '610300');
INSERT INTO `area` VALUES ('2783', '610323', '岐山县', '610300');
INSERT INTO `area` VALUES ('2784', '610324', '扶风县', '610300');
INSERT INTO `area` VALUES ('2785', '610326', '眉　县', '610300');
INSERT INTO `area` VALUES ('2786', '610327', '陇　县', '610300');
INSERT INTO `area` VALUES ('2787', '610328', '千阳县', '610300');
INSERT INTO `area` VALUES ('2788', '610329', '麟游县', '610300');
INSERT INTO `area` VALUES ('2789', '610330', '凤　县', '610300');
INSERT INTO `area` VALUES ('2790', '610331', '太白县', '610300');
INSERT INTO `area` VALUES ('2791', '610401', '市辖区', '610400');
INSERT INTO `area` VALUES ('2792', '610402', '秦都区', '610400');
INSERT INTO `area` VALUES ('2793', '610403', '杨凌区', '610400');
INSERT INTO `area` VALUES ('2794', '610404', '渭城区', '610400');
INSERT INTO `area` VALUES ('2795', '610422', '三原县', '610400');
INSERT INTO `area` VALUES ('2796', '610423', '泾阳县', '610400');
INSERT INTO `area` VALUES ('2797', '610424', '乾　县', '610400');
INSERT INTO `area` VALUES ('2798', '610425', '礼泉县', '610400');
INSERT INTO `area` VALUES ('2799', '610426', '永寿县', '610400');
INSERT INTO `area` VALUES ('2800', '610427', '彬　县', '610400');
INSERT INTO `area` VALUES ('2801', '610428', '长武县', '610400');
INSERT INTO `area` VALUES ('2802', '610429', '旬邑县', '610400');
INSERT INTO `area` VALUES ('2803', '610430', '淳化县', '610400');
INSERT INTO `area` VALUES ('2804', '610431', '武功县', '610400');
INSERT INTO `area` VALUES ('2805', '610481', '兴平市', '610400');
INSERT INTO `area` VALUES ('2806', '610501', '市辖区', '610500');
INSERT INTO `area` VALUES ('2807', '610502', '临渭区', '610500');
INSERT INTO `area` VALUES ('2808', '610521', '华　县', '610500');
INSERT INTO `area` VALUES ('2809', '610522', '潼关县', '610500');
INSERT INTO `area` VALUES ('2810', '610523', '大荔县', '610500');
INSERT INTO `area` VALUES ('2811', '610524', '合阳县', '610500');
INSERT INTO `area` VALUES ('2812', '610525', '澄城县', '610500');
INSERT INTO `area` VALUES ('2813', '610526', '蒲城县', '610500');
INSERT INTO `area` VALUES ('2814', '610527', '白水县', '610500');
INSERT INTO `area` VALUES ('2815', '610528', '富平县', '610500');
INSERT INTO `area` VALUES ('2816', '610581', '韩城市', '610500');
INSERT INTO `area` VALUES ('2817', '610582', '华阴市', '610500');
INSERT INTO `area` VALUES ('2818', '610601', '市辖区', '610600');
INSERT INTO `area` VALUES ('2819', '610602', '宝塔区', '610600');
INSERT INTO `area` VALUES ('2820', '610621', '延长县', '610600');
INSERT INTO `area` VALUES ('2821', '610622', '延川县', '610600');
INSERT INTO `area` VALUES ('2822', '610623', '子长县', '610600');
INSERT INTO `area` VALUES ('2823', '610624', '安塞县', '610600');
INSERT INTO `area` VALUES ('2824', '610625', '志丹县', '610600');
INSERT INTO `area` VALUES ('2825', '610626', '吴旗县', '610600');
INSERT INTO `area` VALUES ('2826', '610627', '甘泉县', '610600');
INSERT INTO `area` VALUES ('2827', '610628', '富　县', '610600');
INSERT INTO `area` VALUES ('2828', '610629', '洛川县', '610600');
INSERT INTO `area` VALUES ('2829', '610630', '宜川县', '610600');
INSERT INTO `area` VALUES ('2830', '610631', '黄龙县', '610600');
INSERT INTO `area` VALUES ('2831', '610632', '黄陵县', '610600');
INSERT INTO `area` VALUES ('2832', '610701', '市辖区', '610700');
INSERT INTO `area` VALUES ('2833', '610702', '汉台区', '610700');
INSERT INTO `area` VALUES ('2834', '610721', '南郑县', '610700');
INSERT INTO `area` VALUES ('2835', '610722', '城固县', '610700');
INSERT INTO `area` VALUES ('2836', '610723', '洋　县', '610700');
INSERT INTO `area` VALUES ('2837', '610724', '西乡县', '610700');
INSERT INTO `area` VALUES ('2838', '610725', '勉　县', '610700');
INSERT INTO `area` VALUES ('2839', '610726', '宁强县', '610700');
INSERT INTO `area` VALUES ('2840', '610727', '略阳县', '610700');
INSERT INTO `area` VALUES ('2841', '610728', '镇巴县', '610700');
INSERT INTO `area` VALUES ('2842', '610729', '留坝县', '610700');
INSERT INTO `area` VALUES ('2843', '610730', '佛坪县', '610700');
INSERT INTO `area` VALUES ('2844', '610801', '市辖区', '610800');
INSERT INTO `area` VALUES ('2845', '610802', '榆阳区', '610800');
INSERT INTO `area` VALUES ('2846', '610821', '神木县', '610800');
INSERT INTO `area` VALUES ('2847', '610822', '府谷县', '610800');
INSERT INTO `area` VALUES ('2848', '610823', '横山县', '610800');
INSERT INTO `area` VALUES ('2849', '610824', '靖边县', '610800');
INSERT INTO `area` VALUES ('2850', '610825', '定边县', '610800');
INSERT INTO `area` VALUES ('2851', '610826', '绥德县', '610800');
INSERT INTO `area` VALUES ('2852', '610827', '米脂县', '610800');
INSERT INTO `area` VALUES ('2853', '610828', '佳　县', '610800');
INSERT INTO `area` VALUES ('2854', '610829', '吴堡县', '610800');
INSERT INTO `area` VALUES ('2855', '610830', '清涧县', '610800');
INSERT INTO `area` VALUES ('2856', '610831', '子洲县', '610800');
INSERT INTO `area` VALUES ('2857', '610901', '市辖区', '610900');
INSERT INTO `area` VALUES ('2858', '610902', '汉滨区', '610900');
INSERT INTO `area` VALUES ('2859', '610921', '汉阴县', '610900');
INSERT INTO `area` VALUES ('2860', '610922', '石泉县', '610900');
INSERT INTO `area` VALUES ('2861', '610923', '宁陕县', '610900');
INSERT INTO `area` VALUES ('2862', '610924', '紫阳县', '610900');
INSERT INTO `area` VALUES ('2863', '610925', '岚皋县', '610900');
INSERT INTO `area` VALUES ('2864', '610926', '平利县', '610900');
INSERT INTO `area` VALUES ('2865', '610927', '镇坪县', '610900');
INSERT INTO `area` VALUES ('2866', '610928', '旬阳县', '610900');
INSERT INTO `area` VALUES ('2867', '610929', '白河县', '610900');
INSERT INTO `area` VALUES ('2868', '611001', '市辖区', '611000');
INSERT INTO `area` VALUES ('2869', '611002', '商州区', '611000');
INSERT INTO `area` VALUES ('2870', '611021', '洛南县', '611000');
INSERT INTO `area` VALUES ('2871', '611022', '丹凤县', '611000');
INSERT INTO `area` VALUES ('2872', '611023', '商南县', '611000');
INSERT INTO `area` VALUES ('2873', '611024', '山阳县', '611000');
INSERT INTO `area` VALUES ('2874', '611025', '镇安县', '611000');
INSERT INTO `area` VALUES ('2875', '611026', '柞水县', '611000');
INSERT INTO `area` VALUES ('2876', '620101', '市辖区', '620100');
INSERT INTO `area` VALUES ('2877', '620102', '城关区', '620100');
INSERT INTO `area` VALUES ('2878', '620103', '七里河区', '620100');
INSERT INTO `area` VALUES ('2879', '620104', '西固区', '620100');
INSERT INTO `area` VALUES ('2880', '620105', '安宁区', '620100');
INSERT INTO `area` VALUES ('2881', '620111', '红古区', '620100');
INSERT INTO `area` VALUES ('2882', '620121', '永登县', '620100');
INSERT INTO `area` VALUES ('2883', '620122', '皋兰县', '620100');
INSERT INTO `area` VALUES ('2884', '620123', '榆中县', '620100');
INSERT INTO `area` VALUES ('2885', '620201', '市辖区', '620200');
INSERT INTO `area` VALUES ('2886', '620301', '市辖区', '620300');
INSERT INTO `area` VALUES ('2887', '620302', '金川区', '620300');
INSERT INTO `area` VALUES ('2888', '620321', '永昌县', '620300');
INSERT INTO `area` VALUES ('2889', '620401', '市辖区', '620400');
INSERT INTO `area` VALUES ('2890', '620402', '白银区', '620400');
INSERT INTO `area` VALUES ('2891', '620403', '平川区', '620400');
INSERT INTO `area` VALUES ('2892', '620421', '靖远县', '620400');
INSERT INTO `area` VALUES ('2893', '620422', '会宁县', '620400');
INSERT INTO `area` VALUES ('2894', '620423', '景泰县', '620400');
INSERT INTO `area` VALUES ('2895', '620501', '市辖区', '620500');
INSERT INTO `area` VALUES ('2896', '620502', '秦城区', '620500');
INSERT INTO `area` VALUES ('2897', '620503', '北道区', '620500');
INSERT INTO `area` VALUES ('2898', '620521', '清水县', '620500');
INSERT INTO `area` VALUES ('2899', '620522', '秦安县', '620500');
INSERT INTO `area` VALUES ('2900', '620523', '甘谷县', '620500');
INSERT INTO `area` VALUES ('2901', '620524', '武山县', '620500');
INSERT INTO `area` VALUES ('2902', '620525', '张家川回族自治县', '620500');
INSERT INTO `area` VALUES ('2903', '620601', '市辖区', '620600');
INSERT INTO `area` VALUES ('2904', '620602', '凉州区', '620600');
INSERT INTO `area` VALUES ('2905', '620621', '民勤县', '620600');
INSERT INTO `area` VALUES ('2906', '620622', '古浪县', '620600');
INSERT INTO `area` VALUES ('2907', '620623', '天祝藏族自治县', '620600');
INSERT INTO `area` VALUES ('2908', '620701', '市辖区', '620700');
INSERT INTO `area` VALUES ('2909', '620702', '甘州区', '620700');
INSERT INTO `area` VALUES ('2910', '620721', '肃南裕固族自治县', '620700');
INSERT INTO `area` VALUES ('2911', '620722', '民乐县', '620700');
INSERT INTO `area` VALUES ('2912', '620723', '临泽县', '620700');
INSERT INTO `area` VALUES ('2913', '620724', '高台县', '620700');
INSERT INTO `area` VALUES ('2914', '620725', '山丹县', '620700');
INSERT INTO `area` VALUES ('2915', '620801', '市辖区', '620800');
INSERT INTO `area` VALUES ('2916', '620802', '崆峒区', '620800');
INSERT INTO `area` VALUES ('2917', '620821', '泾川县', '620800');
INSERT INTO `area` VALUES ('2918', '620822', '灵台县', '620800');
INSERT INTO `area` VALUES ('2919', '620823', '崇信县', '620800');
INSERT INTO `area` VALUES ('2920', '620824', '华亭县', '620800');
INSERT INTO `area` VALUES ('2921', '620825', '庄浪县', '620800');
INSERT INTO `area` VALUES ('2922', '620826', '静宁县', '620800');
INSERT INTO `area` VALUES ('2923', '620901', '市辖区', '620900');
INSERT INTO `area` VALUES ('2924', '620902', '肃州区', '620900');
INSERT INTO `area` VALUES ('2925', '620921', '金塔县', '620900');
INSERT INTO `area` VALUES ('2926', '620922', '安西县', '620900');
INSERT INTO `area` VALUES ('2927', '620923', '肃北蒙古族自治县', '620900');
INSERT INTO `area` VALUES ('2928', '620924', '阿克塞哈萨克族自治县', '620900');
INSERT INTO `area` VALUES ('2929', '620981', '玉门市', '620900');
INSERT INTO `area` VALUES ('2930', '620982', '敦煌市', '620900');
INSERT INTO `area` VALUES ('2931', '621001', '市辖区', '621000');
INSERT INTO `area` VALUES ('2932', '621002', '西峰区', '621000');
INSERT INTO `area` VALUES ('2933', '621021', '庆城县', '621000');
INSERT INTO `area` VALUES ('2934', '621022', '环　县', '621000');
INSERT INTO `area` VALUES ('2935', '621023', '华池县', '621000');
INSERT INTO `area` VALUES ('2936', '621024', '合水县', '621000');
INSERT INTO `area` VALUES ('2937', '621025', '正宁县', '621000');
INSERT INTO `area` VALUES ('2938', '621026', '宁　县', '621000');
INSERT INTO `area` VALUES ('2939', '621027', '镇原县', '621000');
INSERT INTO `area` VALUES ('2940', '621101', '市辖区', '621100');
INSERT INTO `area` VALUES ('2941', '621102', '安定区', '621100');
INSERT INTO `area` VALUES ('2942', '621121', '通渭县', '621100');
INSERT INTO `area` VALUES ('2943', '621122', '陇西县', '621100');
INSERT INTO `area` VALUES ('2944', '621123', '渭源县', '621100');
INSERT INTO `area` VALUES ('2945', '621124', '临洮县', '621100');
INSERT INTO `area` VALUES ('2946', '621125', '漳　县', '621100');
INSERT INTO `area` VALUES ('2947', '621126', '岷　县', '621100');
INSERT INTO `area` VALUES ('2948', '621201', '市辖区', '621200');
INSERT INTO `area` VALUES ('2949', '621202', '武都区', '621200');
INSERT INTO `area` VALUES ('2950', '621221', '成　县', '621200');
INSERT INTO `area` VALUES ('2951', '621222', '文　县', '621200');
INSERT INTO `area` VALUES ('2952', '621223', '宕昌县', '621200');
INSERT INTO `area` VALUES ('2953', '621224', '康　县', '621200');
INSERT INTO `area` VALUES ('2954', '621225', '西和县', '621200');
INSERT INTO `area` VALUES ('2955', '621226', '礼　县', '621200');
INSERT INTO `area` VALUES ('2956', '621227', '徽　县', '621200');
INSERT INTO `area` VALUES ('2957', '621228', '两当县', '621200');
INSERT INTO `area` VALUES ('2958', '622901', '临夏市', '622900');
INSERT INTO `area` VALUES ('2959', '622921', '临夏县', '622900');
INSERT INTO `area` VALUES ('2960', '622922', '康乐县', '622900');
INSERT INTO `area` VALUES ('2961', '622923', '永靖县', '622900');
INSERT INTO `area` VALUES ('2962', '622924', '广河县', '622900');
INSERT INTO `area` VALUES ('2963', '622925', '和政县', '622900');
INSERT INTO `area` VALUES ('2964', '622926', '东乡族自治县', '622900');
INSERT INTO `area` VALUES ('2965', '622927', '积石山保安族东乡族撒拉族自治县', '622900');
INSERT INTO `area` VALUES ('2966', '623001', '合作市', '623000');
INSERT INTO `area` VALUES ('2967', '623021', '临潭县', '623000');
INSERT INTO `area` VALUES ('2968', '623022', '卓尼县', '623000');
INSERT INTO `area` VALUES ('2969', '623023', '舟曲县', '623000');
INSERT INTO `area` VALUES ('2970', '623024', '迭部县', '623000');
INSERT INTO `area` VALUES ('2971', '623025', '玛曲县', '623000');
INSERT INTO `area` VALUES ('2972', '623026', '碌曲县', '623000');
INSERT INTO `area` VALUES ('2973', '623027', '夏河县', '623000');
INSERT INTO `area` VALUES ('2974', '630101', '市辖区', '630100');
INSERT INTO `area` VALUES ('2975', '630102', '城东区', '630100');
INSERT INTO `area` VALUES ('2976', '630103', '城中区', '630100');
INSERT INTO `area` VALUES ('2977', '630104', '城西区', '630100');
INSERT INTO `area` VALUES ('2978', '630105', '城北区', '630100');
INSERT INTO `area` VALUES ('2979', '630121', '大通回族土族自治县', '630100');
INSERT INTO `area` VALUES ('2980', '630122', '湟中县', '630100');
INSERT INTO `area` VALUES ('2981', '630123', '湟源县', '630100');
INSERT INTO `area` VALUES ('2982', '632121', '平安县', '632100');
INSERT INTO `area` VALUES ('2983', '632122', '民和回族土族自治县', '632100');
INSERT INTO `area` VALUES ('2984', '632123', '乐都县', '632100');
INSERT INTO `area` VALUES ('2985', '632126', '互助土族自治县', '632100');
INSERT INTO `area` VALUES ('2986', '632127', '化隆回族自治县', '632100');
INSERT INTO `area` VALUES ('2987', '632128', '循化撒拉族自治县', '632100');
INSERT INTO `area` VALUES ('2988', '632221', '门源回族自治县', '632200');
INSERT INTO `area` VALUES ('2989', '632222', '祁连县', '632200');
INSERT INTO `area` VALUES ('2990', '632223', '海晏县', '632200');
INSERT INTO `area` VALUES ('2991', '632224', '刚察县', '632200');
INSERT INTO `area` VALUES ('2992', '632321', '同仁县', '632300');
INSERT INTO `area` VALUES ('2993', '632322', '尖扎县', '632300');
INSERT INTO `area` VALUES ('2994', '632323', '泽库县', '632300');
INSERT INTO `area` VALUES ('2995', '632324', '河南蒙古族自治县', '632300');
INSERT INTO `area` VALUES ('2996', '632521', '共和县', '632500');
INSERT INTO `area` VALUES ('2997', '632522', '同德县', '632500');
INSERT INTO `area` VALUES ('2998', '632523', '贵德县', '632500');
INSERT INTO `area` VALUES ('2999', '632524', '兴海县', '632500');
INSERT INTO `area` VALUES ('3000', '632525', '贵南县', '632500');
INSERT INTO `area` VALUES ('3001', '632621', '玛沁县', '632600');
INSERT INTO `area` VALUES ('3002', '632622', '班玛县', '632600');
INSERT INTO `area` VALUES ('3003', '632623', '甘德县', '632600');
INSERT INTO `area` VALUES ('3004', '632624', '达日县', '632600');
INSERT INTO `area` VALUES ('3005', '632625', '久治县', '632600');
INSERT INTO `area` VALUES ('3006', '632626', '玛多县', '632600');
INSERT INTO `area` VALUES ('3007', '632721', '玉树县', '632700');
INSERT INTO `area` VALUES ('3008', '632722', '杂多县', '632700');
INSERT INTO `area` VALUES ('3009', '632723', '称多县', '632700');
INSERT INTO `area` VALUES ('3010', '632724', '治多县', '632700');
INSERT INTO `area` VALUES ('3011', '632725', '囊谦县', '632700');
INSERT INTO `area` VALUES ('3012', '632726', '曲麻莱县', '632700');
INSERT INTO `area` VALUES ('3013', '632801', '格尔木市', '632800');
INSERT INTO `area` VALUES ('3014', '632802', '德令哈市', '632800');
INSERT INTO `area` VALUES ('3015', '632821', '乌兰县', '632800');
INSERT INTO `area` VALUES ('3016', '632822', '都兰县', '632800');
INSERT INTO `area` VALUES ('3017', '632823', '天峻县', '632800');
INSERT INTO `area` VALUES ('3018', '640101', '市辖区', '640100');
INSERT INTO `area` VALUES ('3019', '640104', '兴庆区', '640100');
INSERT INTO `area` VALUES ('3020', '640105', '西夏区', '640100');
INSERT INTO `area` VALUES ('3021', '640106', '金凤区', '640100');
INSERT INTO `area` VALUES ('3022', '640121', '永宁县', '640100');
INSERT INTO `area` VALUES ('3023', '640122', '贺兰县', '640100');
INSERT INTO `area` VALUES ('3024', '640181', '灵武市', '640100');
INSERT INTO `area` VALUES ('3025', '640201', '市辖区', '640200');
INSERT INTO `area` VALUES ('3026', '640202', '大武口区', '640200');
INSERT INTO `area` VALUES ('3027', '640205', '惠农区', '640200');
INSERT INTO `area` VALUES ('3028', '640221', '平罗县', '640200');
INSERT INTO `area` VALUES ('3029', '640301', '市辖区', '640300');
INSERT INTO `area` VALUES ('3030', '640302', '利通区', '640300');
INSERT INTO `area` VALUES ('3031', '640323', '盐池县', '640300');
INSERT INTO `area` VALUES ('3032', '640324', '同心县', '640300');
INSERT INTO `area` VALUES ('3033', '640381', '青铜峡市', '640300');
INSERT INTO `area` VALUES ('3034', '640401', '市辖区', '640400');
INSERT INTO `area` VALUES ('3035', '640402', '原州区', '640400');
INSERT INTO `area` VALUES ('3036', '640422', '西吉县', '640400');
INSERT INTO `area` VALUES ('3037', '640423', '隆德县', '640400');
INSERT INTO `area` VALUES ('3038', '640424', '泾源县', '640400');
INSERT INTO `area` VALUES ('3039', '640425', '彭阳县', '640400');
INSERT INTO `area` VALUES ('3040', '640501', '市辖区', '640500');
INSERT INTO `area` VALUES ('3041', '640502', '沙坡头区', '640500');
INSERT INTO `area` VALUES ('3042', '640521', '中宁县', '640500');
INSERT INTO `area` VALUES ('3043', '640522', '海原县', '640500');
INSERT INTO `area` VALUES ('3044', '650101', '市辖区', '650100');
INSERT INTO `area` VALUES ('3045', '650102', '天山区', '650100');
INSERT INTO `area` VALUES ('3046', '650103', '沙依巴克区', '650100');
INSERT INTO `area` VALUES ('3047', '650104', '新市区', '650100');
INSERT INTO `area` VALUES ('3048', '650105', '水磨沟区', '650100');
INSERT INTO `area` VALUES ('3049', '650106', '头屯河区', '650100');
INSERT INTO `area` VALUES ('3050', '650107', '达坂城区', '650100');
INSERT INTO `area` VALUES ('3051', '650108', '东山区', '650100');
INSERT INTO `area` VALUES ('3052', '650121', '乌鲁木齐县', '650100');
INSERT INTO `area` VALUES ('3053', '650201', '市辖区', '650200');
INSERT INTO `area` VALUES ('3054', '650202', '独山子区', '650200');
INSERT INTO `area` VALUES ('3055', '650203', '克拉玛依区', '650200');
INSERT INTO `area` VALUES ('3056', '650204', '白碱滩区', '650200');
INSERT INTO `area` VALUES ('3057', '650205', '乌尔禾区', '650200');
INSERT INTO `area` VALUES ('3058', '652101', '吐鲁番市', '652100');
INSERT INTO `area` VALUES ('3059', '652122', '鄯善县', '652100');
INSERT INTO `area` VALUES ('3060', '652123', '托克逊县', '652100');
INSERT INTO `area` VALUES ('3061', '652201', '哈密市', '652200');
INSERT INTO `area` VALUES ('3062', '652222', '巴里坤哈萨克自治县', '652200');
INSERT INTO `area` VALUES ('3063', '652223', '伊吾县', '652200');
INSERT INTO `area` VALUES ('3064', '652301', '昌吉市', '652300');
INSERT INTO `area` VALUES ('3065', '652302', '阜康市', '652300');
INSERT INTO `area` VALUES ('3066', '652303', '米泉市', '652300');
INSERT INTO `area` VALUES ('3067', '652323', '呼图壁县', '652300');
INSERT INTO `area` VALUES ('3068', '652324', '玛纳斯县', '652300');
INSERT INTO `area` VALUES ('3069', '652325', '奇台县', '652300');
INSERT INTO `area` VALUES ('3070', '652327', '吉木萨尔县', '652300');
INSERT INTO `area` VALUES ('3071', '652328', '木垒哈萨克自治县', '652300');
INSERT INTO `area` VALUES ('3072', '652701', '博乐市', '652700');
INSERT INTO `area` VALUES ('3073', '652722', '精河县', '652700');
INSERT INTO `area` VALUES ('3074', '652723', '温泉县', '652700');
INSERT INTO `area` VALUES ('3075', '652801', '库尔勒市', '652800');
INSERT INTO `area` VALUES ('3076', '652822', '轮台县', '652800');
INSERT INTO `area` VALUES ('3077', '652823', '尉犁县', '652800');
INSERT INTO `area` VALUES ('3078', '652824', '若羌县', '652800');
INSERT INTO `area` VALUES ('3079', '652825', '且末县', '652800');
INSERT INTO `area` VALUES ('3080', '652826', '焉耆回族自治县', '652800');
INSERT INTO `area` VALUES ('3081', '652827', '和静县', '652800');
INSERT INTO `area` VALUES ('3082', '652828', '和硕县', '652800');
INSERT INTO `area` VALUES ('3083', '652829', '博湖县', '652800');
INSERT INTO `area` VALUES ('3084', '652901', '阿克苏市', '652900');
INSERT INTO `area` VALUES ('3085', '652922', '温宿县', '652900');
INSERT INTO `area` VALUES ('3086', '652923', '库车县', '652900');
INSERT INTO `area` VALUES ('3087', '652924', '沙雅县', '652900');
INSERT INTO `area` VALUES ('3088', '652925', '新和县', '652900');
INSERT INTO `area` VALUES ('3089', '652926', '拜城县', '652900');
INSERT INTO `area` VALUES ('3090', '652927', '乌什县', '652900');
INSERT INTO `area` VALUES ('3091', '652928', '阿瓦提县', '652900');
INSERT INTO `area` VALUES ('3092', '652929', '柯坪县', '652900');
INSERT INTO `area` VALUES ('3093', '653001', '阿图什市', '653000');
INSERT INTO `area` VALUES ('3094', '653022', '阿克陶县', '653000');
INSERT INTO `area` VALUES ('3095', '653023', '阿合奇县', '653000');
INSERT INTO `area` VALUES ('3096', '653024', '乌恰县', '653000');
INSERT INTO `area` VALUES ('3097', '653101', '喀什市', '653100');
INSERT INTO `area` VALUES ('3098', '653121', '疏附县', '653100');
INSERT INTO `area` VALUES ('3099', '653122', '疏勒县', '653100');
INSERT INTO `area` VALUES ('3100', '653123', '英吉沙县', '653100');
INSERT INTO `area` VALUES ('3101', '653124', '泽普县', '653100');
INSERT INTO `area` VALUES ('3102', '653125', '莎车县', '653100');
INSERT INTO `area` VALUES ('3103', '653126', '叶城县', '653100');
INSERT INTO `area` VALUES ('3104', '653127', '麦盖提县', '653100');
INSERT INTO `area` VALUES ('3105', '653128', '岳普湖县', '653100');
INSERT INTO `area` VALUES ('3106', '653129', '伽师县', '653100');
INSERT INTO `area` VALUES ('3107', '653130', '巴楚县', '653100');
INSERT INTO `area` VALUES ('3108', '653131', '塔什库尔干塔吉克自治县', '653100');
INSERT INTO `area` VALUES ('3109', '653201', '和田市', '653200');
INSERT INTO `area` VALUES ('3110', '653221', '和田县', '653200');
INSERT INTO `area` VALUES ('3111', '653222', '墨玉县', '653200');
INSERT INTO `area` VALUES ('3112', '653223', '皮山县', '653200');
INSERT INTO `area` VALUES ('3113', '653224', '洛浦县', '653200');
INSERT INTO `area` VALUES ('3114', '653225', '策勒县', '653200');
INSERT INTO `area` VALUES ('3115', '653226', '于田县', '653200');
INSERT INTO `area` VALUES ('3116', '653227', '民丰县', '653200');
INSERT INTO `area` VALUES ('3117', '654002', '伊宁市', '654000');
INSERT INTO `area` VALUES ('3118', '654003', '奎屯市', '654000');
INSERT INTO `area` VALUES ('3119', '654021', '伊宁县', '654000');
INSERT INTO `area` VALUES ('3120', '654022', '察布查尔锡伯自治县', '654000');
INSERT INTO `area` VALUES ('3121', '654023', '霍城县', '654000');
INSERT INTO `area` VALUES ('3122', '654024', '巩留县', '654000');
INSERT INTO `area` VALUES ('3123', '654025', '新源县', '654000');
INSERT INTO `area` VALUES ('3124', '654026', '昭苏县', '654000');
INSERT INTO `area` VALUES ('3125', '654027', '特克斯县', '654000');
INSERT INTO `area` VALUES ('3126', '654028', '尼勒克县', '654000');
INSERT INTO `area` VALUES ('3127', '654201', '塔城市', '654200');
INSERT INTO `area` VALUES ('3128', '654202', '乌苏市', '654200');
INSERT INTO `area` VALUES ('3129', '654221', '额敏县', '654200');
INSERT INTO `area` VALUES ('3130', '654223', '沙湾县', '654200');
INSERT INTO `area` VALUES ('3131', '654224', '托里县', '654200');
INSERT INTO `area` VALUES ('3132', '654225', '裕民县', '654200');
INSERT INTO `area` VALUES ('3133', '654226', '和布克赛尔蒙古自治县', '654200');
INSERT INTO `area` VALUES ('3134', '654301', '阿勒泰市', '654300');
INSERT INTO `area` VALUES ('3135', '654321', '布尔津县', '654300');
INSERT INTO `area` VALUES ('3136', '654322', '富蕴县', '654300');
INSERT INTO `area` VALUES ('3137', '654323', '福海县', '654300');
INSERT INTO `area` VALUES ('3138', '654324', '哈巴河县', '654300');
INSERT INTO `area` VALUES ('3139', '654325', '青河县', '654300');
INSERT INTO `area` VALUES ('3140', '654326', '吉木乃县', '654300');
INSERT INTO `area` VALUES ('3141', '659001', '石河子市', '659000');
INSERT INTO `area` VALUES ('3142', '659002', '阿拉尔市', '659000');
INSERT INTO `area` VALUES ('3143', '659003', '图木舒克市', '659000');
INSERT INTO `area` VALUES ('3144', '659004', '五家渠市', '659000');

-- ----------------------------
-- Table structure for `auth_group`
-- ----------------------------
DROP TABLE IF EXISTS `auth_group`;
CREATE TABLE `auth_group` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `title` char(100) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `rules` char(80) NOT NULL DEFAULT '',
  `description` varchar(100) NOT NULL DEFAULT '',
  `type` tinyint(1) NOT NULL COMMENT '1总后台  2商家后台',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of auth_group
-- ----------------------------
INSERT INTO `auth_group` VALUES ('1', '客服', '1', '10,11,12,13,14,53', '', '1');
INSERT INTO `auth_group` VALUES ('2', '总后台订单管理', '1', '10,16,11,12,53,20,21', '总后台订单管理', '1');
INSERT INTO `auth_group` VALUES ('3', '商家客服', '1', '56', '商家客服', '2');

-- ----------------------------
-- Table structure for `auth_group_access`
-- ----------------------------
DROP TABLE IF EXISTS `auth_group_access`;
CREATE TABLE `auth_group_access` (
  `uid` mediumint(8) unsigned NOT NULL,
  `group_id` mediumint(8) unsigned NOT NULL,
  UNIQUE KEY `uid_group_id` (`uid`,`group_id`),
  KEY `uid` (`uid`),
  KEY `group_id` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of auth_group_access
-- ----------------------------
INSERT INTO `auth_group_access` VALUES ('5', '1');
INSERT INTO `auth_group_access` VALUES ('9', '3');

-- ----------------------------
-- Table structure for `auth_rule`
-- ----------------------------
DROP TABLE IF EXISTS `auth_rule`;
CREATE TABLE `auth_rule` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(80) NOT NULL DEFAULT '' COMMENT '规则唯一英文标识',
  `pid` tinyint(4) NOT NULL COMMENT '上级id',
  `title` char(20) NOT NULL DEFAULT '' COMMENT '规则中文描述',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-url;2-主菜单',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否有效(0:无效,1:有效)',
  `condition` char(100) NOT NULL DEFAULT '',
  `style` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1总后台  2商家后台',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=57 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of auth_rule
-- ----------------------------
INSERT INTO `auth_rule` VALUES ('1', 'Admin/Category/index', '0', '分类', '1', '1', '总后台', '1');
INSERT INTO `auth_rule` VALUES ('2', 'Admin/Category/addMain', '1', '添加分类', '2', '1', '总后台', '1');
INSERT INTO `auth_rule` VALUES ('3', 'Admin/Category/addChild', '1', '添加子类', '2', '1', '总后台', '1');
INSERT INTO `auth_rule` VALUES ('4', 'Admin/Category/editor', '1', '修改分类', '2', '1', '总后台', '1');
INSERT INTO `auth_rule` VALUES ('5', 'Admin/Category/delete', '1', '删除分类', '2', '1', '总后台', '1');
INSERT INTO `auth_rule` VALUES ('6', 'Admin/Product/index', '0', '产品', '1', '1', '总后台', '1');
INSERT INTO `auth_rule` VALUES ('7', 'Admin/Product/add', '6', '添加产品', '2', '1', '总后台', '1');
INSERT INTO `auth_rule` VALUES ('8', 'Admin/Product/editor', '6', '修改产品', '2', '1', '总后台', '1');
INSERT INTO `auth_rule` VALUES ('9', 'Admin/Product/delete', '6', '删除产品', '2', '1', '总后台', '1');
INSERT INTO `auth_rule` VALUES ('10', 'Admin/Index/index', '0', '主页', '1', '1', '总后台', '1');
INSERT INTO `auth_rule` VALUES ('11', 'Admin/Order/index', '0', '订单', '1', '1', '总后台', '1');
INSERT INTO `auth_rule` VALUES ('12', 'Admin/Order/see', '11', '查看订单', '2', '1', '总后台', '1');
INSERT INTO `auth_rule` VALUES ('53', 'Admin/Order/comment', '11', '订单评论', '2', '1', '总后台', '1');
INSERT INTO `auth_rule` VALUES ('14', 'Admin/Comment/plState', '0', '修改评论状态', '2', '1', '总后台', '1');
INSERT INTO `auth_rule` VALUES ('16', 'Admin/Slide/index', '10', '轮播', '2', '1', '总后台', '1');
INSERT INTO `auth_rule` VALUES ('17', 'Admin/Slide/editor', '14', '修改轮播', '2', '1', '总后台', '1');
INSERT INTO `auth_rule` VALUES ('18', 'Admin/Slide/add', '14', '添加轮播', '2', '1', '总后台', '1');
INSERT INTO `auth_rule` VALUES ('19', 'Admin/Slide/del', '14', '删除轮播', '2', '1', '总后台', '1');
INSERT INTO `auth_rule` VALUES ('20', 'Admin/Index/set', '11', '店铺公用设置', '2', '1', '总后台', '1');
INSERT INTO `auth_rule` VALUES ('21', 'Admin/Index/statistics', '11', '数据统计', '2', '1', '总后台', '1');
INSERT INTO `auth_rule` VALUES ('22', 'Admin/Merchant/index', '0', '商家', '1', '1', '总后台', '1');
INSERT INTO `auth_rule` VALUES ('23', 'Admin/Merchant/add', '22', '商家添加', '2', '1', '总后台', '1');
INSERT INTO `auth_rule` VALUES ('24', 'Admin/Merchant/editor', '22', '商家修改', '2', '1', '总后台', '1');
INSERT INTO `auth_rule` VALUES ('25', 'Admin/Merchant/delete', '22', '删除', '2', '1', '总后台', '1');
INSERT INTO `auth_rule` VALUES ('26', 'Admin/Shop/index', '0', '店铺', '1', '1', '总后台', '1');
INSERT INTO `auth_rule` VALUES ('27', 'Admin/Shop/add', '26', '店铺添加', '2', '1', '总后台', '1');
INSERT INTO `auth_rule` VALUES ('28', 'Admin/Shop/editor', '26', '店铺修改', '2', '1', '总后台', '1');
INSERT INTO `auth_rule` VALUES ('29', 'Admin/Shop/delete', '26', '店铺删除', '2', '1', '总后台', '1');
INSERT INTO `auth_rule` VALUES ('30', 'Admin/User/index', '0', '用户', '1', '1', '总后台', '1');
INSERT INTO `auth_rule` VALUES ('31', 'Admin/User/editor', '30', '用户修改', '2', '1', '总后台', '1');
INSERT INTO `auth_rule` VALUES ('32', 'Admin/User/delete', '30', '用户删除', '2', '1', '总后台', '1');
INSERT INTO `auth_rule` VALUES ('33', 'Admin/Bbs/index', '0', '生活圈', '1', '1', '总后台', '1');
INSERT INTO `auth_rule` VALUES ('34', 'Admin/Bbs/activity_see', '33', '生活圈查看', '2', '1', '总后台', '1');
INSERT INTO `auth_rule` VALUES ('35', 'Admin/Bbs/activity_editor', '33', '生活圈修改', '2', '1', '总后台', '1');
INSERT INTO `auth_rule` VALUES ('36', 'Admin/Bbs/activity_delete', '33', '生活圈删除', '2', '1', '总后台', '1');
INSERT INTO `auth_rule` VALUES ('37', 'Admin/Area/index', '0', '小区', '1', '1', '总后台', '1');
INSERT INTO `auth_rule` VALUES ('38', 'Admin/Area/add', '37', '小区添加', '2', '1', '总后台', '1');
INSERT INTO `auth_rule` VALUES ('39', 'Admin/Area/see', '37', '小区修改', '2', '1', '总后台', '1');
INSERT INTO `auth_rule` VALUES ('40', 'Admin/Area/delete', '37', '小区删除', '2', '1', '总后台', '1');
INSERT INTO `auth_rule` VALUES ('41', 'Admin/Area/status', '37', '小区冻结', '2', '1', '总后台', '1');
INSERT INTO `auth_rule` VALUES ('42', 'Admin/Delivery/index', '0', '配送员', '1', '1', '总后台', '1');
INSERT INTO `auth_rule` VALUES ('43', 'Admin/Delivery/add', '42', '配送员添加', '2', '1', '总后台', '1');
INSERT INTO `auth_rule` VALUES ('44', 'Admin/Delivery/editor', '42', '配送员修改', '2', '1', '总后台', '1');
INSERT INTO `auth_rule` VALUES ('45', 'Admin/Delivery/see', '42', '配送员查看', '2', '1', '总后台', '1');
INSERT INTO `auth_rule` VALUES ('46', 'Admin/Delivery/status', '42', '配送员冻结', '2', '1', '总后台', '1');
INSERT INTO `auth_rule` VALUES ('47', 'Admin/Delivery/delete', '42', '配送员删除', '2', '1', '总后台', '1');
INSERT INTO `auth_rule` VALUES ('48', 'Admin/Admin/index', '0', '管理员', '1', '1', '总后台', '1');
INSERT INTO `auth_rule` VALUES ('49', 'Admin/Admin/add', '48', '管理员添加', '2', '1', '总后台', '1');
INSERT INTO `auth_rule` VALUES ('50', 'Admin/Admin/see', '48', '管理员修改', '2', '1', '总后台', '1');
INSERT INTO `auth_rule` VALUES ('51', 'Admin/Admin/delete', '48', '管理员删除', '2', '1', '总后台', '1');
INSERT INTO `auth_rule` VALUES ('52', 'Admin/Admin/status', '48', '管理员冻结', '2', '1', '总后台', '1');
INSERT INTO `auth_rule` VALUES ('56', 'Merchant/Product/index', '0', '商品管理', '1', '1', '商家', '2');

-- ----------------------------
-- Table structure for `bbs`
-- ----------------------------
DROP TABLE IF EXISTS `bbs`;
CREATE TABLE `bbs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `type` int(11) unsigned NOT NULL COMMENT '1活动2需求3技能',
  `title` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '标题',
  `address` varchar(255) CHARACTER SET utf8 NOT NULL,
  `begin_time` varchar(80) CHARACTER SET utf8 NOT NULL COMMENT '开始时间',
  `end_time` varchar(80) CHARACTER SET utf8 NOT NULL COMMENT '结束时间',
  `price` decimal(10,2) unsigned NOT NULL COMMENT '价钱每人',
  `people_num` int(11) unsigned NOT NULL COMMENT '人数',
  `content` text CHARACTER SET utf8 NOT NULL COMMENT '内容',
  `img` text CHARACTER SET utf8 NOT NULL COMMENT '图片',
  `thumb` text CHARACTER SET utf8,
  `time` varchar(60) CHARACTER SET utf8 NOT NULL,
  `status` tinyint(3) unsigned NOT NULL COMMENT '状态0冻结1正常',
  `praise` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '获赞数量',
  `praise_user` text NOT NULL COMMENT '点赞的用户',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of bbs
-- ----------------------------
INSERT INTO `bbs` VALUES ('1', '1', '1', '这是一段活动标题', '火炬路', '2016-04-05', '2016-04-07', '80.00', '5', '派送传单', './Public/upload/20160407/57061f5aee0fe.jpg*./Public/upload/20160407/57061f5aee8ce.jpg*./Public/upload/20160407/57061f5af0426.png*./Public/upload/20160407/57061f5af0bf6.jpg*./Public/upload/20160407/57061f5af13c6.jpg*', './Public/upload/20160407/thumb_57061f5aee0fe.jpg*./Public/upload/20160407/thumb_57061f5aee8ce.jpg*./Public/upload/20160407/thumb_57061f5af0426.png*./Public/upload/20160407/thumb_57061f5af0bf6.jpg*./Public/upload/20160407/thumb_57061f5af13c6.jpg*', '2016-04-05 15:20:22', '1', '1', '1,');
INSERT INTO `bbs` VALUES ('6', '1', '1', '标题', '西安火炬路', '2016-04-16', '2016-04-16', '80.00', '5', '这是一段内容', './Public/upload/20160416/5711da4437398.jpg*./Public/upload/20160416/5711da4437780.jpg*', './Public/upload/20160416/thumb_5711da4437398.jpg*./Public/upload/20160416/thumb_5711da4437780.jpg*', '2016-04-16 14:23:00', '1', '0', '');
INSERT INTO `bbs` VALUES ('7', '1', '1', '标题', '西安火炬路', '2016-04-16', '2016-04-16', '80.00', '5', '这是一段内容', './Public/upload/20160416/5711da8426de0.jpg*./Public/upload/20160416/5711da84275b0.jpg*', './Public/upload/20160416/thumb_5711da8426de0.jpg*./Public/upload/20160416/thumb_5711da84275b0.jpg*', '2016-04-16 14:24:04', '1', '0', '');
INSERT INTO `bbs` VALUES ('8', '1', '2', '标题', '西安火炬路', '2016-04-16', '2016-04-16', '80.00', '0', '这是一段内容', './Public/upload/20160416/5711debedb0b0.jpg*./Public/upload/20160416/5711debedb880.jpg*', './Public/upload/20160416/thumb_5711debedb0b0.jpg*./Public/upload/20160416/thumb_5711debedb880.jpg*', '2016-04-16 14:42:06', '1', '0', '');
INSERT INTO `bbs` VALUES ('9', '1', '3', '标题', '西安火炬路', '2016-04-16', '2016-04-16', '80.00', '0', '这是一段内容', './Public/upload/20160416/5711e0547b188.jpg*./Public/upload/20160416/5711e0547b958.jpg*', './Public/upload/20160416/thumb_5711e0547b188.jpg*./Public/upload/20160416/thumb_5711e0547b958.jpg*', '2016-04-16 14:48:52', '1', '1', '6,');
INSERT INTO `bbs` VALUES ('10', '1', '1', '标题', '西安火炬路', '2016-04-16', '2016-04-16', '80.00', '5', '这是一段内容', '', '', '2016-04-19 14:51:18', '1', '0', '');
INSERT INTO `bbs` VALUES ('11', '1', '1', '标题', '西安火炬路', '2016-04-16', '2016-04-16', '80.00', '5', '这是一段内容', '', '', '2016-04-19 15:00:30', '1', '0', '');
INSERT INTO `bbs` VALUES ('12', '1', '2', '标题', '西安火炬路', '2016-04-16', '2016-04-16', '80.00', '0', '这是一段内容', '', '', '2016-04-19 15:00:39', '1', '0', '');
INSERT INTO `bbs` VALUES ('13', '1', '2', '标题', '西安火炬路', '2016-04-16', '2016-04-16', '80.00', '0', '这是一段内容', '', '', '2016-04-19 15:01:17', '1', '0', '');
INSERT INTO `bbs` VALUES ('14', '1', '1', '标题', '西安火炬路', '2016-04-16', '2016-04-16', '80.00', '5', '这是一段内容', '', '', '2016-04-19 15:01:33', '1', '0', '');
INSERT INTO `bbs` VALUES ('15', '1', '3', '野营', '西安火炬路', '2016-04-16', '2016-04-16', '80.00', '5', '这是一段内容', '', '', '2016-04-19 15:01:40', '1', '0', '');
INSERT INTO `bbs` VALUES ('16', '1', '1', '标题1', '西安火炬路', '2016-04-16', '2016-04-16', '80.00', '5', '这是一段内容', '', '', '2016-04-19 15:02:10', '1', '0', '');
INSERT INTO `bbs` VALUES ('17', '1', '1', '标题', '西安火炬路', '2016-04-16', '2016-04-16', '80.00', '5', '这是一段内容', '', '', '2016-04-20 09:39:20', '1', '0', '');
INSERT INTO `bbs` VALUES ('18', '1', '1', '标题', '西安火炬路', '2016-04-16', '2016-04-16', '80.00', '5', '这是一段内容', '', '', '2016-04-20 09:43:36', '1', '0', '');
INSERT INTO `bbs` VALUES ('19', '11', '1', 'good', 'view', '2016年04月21日', '2016年04月22日', '300.00', '80', '300', '', '', '2016-04-20 10:10:22', '1', '0', '');
INSERT INTO `bbs` VALUES ('20', '11', '1', 'good', 'view', '2016年04月21日', '2016年04月22日', '300.00', '80', '300', '', '', '2016-04-20 10:10:34', '1', '0', '');
INSERT INTO `bbs` VALUES ('21', '11', '1', 'he', 'in', '2016年04月24日', '2016年04月28日', '0.00', '0', 'he has', '', '', '2016-04-24 14:39:48', '1', '0', '');
INSERT INTO `bbs` VALUES ('22', '11', '1', 'by', 'boys', '2016年04月24日', '2016年04月25日', '89000.00', '1999', '89000', '', '', '2016-04-24 14:41:45', '1', '0', '');
INSERT INTO `bbs` VALUES ('23', '6', '1', '测试', '西安火炬路东新世纪广场', '2016-04-28', '2016-04-29', '80.00', '5', '这只第一次测试', './Public/upload/20160428/57216a8c77bfc.jpeg*', './Public/upload/20160428/thumb_57216a8c77bfc.jpeg*', '2016-04-28 09:42:36', '1', '0', '');
INSERT INTO `bbs` VALUES ('24', '6', '1', '测试2⃣️', '西安火炬路东新世纪广场', '2016-04-28', '2016-04-29', '80.00', '5', '这只第二次测试', './Public/upload/20160428/57219dc7ef249.jpeg*', './Public/upload/20160428/thumb_57219dc7ef249.jpeg*', '2016-04-28 13:21:12', '1', '0', '');
INSERT INTO `bbs` VALUES ('25', '6', '1', '图片展示', '西安火炬路', '2016-04-16', '2016-04-16', '80.00', '5', '这是张图片', './Public/upload/20160428/5721afa460478.jpg*./Public/upload/20160428/5721afa464168.png*', './Public/upload/20160428/thumb_5721afa460478.jpg*./Public/upload/20160428/thumb_5721afa464168.png*', '2016-04-28 14:37:24', '1', '0', '');
INSERT INTO `bbs` VALUES ('26', '6', '1', '图片展示', '西安火炬路', '2016-04-16', '2016-04-16', '80.00', '5', '这是张图片', './Public/upload/20160428/5721afc15c94a.jpg*./Public/upload/20160428/5721afc16063a.png*./Public/upload/20160428/5721afc16063a.jpg*', './Public/upload/20160428/thumb_5721afc15c94a.jpg*./Public/upload/20160428/thumb_5721afc16063a.png*./Public/upload/20160428/thumb_5721afc16063a.jpg*', '2016-04-28 14:37:53', '1', '0', '');
INSERT INTO `bbs` VALUES ('27', '6', '1', '图片展示', '西安火炬路', '2016-04-16', '2016-04-16', '80.00', '5', '这是张图片', './Public/upload/20160428/5721afd436b2c.jpg*./Public/upload/20160428/5721afd43a81c.png*./Public/upload/20160428/5721afd43a81c.jpg*./Public/upload/20160428/5721afd43a81c.jpeg*', './Public/upload/20160428/thumb_5721afd436b2c.jpg*./Public/upload/20160428/thumb_5721afd43a81c.png*./Public/upload/20160428/thumb_5721afd43a81c.jpg*./Public/upload/20160428/thumb_5721afd43a81c.jpeg*', '2016-04-28 14:38:12', '1', '0', '');
INSERT INTO `bbs` VALUES ('31', '1', '1', '标题', '西安火炬路', '2016-04-16', '2016-04-16', '80.00', '5', '这是一段内容', './Public/upload/20160428/5721b79489e41.jpg*./Public/upload/20160428/5721b7948a611.jpg*./Public/upload/20160428/5721b7948ade1.jpg*./Public/upload/20160428/5721b7948b1c9.jpg*./Public/upload/20160428/5721b7948b999.jpg*', './Public/upload/20160428/thumb_5721b79489e41.jpg*./Public/upload/20160428/thumb_5721b7948a611.jpg*./Public/upload/20160428/thumb_5721b7948ade1.jpg*./Public/upload/20160428/thumb_5721b7948b1c9.jpg*./Public/upload/20160428/thumb_5721b7948b999.jpg*', '2016-04-28 15:11:16', '1', '0', '');
INSERT INTO `bbs` VALUES ('32', '1', '1', '标题', '西安火炬路', '2016-04-16', '2016-04-16', '80.00', '5', '这是一段内容', '', '', '2016-04-28 15:17:23', '1', '0', '');
INSERT INTO `bbs` VALUES ('33', '1', '1', '标题', '西安火炬路', '2016-04-16', '2016-04-16', '80.00', '5', '这是一段内容', './Public/upload/20160428/5721b9598f895.jpg*./Public/upload/20160428/5721b95993585.jpeg*./Public/upload/20160428/5721b95993585.jpg*', './Public/upload/20160428/thumb_5721b9598f895.jpg*./Public/upload/20160428/thumb_5721b95993585.jpeg*./Public/upload/20160428/thumb_5721b95993585.jpg*', '2016-04-28 15:18:49', '1', '1', '6,');
INSERT INTO `bbs` VALUES ('34', '11', '1', 'hello', 'damai', '2016年05月01日', '2016年05月03日', '6.00', '10', '6', '', '', '2016-05-01 18:32:36', '1', '0', '');
INSERT INTO `bbs` VALUES ('35', '6', '1', '标题', '西安火炬路', '2016-04-16', '2016-04-16', '80.00', '5', '这是一段内容', '', '', '2016-05-02 12:01:00', '1', '0', '');
INSERT INTO `bbs` VALUES ('36', '6', '1', '标题:', '碑林', '2016-05-02', '2016-05-03', '10.00', '8', '上帝保佑', '', '', '2016-05-02 12:04:48', '1', '0', '');
INSERT INTO `bbs` VALUES ('37', '6', '1', '标题:', '碑林', '2016-05-02', '2016-05-03', '10.00', '8', '上帝保佑', '', '', '2016-05-02 12:04:59', '1', '0', '');
INSERT INTO `bbs` VALUES ('38', '6', '1', '标题:', '碑林', '2016-05-02', '2016-05-03', '10.00', '8', '上帝保佑', '', '', '2016-05-02 12:05:00', '1', '0', '');
INSERT INTO `bbs` VALUES ('39', '6', '1', '标题:', '碑林', '2016-05-02', '2016-05-03', '10.00', '8', '上帝保佑', '', '', '2016-05-02 12:05:01', '1', '0', '');
INSERT INTO `bbs` VALUES ('40', '6', '1', '标题:', '碑林', '2016-05-02', '2016-05-03', '10.00', '8', '上帝保佑', '', '', '2016-05-02 12:05:01', '1', '0', '');
INSERT INTO `bbs` VALUES ('41', '6', '1', '标题:', '碑林', '2016-05-02', '2016-05-03', '10.00', '8', '上帝保佑', '', '', '2016-05-02 12:05:01', '1', '0', '');
INSERT INTO `bbs` VALUES ('42', '6', '2', '标题:', '省体育场', '2016-05-03', '2016-05-04', '10000.00', '0', '机不可失 时不再来', '', '', '2016-05-02 12:09:59', '1', '0', '');
INSERT INTO `bbs` VALUES ('43', '6', '3', '技能名称:', '长安3号', '2016-05-01', '2017-05-01', '100.00', '0', '索尼、爱立信、松下、三星---', '', '', '2016-05-02 12:11:43', '1', '0', '');
INSERT INTO `bbs` VALUES ('44', '6', '1', '标题:', '瀑布', '2016-05-02', '2016-05-03', '1000.00', '10000', '丹江口市', '', '', '2016-05-02 13:46:05', '1', '0', '');
INSERT INTO `bbs` VALUES ('45', '6', '1', '我是有图片描述的哦', '钟楼', '2016-05-02', '2016-06-02', '9.00', '10', '睡觉看书看看', './Public/upload/20160502/5726fa27dbf8c.png*./Public/upload/20160502/5726fa27dfc7c.png*', './Public/upload/20160502/thumb_5726fa27dbf8c.png*./Public/upload/20160502/thumb_5726fa27dfc7c.png*', '2016-05-02 14:56:40', '1', '0', '');
INSERT INTO `bbs` VALUES ('46', '6', '1', '电话', '承诺', '2017-05-02', '2020-05-02', '10000.00', '1', '记得记得记得', './Public/upload/20160502/5726fb4252e2c.png*', './Public/upload/20160502/thumb_5726fb4252e2c.png*', '2016-05-02 15:01:22', '1', '0', '');
INSERT INTO `bbs` VALUES ('47', '1', '1', '标题', '西安火炬路', '2016-04-16', '2016-04-16', '80.00', '5', '这是一段内容', './Public/upload/20160502/5726fe383082c.jpg*./Public/upload/20160502/5726fe3830ffc.jpg*./Public/upload/20160502/5726fe38317cc.jpg*./Public/upload/20160502/5726fe3831bb4.jpg*./Public/upload/20160502/5726fe3832384.jpg*', './Public/upload/20160502/thumb_5726fe383082c.jpg*./Public/upload/20160502/thumb_5726fe3830ffc.jpg*./Public/upload/20160502/thumb_5726fe38317cc.jpg*./Public/upload/20160502/thumb_5726fe3831bb4.jpg*./Public/upload/20160502/thumb_5726fe3832384.jpg*', '2016-05-02 15:14:00', '1', '0', '');
INSERT INTO `bbs` VALUES ('48', '1', '1', '标题', '西安火炬路', '2016-04-16', '2016-04-16', '80.00', '5', '这是一段内容', './Public/upload/20160502/5726fe53a7684.jpg*./Public/upload/20160502/5726fe53a7e54.jpg*./Public/upload/20160502/5726fe53a8624.jpg*./Public/upload/20160502/5726fe53a8a0c.jpg*./Public/upload/20160502/5726fe53a95c4.jpg*', './Public/upload/20160502/thumb_5726fe53a7684.jpg*./Public/upload/20160502/thumb_5726fe53a7e54.jpg*./Public/upload/20160502/thumb_5726fe53a8624.jpg*./Public/upload/20160502/thumb_5726fe53a8a0c.jpg*./Public/upload/20160502/thumb_5726fe53a95c4.jpg*', '2016-05-02 15:14:27', '1', '0', '');
INSERT INTO `bbs` VALUES ('49', '1', '1', '标题', '西安火炬路', '2016-04-16', '2016-04-16', '80.00', '5', '这是一段内容', './Public/upload/20160502/5726fe657f1fc.jpg*./Public/upload/20160502/5726fe657f9cc.jpg*./Public/upload/20160502/5726fe658019c.jpg*./Public/upload/20160502/5726fe658096c.jpg*./Public/upload/20160502/5726fe658113c.jpg*', './Public/upload/20160502/thumb_5726fe657f1fc.jpg*./Public/upload/20160502/thumb_5726fe657f9cc.jpg*./Public/upload/20160502/thumb_5726fe658019c.jpg*./Public/upload/20160502/thumb_5726fe658096c.jpg*./Public/upload/20160502/thumb_5726fe658113c.jpg*', '2016-05-02 15:14:45', '1', '1', '6,');
INSERT INTO `bbs` VALUES ('50', '6', '1', '我', '经典款', '2016-05-02', '2016-05-03', '88.00', '3', '我们都是', './Public/upload/20160502/57270371abdec.png*', './Public/upload/20160502/thumb_57270371abdec.png*', '2016-05-02 15:36:17', '1', '1', '6,');
INSERT INTO `bbs` VALUES ('51', '6', '2', '现在招聘一临时工', '尽快发', '2016-05-02', '2016-06-02', '1000.00', '0', '香蕉', './Public/upload/20160502/572703d75dfbf.png*./Public/upload/20160502/572703d761caf.png*', './Public/upload/20160502/thumb_572703d75dfbf.png*./Public/upload/20160502/thumb_572703d761caf.png*', '2016-05-02 15:37:59', '1', '0', '');
INSERT INTO `bbs` VALUES ('52', '6', '2', '测试', '带回家', '2016-05-02', '2016-05-03', '44.00', '0', '软件', './Public/upload/20160502/572706249d6ba.png*', './Public/upload/20160502/thumb_572706249d6ba.png*', '2016-05-02 15:47:48', '1', '0', '');
INSERT INTO `bbs` VALUES ('53', '6', '2', '多图测试', '粉丝阿凡达撒风', '2016-05-02', '2016-05-04', '324.00', '0', '东方大厦发生', './Public/upload/20160502/57270a38053b3.png*', './Public/upload/20160502/thumb_57270a38053b3.png*', '2016-05-02 16:05:12', '1', '1', '6,');
INSERT INTO `bbs` VALUES ('54', '1', '1', '标题', '西安火炬路', '2016-04-16', '2016-04-16', '80.00', '5', '这是一段内容', './Public/upload/20160502/57270d62b41a4.jpg*./Public/upload/20160502/57270d62b458c.jpg*./Public/upload/20160502/57270d62b4d5c.jpg*./Public/upload/20160502/57270d62b5144.jpg*./Public/upload/20160502/57270d62b5914.jpg*', './Public/upload/20160502/thumb_57270d62b41a4.jpg*./Public/upload/20160502/thumb_57270d62b458c.jpg*./Public/upload/20160502/thumb_57270d62b4d5c.jpg*./Public/upload/20160502/thumb_57270d62b5144.jpg*./Public/upload/20160502/thumb_57270d62b5914.jpg*', '2016-05-02 16:18:42', '1', '0', '');
INSERT INTO `bbs` VALUES ('55', '1', '1', '标题', '西安火炬路', '2016-04-16', '2016-04-16', '80.00', '5', '这是一段内容', './Public/upload/20160502/57270d62ef2f4.jpg*./Public/upload/20160502/57270d62ef6dc.jpg*./Public/upload/20160502/57270d62efeac.jpg*./Public/upload/20160502/57270d62f067c.jpg*./Public/upload/20160502/57270d62f0e4c.jpg*', './Public/upload/20160502/thumb_57270d62ef2f4.jpg*./Public/upload/20160502/thumb_57270d62ef6dc.jpg*./Public/upload/20160502/thumb_57270d62efeac.jpg*./Public/upload/20160502/thumb_57270d62f067c.jpg*./Public/upload/20160502/thumb_57270d62f0e4c.jpg*', '2016-05-02 16:18:43', '1', '0', '');
INSERT INTO `bbs` VALUES ('56', '1', '1', '标题', '西安火炬路', '2016-04-16', '2016-04-16', '80.00', '5', '这是一段内容', './Public/upload/20160502/57270d6321dcc.jpg*./Public/upload/20160502/57270d63221b4.jpg*./Public/upload/20160502/57270d6322984.jpg*./Public/upload/20160502/57270d6322d6c.jpg*./Public/upload/20160502/57270d632353c.jpg*', './Public/upload/20160502/thumb_57270d6321dcc.jpg*./Public/upload/20160502/thumb_57270d63221b4.jpg*./Public/upload/20160502/thumb_57270d6322984.jpg*./Public/upload/20160502/thumb_57270d6322d6c.jpg*./Public/upload/20160502/thumb_57270d632353c.jpg*', '2016-05-02 16:18:43', '1', '0', '');
INSERT INTO `bbs` VALUES ('57', '1', '1', '标题', '西安火炬路', '2016-04-16', '2016-04-16', '80.00', '5', '这是一段内容', './Public/upload/20160502/57270d63561bc.jpg*./Public/upload/20160502/57270d635698c.jpg*./Public/upload/20160502/57270d635715c.jpg*./Public/upload/20160502/57270d635792c.jpg*./Public/upload/20160502/57270d6357d14.jpg*', './Public/upload/20160502/thumb_57270d63561bc.jpg*./Public/upload/20160502/thumb_57270d635698c.jpg*./Public/upload/20160502/thumb_57270d635715c.jpg*./Public/upload/20160502/thumb_57270d635792c.jpg*./Public/upload/20160502/thumb_57270d6357d14.jpg*', '2016-05-02 16:18:43', '1', '0', '');
INSERT INTO `bbs` VALUES ('58', '1', '1', '标题', '西安火炬路', '2016-04-16', '2016-04-16', '80.00', '5', '这是一段内容', './Public/upload/20160502/57270d637f9cc.jpg*./Public/upload/20160502/57270d637fdb4.jpg*./Public/upload/20160502/57270d638019c.jpg*./Public/upload/20160502/57270d638096c.jpg*./Public/upload/20160502/57270d6380d54.jpg*', './Public/upload/20160502/thumb_57270d637f9cc.jpg*./Public/upload/20160502/thumb_57270d637fdb4.jpg*./Public/upload/20160502/thumb_57270d638019c.jpg*./Public/upload/20160502/thumb_57270d638096c.jpg*./Public/upload/20160502/thumb_57270d6380d54.jpg*', '2016-05-02 16:18:43', '1', '1', '6,');
INSERT INTO `bbs` VALUES ('59', '1', '1', '标题', '西安火炬路', '2016-04-16', '2016-04-16', '80.00', '5', '这是一段内容', './Public/upload/20160502/57270d63acc74.jpg*./Public/upload/20160502/57270d63ad444.jpg*./Public/upload/20160502/57270d63ad82c.jpg*./Public/upload/20160502/57270d63adc14.jpg*./Public/upload/20160502/57270d63adffc.jpg*', './Public/upload/20160502/thumb_57270d63acc74.jpg*./Public/upload/20160502/thumb_57270d63ad444.jpg*./Public/upload/20160502/thumb_57270d63ad82c.jpg*./Public/upload/20160502/thumb_57270d63adc14.jpg*./Public/upload/20160502/thumb_57270d63adffc.jpg*', '2016-05-02 16:18:43', '1', '1', '6,');
INSERT INTO `bbs` VALUES ('60', '1', '1', '多图标题', '西安火炬路', '2016-04-16', '2016-04-16', '80.00', '5', '这是一段内容', './Public/upload/20160502/5727115be910d.jpg*', './Public/upload/20160502/thumb_5727115be910d.jpg*', '2016-05-02 16:35:39', '1', '1', '6,');
INSERT INTO `bbs` VALUES ('61', '1', '1', '标题', '西安火炬路', '2016-04-16', '2016-04-16', '80.00', '5', '这是一段内容', './Public/upload/20160502/57271e2e43f82.png*', './Public/upload/20160502/thumb_57271e2e43f82.png*', '2016-05-02 17:30:22', '1', '0', '');
INSERT INTO `bbs` VALUES ('62', '1', '1', '标题', '西安火炬路', '2016-04-16', '2016-04-16', '80.00', '5', '这是一段内容', './Public/upload/20160502/57271ebbef6dc.png*./Public/upload/20160502/57271ebbefac4.png*./Public/upload/20160502/57271ebbf0294.png*./Public/upload/20160502/57271ebbf067c.png*./Public/upload/20160502/57271ebbf0a64.png*', './Public/upload/20160502/thumb_57271ebbef6dc.png*./Public/upload/20160502/thumb_57271ebbefac4.png*./Public/upload/20160502/thumb_57271ebbf0294.png*./Public/upload/20160502/thumb_57271ebbf067c.png*./Public/upload/20160502/thumb_57271ebbf0a64.png*', '2016-05-02 17:32:44', '1', '0', '');
INSERT INTO `bbs` VALUES ('63', '1', '1', '标题', '西安火炬路', '2016-04-16', '2016-04-16', '80.00', '5', '这是一段内容', '', '', '2016-05-02 17:33:42', '1', '0', '');
INSERT INTO `bbs` VALUES ('64', '1', '1', '标题', '西安火炬路', '2016-04-16', '2016-04-16', '80.00', '5', '这是一段内容', './Public/upload/20160502/57271f04bddc6.jpg*./Public/upload/20160502/57271f04bed66.jpg*./Public/upload/20160502/57271f04bf91e.jpg*', './Public/upload/20160502/thumb_57271f04bddc6.jpg*./Public/upload/20160502/thumb_57271f04bed66.jpg*./Public/upload/20160502/thumb_57271f04bf91e.jpg*', '2016-05-02 17:33:56', '1', '0', '');
INSERT INTO `bbs` VALUES ('65', '1', '1', '标题', '西安火炬路', '2016-04-16', '2016-04-16', '80.00', '5', '这是一段内容', './Public/upload/20160502/57271f0f26e20.jpg*./Public/upload/20160502/57271f0f279d8.jpg*./Public/upload/20160502/57271f0f28590.jpg*./Public/upload/20160502/57271f0f29148.jpg*./Public/upload/20160502/57271f0f29d01.jpg*', './Public/upload/20160502/thumb_57271f0f26e20.jpg*./Public/upload/20160502/thumb_57271f0f279d8.jpg*./Public/upload/20160502/thumb_57271f0f28590.jpg*./Public/upload/20160502/thumb_57271f0f29148.jpg*./Public/upload/20160502/thumb_57271f0f29d01.jpg*', '2016-05-02 17:34:07', '1', '0', '');
INSERT INTO `bbs` VALUES ('66', '6', '1', '多图测试', '亚洲', '2016-05-02', '2016-05-03', '1.00', '10', '一个好', './Public/upload/20160502/572720b355c41.png*./Public/upload/20160502/572720b3592f2.png*./Public/upload/20160502/572720b35eccc.png*./Public/upload/20160502/572720b360ff4.png*./Public/upload/20160502/572720b362764.png*', './Public/upload/20160502/thumb_572720b355c41.png*./Public/upload/20160502/thumb_572720b3592f2.png*./Public/upload/20160502/thumb_572720b35eccc.png*./Public/upload/20160502/thumb_572720b360ff4.png*./Public/upload/20160502/thumb_572720b362764.png*', '2016-05-02 17:41:07', '1', '0', '');
INSERT INTO `bbs` VALUES ('67', '6', '2', '', '八廓街j', '2017-05-02', '2018-05-02', '188.00', '0', '在还不快', './Public/upload/20160502/5727216dd4a36.png*./Public/upload/20160502/5727216dda7f7.png*./Public/upload/20160502/5727216dddac0.png*./Public/upload/20160502/5727216de01d0.png*./Public/upload/20160502/5727216de24f9.png*', './Public/upload/20160502/thumb_5727216dd4a36.png*./Public/upload/20160502/thumb_5727216dda7f7.png*./Public/upload/20160502/thumb_5727216dddac0.png*./Public/upload/20160502/thumb_5727216de01d0.png*./Public/upload/20160502/thumb_5727216de24f9.png*', '2016-05-02 17:44:14', '1', '0', '');
INSERT INTO `bbs` VALUES ('68', '6', '2', '美女', '火车站', '2016-05-02', '2016-05-03', '999.00', '0', '汤圆', '', '', '2016-05-02 17:56:01', '1', '0', '');
INSERT INTO `bbs` VALUES ('69', '6', '2', '美女', '火车站', '2016-05-02', '2016-05-03', '999.00', '0', '汤圆', './Public/upload/20160502/572724435eb06.png*', './Public/upload/20160502/thumb_572724435eb06.png*', '2016-05-02 17:56:19', '1', '0', '');
INSERT INTO `bbs` VALUES ('70', '6', '2', '美女', '火车站', '2016-05-02', '2016-05-03', '999.00', '0', '汤圆', './Public/upload/20160502/57272456aedf8.png*', './Public/upload/20160502/thumb_57272456aedf8.png*', '2016-05-02 17:56:38', '1', '0', '');
INSERT INTO `bbs` VALUES ('71', '6', '3', '大家看的', '你自己想想', '2016-05-02', '2016-05-03', '8.00', '0', '大家看的开的', '', '', '2016-05-02 18:01:16', '1', '0', '');
INSERT INTO `bbs` VALUES ('72', '6', '3', '飞机', '附近', '2016-05-02', '2016-05-03', '1000.00', '0', '得好好计划', './Public/upload/20160502/5727283add17b.png*./Public/upload/20160502/5727283ae0e6b.png*', './Public/upload/20160502/thumb_5727283add17b.png*./Public/upload/20160502/thumb_5727283ae0e6b.png*', '2016-05-02 18:13:15', '1', '1', '6,');
INSERT INTO `bbs` VALUES ('73', '6', '1', '成绩单', '基督教', '2016-05-02', '2016-12-02', '864.00', '564', '蝴蝶结', './Public/upload/20160502/5727286d4b823.png*./Public/upload/20160502/5727286d4f513.png*./Public/upload/20160502/5727286d53203.png*', './Public/upload/20160502/thumb_5727286d4b823.png*./Public/upload/20160502/thumb_5727286d4f513.png*./Public/upload/20160502/thumb_5727286d53203.png*', '2016-05-02 18:14:05', '1', '0', '');
INSERT INTO `bbs` VALUES ('74', '6', '2', '心心相印', '楠溪江', '2016-05-03', '2016-05-03', '999.00', '0', '等级考试', './Public/upload/20160503/5727fd671e556.png*./Public/upload/20160503/5727fd6722246.png*./Public/upload/20160503/5727fd6725f36.png*', './Public/upload/20160503/thumb_5727fd671e556.png*./Public/upload/20160503/thumb_5727fd6722246.png*./Public/upload/20160503/thumb_5727fd6725f36.png*', '2016-05-03 09:22:47', '1', '0', '');
INSERT INTO `bbs` VALUES ('75', '6', '3', '修手机', '哪都去', '2016-05-03', '2017-05-03', '1000.00', '0', '实惠', './Public/upload/20160503/5728021ad8018.png*', './Public/upload/20160503/thumb_5728021ad8018.png*', '2016-05-03 09:42:50', '1', '0', '');
INSERT INTO `bbs` VALUES ('76', '1', '1', '标题', '西安火炬路', '2016-04-16', '2016-04-16', '80.00', '5', '这是一段内容', '', '', '2016-05-03 10:11:38', '1', '0', '');
INSERT INTO `bbs` VALUES ('77', '1', '1', '标题', '西安火炬路', '2016-04-16', '2016-04-16', '80.00', '5', '这是一段内容', './Public/upload/20160503/57280908151e7.jpg*./Public/upload/20160503/572809081750f.jpeg*./Public/upload/20160503/5728090819837.jpeg*./Public/upload/20160503/572809081abbf.jpg*./Public/upload/20160503/572809081bb5f.jpeg*', './Public/upload/20160503/thumb_57280908151e7.jpg*./Public/upload/20160503/thumb_572809081750f.jpeg*./Public/upload/20160503/thumb_5728090819837.jpeg*./Public/upload/20160503/thumb_572809081abbf.jpg*./Public/upload/20160503/thumb_572809081bb5f.jpeg*', '2016-05-03 10:12:24', '1', '0', '');
INSERT INTO `bbs` VALUES ('78', '1', '1', '标题', '西安火炬路', '2016-04-16', '2016-04-16', '80.00', '5', '这是一段内容', './Public/upload/20160503/57280952d30f7.jpg*./Public/upload/20160503/57280952d4097.jpeg*./Public/upload/20160503/57280952d4c4f.jpeg*./Public/upload/20160503/57280952d5807.jpg*./Public/upload/20160503/57280952d63bf.jpeg*', './Public/upload/20160503/thumb_57280952d30f7.jpg*./Public/upload/20160503/thumb_57280952d4097.jpeg*./Public/upload/20160503/thumb_57280952d4c4f.jpeg*./Public/upload/20160503/thumb_57280952d5807.jpg*./Public/upload/20160503/thumb_57280952d63bf.jpeg*', '2016-05-03 10:13:39', '1', '0', '');
INSERT INTO `bbs` VALUES ('79', '1', '1', '标题', '西安火炬路', '2016-04-16', '2016-04-16', '80.00', '5', '这是一段内容', './Public/upload/20160503/572809b47781f.jpg*./Public/upload/20160503/572809b4787bf.jpeg*./Public/upload/20160503/572809b479377.jpeg*./Public/upload/20160503/572809b479f2f.jpg*./Public/upload/20160503/572809b47aae7.jpeg*', './Public/upload/20160503/thumb_572809b47781f.jpg*./Public/upload/20160503/thumb_572809b4787bf.jpeg*./Public/upload/20160503/thumb_572809b479377.jpeg*./Public/upload/20160503/thumb_572809b479f2f.jpg*./Public/upload/20160503/thumb_572809b47aae7.jpeg*', '2016-05-03 10:15:16', '1', '0', '');
INSERT INTO `bbs` VALUES ('80', '1', '1', '标题', '西安火炬路', '2016-04-16', '2016-04-16', '80.00', '5', '这是一段内容', './Public/upload/20160503/57280a3c53217.jpg*./Public/upload/20160503/57280a3c539e7.jpg*./Public/upload/20160503/57280a3c53dcf.png*./Public/upload/20160503/57280a3c54987.png*./Public/upload/20160503/57280a3c55157.png*', './Public/upload/20160503/thumb_57280a3c53217.jpg*./Public/upload/20160503/thumb_57280a3c539e7.jpg*./Public/upload/20160503/thumb_57280a3c53dcf.png*./Public/upload/20160503/thumb_57280a3c54987.png*./Public/upload/20160503/thumb_57280a3c55157.png*', '2016-05-03 10:17:32', '1', '0', '');
INSERT INTO `bbs` VALUES ('81', '6', '3', '新年', '表姐夫', '2016-05-03', '2017-05-03', '89.00', '0', '你的决定', './Public/upload/20160503/57280ac39e5cd.png*./Public/upload/20160503/57280ac3a22bd.png*', './Public/upload/20160503/thumb_57280ac39e5cd.png*./Public/upload/20160503/thumb_57280ac3a22bd.png*', '2016-05-03 10:19:47', '1', '0', '');
INSERT INTO `bbs` VALUES ('82', '6', '2', '第三次测试', '疾风劲草', '2016-05-03', '2016-06-03', '646.00', '0', '多款可选', './Public/upload/20160503/57280c70cbf1c.png*./Public/upload/20160503/57280c70cfc0c.png*./Public/upload/20160503/57280c70d38fc.png*', './Public/upload/20160503/thumb_57280c70cbf1c.png*./Public/upload/20160503/thumb_57280c70cfc0c.png*./Public/upload/20160503/thumb_57280c70d38fc.png*', '2016-05-03 10:26:57', '1', '0', '');
INSERT INTO `bbs` VALUES ('83', '1', '1', '标题', '西安火炬路', '2016-04-16', '2016-04-16', '80.00', '5', '这是一段内容', './Public/upload/20160503/57280f5a8721f.jpg*./Public/upload/20160503/57280f5a8915f.jpg*./Public/upload/20160503/57280f5a8b86f.jpg*./Public/upload/20160503/57280f5a8d3c7.png*./Public/upload/20160503/57280f5a8df7f.png*', './Public/upload/20160503/thumb_57280f5a8721f.jpg*./Public/upload/20160503/thumb_57280f5a8915f.jpg*./Public/upload/20160503/thumb_57280f5a8b86f.jpg*./Public/upload/20160503/thumb_57280f5a8d3c7.png*./Public/upload/20160503/thumb_57280f5a8df7f.png*', '2016-05-03 10:39:22', '1', '0', '');
INSERT INTO `bbs` VALUES ('84', '1', '1', '标题', '西安火炬路', '2016-04-16', '2016-04-16', '80.00', '5', '这是一段内容', './Public/upload/20160503/572810803b72f.jpg*./Public/upload/20160503/572810803beff.jpg*./Public/upload/20160503/572810803c2e7.jpg*./Public/upload/20160503/572810803cab7.jpg*./Public/upload/20160503/572810803d287.jpg*', './Public/upload/20160503/thumb_572810803b72f.jpg*./Public/upload/20160503/thumb_572810803beff.jpg*./Public/upload/20160503/thumb_572810803c2e7.jpg*./Public/upload/20160503/thumb_572810803cab7.jpg*./Public/upload/20160503/thumb_572810803d287.jpg*', '2016-05-03 10:44:16', '1', '0', '');

-- ----------------------------
-- Table structure for `bbs_comment`
-- ----------------------------
DROP TABLE IF EXISTS `bbs_comment`;
CREATE TABLE `bbs_comment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `bbs_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `time` char(50) NOT NULL,
  `img` text NOT NULL,
  `thumb` text NOT NULL,
  `com_centent` text NOT NULL,
  `status` tinyint(3) unsigned NOT NULL COMMENT '0冻结1正常',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of bbs_comment
-- ----------------------------
INSERT INTO `bbs_comment` VALUES ('2', '1', '1', '2016-04-07 15:16:20', './Public/upload/20160407/570605525d0ae.jpg*./Public/upload/20160407/570605525dc66.jpg*./Public/upload/20160407/570605525e04e.jpg*./Public/upload/20160407/570605525ec06.jpg*./Public/upload/20160407/570605525f3d6.jpg*./Public/upload/20160407/570605525f7be.jpg*', './Public/upload/20160407/thumb_57060661d4abe.jpg*./Public/upload/20160407/thumb_57060661d5a5e.jpg*./Public/upload/20160407/thumb_57060661d6616.jpg*./Public/upload/20160407/thumb_57060661d6de6.jpg*./Public/upload/20160407/thumb_57060661d75b6.jpg*./Public/upload/20160407/thumb_57060661d816e.png*', '这是一段内容', '1');
INSERT INTO `bbs_comment` VALUES ('3', '1', '11', '2016-04-08 15:16:20', './Public/upload/20160407/570605525d0ae.jpg*./Public/upload/20160407/570605525dc66.jpg*./Public/upload/20160407/570605525e04e.jpg*./Public/upload/20160407/570605525ec06.jpg*./Public/upload/20160407/570605525f3d6.jpg*./Public/upload/20160407/570605525f7be.jpg*', './Public/upload/20160407/thumb_57060661d4abe.jpg*./Public/upload/20160407/thumb_57060661d5a5e.jpg*./Public/upload/20160407/thumb_57060661d6616.jpg*./Public/upload/20160407/thumb_57060661d6de6.jpg*./Public/upload/20160407/thumb_57060661d75b6.jpg*./Public/upload/20160407/thumb_57060661d816e.png*', '这是一段内容', '1');
INSERT INTO `bbs_comment` VALUES ('4', '1', '1', '2016-04-16 13:34:41', './Public/upload/20160416/5711cef1d1858.jpg*./Public/upload/20160416/5711cef1d2410.jpg*', './Public/upload/20160416/thumb_5711cef1d1858.jpg*./Public/upload/20160416/thumb_5711cef1d2410.jpg*', '这是一段内容', '1');
INSERT INTO `bbs_comment` VALUES ('5', '1', '1', '2016-04-16 14:35:54', './Public/upload/20160416/5711dd4a24ab8.png*./Public/upload/20160416/5711dd4a25670.jpg*', './Public/upload/20160416/thumb_5711dd4a24ab8.png*./Public/upload/20160416/thumb_5711dd4a25670.jpg*', '这是一段内容', '1');
INSERT INTO `bbs_comment` VALUES ('6', '1', '1', '2016-04-16 14:36:05', './Public/upload/20160416/5711dd5591500.jpg*./Public/upload/20160416/5711dd55918e8.jpg*', './Public/upload/20160416/thumb_5711dd5591500.jpg*./Public/upload/20160416/thumb_5711dd55918e8.jpg*', '这是一段内容', '1');
INSERT INTO `bbs_comment` VALUES ('7', '1', '1', '2016-04-16 14:38:14', './Public/upload/20160416/5711ddd640fd8.jpg*./Public/upload/20160416/5711ddd6413c0.jpg*', './Public/upload/20160416/thumb_5711ddd640fd8.jpg*./Public/upload/20160416/thumb_5711ddd6413c0.jpg*', '这是一段内容', '1');
INSERT INTO `bbs_comment` VALUES ('8', '1', '1', '2016-04-16 14:38:55', './Public/upload/20160416/5711ddff56f68.jpg*./Public/upload/20160416/5711ddff57738.jpg*', './Public/upload/20160416/thumb_5711ddff56f68.jpg*./Public/upload/20160416/thumb_5711ddff57738.jpg*', '这是一段内容', '1');

-- ----------------------------
-- Table structure for `cart`
-- ----------------------------
DROP TABLE IF EXISTS `cart`;
CREATE TABLE `cart` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `product_id` int(11) NOT NULL,
  `number` int(11) NOT NULL,
  `time` char(50) NOT NULL,
  `status` int(11) unsigned NOT NULL DEFAULT '1' COMMENT '1正常0冻结',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=805 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of cart
-- ----------------------------
INSERT INTO `cart` VALUES ('803', '11', '38', '1', '2016-05-03 09:46:17', '1');
INSERT INTO `cart` VALUES ('804', '11', '41', '1', '2016-05-03 09:46:17', '1');

-- ----------------------------
-- Table structure for `category`
-- ----------------------------
DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(30) NOT NULL COMMENT '分类名称',
  `pid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '上级分类的ID，0：代表顶级',
  `sort_num` tinyint(4) NOT NULL DEFAULT '0' COMMENT '栏目排序',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `day` int(11) unsigned NOT NULL COMMENT '退货天数',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=73 DEFAULT CHARSET=utf8 COMMENT='商品分类表';

-- ----------------------------
-- Records of category
-- ----------------------------
INSERT INTO `category` VALUES ('4', '超市', '0', '0', '1', '3');
INSERT INTO `category` VALUES ('5', '食品饮料', '4', '0', '1', '3');
INSERT INTO `category` VALUES ('6', '饮料冲剂', '4', '0', '1', '3');
INSERT INTO `category` VALUES ('7', '营养冲饮', '4', '0', '1', '3');
INSERT INTO `category` VALUES ('8', '上门维护', '0', '0', '1', '3');
INSERT INTO `category` VALUES ('9', '电脑维修', '8', '0', '1', '3');
INSERT INTO `category` VALUES ('10', '电器维修', '8', '0', '1', '3');
INSERT INTO `category` VALUES ('11', '搬家', '8', '0', '1', '3');
INSERT INTO `category` VALUES ('14', '牛奶乳品', '4', '0', '1', '3');
INSERT INTO `category` VALUES ('15', '精油干货', '4', '0', '1', '3');
INSERT INTO `category` VALUES ('16', '酒', '4', '0', '1', '3');
INSERT INTO `category` VALUES ('17', '纸巾洗护', '4', '0', '1', '3');
INSERT INTO `category` VALUES ('19', '代购', '4', '0', '1', '3');
INSERT INTO `category` VALUES ('20', '办公用品', '4', '0', '1', '3');
INSERT INTO `category` VALUES ('23', '代驾', '8', '0', '1', '3');
INSERT INTO `category` VALUES ('24', '送水', '8', '0', '1', '3');
INSERT INTO `category` VALUES ('25', '办宽带', '8', '0', '1', '3');
INSERT INTO `category` VALUES ('26', '装修', '8', '0', '1', '3');
INSERT INTO `category` VALUES ('28', '衣物送洗', '0', '0', '1', '3');
INSERT INTO `category` VALUES ('29', '生活信息', '0', '0', '1', '3');
INSERT INTO `category` VALUES ('32', '方便素食', '5', '0', '1', '1');
INSERT INTO `category` VALUES ('33', '糕点饼干', '5', '0', '1', '2');
INSERT INTO `category` VALUES ('34', '饮用水', '6', '0', '1', '3');
INSERT INTO `category` VALUES ('35', '坚果炒货', '5', '0', '1', '3');
INSERT INTO `category` VALUES ('36', '果冻蜜饯', '5', '0', '1', '3');
INSERT INTO `category` VALUES ('37', '糖果巧克', '5', '0', '1', '3');
INSERT INTO `category` VALUES ('38', '儿童食品', '5', '0', '1', '3');
INSERT INTO `category` VALUES ('39', '茶饮料', '6', '0', '1', '3');
INSERT INTO `category` VALUES ('40', '功能饮料', '6', '0', '1', '3');
INSERT INTO `category` VALUES ('41', '果蔬饮料', '6', '0', '1', '3');
INSERT INTO `category` VALUES ('42', '含乳饮品', '6', '0', '1', '3');
INSERT INTO `category` VALUES ('43', '碳酸饮料', '6', '0', '1', '3');
INSERT INTO `category` VALUES ('44', '咖啡饮料', '6', '0', '1', '3');
INSERT INTO `category` VALUES ('45', '蜂蜜', '7', '0', '1', '3');
INSERT INTO `category` VALUES ('46', '奶茶', '7', '0', '1', '3');
INSERT INTO `category` VALUES ('47', '豆浆粉', '7', '0', '1', '3');
INSERT INTO `category` VALUES ('48', '纯牛奶', '14', '0', '1', '3');
INSERT INTO `category` VALUES ('49', '酸牛奶', '14', '0', '1', '3');
INSERT INTO `category` VALUES ('50', '厨房调料', '15', '0', '1', '3');
INSERT INTO `category` VALUES ('51', '酱菜', '15', '0', '1', '3');
INSERT INTO `category` VALUES ('52', '白酒', '16', '0', '1', '3');
INSERT INTO `category` VALUES ('53', '红酒', '16', '0', '1', '3');
INSERT INTO `category` VALUES ('54', '葡萄酒', '16', '0', '1', '3');
INSERT INTO `category` VALUES ('55', '果酒', '16', '0', '1', '3');
INSERT INTO `category` VALUES ('56', '滋补酒', '16', '0', '1', '3');
INSERT INTO `category` VALUES ('57', '口腔护理', '17', '0', '1', '3');
INSERT INTO `category` VALUES ('58', '面部护理', '17', '0', '1', '3');
INSERT INTO `category` VALUES ('59', '洗护沐浴', '17', '0', '1', '3');
INSERT INTO `category` VALUES ('60', '女性护理', '17', '0', '1', '3');
INSERT INTO `category` VALUES ('61', '纸巾湿巾', '17', '0', '1', '3');
INSERT INTO `category` VALUES ('62', '香烟', '19', '0', '1', '3');
INSERT INTO `category` VALUES ('63', '水果生鲜', '19', '0', '1', '3');
INSERT INTO `category` VALUES ('64', '早餐外卖', '19', '0', '1', '3');
INSERT INTO `category` VALUES ('65', '休闲娱乐', '4', '0', '1', '3');
INSERT INTO `category` VALUES ('66', '棋牌桌游', '65', '0', '1', '3');
INSERT INTO `category` VALUES ('67', '办公文具', '20', '0', '1', '3');
INSERT INTO `category` VALUES ('68', '进口商品', '4', '0', '1', '3');
INSERT INTO `category` VALUES ('69', '进口零食', '68', '0', '1', '3');
INSERT INTO `category` VALUES ('70', '生鲜熟食', '4', '0', '1', '3');
INSERT INTO `category` VALUES ('71', '水果', '8', '0', '1', '3');
INSERT INTO `category` VALUES ('72', '水果', '70', '0', '1', '3');

-- ----------------------------
-- Table structure for `city`
-- ----------------------------
DROP TABLE IF EXISTS `city`;
CREATE TABLE `city` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `this_id` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `pid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=346 DEFAULT CHARSET=gbk;

-- ----------------------------
-- Records of city
-- ----------------------------
INSERT INTO `city` VALUES ('1', '110100', '市辖区', '110000');
INSERT INTO `city` VALUES ('2', '110200', '县', '110000');
INSERT INTO `city` VALUES ('3', '120100', '市辖区', '120000');
INSERT INTO `city` VALUES ('4', '120200', '县', '120000');
INSERT INTO `city` VALUES ('5', '130100', '石家庄市', '130000');
INSERT INTO `city` VALUES ('6', '130200', '唐山市', '130000');
INSERT INTO `city` VALUES ('7', '130300', '秦皇岛市', '130000');
INSERT INTO `city` VALUES ('8', '130400', '邯郸市', '130000');
INSERT INTO `city` VALUES ('9', '130500', '邢台市', '130000');
INSERT INTO `city` VALUES ('10', '130600', '保定市', '130000');
INSERT INTO `city` VALUES ('11', '130700', '张家口市', '130000');
INSERT INTO `city` VALUES ('12', '130800', '承德市', '130000');
INSERT INTO `city` VALUES ('13', '130900', '沧州市', '130000');
INSERT INTO `city` VALUES ('14', '131000', '廊坊市', '130000');
INSERT INTO `city` VALUES ('15', '131100', '衡水市', '130000');
INSERT INTO `city` VALUES ('16', '140100', '太原市', '140000');
INSERT INTO `city` VALUES ('17', '140200', '大同市', '140000');
INSERT INTO `city` VALUES ('18', '140300', '阳泉市', '140000');
INSERT INTO `city` VALUES ('19', '140400', '长治市', '140000');
INSERT INTO `city` VALUES ('20', '140500', '晋城市', '140000');
INSERT INTO `city` VALUES ('21', '140600', '朔州市', '140000');
INSERT INTO `city` VALUES ('22', '140700', '晋中市', '140000');
INSERT INTO `city` VALUES ('23', '140800', '运城市', '140000');
INSERT INTO `city` VALUES ('24', '140900', '忻州市', '140000');
INSERT INTO `city` VALUES ('25', '141000', '临汾市', '140000');
INSERT INTO `city` VALUES ('26', '141100', '吕梁市', '140000');
INSERT INTO `city` VALUES ('27', '150100', '呼和浩特市', '150000');
INSERT INTO `city` VALUES ('28', '150200', '包头市', '150000');
INSERT INTO `city` VALUES ('29', '150300', '乌海市', '150000');
INSERT INTO `city` VALUES ('30', '150400', '赤峰市', '150000');
INSERT INTO `city` VALUES ('31', '150500', '通辽市', '150000');
INSERT INTO `city` VALUES ('32', '150600', '鄂尔多斯市', '150000');
INSERT INTO `city` VALUES ('33', '150700', '呼伦贝尔市', '150000');
INSERT INTO `city` VALUES ('34', '150800', '巴彦淖尔市', '150000');
INSERT INTO `city` VALUES ('35', '150900', '乌兰察布市', '150000');
INSERT INTO `city` VALUES ('36', '152200', '兴安盟', '150000');
INSERT INTO `city` VALUES ('37', '152500', '锡林郭勒盟', '150000');
INSERT INTO `city` VALUES ('38', '152900', '阿拉善盟', '150000');
INSERT INTO `city` VALUES ('39', '210100', '沈阳市', '210000');
INSERT INTO `city` VALUES ('40', '210200', '大连市', '210000');
INSERT INTO `city` VALUES ('41', '210300', '鞍山市', '210000');
INSERT INTO `city` VALUES ('42', '210400', '抚顺市', '210000');
INSERT INTO `city` VALUES ('43', '210500', '本溪市', '210000');
INSERT INTO `city` VALUES ('44', '210600', '丹东市', '210000');
INSERT INTO `city` VALUES ('45', '210700', '锦州市', '210000');
INSERT INTO `city` VALUES ('46', '210800', '营口市', '210000');
INSERT INTO `city` VALUES ('47', '210900', '阜新市', '210000');
INSERT INTO `city` VALUES ('48', '211000', '辽阳市', '210000');
INSERT INTO `city` VALUES ('49', '211100', '盘锦市', '210000');
INSERT INTO `city` VALUES ('50', '211200', '铁岭市', '210000');
INSERT INTO `city` VALUES ('51', '211300', '朝阳市', '210000');
INSERT INTO `city` VALUES ('52', '211400', '葫芦岛市', '210000');
INSERT INTO `city` VALUES ('53', '220100', '长春市', '220000');
INSERT INTO `city` VALUES ('54', '220200', '吉林市', '220000');
INSERT INTO `city` VALUES ('55', '220300', '四平市', '220000');
INSERT INTO `city` VALUES ('56', '220400', '辽源市', '220000');
INSERT INTO `city` VALUES ('57', '220500', '通化市', '220000');
INSERT INTO `city` VALUES ('58', '220600', '白山市', '220000');
INSERT INTO `city` VALUES ('59', '220700', '松原市', '220000');
INSERT INTO `city` VALUES ('60', '220800', '白城市', '220000');
INSERT INTO `city` VALUES ('61', '222400', '延边朝鲜族自治州', '220000');
INSERT INTO `city` VALUES ('62', '230100', '哈尔滨市', '230000');
INSERT INTO `city` VALUES ('63', '230200', '齐齐哈尔市', '230000');
INSERT INTO `city` VALUES ('64', '230300', '鸡西市', '230000');
INSERT INTO `city` VALUES ('65', '230400', '鹤岗市', '230000');
INSERT INTO `city` VALUES ('66', '230500', '双鸭山市', '230000');
INSERT INTO `city` VALUES ('67', '230600', '大庆市', '230000');
INSERT INTO `city` VALUES ('68', '230700', '伊春市', '230000');
INSERT INTO `city` VALUES ('69', '230800', '佳木斯市', '230000');
INSERT INTO `city` VALUES ('70', '230900', '七台河市', '230000');
INSERT INTO `city` VALUES ('71', '231000', '牡丹江市', '230000');
INSERT INTO `city` VALUES ('72', '231100', '黑河市', '230000');
INSERT INTO `city` VALUES ('73', '231200', '绥化市', '230000');
INSERT INTO `city` VALUES ('74', '232700', '大兴安岭地区', '230000');
INSERT INTO `city` VALUES ('75', '310100', '市辖区', '310000');
INSERT INTO `city` VALUES ('76', '310200', '县', '310000');
INSERT INTO `city` VALUES ('77', '320100', '南京市', '320000');
INSERT INTO `city` VALUES ('78', '320200', '无锡市', '320000');
INSERT INTO `city` VALUES ('79', '320300', '徐州市', '320000');
INSERT INTO `city` VALUES ('80', '320400', '常州市', '320000');
INSERT INTO `city` VALUES ('81', '320500', '苏州市', '320000');
INSERT INTO `city` VALUES ('82', '320600', '南通市', '320000');
INSERT INTO `city` VALUES ('83', '320700', '连云港市', '320000');
INSERT INTO `city` VALUES ('84', '320800', '淮安市', '320000');
INSERT INTO `city` VALUES ('85', '320900', '盐城市', '320000');
INSERT INTO `city` VALUES ('86', '321000', '扬州市', '320000');
INSERT INTO `city` VALUES ('87', '321100', '镇江市', '320000');
INSERT INTO `city` VALUES ('88', '321200', '泰州市', '320000');
INSERT INTO `city` VALUES ('89', '321300', '宿迁市', '320000');
INSERT INTO `city` VALUES ('90', '330100', '杭州市', '330000');
INSERT INTO `city` VALUES ('91', '330200', '宁波市', '330000');
INSERT INTO `city` VALUES ('92', '330300', '温州市', '330000');
INSERT INTO `city` VALUES ('93', '330400', '嘉兴市', '330000');
INSERT INTO `city` VALUES ('94', '330500', '湖州市', '330000');
INSERT INTO `city` VALUES ('95', '330600', '绍兴市', '330000');
INSERT INTO `city` VALUES ('96', '330700', '金华市', '330000');
INSERT INTO `city` VALUES ('97', '330800', '衢州市', '330000');
INSERT INTO `city` VALUES ('98', '330900', '舟山市', '330000');
INSERT INTO `city` VALUES ('99', '331000', '台州市', '330000');
INSERT INTO `city` VALUES ('100', '331100', '丽水市', '330000');
INSERT INTO `city` VALUES ('101', '340100', '合肥市', '340000');
INSERT INTO `city` VALUES ('102', '340200', '芜湖市', '340000');
INSERT INTO `city` VALUES ('103', '340300', '蚌埠市', '340000');
INSERT INTO `city` VALUES ('104', '340400', '淮南市', '340000');
INSERT INTO `city` VALUES ('105', '340500', '马鞍山市', '340000');
INSERT INTO `city` VALUES ('106', '340600', '淮北市', '340000');
INSERT INTO `city` VALUES ('107', '340700', '铜陵市', '340000');
INSERT INTO `city` VALUES ('108', '340800', '安庆市', '340000');
INSERT INTO `city` VALUES ('109', '341000', '黄山市', '340000');
INSERT INTO `city` VALUES ('110', '341100', '滁州市', '340000');
INSERT INTO `city` VALUES ('111', '341200', '阜阳市', '340000');
INSERT INTO `city` VALUES ('112', '341300', '宿州市', '340000');
INSERT INTO `city` VALUES ('113', '341400', '巢湖市', '340000');
INSERT INTO `city` VALUES ('114', '341500', '六安市', '340000');
INSERT INTO `city` VALUES ('115', '341600', '亳州市', '340000');
INSERT INTO `city` VALUES ('116', '341700', '池州市', '340000');
INSERT INTO `city` VALUES ('117', '341800', '宣城市', '340000');
INSERT INTO `city` VALUES ('118', '350100', '福州市', '350000');
INSERT INTO `city` VALUES ('119', '350200', '厦门市', '350000');
INSERT INTO `city` VALUES ('120', '350300', '莆田市', '350000');
INSERT INTO `city` VALUES ('121', '350400', '三明市', '350000');
INSERT INTO `city` VALUES ('122', '350500', '泉州市', '350000');
INSERT INTO `city` VALUES ('123', '350600', '漳州市', '350000');
INSERT INTO `city` VALUES ('124', '350700', '南平市', '350000');
INSERT INTO `city` VALUES ('125', '350800', '龙岩市', '350000');
INSERT INTO `city` VALUES ('126', '350900', '宁德市', '350000');
INSERT INTO `city` VALUES ('127', '360100', '南昌市', '360000');
INSERT INTO `city` VALUES ('128', '360200', '景德镇市', '360000');
INSERT INTO `city` VALUES ('129', '360300', '萍乡市', '360000');
INSERT INTO `city` VALUES ('130', '360400', '九江市', '360000');
INSERT INTO `city` VALUES ('131', '360500', '新余市', '360000');
INSERT INTO `city` VALUES ('132', '360600', '鹰潭市', '360000');
INSERT INTO `city` VALUES ('133', '360700', '赣州市', '360000');
INSERT INTO `city` VALUES ('134', '360800', '吉安市', '360000');
INSERT INTO `city` VALUES ('135', '360900', '宜春市', '360000');
INSERT INTO `city` VALUES ('136', '361000', '抚州市', '360000');
INSERT INTO `city` VALUES ('137', '361100', '上饶市', '360000');
INSERT INTO `city` VALUES ('138', '370100', '济南市', '370000');
INSERT INTO `city` VALUES ('139', '370200', '青岛市', '370000');
INSERT INTO `city` VALUES ('140', '370300', '淄博市', '370000');
INSERT INTO `city` VALUES ('141', '370400', '枣庄市', '370000');
INSERT INTO `city` VALUES ('142', '370500', '东营市', '370000');
INSERT INTO `city` VALUES ('143', '370600', '烟台市', '370000');
INSERT INTO `city` VALUES ('144', '370700', '潍坊市', '370000');
INSERT INTO `city` VALUES ('145', '370800', '济宁市', '370000');
INSERT INTO `city` VALUES ('146', '370900', '泰安市', '370000');
INSERT INTO `city` VALUES ('147', '371000', '威海市', '370000');
INSERT INTO `city` VALUES ('148', '371100', '日照市', '370000');
INSERT INTO `city` VALUES ('149', '371200', '莱芜市', '370000');
INSERT INTO `city` VALUES ('150', '371300', '临沂市', '370000');
INSERT INTO `city` VALUES ('151', '371400', '德州市', '370000');
INSERT INTO `city` VALUES ('152', '371500', '聊城市', '370000');
INSERT INTO `city` VALUES ('153', '371600', '滨州市', '370000');
INSERT INTO `city` VALUES ('154', '371700', '荷泽市', '370000');
INSERT INTO `city` VALUES ('155', '410100', '郑州市', '410000');
INSERT INTO `city` VALUES ('156', '410200', '开封市', '410000');
INSERT INTO `city` VALUES ('157', '410300', '洛阳市', '410000');
INSERT INTO `city` VALUES ('158', '410400', '平顶山市', '410000');
INSERT INTO `city` VALUES ('159', '410500', '安阳市', '410000');
INSERT INTO `city` VALUES ('160', '410600', '鹤壁市', '410000');
INSERT INTO `city` VALUES ('161', '410700', '新乡市', '410000');
INSERT INTO `city` VALUES ('162', '410800', '焦作市', '410000');
INSERT INTO `city` VALUES ('163', '410900', '濮阳市', '410000');
INSERT INTO `city` VALUES ('164', '411000', '许昌市', '410000');
INSERT INTO `city` VALUES ('165', '411100', '漯河市', '410000');
INSERT INTO `city` VALUES ('166', '411200', '三门峡市', '410000');
INSERT INTO `city` VALUES ('167', '411300', '南阳市', '410000');
INSERT INTO `city` VALUES ('168', '411400', '商丘市', '410000');
INSERT INTO `city` VALUES ('169', '411500', '信阳市', '410000');
INSERT INTO `city` VALUES ('170', '411600', '周口市', '410000');
INSERT INTO `city` VALUES ('171', '411700', '驻马店市', '410000');
INSERT INTO `city` VALUES ('172', '420100', '武汉市', '420000');
INSERT INTO `city` VALUES ('173', '420200', '黄石市', '420000');
INSERT INTO `city` VALUES ('174', '420300', '十堰市', '420000');
INSERT INTO `city` VALUES ('175', '420500', '宜昌市', '420000');
INSERT INTO `city` VALUES ('176', '420600', '襄樊市', '420000');
INSERT INTO `city` VALUES ('177', '420700', '鄂州市', '420000');
INSERT INTO `city` VALUES ('178', '420800', '荆门市', '420000');
INSERT INTO `city` VALUES ('179', '420900', '孝感市', '420000');
INSERT INTO `city` VALUES ('180', '421000', '荆州市', '420000');
INSERT INTO `city` VALUES ('181', '421100', '黄冈市', '420000');
INSERT INTO `city` VALUES ('182', '421200', '咸宁市', '420000');
INSERT INTO `city` VALUES ('183', '421300', '随州市', '420000');
INSERT INTO `city` VALUES ('184', '422800', '恩施土家族苗族自治州', '420000');
INSERT INTO `city` VALUES ('185', '429000', '省直辖行政单位', '420000');
INSERT INTO `city` VALUES ('186', '430100', '长沙市', '430000');
INSERT INTO `city` VALUES ('187', '430200', '株洲市', '430000');
INSERT INTO `city` VALUES ('188', '430300', '湘潭市', '430000');
INSERT INTO `city` VALUES ('189', '430400', '衡阳市', '430000');
INSERT INTO `city` VALUES ('190', '430500', '邵阳市', '430000');
INSERT INTO `city` VALUES ('191', '430600', '岳阳市', '430000');
INSERT INTO `city` VALUES ('192', '430700', '常德市', '430000');
INSERT INTO `city` VALUES ('193', '430800', '张家界市', '430000');
INSERT INTO `city` VALUES ('194', '430900', '益阳市', '430000');
INSERT INTO `city` VALUES ('195', '431000', '郴州市', '430000');
INSERT INTO `city` VALUES ('196', '431100', '永州市', '430000');
INSERT INTO `city` VALUES ('197', '431200', '怀化市', '430000');
INSERT INTO `city` VALUES ('198', '431300', '娄底市', '430000');
INSERT INTO `city` VALUES ('199', '433100', '湘西土家族苗族自治州', '430000');
INSERT INTO `city` VALUES ('200', '440100', '广州市', '440000');
INSERT INTO `city` VALUES ('201', '440200', '韶关市', '440000');
INSERT INTO `city` VALUES ('202', '440300', '深圳市', '440000');
INSERT INTO `city` VALUES ('203', '440400', '珠海市', '440000');
INSERT INTO `city` VALUES ('204', '440500', '汕头市', '440000');
INSERT INTO `city` VALUES ('205', '440600', '佛山市', '440000');
INSERT INTO `city` VALUES ('206', '440700', '江门市', '440000');
INSERT INTO `city` VALUES ('207', '440800', '湛江市', '440000');
INSERT INTO `city` VALUES ('208', '440900', '茂名市', '440000');
INSERT INTO `city` VALUES ('209', '441200', '肇庆市', '440000');
INSERT INTO `city` VALUES ('210', '441300', '惠州市', '440000');
INSERT INTO `city` VALUES ('211', '441400', '梅州市', '440000');
INSERT INTO `city` VALUES ('212', '441500', '汕尾市', '440000');
INSERT INTO `city` VALUES ('213', '441600', '河源市', '440000');
INSERT INTO `city` VALUES ('214', '441700', '阳江市', '440000');
INSERT INTO `city` VALUES ('215', '441800', '清远市', '440000');
INSERT INTO `city` VALUES ('216', '441900', '东莞市', '440000');
INSERT INTO `city` VALUES ('217', '442000', '中山市', '440000');
INSERT INTO `city` VALUES ('218', '445100', '潮州市', '440000');
INSERT INTO `city` VALUES ('219', '445200', '揭阳市', '440000');
INSERT INTO `city` VALUES ('220', '445300', '云浮市', '440000');
INSERT INTO `city` VALUES ('221', '450100', '南宁市', '450000');
INSERT INTO `city` VALUES ('222', '450200', '柳州市', '450000');
INSERT INTO `city` VALUES ('223', '450300', '桂林市', '450000');
INSERT INTO `city` VALUES ('224', '450400', '梧州市', '450000');
INSERT INTO `city` VALUES ('225', '450500', '北海市', '450000');
INSERT INTO `city` VALUES ('226', '450600', '防城港市', '450000');
INSERT INTO `city` VALUES ('227', '450700', '钦州市', '450000');
INSERT INTO `city` VALUES ('228', '450800', '贵港市', '450000');
INSERT INTO `city` VALUES ('229', '450900', '玉林市', '450000');
INSERT INTO `city` VALUES ('230', '451000', '百色市', '450000');
INSERT INTO `city` VALUES ('231', '451100', '贺州市', '450000');
INSERT INTO `city` VALUES ('232', '451200', '河池市', '450000');
INSERT INTO `city` VALUES ('233', '451300', '来宾市', '450000');
INSERT INTO `city` VALUES ('234', '451400', '崇左市', '450000');
INSERT INTO `city` VALUES ('235', '460100', '海口市', '460000');
INSERT INTO `city` VALUES ('236', '460200', '三亚市', '460000');
INSERT INTO `city` VALUES ('237', '469000', '省直辖县级行政单位', '460000');
INSERT INTO `city` VALUES ('238', '500100', '市辖区', '500000');
INSERT INTO `city` VALUES ('239', '500200', '县', '500000');
INSERT INTO `city` VALUES ('240', '500300', '市', '500000');
INSERT INTO `city` VALUES ('241', '510100', '成都市', '510000');
INSERT INTO `city` VALUES ('242', '510300', '自贡市', '510000');
INSERT INTO `city` VALUES ('243', '510400', '攀枝花市', '510000');
INSERT INTO `city` VALUES ('244', '510500', '泸州市', '510000');
INSERT INTO `city` VALUES ('245', '510600', '德阳市', '510000');
INSERT INTO `city` VALUES ('246', '510700', '绵阳市', '510000');
INSERT INTO `city` VALUES ('247', '510800', '广元市', '510000');
INSERT INTO `city` VALUES ('248', '510900', '遂宁市', '510000');
INSERT INTO `city` VALUES ('249', '511000', '内江市', '510000');
INSERT INTO `city` VALUES ('250', '511100', '乐山市', '510000');
INSERT INTO `city` VALUES ('251', '511300', '南充市', '510000');
INSERT INTO `city` VALUES ('252', '511400', '眉山市', '510000');
INSERT INTO `city` VALUES ('253', '511500', '宜宾市', '510000');
INSERT INTO `city` VALUES ('254', '511600', '广安市', '510000');
INSERT INTO `city` VALUES ('255', '511700', '达州市', '510000');
INSERT INTO `city` VALUES ('256', '511800', '雅安市', '510000');
INSERT INTO `city` VALUES ('257', '511900', '巴中市', '510000');
INSERT INTO `city` VALUES ('258', '512000', '资阳市', '510000');
INSERT INTO `city` VALUES ('259', '513200', '阿坝藏族羌族自治州', '510000');
INSERT INTO `city` VALUES ('260', '513300', '甘孜藏族自治州', '510000');
INSERT INTO `city` VALUES ('261', '513400', '凉山彝族自治州', '510000');
INSERT INTO `city` VALUES ('262', '520100', '贵阳市', '520000');
INSERT INTO `city` VALUES ('263', '520200', '六盘水市', '520000');
INSERT INTO `city` VALUES ('264', '520300', '遵义市', '520000');
INSERT INTO `city` VALUES ('265', '520400', '安顺市', '520000');
INSERT INTO `city` VALUES ('266', '522200', '铜仁地区', '520000');
INSERT INTO `city` VALUES ('267', '522300', '黔西南布依族苗族自治州', '520000');
INSERT INTO `city` VALUES ('268', '522400', '毕节地区', '520000');
INSERT INTO `city` VALUES ('269', '522600', '黔东南苗族侗族自治州', '520000');
INSERT INTO `city` VALUES ('270', '522700', '黔南布依族苗族自治州', '520000');
INSERT INTO `city` VALUES ('271', '530100', '昆明市', '530000');
INSERT INTO `city` VALUES ('272', '530300', '曲靖市', '530000');
INSERT INTO `city` VALUES ('273', '530400', '玉溪市', '530000');
INSERT INTO `city` VALUES ('274', '530500', '保山市', '530000');
INSERT INTO `city` VALUES ('275', '530600', '昭通市', '530000');
INSERT INTO `city` VALUES ('276', '530700', '丽江市', '530000');
INSERT INTO `city` VALUES ('277', '530800', '思茅市', '530000');
INSERT INTO `city` VALUES ('278', '530900', '临沧市', '530000');
INSERT INTO `city` VALUES ('279', '532300', '楚雄彝族自治州', '530000');
INSERT INTO `city` VALUES ('280', '532500', '红河哈尼族彝族自治州', '530000');
INSERT INTO `city` VALUES ('281', '532600', '文山壮族苗族自治州', '530000');
INSERT INTO `city` VALUES ('282', '532800', '西双版纳傣族自治州', '530000');
INSERT INTO `city` VALUES ('283', '532900', '大理白族自治州', '530000');
INSERT INTO `city` VALUES ('284', '533100', '德宏傣族景颇族自治州', '530000');
INSERT INTO `city` VALUES ('285', '533300', '怒江傈僳族自治州', '530000');
INSERT INTO `city` VALUES ('286', '533400', '迪庆藏族自治州', '530000');
INSERT INTO `city` VALUES ('287', '540100', '拉萨市', '540000');
INSERT INTO `city` VALUES ('288', '542100', '昌都地区', '540000');
INSERT INTO `city` VALUES ('289', '542200', '山南地区', '540000');
INSERT INTO `city` VALUES ('290', '542300', '日喀则地区', '540000');
INSERT INTO `city` VALUES ('291', '542400', '那曲地区', '540000');
INSERT INTO `city` VALUES ('292', '542500', '阿里地区', '540000');
INSERT INTO `city` VALUES ('293', '542600', '林芝地区', '540000');
INSERT INTO `city` VALUES ('294', '610100', '西安市', '610000');
INSERT INTO `city` VALUES ('295', '610200', '铜川市', '610000');
INSERT INTO `city` VALUES ('296', '610300', '宝鸡市', '610000');
INSERT INTO `city` VALUES ('297', '610400', '咸阳市', '610000');
INSERT INTO `city` VALUES ('298', '610500', '渭南市', '610000');
INSERT INTO `city` VALUES ('299', '610600', '延安市', '610000');
INSERT INTO `city` VALUES ('300', '610700', '汉中市', '610000');
INSERT INTO `city` VALUES ('301', '610800', '榆林市', '610000');
INSERT INTO `city` VALUES ('302', '610900', '安康市', '610000');
INSERT INTO `city` VALUES ('303', '611000', '商洛市', '610000');
INSERT INTO `city` VALUES ('304', '620100', '兰州市', '620000');
INSERT INTO `city` VALUES ('305', '620200', '嘉峪关市', '620000');
INSERT INTO `city` VALUES ('306', '620300', '金昌市', '620000');
INSERT INTO `city` VALUES ('307', '620400', '白银市', '620000');
INSERT INTO `city` VALUES ('308', '620500', '天水市', '620000');
INSERT INTO `city` VALUES ('309', '620600', '武威市', '620000');
INSERT INTO `city` VALUES ('310', '620700', '张掖市', '620000');
INSERT INTO `city` VALUES ('311', '620800', '平凉市', '620000');
INSERT INTO `city` VALUES ('312', '620900', '酒泉市', '620000');
INSERT INTO `city` VALUES ('313', '621000', '庆阳市', '620000');
INSERT INTO `city` VALUES ('314', '621100', '定西市', '620000');
INSERT INTO `city` VALUES ('315', '621200', '陇南市', '620000');
INSERT INTO `city` VALUES ('316', '622900', '临夏回族自治州', '620000');
INSERT INTO `city` VALUES ('317', '623000', '甘南藏族自治州', '620000');
INSERT INTO `city` VALUES ('318', '630100', '西宁市', '630000');
INSERT INTO `city` VALUES ('319', '632100', '海东地区', '630000');
INSERT INTO `city` VALUES ('320', '632200', '海北藏族自治州', '630000');
INSERT INTO `city` VALUES ('321', '632300', '黄南藏族自治州', '630000');
INSERT INTO `city` VALUES ('322', '632500', '海南藏族自治州', '630000');
INSERT INTO `city` VALUES ('323', '632600', '果洛藏族自治州', '630000');
INSERT INTO `city` VALUES ('324', '632700', '玉树藏族自治州', '630000');
INSERT INTO `city` VALUES ('325', '632800', '海西蒙古族藏族自治州', '630000');
INSERT INTO `city` VALUES ('326', '640100', '银川市', '640000');
INSERT INTO `city` VALUES ('327', '640200', '石嘴山市', '640000');
INSERT INTO `city` VALUES ('328', '640300', '吴忠市', '640000');
INSERT INTO `city` VALUES ('329', '640400', '固原市', '640000');
INSERT INTO `city` VALUES ('330', '640500', '中卫市', '640000');
INSERT INTO `city` VALUES ('331', '650100', '乌鲁木齐市', '650000');
INSERT INTO `city` VALUES ('332', '650200', '克拉玛依市', '650000');
INSERT INTO `city` VALUES ('333', '652100', '吐鲁番地区', '650000');
INSERT INTO `city` VALUES ('334', '652200', '哈密地区', '650000');
INSERT INTO `city` VALUES ('335', '652300', '昌吉回族自治州', '650000');
INSERT INTO `city` VALUES ('336', '652700', '博尔塔拉蒙古自治州', '650000');
INSERT INTO `city` VALUES ('337', '652800', '巴音郭楞蒙古自治州', '650000');
INSERT INTO `city` VALUES ('338', '652900', '阿克苏地区', '650000');
INSERT INTO `city` VALUES ('339', '653000', '克孜勒苏柯尔克孜自治州', '650000');
INSERT INTO `city` VALUES ('340', '653100', '喀什地区', '650000');
INSERT INTO `city` VALUES ('341', '653200', '和田地区', '650000');
INSERT INTO `city` VALUES ('342', '654000', '伊犁哈萨克自治州', '650000');
INSERT INTO `city` VALUES ('343', '654200', '塔城地区', '650000');
INSERT INTO `city` VALUES ('344', '654300', '阿勒泰地区', '650000');
INSERT INTO `city` VALUES ('345', '659000', '省直辖行政单位', '650000');

-- ----------------------------
-- Table structure for `comment`
-- ----------------------------
DROP TABLE IF EXISTS `comment`;
CREATE TABLE `comment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_tel` int(11) NOT NULL COMMENT '评论人手机',
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `content` text NOT NULL COMMENT '评论的内容',
  `star` tinyint(3) NOT NULL DEFAULT '3' COMMENT '打的分',
  `add_time` int(11) NOT NULL COMMENT '评论时间',
  `order_id` varchar(50) NOT NULL COMMENT '订单id',
  `goods_id` int(10) NOT NULL COMMENT '商品的ID',
  `norms` varchar(100) NOT NULL COMMENT '规格',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态0不显示 1显示',
  PRIMARY KEY (`id`),
  KEY `goods_id` (`goods_id`)
) ENGINE=MyISAM AUTO_INCREMENT=47 DEFAULT CHARSET=utf8 COMMENT='评论';

-- ----------------------------
-- Records of comment
-- ----------------------------
INSERT INTO `comment` VALUES ('2', '2147483647', '1', '不错', '3', '2147483647', '32323232323232', '7', '45g', '1');
INSERT INTO `comment` VALUES ('8', '2147483647', '1', '内容1', '2', '1461043119', '35', '7', '100g', '1');
INSERT INTO `comment` VALUES ('9', '2147483647', '1', '内容1', '2', '1461043129', '35', '7', '100g', '1');
INSERT INTO `comment` VALUES ('10', '2147483647', '1', '内容2', '5', '1461043129', '35', '12', '343', '1');
INSERT INTO `comment` VALUES ('11', '2147483647', '1', '内容1', '2', '1461294369', '35', '7', '100g', '1');
INSERT INTO `comment` VALUES ('12', '2147483647', '1', '内容2', '5', '1461294369', '35', '12', '343', '1');
INSERT INTO `comment` VALUES ('13', '2147483647', '1', '内容1', '2', '1461294395', '35', '7', '100g', '1');
INSERT INTO `comment` VALUES ('14', '2147483647', '1', '内容2', '5', '1461294395', '35', '12', '343', '1');
INSERT INTO `comment` VALUES ('15', '2147483647', '1', '内容1', '2', '1461294438', '86', '7', '100g', '1');
INSERT INTO `comment` VALUES ('16', '2147483647', '1', '内容2', '5', '1461294438', '86', '12', '343', '1');
INSERT INTO `comment` VALUES ('17', '2147483647', '1', '内容1', '2', '1461378929', '35', '7', '100g', '1');
INSERT INTO `comment` VALUES ('18', '2147483647', '1', '内容2', '5', '1461378929', '35', '12', '343', '1');
INSERT INTO `comment` VALUES ('19', '2147483647', '6', '内容1', '2', '1461378966', '129', '10', '350ml', '1');
INSERT INTO `comment` VALUES ('20', '2147483647', '6', '非常赞！物美价廉', '5', '1461380717', '96', '12', '343', '1');
INSERT INTO `comment` VALUES ('21', '2147483647', '6', '不是82年拉菲，不好！', '1', '1461380717', '96', '11', '500ml', '1');
INSERT INTO `comment` VALUES ('22', '2147483647', '6', '啥破楼！', '1', '1461382062', '102', '172', '1', '1');
INSERT INTO `comment` VALUES ('23', '2147483647', '6', '', '0', '1461383175', '95', '112', '400ml', '1');
INSERT INTO `comment` VALUES ('24', '2147483647', '6', '', '0', '1461383175', '95', '11', '500ml', '1');
INSERT INTO `comment` VALUES ('25', '2147483647', '6', '很一般！', '3', '1461383610', '145', '11', '500ml', '1');
INSERT INTO `comment` VALUES ('26', '2147483647', '6', 'i', '0', '1461384722', '94', '7', '100g', '1');
INSERT INTO `comment` VALUES ('27', '2147483647', '6', '花语', '2', '1461384774', '78', '12', '343', '1');
INSERT INTO `comment` VALUES ('28', '2147483647', '6', '有点贵！', '2', '1461546615', '143', '10', '350ml', '1');
INSERT INTO `comment` VALUES ('29', '2147483647', '6', '还可以', '4', '1461563287', '145', '11', '500ml', '1');
INSERT INTO `comment` VALUES ('30', '2147483647', '6', '有点贵', '0', '1461572812', '142', '10', '350ml', '1');
INSERT INTO `comment` VALUES ('31', '2147483647', '6', '啥么？', '1', '1461572827', '141', '165', '', '1');
INSERT INTO `comment` VALUES ('32', '2147483647', '6', '真难看', '2', '1461572845', '140', '182', '44kg', '1');
INSERT INTO `comment` VALUES ('33', '2147483647', '6', '-', '0', '1461574546', '137', '10', '350ml', '1');
INSERT INTO `comment` VALUES ('34', '2147483647', '6', '¥', '0', '1461574554', '134', '10', '350ml', '1');
INSERT INTO `comment` VALUES ('35', '2147483647', '6', '还不错', '4', '1461574569', '132', '7', '100g', '1');
INSERT INTO `comment` VALUES ('36', '2147483647', '11', '考虑考虑', '3', '1461834398', '247', '172', '1g', '1');
INSERT INTO `comment` VALUES ('37', '2147483647', '6', '染发颜色\n但不在乎', '3', '1462067087', '271', '10', '350ml', '1');
INSERT INTO `comment` VALUES ('38', '2147483647', '11', '内容1', '2', '1462072181', '270', '212', '12g', '1');
INSERT INTO `comment` VALUES ('39', '2147483647', '11', '内容1', '2', '1462072184', '270', '212', '12g', '1');
INSERT INTO `comment` VALUES ('40', '2147483647', '11', '123545465', '2', '1462072195', '270', '212', '12g', '1');
INSERT INTO `comment` VALUES ('41', '2147483647', '11', '刚刚好', '5', '1462081836', '269', '212', '12g', '1');
INSERT INTO `comment` VALUES ('42', '2147483647', '11', '内容1', '2', '1462088103', '275', '32', '54g', '1');
INSERT INTO `comment` VALUES ('43', '2147483647', '11', '给点评论吧～～', '5', '1462088674', '277', '212', '12g', '1');
INSERT INTO `comment` VALUES ('44', '2147483647', '11', '给点评论吧～～', '5', '1462088695', '277', '212', '12g', '0');
INSERT INTO `comment` VALUES ('45', '2147483647', '11', '给点评论吧～～', '3', '1462171316', '283', '212', '12g', '1');
INSERT INTO `comment` VALUES ('46', '2147483647', '11', '给点评论吧～～', '1', '1462179350', '288', '172', '1g', '1');

-- ----------------------------
-- Table structure for `community`
-- ----------------------------
DROP TABLE IF EXISTS `community`;
CREATE TABLE `community` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(160) CHARACTER SET utf8 NOT NULL COMMENT '小区名称',
  `province` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '省',
  `city` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '市',
  `area` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '所属县区',
  `lnt` varchar(60) NOT NULL,
  `lng` varchar(60) NOT NULL,
  `time` varchar(60) CHARACTER SET utf8 NOT NULL,
  `status` tinyint(3) unsigned NOT NULL COMMENT '状态0冻结1正常',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of community
-- ----------------------------
INSERT INTO `community` VALUES ('3', '普天小区', '陕西省', '西安市', '碑林区', '34.223531', '108.97133', '2016-04-21 13:47:31', '1');
INSERT INTO `community` VALUES ('4', '兰蒂斯城', '陕西省', '西安市', '碑林区', '34.260297', '109.005681', '2016-04-21 13:46:37', '1');
INSERT INTO `community` VALUES ('5', '黄埔花园', '陕西省', '西安市', '碑林区', '34.262168', '109.036461', '2016-04-21 13:43:44', '1');

-- ----------------------------
-- Table structure for `coupon`
-- ----------------------------
DROP TABLE IF EXISTS `coupon`;
CREATE TABLE `coupon` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) NOT NULL COMMENT '店铺id',
  `user_id` int(11) unsigned NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `min_price` decimal(10,2) NOT NULL,
  `begin_time` varchar(60) NOT NULL,
  `end_time` varchar(60) NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1优惠券2配送券',
  `time` varchar(60) NOT NULL,
  `status` int(11) unsigned NOT NULL DEFAULT '1' COMMENT '1正常0冻结2已使用',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=99 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of coupon
-- ----------------------------
INSERT INTO `coupon` VALUES ('73', '3', '1', '10.00', '50.00', '2016-04-28', '2016-05-01', '1', '2016-04-28 13:34:07', '1');
INSERT INTO `coupon` VALUES ('75', '3', '11', '10.00', '10.00', '2016-04-28', '2016-05-08', '2', '2016-04-28 13:34:35', '2');
INSERT INTO `coupon` VALUES ('76', '3', '6', '10.00', '50.00', '2016-04-28', '2016-05-01', '1', '2016-04-28 13:34:54', '2');
INSERT INTO `coupon` VALUES ('77', '3', '6', '10.00', '50.00', '2016-04-28', '2016-05-01', '1', '2016-04-28 13:34:54', '1');
INSERT INTO `coupon` VALUES ('78', '3', '6', '10.00', '50.00', '2016-04-28', '2016-05-01', '1', '2016-04-28 13:34:55', '1');
INSERT INTO `coupon` VALUES ('79', '3', '6', '10.00', '10.00', '2016-04-28', '2016-05-08', '2', '2016-04-28 13:34:58', '1');
INSERT INTO `coupon` VALUES ('80', '3', '6', '10.00', '10.00', '2016-04-28', '2016-05-08', '2', '2016-04-28 13:34:58', '1');
INSERT INTO `coupon` VALUES ('81', '3', '6', '10.00', '10.00', '2016-04-28', '2016-05-08', '2', '2016-04-28 13:34:58', '2');
INSERT INTO `coupon` VALUES ('82', '3', '6', '10.00', '10.00', '2016-04-28', '2016-05-08', '2', '2016-04-28 13:34:58', '2');
INSERT INTO `coupon` VALUES ('83', '3', '11', '10.00', '50.00', '2016-04-28', '2016-05-01', '1', '2016-04-28 13:45:27', '1');
INSERT INTO `coupon` VALUES ('84', '3', '11', '10.00', '50.00', '2016-04-28', '2016-05-01', '1', '2016-04-28 13:48:35', '1');
INSERT INTO `coupon` VALUES ('85', '3', '11', '10.00', '50.00', '2016-04-28', '2016-05-01', '1', '2016-04-28 13:48:48', '2');
INSERT INTO `coupon` VALUES ('86', '3', '1', '10.00', '50.00', '2016-04-28', '2016-05-01', '1', '2016-04-28 14:06:22', '1');
INSERT INTO `coupon` VALUES ('87', '3', '6', '10.00', '50.00', '2016-04-28', '2016-05-01', '1', '2016-04-28 14:06:31', '1');
INSERT INTO `coupon` VALUES ('88', '3', '11', '10.00', '50.00', '2016-04-28', '2016-05-01', '1', '2016-04-28 14:12:22', '1');
INSERT INTO `coupon` VALUES ('89', '3', '11', '0.00', '10.00', '2016-04-28', '2016-05-03', '2', '2016-04-28 14:16:07', '2');
INSERT INTO `coupon` VALUES ('90', '3', '11', '10.00', '10.00', '2016-05-02', '2016-05-12', '2', '2016-05-02 18:13:30', '2');
INSERT INTO `coupon` VALUES ('91', '3', '11', '10.00', '50.00', '2016-05-02', '2016-05-05', '1', '2016-05-02 18:14:07', '2');
INSERT INTO `coupon` VALUES ('92', '3', '11', '10.00', '50.00', '2016-05-02', '2016-05-05', '1', '2016-05-02 18:14:08', '1');
INSERT INTO `coupon` VALUES ('93', '3', '11', '10.00', '50.00', '2016-05-02', '2016-05-05', '1', '2016-05-02 18:14:09', '1');
INSERT INTO `coupon` VALUES ('94', '3', '11', '10.00', '50.00', '2016-05-02', '2016-05-05', '1', '2016-05-02 18:14:21', '1');
INSERT INTO `coupon` VALUES ('95', '3', '11', '10.00', '50.00', '2016-05-02', '2016-05-05', '1', '2016-05-02 18:14:21', '1');
INSERT INTO `coupon` VALUES ('96', '3', '11', '10.00', '50.00', '2016-05-02', '2016-05-05', '1', '2016-05-02 18:15:16', '1');
INSERT INTO `coupon` VALUES ('97', '3', '11', '10.00', '50.00', '2016-05-02', '2016-05-05', '1', '2016-05-02 18:15:17', '1');
INSERT INTO `coupon` VALUES ('98', '3', '11', '10.00', '50.00', '2016-05-02', '2016-05-05', '1', '2016-05-02 18:15:18', '1');

-- ----------------------------
-- Table structure for `coupon_config`
-- ----------------------------
DROP TABLE IF EXISTS `coupon_config`;
CREATE TABLE `coupon_config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) NOT NULL COMMENT '店铺id',
  `day` int(11) unsigned NOT NULL COMMENT '有效天数',
  `ratio` int(11) unsigned NOT NULL COMMENT '积分兑换优惠券比例(如100积分/优惠券)',
  `use_coupon` decimal(10,2) NOT NULL COMMENT '订单金额必须大于最低消费额才可使用',
  `coupon_price` decimal(10,2) NOT NULL COMMENT '此优惠券的可抵用价格',
  `type` tinyint(3) unsigned NOT NULL COMMENT '1优惠券2配送券',
  `time` varchar(60) NOT NULL COMMENT '添加时间',
  `status` tinyint(11) unsigned NOT NULL DEFAULT '1' COMMENT '1正常0冻结',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of coupon_config
-- ----------------------------
INSERT INTO `coupon_config` VALUES ('2', '3', '3', '2', '50.00', '10.00', '1', '2016-04-26 11:41:16', '1');
INSERT INTO `coupon_config` VALUES ('3', '3', '5', '100', '60.00', '15.00', '1', '2016-04-26 11:44:26', '1');
INSERT INTO `coupon_config` VALUES ('11', '3', '1', '100', '100.00', '10.00', '1', '2016-04-27 14:43:56', '1');
INSERT INTO `coupon_config` VALUES ('12', '3', '100', '150', '50.00', '100.00', '1', '2016-04-27 14:49:37', '1');
INSERT INTO `coupon_config` VALUES ('13', '3', '10', '1000', '100.00', '10.00', '2', '2016-04-27 15:10:21', '1');
INSERT INTO `coupon_config` VALUES ('14', '3', '10', '100', '100.00', '10.00', '2', '2016-04-27 15:41:02', '1');
INSERT INTO `coupon_config` VALUES ('15', '3', '10', '10', '10.00', '10.00', '2', '2016-04-27 15:42:12', '1');
INSERT INTO `coupon_config` VALUES ('16', '3', '5', '10', '10.00', '0.00', '2', '2016-04-27 15:46:33', '1');
INSERT INTO `coupon_config` VALUES ('17', '3', '2', '16', '344.00', '1.00', '2', '2016-04-28 18:04:42', '1');
INSERT INTO `coupon_config` VALUES ('18', '3', '8', '10000', '288.00', '5.00', '1', '2016-04-28 18:30:28', '1');
INSERT INTO `coupon_config` VALUES ('19', '3', '3', '10000', '100.00', '1.00', '2', '2016-04-28 18:30:45', '1');

-- ----------------------------
-- Table structure for `goods`
-- ----------------------------
DROP TABLE IF EXISTS `goods`;
CREATE TABLE `goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goods_name` varchar(45) NOT NULL COMMENT '商品名称',
  `cat_id` smallint(5) NOT NULL COMMENT '主分类的id',
  `market_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '市场价',
  `shop_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '本店价',
  `jifen` int(10) NOT NULL COMMENT '赠送积分',
  `is_promote` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否促销',
  `promote_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '促销价',
  `goods_img` varchar(150) NOT NULL DEFAULT '' COMMENT 'logo原图',
  `goods_thumb` varchar(150) NOT NULL DEFAULT '' COMMENT 'logo缩略图',
  `is_best` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否推荐',
  `is_seckill` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否秒杀',
  `is_group` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否团购',
  `sort_num` tinyint(3) unsigned NOT NULL DEFAULT '100' COMMENT '排序数字',
  `addtime` int(10) unsigned NOT NULL COMMENT '添加时间',
  `sale_num` int(11) NOT NULL COMMENT '销售量',
  `click` int(11) NOT NULL COMMENT '浏览量',
  `goods_number` int(11) NOT NULL COMMENT '商品数量',
  `goods_model` varchar(200) NOT NULL COMMENT '规格',
  `status` tinyint(4) NOT NULL COMMENT '0,下架 1上架',
  `shop_id` int(11) NOT NULL COMMENT '店铺id',
  `sale_price` decimal(10,2) NOT NULL COMMENT '销量总价格',
  `goods_from` varchar(30) NOT NULL COMMENT '商品来自 如自营和供应商',
  `id_return` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否允许退货1允许0不允许',
  PRIMARY KEY (`id`),
  KEY `shop_price` (`shop_price`),
  KEY `cat_id` (`cat_id`)
) ENGINE=MyISAM AUTO_INCREMENT=222 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of goods
-- ----------------------------
INSERT INTO `goods` VALUES ('6', '好吃2', '33', '12.00', '10.00', '0', '0', '0.00', './Public/upload/goods/thumb0570475306286f.jpg', './Public/upload/goods/thumb1570475306286f.jpg', '1', '0', '0', '100', '0', '5', '42', '39', '500g', '1', '4', '50.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('10', '好吃3', '33', '3.00', '4.00', '0', '0', '0.00', './Public/upload/goods/thumb05706320d34590.jpg', './Public/upload/goods/thumb15706320d34590.jpg', '1', '0', '0', '100', '0', '63', '63', '97', '350ml', '0', '3', '232.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('11', '好吃4', '33', '45.00', '43.00', '5', '0', '0.00', './Public/upload/goods/thumb057074e0e0bdc5.jpg', './Public/upload/goods/thumb157074e0e0bdc5.jpg', '1', '0', '0', '100', '0', '80', '178', '-39', '500ml', '1', '3', '3225.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('13', '好吃6', '33', '34.00', '34.00', '0', '0', '0.00', './Public/upload/goods/thumb0570872d700482.jpg', './Public/upload/goods/thumb1570872d700482.jpg', '1', '0', '0', '100', '0', '4', '141', '30', '34', '1', '6', '136.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('14', '好吃7', '33', '34.00', '34.00', '0', '0', '0.00', './Public/upload/goods/thumb0570873590b601.jpg', './Public/upload/goods/thumb1570873590b601.jpg', '1', '0', '0', '100', '0', '1', '65', '33', '34', '1', '4', '34.00', '天天供应商', '1');
INSERT INTO `goods` VALUES ('110', 'gggh', '7', '33.00', '554.00', '0', '0', '0.00', './Public/upload/goods/thumb05712fe8028e52.png', './Public/upload/goods/thumb15712fe8028e52.png', '0', '0', '0', '100', '0', '0', '0', '66', '55g', '1', '3', '0.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('19', '好吃9', '33', '0.00', '0.00', '0', '0', '0.00', './Public/upload/goods/thumb0570cc3cb48718.png', './Public/upload/goods/thumb1570cc3cb48718.png', '0', '0', '0', '100', '0', '0', '34', '0', '', '1', '3', '0.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('153', '花', '33', '27.00', '18.00', '0', '0', '0.00', './Public/upload/goods/thumb0571715294d91e.jpg', './Public/upload/goods/thumb1571715294d91e.jpg', '0', '0', '0', '100', '0', '26', '9', '-14', '1', '1', '3', '468.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('25', '好吃0', '33', '0.00', '0.00', '0', '0', '0.00', './Public/upload/goods/thumb0570da12090c98.png', './Public/upload/goods/thumb1570da12090c98.png', '0', '0', '0', '100', '0', '0', '34', '0', '55g', '1', '3', '0.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('26', '好吃45', '31', '0.00', '0.00', '0', '0', '0.00', './Public/upload/goods/thumb0570daaa6e56d3.png', './Public/upload/goods/thumb1570daaa6e56d3.png', '0', '0', '0', '100', '0', '0', '33', '0', '', '1', '3', '0.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('27', '好吃', '31', '0.00', '0.00', '0', '0', '0.00', './Public/upload/goods/thumb0570daaaf4d612.png', './Public/upload/goods/thumb1570daaaf4d612.png', '0', '0', '0', '100', '0', '0', '33', '0', '', '1', '3', '0.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('28', '好吃', '31', '0.00', '0.00', '0', '0', '0.00', './Public/upload/goods/thumb0570daab45e78b.png', './Public/upload/goods/thumb1570daab45e78b.png', '0', '0', '0', '100', '0', '0', '33', '0', '', '1', '3', '0.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('29', '好吃', '31', '0.00', '0.00', '0', '0', '0.00', './Public/upload/goods/thumb0570daab8960a2.png', './Public/upload/goods/thumb1570daab8960a2.png', '0', '0', '0', '100', '0', '0', '33', '0', '', '1', '3', '0.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('30', '好吃', '31', '0.00', '0.00', '0', '0', '0.00', './Public/upload/goods/thumb0570daabc26078.png', './Public/upload/goods/thumb1570daabc26078.png', '0', '0', '0', '100', '0', '0', '33', '0', '', '1', '3', '0.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('31', '好吃', '31', '0.00', '0.00', '0', '0', '0.00', './Public/upload/goods/thumb0570daabfdbabf.png', './Public/upload/goods/thumb1570daabfdbabf.png', '0', '0', '0', '100', '0', '0', '33', '0', '', '1', '3', '0.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('32', '好吃', '32', '55.00', '44.00', '0', '0', '0.00', './Public/upload/goods/thumb0570db3c99e74f.png', './Public/upload/goods/thumb1570db3c99e74f.png', '0', '1', '0', '100', '0', '11', '2', '44', '54g', '1', '3', '484.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('33', '好吃', '32', '44.00', '11.00', '0', '0', '0.00', './Public/upload/goods/thumb0570db68dbae2b.png', './Public/upload/goods/thumb1570db68dbae2b.png', '0', '0', '1', '100', '0', '2', '0', '120', '554g', '1', '3', '22.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('34', '好吃', '31', '0.00', '0.00', '0', '0', '0.00', './Public/upload/goods/thumb0570e051cb1bc6.png', './Public/upload/goods/thumb1570e051cb1bc6.png', '0', '0', '0', '100', '0', '1', '33', '-1', '58', '1', '3', '0.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('35', '好吃', '33', '122.00', '11.00', '0', '0', '0.00', './Public/upload/goods/thumb0570f4f532d6e3.jpg', './Public/upload/goods/thumb1570f4f532d6e3.jpg', '0', '0', '0', '100', '0', '4', '6', '8', '22g', '1', '3', '44.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('36', '好吃', '32', '121.00', '121.00', '0', '0', '0.00', './Public/upload/goods/thumb0570e0ac96247d.png', './Public/upload/goods/thumb1570e0ac96247d.png', '0', '0', '0', '100', '0', '5', '2', '1207', '121g', '1', '3', '605.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('37', '辣条啊', '31', '0.00', '1.11', '0', '0', '0.00', './Public/upload/goods/thumb0570e0c5cdede2.png', './Public/upload/goods/thumb1570e0c5cdede2.png', '0', '0', '0', '100', '0', '0', '33', '12', '50g', '1', '3', '0.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('38', '好吃', '34', '12.00', '121.00', '0', '0', '0.00', './Public/upload/goods/thumb0570ef31b9e73a.png', './Public/upload/goods/thumb1570ef31b9e73a.png', '0', '0', '0', '100', '0', '0', '1', '12', '12g', '1', '3', '0.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('39', '好吃', '33', '432.00', '24.00', '0', '0', '0.00', './Public/upload/goods/thumb0570ef5d1c7f5d.png', './Public/upload/goods/thumb1570ef5d1c7f5d.png', '0', '0', '0', '100', '0', '0', '0', '234', '23g', '1', '3', '0.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('40', '好吃', '32', '22.00', '222.00', '0', '0', '0.00', './Public/upload/goods/thumb0570ef8ed1a792.png', './Public/upload/goods/thumb1570ef8ed1a792.png', '0', '0', '0', '100', '0', '3', '2', '19', '22g', '1', '3', '666.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('41', '好吃', '34', '123.00', '123.00', '0', '0', '0.00', './Public/upload/goods/thumb0570ef9a84f30b.png', './Public/upload/goods/thumb1570ef9a84f30b.png', '0', '0', '0', '100', '0', '0', '1', '123', '123g', '1', '3', '0.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('137', '呵呵', '17', '45.00', '15.00', '0', '0', '0.00', './Public/upload/goods/thumb05717060dc8856.png', './Public/upload/goods/thumb15717060dc8856.png', '0', '0', '0', '100', '0', '0', '0', '15', '12kg', '1', '3', '0.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('47', '好吃', '33', '0.00', '0.00', '0', '0', '0.00', './Public/upload/goods/thumb0570efbacdcfaa.jpg', './Public/upload/goods/thumb1570efbacdcfaa.jpg', '0', '0', '0', '100', '0', '0', '34', '0', '', '1', '3', '0.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('48', '好吃', '33', '0.00', '0.00', '0', '0', '0.00', './Public/upload/goods/thumb0570efbc715a34.jpg', './Public/upload/goods/thumb1570efbc715a34.jpg', '0', '0', '0', '100', '0', '0', '34', '0', '', '0', '3', '0.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('141', '这样一来我', '34', '4455.00', '55.00', '0', '0', '0.00', './Public/upload/goods/thumb057170b7c6b2c0.png', './Public/upload/goods/thumb157170b7c6b2c0.png', '0', '0', '0', '100', '0', '10', '8', '45', '55kg', '1', '3', '550.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('57', '好吃', '33', '11.00', '12.00', '0', '0', '0.00', './Public/upload/goods/thumb0570f00b0cb4d6.png', './Public/upload/goods/thumb1570f00b0cb4d6.png', '0', '0', '0', '100', '0', '0', '0', '11', '45g', '0', '3', '0.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('58', '好吃', '33', '0.00', '0.00', '0', '0', '0.00', './Public/upload/goods/thumb0570f019c26da3.png', './Public/upload/goods/thumb1570f019c26da3.png', '0', '0', '0', '100', '0', '0', '34', '0', '', '1', '3', '0.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('59', '好吃', '33', '0.00', '0.00', '0', '0', '0.00', './Public/upload/goods/thumb0570f01a5a4d54.png', './Public/upload/goods/thumb1570f01a5a4d54.png', '0', '0', '0', '100', '0', '0', '34', '0', '', '1', '3', '0.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('60', '好吃', '33', '0.00', '0.00', '0', '0', '0.00', './Public/upload/goods/thumb0570f01a7cdf27.png', './Public/upload/goods/thumb1570f01a7cdf27.png', '0', '0', '0', '100', '0', '0', '34', '0', '', '1', '3', '0.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('61', '好吃', '7', '0.00', '0.00', '0', '0', '0.00', './Public/upload/goods/thumb0570f01a9034f9.png', './Public/upload/goods/thumb1570f01a9034f9.png', '0', '0', '0', '100', '0', '1', '34', '-1', '33ml', '1', '3', '0.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('62', '好吃', '33', '0.00', '0.00', '0', '0', '0.00', './Public/upload/goods/thumb0570f0208ca602.png', './Public/upload/goods/thumb1570f0208ca602.png', '0', '0', '0', '100', '0', '0', '34', '0', '', '1', '3', '0.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('63', '好吃', '33', '0.00', '0.00', '0', '0', '0.00', './Public/upload/goods/thumb0570f02107376f.png', './Public/upload/goods/thumb1570f02107376f.png', '0', '0', '0', '100', '0', '5', '34', '-5', '', '1', '3', '0.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('64', '好吃', '33', '0.00', '0.00', '0', '0', '0.00', './Public/upload/goods/thumb0570f0215dc279.png', './Public/upload/goods/thumb1570f0215dc279.png', '0', '0', '0', '100', '0', '0', '35', '0', '', '1', '3', '0.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('65', '好吃', '33', '0.00', '0.00', '0', '0', '0.00', './Public/upload/goods/thumb0570f021f253f9.png', './Public/upload/goods/thumb1570f021f253f9.png', '0', '0', '0', '100', '0', '4', '35', '-4', '', '1', '3', '0.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('70', '好吃', '0', '0.00', '0.00', '0', '0', '0.00', '', '', '0', '0', '0', '100', '0', '0', '0', '0', '', '0', '0', '0.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('155', '经理部', '14', '45.00', '55.00', '0', '0', '0.00', './Public/upload/goods/thumb057171944c8ad5.png', './Public/upload/goods/thumb157171944c8ad5.png', '0', '0', '0', '100', '0', '0', '0', '125', '55kg', '1', '3', '0.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('148', '我的', '17', '154.00', '144.00', '0', '0', '0.00', './Public/upload/goods/thumb057171455e4d8b.png', './Public/upload/goods/thumb157171455e4d8b.png', '0', '0', '0', '100', '0', '0', '0', '11', '15gj', '1', '3', '0.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('164', '小李飞刀', '51', '0.00', '0.00', '0', '0', '0.00', './Public/upload/goods/thumb0571725d4d9e06.jpg', './Public/upload/goods/thumb1571725d4d9e06.jpg', '0', '0', '0', '100', '0', '1', '3', '-1', '', '1', '3', '0.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('129', '并发放', '7', '123.00', '12.00', '0', '0', '0.00', './Public/upload/goods/thumb05716dc2344616.png', './Public/upload/goods/thumb15716dc2344616.png', '0', '0', '0', '100', '0', '0', '0', '23', '231g', '1', '3', '0.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('135', '花海', '34', '345.00', '345.00', '0', '0', '0.00', './Public/upload/goods/thumb05716f014dcaae.png', './Public/upload/goods/thumb15716f014dcaae.png', '0', '0', '0', '100', '0', '2', '7', '343', '345g', '1', '3', '690.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('131', '达成', '14', '55.00', '55.00', '0', '0', '0.00', './Public/upload/goods/thumb05716e0ab48c8c.png', './Public/upload/goods/thumb15716e0ab48c8c.png', '0', '0', '0', '100', '0', '0', '0', '55', '421g', '1', '3', '0.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('171', '喔', '33', '0.00', '10.00', '0', '0', '0.00', './Public/upload/goods/thumb05717338698634.jpg', './Public/upload/goods/thumb15717338698634.jpg', '0', '0', '1', '100', '0', '1', '17', '-1', '', '0', '3', '10.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('96', '好吃', '33', '0.00', '0.00', '0', '0', '0.00', './Public/upload/goods/thumb0570f16bc0bf8d.jpg', './Public/upload/goods/thumb1570f16bc0bf8d.jpg', '0', '0', '0', '100', '0', '0', '34', '0', 'dfa', '0', '3', '0.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('97', '好吃', '33', '0.00', '0.00', '0', '0', '0.00', './Public/upload/goods/thumb0570f16c7d5582.jpg', './Public/upload/goods/thumb1570f16c7d5582.jpg', '0', '0', '0', '100', '0', '0', '34', '0', '34ml', '0', '3', '0.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('98', '好吃', '33', '0.00', '0.00', '0', '0', '0.00', './Public/upload/goods/thumb0570f16d238b54.jpg', './Public/upload/goods/thumb1570f16d238b54.jpg', '0', '0', '0', '100', '0', '1', '34', '-1', '', '0', '3', '0.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('99', '好吃', '33', '0.00', '0.00', '0', '0', '0.00', './Public/upload/goods/thumb0570f22d874543.jpg', './Public/upload/goods/thumb1570f22d874543.jpg', '0', '0', '0', '100', '0', '8', '35', '-8', '', '1', '3', '0.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('100', '好吃', '33', '0.00', '0.00', '0', '0', '0.00', './Public/upload/goods/thumb0570f22f82349b.jpg', './Public/upload/goods/thumb1570f22f82349b.jpg', '0', '0', '0', '100', '0', '7', '51', '-7', '', '1', '3', '0.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('101', '大白菜', '33', '0.00', '0.00', '0', '0', '0.00', './Public/upload/goods/thumb0570f236911db1.jpg', './Public/upload/goods/thumb1570f236911db1.jpg', '0', '0', '0', '100', '0', '16', '56', '-16', 'df', '1', '3', '0.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('165', '测试', '33', '0.00', '0.00', '0', '0', '0.00', './Public/upload/goods/thumb057172a4c3553e.jpg', './Public/upload/goods/thumb157172a4c3553e.jpg', '0', '0', '0', '100', '0', '16', '22', '-16', '', '1', '3', '0.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('107', '泡椒牛蛙和我', '33', '0.00', '0.50', '0', '0', '0.00', './Public/upload/goods/thumb0570f2671afe75.png', './Public/upload/goods/thumb1570f2671afe75.png', '0', '0', '0', '100', '0', '20', '53', '-20', '15g', '1', '3', '10.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('166', '我', '49', '0.00', '0.00', '0', '0', '0.00', './Public/upload/goods/thumb057172bbf4ff3a.jpg', './Public/upload/goods/thumb157172bbf4ff3a.jpg', '0', '0', '0', '100', '0', '0', '0', '0', '', '1', '3', '0.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('172', '渣渣', '35', '3.00', '1.00', '0', '0', '0.00', './Public/upload/goods/thumb05718360d42827.jpg', './Public/upload/goods/thumb15718360d42827.jpg', '0', '0', '0', '100', '0', '29', '47', '-17', '1g', '1', '3', '29.00', '留一步自营', '1');
INSERT INTO `goods` VALUES ('176', 'df', '32', '34.00', '34.00', '0', '0', '0.00', './Public/upload/goods/thumb057185e304d02d.jpg', './Public/upload/goods/thumb157185e304d02d.jpg', '1', '0', '0', '100', '0', '0', '5', '34', '34ml', '1', '0', '0.00', '34', '1');
INSERT INTO `goods` VALUES ('181', '我们自己去', '66', '14.00', '44.00', '0', '0', '0.00', './Public/upload/goods/thumb057186ef693f3d.png', './Public/upload/goods/thumb157186ef693f3d.png', '0', '0', '0', '100', '0', '0', '0', '12', '12kg', '1', '3', '0.00', '', '1');
INSERT INTO `goods` VALUES ('182', 'llo', '33', '55.00', '888.00', '0', '0', '0.00', './Public/upload/goods/thumb05718705db3424.png', './Public/upload/goods/thumb15718705db3424.png', '0', '0', '0', '100', '0', '3', '26', '886', '44kg', '0', '3', '2664.00', '', '1');
INSERT INTO `goods` VALUES ('183', '测试', '39', '11.00', '11.00', '0', '0', '0.00', './Public/upload/goods/thumb057187235f2a51.jpg', './Public/upload/goods/thumb157187235f2a51.jpg', '0', '0', '0', '100', '0', '0', '1', '11', '11', '0', '3', '0.00', '', '1');
INSERT INTO `goods` VALUES ('188', '酱油', '50', '12.50', '0.00', '0', '0', '0.00', '', '', '0', '0', '0', '100', '0', '0', '0', '96', '', '0', '8', '11.00', '800ml', '1');
INSERT INTO `goods` VALUES ('189', '卫生纸', '17', '26.00', '0.00', '0', '0', '0.00', '', '', '0', '0', '0', '100', '0', '0', '0', '366', '', '0', '8', '24.00', '10个', '1');
INSERT INTO `goods` VALUES ('221', '我自己都会', '54', '998.00', '151.00', '0', '0', '0.00', './Public/upload/goods/thumb05726c56aa767a.png', './Public/upload/goods/thumb15726c56aa767a.png', '0', '0', '0', '100', '0', '0', '0', '155', '152ml', '0', '3', '0.00', '', '1');
INSERT INTO `goods` VALUES ('212', '幸福', '33', '15.00', '99999999.99', '0', '0', '0.00', './Public/upload/goods/thumb057255f5c0e865.jpg', './Public/upload/goods/thumb157255f5c0e865.jpg', '1', '0', '0', '100', '0', '207', '46', '-57', '12g', '0', '3', '99999999.99', '', '1');
INSERT INTO `goods` VALUES ('216', '12312', '41', '213.00', '123.00', '0', '0', '0.00', './Public/upload/goods/thumb0571ad07fce775.png', './Public/upload/goods/thumb1571ad07fce775.png', '0', '0', '0', '100', '0', '0', '0', '23', '123g', '0', '3', '0.00', '', '1');
INSERT INTO `goods` VALUES ('218', '键盘侠专用', '67', '60.00', '40.00', '0', '0', '0.00', './Public/upload/goods/thumb0571c56ffda605.jpg', './Public/upload/goods/thumb1571c56ffda605.jpg', '1', '0', '0', '100', '0', '30', '26', '54', '12g', '1', '3', '1200.00', '留一步自营', '1');

-- ----------------------------
-- Table structure for `goods_desc`
-- ----------------------------
DROP TABLE IF EXISTS `goods_desc`;
CREATE TABLE `goods_desc` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goods_id` int(11) NOT NULL COMMENT '商品id',
  `pic` varchar(100) NOT NULL COMMENT '图片url',
  `desc` varchar(200) NOT NULL COMMENT '描述',
  `height` int(11) NOT NULL COMMENT '图片高度',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=474 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of goods_desc
-- ----------------------------
INSERT INTO `goods_desc` VALUES ('387', '172', './Public/upload/goods/thumb1461204493_1947443491.jpg', '麻子', '400');
INSERT INTO `goods_desc` VALUES ('388', '172', './Public/upload/goods/thumb1461204493_1565882648.jpg', '', '400');
INSERT INTO `goods_desc` VALUES ('389', '172', './Public/upload/goods/thumb1461204493_512869160.jpg', '', '400');
INSERT INTO `goods_desc` VALUES ('390', '172', './Public/upload/goods/thumb1461204493_978057764.jpg', '', '400');
INSERT INTO `goods_desc` VALUES ('391', '172', './Public/upload/goods/thumb1461204493_768251416.jpg', '', '400');
INSERT INTO `goods_desc` VALUES ('392', '172', './Public/upload/goods/thumb1461204493_542470712.jpg', '', '398');
INSERT INTO `goods_desc` VALUES ('393', '172', './Public/upload/goods/thumb1461204493_2024585880.jpg', '红包', '398');
INSERT INTO `goods_desc` VALUES ('394', '172', './Public/upload/goods/thumb1461204493_100367467.jpg', '', '283');
INSERT INTO `goods_desc` VALUES ('395', '173', './Public/upload/goods/thumb1461205252_587602083.jpg', '好', '0');
INSERT INTO `goods_desc` VALUES ('396', '173', './Public/upload/goods/thumb1461205252_1173949143.jpg', 'faf', '0');
INSERT INTO `goods_desc` VALUES ('397', '173', './Public/upload/goods/thumb1461205252_506779462.jpg', 'dfd', '0');
INSERT INTO `goods_desc` VALUES ('398', '174', './Public/upload/goods/thumb1461209778_1179026815.png', '', '0');
INSERT INTO `goods_desc` VALUES ('399', '174', './Public/upload/goods/thumb1461209778_420314462.png', '', '0');
INSERT INTO `goods_desc` VALUES ('400', '175', './Public/upload/goods/thumb1461213737_302597559.png', '', '0');
INSERT INTO `goods_desc` VALUES ('401', '175', './Public/upload/goods/thumb1461213737_466836221.png', '', '0');
INSERT INTO `goods_desc` VALUES ('402', '176', './Public/upload/goods/thumb1461214768_2039821356.jpg', '', '0');
INSERT INTO `goods_desc` VALUES ('403', '177', './Public/upload/goods/thumb1461215645_432564623.png', '', '0');
INSERT INTO `goods_desc` VALUES ('404', '177', './Public/upload/goods/thumb1461215645_430354918.jpg', '', '0');
INSERT INTO `goods_desc` VALUES ('405', '178', './Public/upload/goods/thumb1461215645_432564623.png', '', '0');
INSERT INTO `goods_desc` VALUES ('406', '178', './Public/upload/goods/thumb1461215645_430354918.jpg', '', '0');
INSERT INTO `goods_desc` VALUES ('407', '179', './Public/upload/goods/thumb1461215969_1368140810.png', '', '0');
INSERT INTO `goods_desc` VALUES ('408', '179', './Public/upload/goods/thumb1461215969_2018319643.png', '', '0');
INSERT INTO `goods_desc` VALUES ('409', '180', './Public/upload/goods/thumb1461215969_1368140810.png', '', '0');
INSERT INTO `goods_desc` VALUES ('410', '180', './Public/upload/goods/thumb1461215969_2018319643.png', '', '0');
INSERT INTO `goods_desc` VALUES ('411', '181', './Public/upload/goods/thumb1461219062_1839314001.png', '哦哦哦。你的人们啊哈哈？', '285');
INSERT INTO `goods_desc` VALUES ('412', '181', './Public/upload/goods/thumb1461219062_987263322.png', '', '285');
INSERT INTO `goods_desc` VALUES ('413', '182', './Public/upload/goods/thumb1461219421_2115854941.png', '', '531');
INSERT INTO `goods_desc` VALUES ('414', '182', './Public/upload/goods/thumb1461219421_1565533507.png', '', '301');
INSERT INTO `goods_desc` VALUES ('415', '182', './Public/upload/goods/thumb1461219421_1830012920.png', '', '531');
INSERT INTO `goods_desc` VALUES ('416', '182', './Public/upload/goods/thumb1461219421_1440458109.png', '', '531');
INSERT INTO `goods_desc` VALUES ('417', '183', './Public/upload/goods/thumb1461219894_563524216.jpg', '', '400');
INSERT INTO `goods_desc` VALUES ('418', '184', './Public/upload/goods/thumb1461222168_349512299.jpg', '好', '225');
INSERT INTO `goods_desc` VALUES ('419', '185', './Public/upload/goods/thumb1461222808_2020019731.jpg', '我们', '400');
INSERT INTO `goods_desc` VALUES ('420', '190', './Public/upload/goods/thumb1461224966_1383745567.jpg', '', '400');
INSERT INTO `goods_desc` VALUES ('421', '191', './Public/upload/goods/thumb1461225050_800957464.jpg', '111111', '400');
INSERT INTO `goods_desc` VALUES ('422', '192', './Public/upload/goods/thumb1461225164_1274162803.jpg', '', '400');
INSERT INTO `goods_desc` VALUES ('423', '193', './Public/upload/goods/thumb1461227946_1603305985.png', '', '712');
INSERT INTO `goods_desc` VALUES ('424', '194', './Public/upload/goods/thumb1461229505_2058330355.jpg', '', '400');
INSERT INTO `goods_desc` VALUES ('52', '54', './Public/upload/goods/thumb570f000d4f65f.jpg', '2234', '300');
INSERT INTO `goods_desc` VALUES ('53', '55', './Public/upload/goods/thumb570f003603cb6.jpg', '2234', '296');
INSERT INTO `goods_desc` VALUES ('54', '56', './Public/upload/goods/thumb570f00790537f.jpg', '2234', '438');
INSERT INTO `goods_desc` VALUES ('425', '195', './Public/upload/goods/thumb1461230017_899693257.png', '2234', '400');
INSERT INTO `goods_desc` VALUES ('426', '196', './Public/upload/goods/thumb1461230033_685132170.png', '2234', '400');
INSERT INTO `goods_desc` VALUES ('427', '197', './Public/upload/goods/thumb1461230224_125628490.jpg', '2234', '400');
INSERT INTO `goods_desc` VALUES ('428', '198', './Public/upload/goods/thumb1461230452_409791511.jpg', '11111', '400');
INSERT INTO `goods_desc` VALUES ('429', '199', './Public/upload/goods/thumb1461231472_857332071.JPEG', '', '533');
INSERT INTO `goods_desc` VALUES ('430', '200', './Public/upload/goods/thumb1461231811_1931144816.jpg', '2234', '517');
INSERT INTO `goods_desc` VALUES ('431', '201', './Public/upload/goods/thumb1461232269_1849344850.png', '', '261');
INSERT INTO `goods_desc` VALUES ('432', '202', './Public/upload/goods/thumb1461232458_974400929.png', '', '711');
INSERT INTO `goods_desc` VALUES ('433', '203', './Public/upload/goods/thumb1461232731_1237895971.jpg', '', '400');
INSERT INTO `goods_desc` VALUES ('434', '204', './Public/upload/goods/thumb1461304527_1242791676.png', '', '261');
INSERT INTO `goods_desc` VALUES ('435', '205', './Public/upload/goods/thumb1461304629_1676471611.png', '', '261');
INSERT INTO `goods_desc` VALUES ('436', '205', './Public/upload/goods/thumb1461304629_1172671446.png', '', '261');
INSERT INTO `goods_desc` VALUES ('437', '206', './Public/upload/goods/thumb1461304681_1310224359.png', '', '261');
INSERT INTO `goods_desc` VALUES ('438', '206', './Public/upload/goods/thumb1461304681_893496359.png', '', '261');
INSERT INTO `goods_desc` VALUES ('73', '66', './Public/upload/goods/thumb570f034d67398.jpg', '2234', '438');
INSERT INTO `goods_desc` VALUES ('439', '206', './Public/upload/goods/thumb1461304681_1903492546.png', '', '356');
INSERT INTO `goods_desc` VALUES ('440', '206', './Public/upload/goods/thumb1461304681_1439393497.png', '', '384');
INSERT INTO `goods_desc` VALUES ('441', '206', './Public/upload/goods/thumb1461304681_779387519.png', '', '400');
INSERT INTO `goods_desc` VALUES ('442', '206', './Public/upload/goods/thumb1461304681_1847040735.png', '', '711');
INSERT INTO `goods_desc` VALUES ('80', '71', './Public/upload/goods/thumb570f04fda104b.jpg', '2234', '438');
INSERT INTO `goods_desc` VALUES ('81', '72', './Public/upload/goods/thumb570f0510e6612.jpg', '2234', '438');
INSERT INTO `goods_desc` VALUES ('82', '73', './Public/upload/goods/thumb570f05224e473.jpg', '2234', '438');
INSERT INTO `goods_desc` VALUES ('83', '73', './Public/upload/goods/thumb570f05224ec44.png', '34343', '542');
INSERT INTO `goods_desc` VALUES ('84', '74', './Public/upload/goods/thumb570f055945863.jpg', '2234', '438');
INSERT INTO `goods_desc` VALUES ('85', '74', './Public/upload/goods/thumb570f05594641b.png', '34343', '542');
INSERT INTO `goods_desc` VALUES ('86', '74', './Public/upload/goods/thumb570f055946beb.png', '34343', '542');
INSERT INTO `goods_desc` VALUES ('87', '75', './Public/upload/goods/thumb570f05747217e.jpg', '2234', '438');
INSERT INTO `goods_desc` VALUES ('88', '75', './Public/upload/goods/thumb570f05747294e.png', '34343', '542');
INSERT INTO `goods_desc` VALUES ('89', '75', './Public/upload/goods/thumb570f05747311e.png', '34343', '542');
INSERT INTO `goods_desc` VALUES ('90', '75', './Public/upload/goods/thumb570f0574738ee.jpg', '34', '296');
INSERT INTO `goods_desc` VALUES ('443', '207', './Public/upload/goods/thumb1461304910_1143254535.JPEG', '', '248');
INSERT INTO `goods_desc` VALUES ('444', '208', './Public/upload/goods/thumb1461305073_1444846495.JPEG', '', '248');
INSERT INTO `goods_desc` VALUES ('93', '77', './Public/upload/goods/thumb570f0a2e57498.jpg', '2234', '438');
INSERT INTO `goods_desc` VALUES ('94', '77', './Public/upload/goods/thumb570f0a2e57c68.png', '34343', '542');
INSERT INTO `goods_desc` VALUES ('95', '77', './Public/upload/goods/thumb570f0a2e58438.png', '34343', '542');
INSERT INTO `goods_desc` VALUES ('96', '77', './Public/upload/goods/thumb570f0a2e58c08.jpg', '34', '296');
INSERT INTO `goods_desc` VALUES ('97', '78', './Public/upload/goods/thumb570f0a3915465.jpg', '2234', '438');
INSERT INTO `goods_desc` VALUES ('98', '78', './Public/upload/goods/thumb570f0a391601e.png', '34343', '542');
INSERT INTO `goods_desc` VALUES ('99', '78', './Public/upload/goods/thumb570f0a39167ee.png', '34343', '542');
INSERT INTO `goods_desc` VALUES ('100', '79', './Public/upload/goods/thumb570f0a470bb44.jpg', '2234', '438');
INSERT INTO `goods_desc` VALUES ('101', '79', './Public/upload/goods/thumb570f0a470c314.png', '34343', '542');
INSERT INTO `goods_desc` VALUES ('102', '80', './Public/upload/goods/thumb570f0b441130c.jpg', '2234', '296');
INSERT INTO `goods_desc` VALUES ('448', '210', './Public/upload/goods/thumb1461305754_92401362.jpg', '', '300');
INSERT INTO `goods_desc` VALUES ('449', '211', './Public/upload/goods/thumb1461306069_1046757082.png', '', '261');
INSERT INTO `goods_desc` VALUES ('450', '211', './Public/upload/goods/thumb1461306069_82994345.png', '', '261');
INSERT INTO `goods_desc` VALUES ('107', '83', './Public/upload/goods/thumb570f0c15244a8.jpg', '2234', '296');
INSERT INTO `goods_desc` VALUES ('108', '83', './Public/upload/goods/thumb570f0c15244a8.png', '34343', '542');
INSERT INTO `goods_desc` VALUES ('109', '83', './Public/upload/goods/thumb570f0c1528198.jpg', '34343', '300');
INSERT INTO `goods_desc` VALUES ('110', '84', './Public/upload/goods/thumb570f0c322097b.jpg', '2234', '296');
INSERT INTO `goods_desc` VALUES ('111', '84', './Public/upload/goods/thumb570f0c322466b.png', '34343', '542');
INSERT INTO `goods_desc` VALUES ('112', '84', './Public/upload/goods/thumb570f0c322466b.jpg', '34343', '300');
INSERT INTO `goods_desc` VALUES ('113', '85', './Public/upload/goods/thumb570f0c7836ca6.png', '2234', '402');
INSERT INTO `goods_desc` VALUES ('114', '85', './Public/upload/goods/thumb570f0c783a996.png', '2234', '397');
INSERT INTO `goods_desc` VALUES ('115', '86', './Public/upload/goods/thumb570f0cd3a4026.jpg', '2234', '296');
INSERT INTO `goods_desc` VALUES ('116', '86', './Public/upload/goods/thumb570f0cd3a7d16.png', '34343', '542');
INSERT INTO `goods_desc` VALUES ('117', '86', './Public/upload/goods/thumb570f0cd3a7d16.jpg', '34343', '300');
INSERT INTO `goods_desc` VALUES ('445', '208', './Public/upload/goods/thumb1461305073_1694826516.JPEG', '', '550');
INSERT INTO `goods_desc` VALUES ('446', '209', './Public/upload/goods/thumb1461305696_410679699.JPEG', '', '705');
INSERT INTO `goods_desc` VALUES ('447', '209', './Public/upload/goods/thumb1461305696_529304106.jpg', '', '705');
INSERT INTO `goods_desc` VALUES ('231', '113', './Public/upload/goods/thumb160417165010_15.png', '', '285');
INSERT INTO `goods_desc` VALUES ('124', '89', './Public/upload/goods/thumb570f108450eb2.jpg', '2234', '296');
INSERT INTO `goods_desc` VALUES ('125', '89', './Public/upload/goods/thumb570f108454ba2.png', '34343', '542');
INSERT INTO `goods_desc` VALUES ('126', '89', './Public/upload/goods/thumb570f108454ba2.jpg', '34343', '267');
INSERT INTO `goods_desc` VALUES ('127', '89', './Public/upload/goods/thumb570f108458892.jpg', '34', '300');
INSERT INTO `goods_desc` VALUES ('128', '90', './Public/upload/goods/thumb570f147d2de7c.jpg', '2234', '300');
INSERT INTO `goods_desc` VALUES ('129', '90', './Public/upload/goods/thumb570f147d31b6c.jpg', '34343', '266');
INSERT INTO `goods_desc` VALUES ('130', '91', './Public/upload/goods/thumb570f14d4a8921.jpg', '2234', '300');
INSERT INTO `goods_desc` VALUES ('131', '91', './Public/upload/goods/thumb570f14d4a90f1.jpg', '34343', '266');
INSERT INTO `goods_desc` VALUES ('132', '91', './Public/upload/goods/thumb570f14d4a98c1.jpg', '34343', '300');
INSERT INTO `goods_desc` VALUES ('133', '92', './Public/upload/goods/thumb570f152c180a0.jpg', '2234', '300');
INSERT INTO `goods_desc` VALUES ('134', '92', './Public/upload/goods/thumb570f152c18871.jpg', '34343', '266');
INSERT INTO `goods_desc` VALUES ('135', '92', './Public/upload/goods/thumb570f152c19041.jpg', '34343', '300');
INSERT INTO `goods_desc` VALUES ('136', '92', './Public/upload/goods/thumb570f152c19429.jpg', '34', '266');
INSERT INTO `goods_desc` VALUES ('137', '93', './Public/upload/goods/thumb570f15496327d.jpg', '2234', '300');
INSERT INTO `goods_desc` VALUES ('138', '93', './Public/upload/goods/thumb570f154963a4d.jpg', '34343', '266');
INSERT INTO `goods_desc` VALUES ('139', '93', './Public/upload/goods/thumb570f154963e35.jpg', '34343', '300');
INSERT INTO `goods_desc` VALUES ('140', '93', './Public/upload/goods/thumb570f154964605.jpg', '34', '266');
INSERT INTO `goods_desc` VALUES ('141', '94', './Public/upload/goods/thumb570f161475dc4.jpg', '2234', '266');
INSERT INTO `goods_desc` VALUES ('142', '94', './Public/upload/goods/thumb570f161476594.jpg', '34343', '300');
INSERT INTO `goods_desc` VALUES ('143', '94', './Public/upload/goods/thumb570f161476d64.jpg', '34343', '266');
INSERT INTO `goods_desc` VALUES ('144', '94', './Public/upload/goods/thumb570f161477534.jpg', '34', '266');
INSERT INTO `goods_desc` VALUES ('145', '95', './Public/upload/goods/thumb570f167378e57.jpg', '2234', '266');
INSERT INTO `goods_desc` VALUES ('146', '95', './Public/upload/goods/thumb570f167379627.jpg', '34343', '300');
INSERT INTO `goods_desc` VALUES ('147', '95', './Public/upload/goods/thumb570f167379a0f.jpg', '34343', '266');
INSERT INTO `goods_desc` VALUES ('148', '95', './Public/upload/goods/thumb570f16737a1df.jpg', '34', '266');
INSERT INTO `goods_desc` VALUES ('149', '96', './Public/upload/goods/thumb570f16bc39ace.jpg', '2234', '300');
INSERT INTO `goods_desc` VALUES ('150', '97', './Public/upload/goods/thumb570f16c80ee82.jpg', '2234', '300');
INSERT INTO `goods_desc` VALUES ('151', '98', './Public/upload/goods/thumb570f16d266695.jpg', '2234', '266');
INSERT INTO `goods_desc` VALUES ('152', '99', './Public/upload/goods/thumb160414125552_53.jpg', '2234', '300');
INSERT INTO `goods_desc` VALUES ('153', '99', './Public/upload/goods/thumb160414125552_37.jpg', '34343', '300');
INSERT INTO `goods_desc` VALUES ('154', '99', './Public/upload/goods/thumb160414125552_45.jpg', '34343', '266');
INSERT INTO `goods_desc` VALUES ('155', '100', './Public/upload/goods/thumb160414125624_76.jpg', '2234', '300');
INSERT INTO `goods_desc` VALUES ('156', '100', './Public/upload/goods/thumb160414125624_52.jpg', '34343', '300');
INSERT INTO `goods_desc` VALUES ('157', '100', './Public/upload/goods/thumb160414125624_18.jpg', '34343', '266');
INSERT INTO `goods_desc` VALUES ('158', '101', './Public/upload/goods/thumb160414125817_11.jpg', '2234', '300');
INSERT INTO `goods_desc` VALUES ('159', '101', './Public/upload/goods/thumb160414125817_94.jpg', '34343', '300');
INSERT INTO `goods_desc` VALUES ('160', '101', './Public/upload/goods/thumb160414125817_77.jpg', '34343', '266');
INSERT INTO `goods_desc` VALUES ('161', '101', './Public/upload/goods/thumb160414125817_93.jpg', '34', '300');
INSERT INTO `goods_desc` VALUES ('162', '102', './Public/upload/goods/thumb160414125829_49.jpg', '2234', '300');
INSERT INTO `goods_desc` VALUES ('163', '102', './Public/upload/goods/thumb160414125829_81.jpg', '34343', '300');
INSERT INTO `goods_desc` VALUES ('164', '102', './Public/upload/goods/thumb160414125829_95.jpg', '34343', '266');
INSERT INTO `goods_desc` VALUES ('165', '102', './Public/upload/goods/thumb160414125829_34.jpg', '34', '300');
INSERT INTO `goods_desc` VALUES ('166', '103', './Public/upload/goods/thumb160414130021_25.jpg', '2234', '300');
INSERT INTO `goods_desc` VALUES ('167', '103', './Public/upload/goods/thumb160414130021_32.jpg', '34343', '300');
INSERT INTO `goods_desc` VALUES ('168', '103', './Public/upload/goods/thumb160414130021_63.jpg', '34343', '266');
INSERT INTO `goods_desc` VALUES ('169', '103', './Public/upload/goods/thumb160414130021_28.jpg', '34', '300');
INSERT INTO `goods_desc` VALUES ('170', '103', './Public/upload/goods/thumb160414130021_59.jpg', 'dfadf', '236');
INSERT INTO `goods_desc` VALUES ('171', '104', './Public/upload/goods/thumb160414130914_48.jpg', '2234', '300');
INSERT INTO `goods_desc` VALUES ('172', '104', './Public/upload/goods/thumb160414130914_40.jpg', '34343', '300');
INSERT INTO `goods_desc` VALUES ('173', '104', './Public/upload/goods/thumb160414130914_89.jpg', '34343', '266');
INSERT INTO `goods_desc` VALUES ('174', '104', './Public/upload/goods/thumb160414130914_14.jpg', '34', '300');
INSERT INTO `goods_desc` VALUES ('175', '104', './Public/upload/goods/thumb160414130914_26.jpg', '', '236');
INSERT INTO `goods_desc` VALUES ('176', '105', './Public/upload/goods/thumb160414130918_51.jpg', '2234', '300');
INSERT INTO `goods_desc` VALUES ('177', '105', './Public/upload/goods/thumb160414130918_44.jpg', '34343', '300');
INSERT INTO `goods_desc` VALUES ('178', '105', './Public/upload/goods/thumb160414130918_81.jpg', '34343', '266');
INSERT INTO `goods_desc` VALUES ('179', '105', './Public/upload/goods/thumb160414130918_88.jpg', '34', '300');
INSERT INTO `goods_desc` VALUES ('180', '105', './Public/upload/goods/thumb160414130918_26.jpg', '', '236');
INSERT INTO `goods_desc` VALUES ('181', '106', './Public/upload/goods/thumb160414131102_90.png', '2234', '402');
INSERT INTO `goods_desc` VALUES ('182', '106', './Public/upload/goods/thumb160414131102_85.png', '2234', '397');
INSERT INTO `goods_desc` VALUES ('183', '106', './Public/upload/goods/thumb160414131102_44.png', '2234', '402');
INSERT INTO `goods_desc` VALUES ('184', '107', './Public/upload/goods/thumb160414131113_96.png', '2234', '402');
INSERT INTO `goods_desc` VALUES ('185', '107', './Public/upload/goods/thumb160414131113_73.png', '2234', '397');
INSERT INTO `goods_desc` VALUES ('187', '108', './Public/upload/goods/thumb160414131210_46.png', '', '285');
INSERT INTO `goods_desc` VALUES ('188', '108', './Public/upload/goods/thumb160414131210_84.png', '', '285');
INSERT INTO `goods_desc` VALUES ('189', '108', './Public/upload/goods/thumb160414131210_92.png', '', '285');
INSERT INTO `goods_desc` VALUES ('190', '108', './Public/upload/goods/thumb160414131210_89.png', '', '285');
INSERT INTO `goods_desc` VALUES ('191', '108', './Public/upload/goods/thumb160414131210_91.png', '', '285');
INSERT INTO `goods_desc` VALUES ('192', '108', './Public/upload/goods/thumb160414131210_22.png', '', '285');
INSERT INTO `goods_desc` VALUES ('193', '108', './Public/upload/goods/thumb160414131210_23.png', '', '285');
INSERT INTO `goods_desc` VALUES ('194', '109', './Public/upload/goods/thumb160417104944_78.png', '', '285');
INSERT INTO `goods_desc` VALUES ('195', '110', './Public/upload/goods/thumb160417110952_94.png', '哈哈', '285');
INSERT INTO `goods_desc` VALUES ('196', '110', './Public/upload/goods/thumb160417110952_47.png', '', '285');
INSERT INTO `goods_desc` VALUES ('197', '110', './Public/upload/goods/thumb160417110952_41.png', '', '285');
INSERT INTO `goods_desc` VALUES ('198', '110', './Public/upload/goods/thumb160417110952_74.png', '', '285');
INSERT INTO `goods_desc` VALUES ('199', '110', './Public/upload/goods/thumb160417110952_52.png', '', '285');
INSERT INTO `goods_desc` VALUES ('459', '216', './Public/upload/goods/thumb1461375104_1829922878.png', '', '266');
INSERT INTO `goods_desc` VALUES ('201', '111', './Public/upload/goods/thumb160417112459_26.png', '', '285');
INSERT INTO `goods_desc` VALUES ('202', '111', './Public/upload/goods/thumb160417112459_17.png', '', '285');
INSERT INTO `goods_desc` VALUES ('203', '111', './Public/upload/goods/thumb160417112459_87.png', '', '285');
INSERT INTO `goods_desc` VALUES ('451', '212', './Public/upload/goods/thumb1461314158_146836305.jpg', '', '533');
INSERT INTO `goods_desc` VALUES ('452', '212', './Public/upload/goods/thumb1461314158_1201141776.jpg', '', '533');
INSERT INTO `goods_desc` VALUES ('453', '213', './Public/upload/goods/thumb1461322826_485736156.jpg', '', '710');
INSERT INTO `goods_desc` VALUES ('454', '213', './Public/upload/goods/thumb1461322826_671428597.jpg', '', '710');
INSERT INTO `goods_desc` VALUES ('455', '214', './Public/upload/goods/thumb1461322944_86321243.jpg', '', '533');
INSERT INTO `goods_desc` VALUES ('456', '214', './Public/upload/goods/thumb1461322944_1396702848.jpg', '', '712');
INSERT INTO `goods_desc` VALUES ('457', '215', './Public/upload/goods/thumb1461323517_121439246.jpg', '', '533');
INSERT INTO `goods_desc` VALUES ('458', '215', './Public/upload/goods/thumb1461323517_1699213936.jpg', '', '533');
INSERT INTO `goods_desc` VALUES ('232', '114', './Public/upload/goods/thumb160417171838_60.png', '', '285');
INSERT INTO `goods_desc` VALUES ('233', '114', './Public/upload/goods/thumb160417171838_50.png', '', '285');
INSERT INTO `goods_desc` VALUES ('234', '114', './Public/upload/goods/thumb160417171838_94.png', '', '285');
INSERT INTO `goods_desc` VALUES ('235', '114', './Public/upload/goods/thumb160417171838_56.png', '', '285');
INSERT INTO `goods_desc` VALUES ('236', '114', './Public/upload/goods/thumb160417171838_51.png', '', '285');
INSERT INTO `goods_desc` VALUES ('237', '114', './Public/upload/goods/thumb160417171838_13.png', '', '285');
INSERT INTO `goods_desc` VALUES ('238', '114', './Public/upload/goods/thumb160417171838_42.png', '', '285');
INSERT INTO `goods_desc` VALUES ('239', '114', './Public/upload/goods/thumb160417171838_96.png', '', '285');
INSERT INTO `goods_desc` VALUES ('240', '114', './Public/upload/goods/thumb160417171838_83.png', '', '285');
INSERT INTO `goods_desc` VALUES ('241', '114', './Public/upload/goods/thumb160417171838_49.png', '', '285');
INSERT INTO `goods_desc` VALUES ('242', '114', './Public/upload/goods/thumb160417171838_34.png', '', '285');
INSERT INTO `goods_desc` VALUES ('243', '115', './Public/upload/goods/thumb160417174544_57.png', '2234', '542');
INSERT INTO `goods_desc` VALUES ('244', '115', './Public/upload/goods/thumb160417174544_98.png', '34343', '542');
INSERT INTO `goods_desc` VALUES ('245', '115', './Public/upload/goods/thumb160417174544_36.png', '34343', '542');
INSERT INTO `goods_desc` VALUES ('246', '115', './Public/upload/goods/thumb160417174544_60.png', '34', '542');
INSERT INTO `goods_desc` VALUES ('247', '116', './Public/upload/goods/thumb57135fa1911e3.png', '2234', '542');
INSERT INTO `goods_desc` VALUES ('248', '116', './Public/upload/goods/thumb57135fa191d9b.png', '34343', '542');
INSERT INTO `goods_desc` VALUES ('249', '116', './Public/upload/goods/thumb57135fa19256b.png', '34343', '542');
INSERT INTO `goods_desc` VALUES ('250', '116', './Public/upload/goods/thumb57135fa193123.png', '34', '542');
INSERT INTO `goods_desc` VALUES ('251', '117', './Public/upload/goods/thumb57135fa7b1eb2.png', '2234', '542');
INSERT INTO `goods_desc` VALUES ('252', '117', './Public/upload/goods/thumb57135fa7b2a6a.png', '34343', '542');
INSERT INTO `goods_desc` VALUES ('253', '117', './Public/upload/goods/thumb57135fa7b323a.png', '34343', '542');
INSERT INTO `goods_desc` VALUES ('254', '117', './Public/upload/goods/thumb57135fa7b3a0a.png', '34', '542');
INSERT INTO `goods_desc` VALUES ('255', '118', './Public/upload/goods/thumb57135fd5c4a0d.png', '2234', '542');
INSERT INTO `goods_desc` VALUES ('256', '118', './Public/upload/goods/thumb57135fd5c51dd.png', '34343', '542');
INSERT INTO `goods_desc` VALUES ('257', '118', './Public/upload/goods/thumb57135fd5c59ad.png', '34343', '542');
INSERT INTO `goods_desc` VALUES ('258', '118', './Public/upload/goods/thumb57135fd5c6565.png', '34', '542');
INSERT INTO `goods_desc` VALUES ('259', '118', './Public/upload/goods/thumb57135fd5c6d35.png', '343', '542');
INSERT INTO `goods_desc` VALUES ('260', '118', './Public/upload/goods/thumb57135fd5c7506.png', '34', '542');
INSERT INTO `goods_desc` VALUES ('261', '118', './Public/upload/goods/thumb57135fd5c7cd6.png', '343', '542');
INSERT INTO `goods_desc` VALUES ('262', '119', './Public/upload/goods/thumb571366119ca82.png', '2234', '542');
INSERT INTO `goods_desc` VALUES ('263', '119', './Public/upload/goods/thumb571366119d252.jpg', '34343', '296');
INSERT INTO `goods_desc` VALUES ('264', '119', './Public/upload/goods/thumb571366119de0a.png', '34343', '542');
INSERT INTO `goods_desc` VALUES ('265', '120', './Public/upload/goods/thumb5713679baaf2c.png', '2234', '542');
INSERT INTO `goods_desc` VALUES ('266', '120', './Public/upload/goods/thumb5713679bab6fd.jpg', '34343', '296');
INSERT INTO `goods_desc` VALUES ('267', '120', './Public/upload/goods/thumb5713679babecd.png', '34343', '542');
INSERT INTO `goods_desc` VALUES ('268', '120', './Public/upload/goods/thumb5713679bac69d.png', '34', '542');
INSERT INTO `goods_desc` VALUES ('269', '120', './Public/upload/goods/thumb5713679bace6d.png', '343', '542');
INSERT INTO `goods_desc` VALUES ('270', '120', './Public/upload/goods/thumb5713679bada25.png', '34', '542');
INSERT INTO `goods_desc` VALUES ('271', '120', './Public/upload/goods/thumb5713679bae1f5.png', '343', '542');
INSERT INTO `goods_desc` VALUES ('272', '121', './Public/upload/goods/thumb571367b99e6d5.png', '2234', '542');
INSERT INTO `goods_desc` VALUES ('273', '121', './Public/upload/goods/thumb571367b99eea6.jpg', '34343', '296');
INSERT INTO `goods_desc` VALUES ('274', '121', './Public/upload/goods/thumb571367b99fa5e.png', '34343', '542');
INSERT INTO `goods_desc` VALUES ('275', '121', './Public/upload/goods/thumb571367b9a022e.png', '34', '542');
INSERT INTO `goods_desc` VALUES ('276', '121', './Public/upload/goods/thumb571367b9a09fe.png', '343', '542');
INSERT INTO `goods_desc` VALUES ('277', '121', './Public/upload/goods/thumb571367b9a11ce.png', '34', '542');
INSERT INTO `goods_desc` VALUES ('278', '121', './Public/upload/goods/thumb571367b9a1d86.png', '343', '542');
INSERT INTO `goods_desc` VALUES ('279', '121', './Public/upload/goods/thumb571367b9a293e.png', '34', '542');
INSERT INTO `goods_desc` VALUES ('280', '122', './Public/upload/goods/thumb57136a427dfbe.png', '2234', '542');
INSERT INTO `goods_desc` VALUES ('281', '122', './Public/upload/goods/thumb57136a427e78f.jpg', '34343', '296');
INSERT INTO `goods_desc` VALUES ('282', '122', './Public/upload/goods/thumb57136a427ef5f.png', '34343', '542');
INSERT INTO `goods_desc` VALUES ('283', '122', './Public/upload/goods/thumb57136a427f72f.png', '34', '542');
INSERT INTO `goods_desc` VALUES ('284', '122', './Public/upload/goods/thumb57136a42802e7.png', '343', '542');
INSERT INTO `goods_desc` VALUES ('285', '122', './Public/upload/goods/thumb57136a4280ab7.png', '34', '542');
INSERT INTO `goods_desc` VALUES ('286', '122', './Public/upload/goods/thumb57136a428166f.png', '343', '542');
INSERT INTO `goods_desc` VALUES ('287', '122', './Public/upload/goods/thumb57136a4282227.png', '34', '542');
INSERT INTO `goods_desc` VALUES ('288', '123', './Public/upload/goods/thumb57143548581ad.png', '2234', '542');
INSERT INTO `goods_desc` VALUES ('289', '123', './Public/upload/goods/thumb571435485897d.png', '34343', '542');
INSERT INTO `goods_desc` VALUES ('290', '124', './Public/upload/goods/thumb5714358a14e84.png', '2234', '542');
INSERT INTO `goods_desc` VALUES ('291', '124', './Public/upload/goods/thumb5714358a15655.png', '34343', '542');
INSERT INTO `goods_desc` VALUES ('292', '125', './Public/upload/goods/thumb1460942576_1497026306.png', '2234', '542');
INSERT INTO `goods_desc` VALUES ('293', '125', './Public/upload/goods/thumb1460942576_664738404.png', '34343', '542');
INSERT INTO `goods_desc` VALUES ('294', '125', './Public/upload/goods/thumb1460942576_1545729743.png', '34343', '542');
INSERT INTO `goods_desc` VALUES ('295', '125', './Public/upload/goods/thumb1460942576_196246057.png', '34', '542');
INSERT INTO `goods_desc` VALUES ('296', '125', './Public/upload/goods/thumb1460942576_1865764147.png', '343', '542');
INSERT INTO `goods_desc` VALUES ('297', '126', './Public/upload/goods/thumb1460942593_632129452.png', '2234', '542');
INSERT INTO `goods_desc` VALUES ('298', '126', './Public/upload/goods/thumb1460942593_1110426686.png', '34343', '542');
INSERT INTO `goods_desc` VALUES ('299', '126', './Public/upload/goods/thumb1460942593_2128541116.png', '34343', '542');
INSERT INTO `goods_desc` VALUES ('300', '126', './Public/upload/goods/thumb1460942593_717678654.png', '34', '542');
INSERT INTO `goods_desc` VALUES ('301', '126', './Public/upload/goods/thumb1460942593_2087694427.png', '343', '542');
INSERT INTO `goods_desc` VALUES ('302', '126', './Public/upload/goods/thumb1460942593_724006321.png', '34', '542');
INSERT INTO `goods_desc` VALUES ('303', '127', './Public/upload/goods/thumb1460942608_1465079414.png', '2234', '542');
INSERT INTO `goods_desc` VALUES ('304', '127', './Public/upload/goods/thumb1460942608_701935254.png', '34343', '542');
INSERT INTO `goods_desc` VALUES ('305', '127', './Public/upload/goods/thumb1460942608_1679373888.png', '34343', '542');
INSERT INTO `goods_desc` VALUES ('306', '127', './Public/upload/goods/thumb1460942608_1874977133.png', '34', '542');
INSERT INTO `goods_desc` VALUES ('307', '127', './Public/upload/goods/thumb1460942608_1839094106.png', '343', '542');
INSERT INTO `goods_desc` VALUES ('308', '127', './Public/upload/goods/thumb1460942608_1229415822.png', '34', '542');
INSERT INTO `goods_desc` VALUES ('309', '127', './Public/upload/goods/thumb1460942608_2039070740.png', '343', '542');
INSERT INTO `goods_desc` VALUES ('310', '128', './Public/upload/goods/thumb1460956982_88893593.png', '', '285');
INSERT INTO `goods_desc` VALUES ('311', '128', './Public/upload/goods/thumb1460956982_1045861164.png', '', '285');
INSERT INTO `goods_desc` VALUES ('312', '128', './Public/upload/goods/thumb1460956982_86075012.png', '', '285');
INSERT INTO `goods_desc` VALUES ('313', '128', './Public/upload/goods/thumb1460956982_659059958.png', '', '285');
INSERT INTO `goods_desc` VALUES ('314', '128', './Public/upload/goods/thumb1460956982_1129243190.png', '', '285');
INSERT INTO `goods_desc` VALUES ('315', '129', './Public/upload/goods/thumb1461115847_1818198735.png', '', '285');
INSERT INTO `goods_desc` VALUES ('316', '129', './Public/upload/goods/thumb1461115847_10593243.png', '', '285');
INSERT INTO `goods_desc` VALUES ('317', '129', './Public/upload/goods/thumb1461115847_2097286412.png', '', '285');
INSERT INTO `goods_desc` VALUES ('318', '129', './Public/upload/goods/thumb1461115847_245605787.png', '', '285');
INSERT INTO `goods_desc` VALUES ('319', '129', './Public/upload/goods/thumb1461115847_2008683912.png', '', '285');
INSERT INTO `goods_desc` VALUES ('320', '130', './Public/upload/goods/thumb1461116979_1981594516.png', '', '285');
INSERT INTO `goods_desc` VALUES ('321', '130', './Public/upload/goods/thumb1461116979_187026137.png', '', '285');
INSERT INTO `goods_desc` VALUES ('322', '130', './Public/upload/goods/thumb1461116979_701256275.png', '', '285');
INSERT INTO `goods_desc` VALUES ('323', '131', './Public/upload/goods/thumb1461117099_501249714.png', '', '285');
INSERT INTO `goods_desc` VALUES ('324', '131', './Public/upload/goods/thumb1461117099_1491233385.png', '', '285');
INSERT INTO `goods_desc` VALUES ('325', '132', './Public/upload/goods/thumb1461117579_1505366075.png', '', '285');
INSERT INTO `goods_desc` VALUES ('326', '132', './Public/upload/goods/thumb1461117579_706706662.png', '', '285');
INSERT INTO `goods_desc` VALUES ('327', '133', './Public/upload/goods/thumb1461120830_1991122548.png', '', '285');
INSERT INTO `goods_desc` VALUES ('328', '133', './Public/upload/goods/thumb1461120830_615344328.png', '', '285');
INSERT INTO `goods_desc` VALUES ('329', '133', './Public/upload/goods/thumb1461120830_333824341.png', '', '285');
INSERT INTO `goods_desc` VALUES ('330', '134', './Public/upload/goods/thumb1461120930_304773336.png', '', '285');
INSERT INTO `goods_desc` VALUES ('331', '134', './Public/upload/goods/thumb1461120930_1444844053.png', '', '285');
INSERT INTO `goods_desc` VALUES ('332', '134', './Public/upload/goods/thumb1461120930_1246182589.png', '', '285');
INSERT INTO `goods_desc` VALUES ('333', '134', './Public/upload/goods/thumb1461120930_1485231893.png', '', '285');
INSERT INTO `goods_desc` VALUES ('334', '134', './Public/upload/goods/thumb1461120930_1857260598.png', '', '285');
INSERT INTO `goods_desc` VALUES ('335', '135', './Public/upload/goods/thumb1461121045_224183725.png', '', '285');
INSERT INTO `goods_desc` VALUES ('336', '135', './Public/upload/goods/thumb1461121045_862272445.png', '', '285');
INSERT INTO `goods_desc` VALUES ('337', '135', './Public/upload/goods/thumb1461121045_1328156483.png', '', '285');
INSERT INTO `goods_desc` VALUES ('338', '135', './Public/upload/goods/thumb1461121045_779825180.png', '', '285');
INSERT INTO `goods_desc` VALUES ('339', '135', './Public/upload/goods/thumb1461121045_441911432.png', '', '285');
INSERT INTO `goods_desc` VALUES ('340', '136', './Public/upload/goods/thumb1461126564_1486879035.png', '', '285');
INSERT INTO `goods_desc` VALUES ('341', '137', './Public/upload/goods/thumb1461126669_2113101219.png', '', '285');
INSERT INTO `goods_desc` VALUES ('342', '138', './Public/upload/goods/thumb1461126899_1025470456.png', '', '285');
INSERT INTO `goods_desc` VALUES ('343', '138', './Public/upload/goods/thumb1461126899_1956058478.png', '', '285');
INSERT INTO `goods_desc` VALUES ('344', '138', './Public/upload/goods/thumb1461126899_1563675267.png', '', '285');
INSERT INTO `goods_desc` VALUES ('345', '138', './Public/upload/goods/thumb1461126899_2134025713.png', '', '285');
INSERT INTO `goods_desc` VALUES ('346', '139', './Public/upload/goods/thumb1461127274_1797455341.png', '', '285');
INSERT INTO `goods_desc` VALUES ('347', '139', './Public/upload/goods/thumb1461127274_757878801.png', '', '285');
INSERT INTO `goods_desc` VALUES ('348', '139', './Public/upload/goods/thumb1461127274_1034084775.png', '', '285');
INSERT INTO `goods_desc` VALUES ('349', '139', './Public/upload/goods/thumb1461127274_105275020.png', '', '285');
INSERT INTO `goods_desc` VALUES ('350', '139', './Public/upload/goods/thumb1461127274_638993129.png', '', '285');
INSERT INTO `goods_desc` VALUES ('351', '140', './Public/upload/goods/thumb1461127778_1062515933.png', '', '285');
INSERT INTO `goods_desc` VALUES ('352', '140', './Public/upload/goods/thumb1461127778_250221812.png', '', '285');
INSERT INTO `goods_desc` VALUES ('353', '140', './Public/upload/goods/thumb1461127778_914667091.png', '', '285');
INSERT INTO `goods_desc` VALUES ('354', '141', './Public/upload/goods/thumb1461128060_850319578.png', '', '285');
INSERT INTO `goods_desc` VALUES ('355', '141', './Public/upload/goods/thumb1461128060_2012487518.png', '', '285');
INSERT INTO `goods_desc` VALUES ('356', '142', './Public/upload/goods/thumb1461128445_823884167.png', '2234', '400');
INSERT INTO `goods_desc` VALUES ('357', '143', './Public/upload/goods/thumb1461128604_1941645359.png', '恩\n', '285');
INSERT INTO `goods_desc` VALUES ('358', '143', './Public/upload/goods/thumb1461128604_332937847.png', '', '285');
INSERT INTO `goods_desc` VALUES ('359', '143', './Public/upload/goods/thumb1461128604_761505678.png', '哦耶\n', '285');
INSERT INTO `goods_desc` VALUES ('360', '148', './Public/upload/goods/thumb1461130326_1270819798.png', '', '285');
INSERT INTO `goods_desc` VALUES ('361', '148', './Public/upload/goods/thumb1461130326_883605158.png', '', '285');
INSERT INTO `goods_desc` VALUES ('362', '149', './Public/upload/goods/thumb1461130358_735608663.png', '2234', '400');
INSERT INTO `goods_desc` VALUES ('363', '150', './Public/upload/goods/thumb1461130366_1148035854.png', '2234', '400');
INSERT INTO `goods_desc` VALUES ('364', '151', './Public/upload/goods/thumb1461130383_1305634085.png', '2234', '400');
INSERT INTO `goods_desc` VALUES ('365', '152', './Public/upload/goods/thumb1461130392_827792106.png', '2234', '400');
INSERT INTO `goods_desc` VALUES ('366', '154', './Public/upload/goods/thumb1461131488_1490009751.png', '还好啦我自己都市里边有人在一起了。你的时候就是没想到', '285');
INSERT INTO `goods_desc` VALUES ('367', '155', './Public/upload/goods/thumb1461131588_1992792111.png', '推荐啦 black 健健康康的成长过程的监督管理委员会批准了。你们的话就要开始啦啦的健健康康快快乐乐健健康康快快乐乐健健康康快快乐乐健健康康快快乐乐健健康康快快乐乐健健康康快快乐乐健健康康快快乐乐健健康康快快乐乐健健康康', '285');
INSERT INTO `goods_desc` VALUES ('368', '155', './Public/upload/goods/thumb1461131588_1090625915.png', '还健健康康快快乐乐健健康康快快乐乐健健康康快快乐乐健健康康快快乐乐健健康康快快乐乐健健康康快快乐乐健健康康快快乐乐健健康康快快乐乐健健康康快快乐乐健健康康快快乐乐健健康康快快乐乐健健康康快快乐乐健健康康', '285');
INSERT INTO `goods_desc` VALUES ('369', '158', './Public/upload/goods/thumb1461132286_613661909.png', '2234', '400');
INSERT INTO `goods_desc` VALUES ('370', '165', './Public/upload/goods/thumb1461135948_884043895.jpg', '这是一段商品描述', '400');
INSERT INTO `goods_desc` VALUES ('371', '166', './Public/upload/goods/thumb1461136319_516264522.jpg', '这是一段商品描述', '400');
INSERT INTO `goods_desc` VALUES ('372', '166', './Public/upload/goods/thumb1461136319_172511754.jpg', '这是一段商品描述', '400');
INSERT INTO `goods_desc` VALUES ('373', '167', './Public/upload/goods/thumb1461137019_2141059451.jpg', '1111', '400');
INSERT INTO `goods_desc` VALUES ('374', '168', './Public/upload/goods/thumb1461137504_633216019.jpg', '1', '400');
INSERT INTO `goods_desc` VALUES ('375', '168', './Public/upload/goods/thumb1461137504_931507255.jpg', '2', '400');
INSERT INTO `goods_desc` VALUES ('376', '169', './Public/upload/goods/thumb1461137744_1611289185.jpg', '1', '400');
INSERT INTO `goods_desc` VALUES ('377', '169', './Public/upload/goods/thumb1461137744_1754135996.jpg', '2', '400');
INSERT INTO `goods_desc` VALUES ('378', '170', './Public/upload/goods/thumb1461138187_170244328.jpg', '', '400');
INSERT INTO `goods_desc` VALUES ('379', '170', './Public/upload/goods/thumb1461138187_337879675.jpg', '', '400');
INSERT INTO `goods_desc` VALUES ('380', '171', './Public/upload/goods/thumb1461138310_176163171.png', '', '711');
INSERT INTO `goods_desc` VALUES ('381', '171', './Public/upload/goods/thumb1461138310_196338189.jpg', '', '400');
INSERT INTO `goods_desc` VALUES ('382', '171', './Public/upload/goods/thumb1461138310_35294158.jpg', '', '400');
INSERT INTO `goods_desc` VALUES ('383', '171', './Public/upload/goods/thumb1461138310_1786801689.jpg', '', '400');
INSERT INTO `goods_desc` VALUES ('384', '171', './Public/upload/goods/thumb1461138310_1046387334.jpg', '', '400');
INSERT INTO `goods_desc` VALUES ('385', '171', './Public/upload/goods/thumb1461138310_1952782582.jpg', '', '400');
INSERT INTO `goods_desc` VALUES ('386', '171', './Public/upload/goods/thumb1461138310_641824199.jpg', '', '400');
INSERT INTO `goods_desc` VALUES ('460', '216', './Public/upload/goods/thumb1461375104_873466771.png', '', '265');
INSERT INTO `goods_desc` VALUES ('461', '217', './Public/upload/goods/thumb1461375578_1161675412.png', '', '265');
INSERT INTO `goods_desc` VALUES ('462', '218', './Public/upload/goods/thumb1461475072_1187624415.jpg', '', '533');
INSERT INTO `goods_desc` VALUES ('463', '218', './Public/upload/goods/thumb1461475072_37190564.jpg', '', '533');
INSERT INTO `goods_desc` VALUES ('464', '219', './Public/upload/goods/thumb1461476251_687437950.jpg', '', '533');
INSERT INTO `goods_desc` VALUES ('465', '219', './Public/upload/goods/thumb1461476251_1684622950.jpg', '', '533');
INSERT INTO `goods_desc` VALUES ('466', '220', './Public/upload/goods/thumb1461476566_741436095.jpg', '', '533');
INSERT INTO `goods_desc` VALUES ('467', '221', './Public/upload/goods/thumb1462158698_2069804473.png', '', '712');
INSERT INTO `goods_desc` VALUES ('468', '221', './Public/upload/goods/thumb1462158698_813975329.png', '', '712');
INSERT INTO `goods_desc` VALUES ('469', '221', './Public/upload/goods/thumb1462158698_817331537.png', '', '712');
INSERT INTO `goods_desc` VALUES ('470', '221', './Public/upload/goods/thumb1462158698_1942587196.png', '', '712');
INSERT INTO `goods_desc` VALUES ('471', '221', './Public/upload/goods/thumb1462158698_1063620921.png', '', '712');
INSERT INTO `goods_desc` VALUES ('472', '221', './Public/upload/goods/thumb1462158698_612134436.png', '', '712');
INSERT INTO `goods_desc` VALUES ('473', '221', './Public/upload/goods/thumb1462158698_1526899428.png', '', '299');

-- ----------------------------
-- Table structure for `integral_info`
-- ----------------------------
DROP TABLE IF EXISTS `integral_info`;
CREATE TABLE `integral_info` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `integral` int(11) unsigned NOT NULL COMMENT '积分数',
  `info` varchar(255) NOT NULL COMMENT '详情',
  `type` tinyint(3) unsigned NOT NULL COMMENT '1加2减',
  `time` varchar(10) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1正常0冻结',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=475 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of integral_info
-- ----------------------------
INSERT INTO `integral_info` VALUES ('2', '1', '2', '购买产品好吃1', '1', '2016-04-22', '1');
INSERT INTO `integral_info` VALUES ('3', '1', '1', '分享信息', '1', '2016-04-22', '1');
INSERT INTO `integral_info` VALUES ('4', '1', '1', '分享信息', '1', '2016-04-22', '1');
INSERT INTO `integral_info` VALUES ('5', '6', '2', '购买产品', '1', '2016-04-22', '1');
INSERT INTO `integral_info` VALUES ('6', '6', '2', '购买产品', '1', '2016-04-22', '1');
INSERT INTO `integral_info` VALUES ('7', '6', '2', '购买产品', '1', '2016-04-22', '1');
INSERT INTO `integral_info` VALUES ('8', '6', '2000', '购买产品', '1', '2016-04-22', '1');
INSERT INTO `integral_info` VALUES ('9', '6', '2', '购买产品', '1', '2016-04-22', '1');
INSERT INTO `integral_info` VALUES ('10', '6', '2', '购买产品', '1', '2016-04-22', '1');
INSERT INTO `integral_info` VALUES ('11', '6', '2', '购买产品', '1', '2016-04-22', '1');
INSERT INTO `integral_info` VALUES ('12', '6', '2', '购买产品', '1', '2016-04-22', '1');
INSERT INTO `integral_info` VALUES ('13', '6', '2', '购买产品', '1', '2016-04-22', '1');
INSERT INTO `integral_info` VALUES ('14', '6', '2', '购买产品', '1', '2016-04-22', '1');
INSERT INTO `integral_info` VALUES ('15', '6', '2', '购买产品', '1', '2016-04-22', '1');
INSERT INTO `integral_info` VALUES ('16', '6', '2', '购买产品', '1', '2016-04-22', '1');
INSERT INTO `integral_info` VALUES ('17', '6', '2', '购买产品', '1', '2016-04-22', '1');
INSERT INTO `integral_info` VALUES ('18', '6', '2', '购买产品', '1', '2016-04-22', '1');
INSERT INTO `integral_info` VALUES ('19', '6', '2', '购买产品', '1', '2016-04-22', '1');
INSERT INTO `integral_info` VALUES ('20', '6', '2', '购买产品', '1', '2016-04-22', '1');
INSERT INTO `integral_info` VALUES ('21', '6', '2', '购买产品', '1', '2016-04-22', '1');
INSERT INTO `integral_info` VALUES ('22', '6', '1', '评价商品', '1', '2016-04-23', '1');
INSERT INTO `integral_info` VALUES ('23', '6', '1', '评价商品', '1', '2016-04-23', '1');
INSERT INTO `integral_info` VALUES ('24', '6', '1', '评价商品', '1', '2016-04-23', '1');
INSERT INTO `integral_info` VALUES ('25', '6', '1', '评价商品', '1', '2016-04-23', '1');
INSERT INTO `integral_info` VALUES ('26', '6', '1', '评价商品', '1', '2016-04-23', '1');
INSERT INTO `integral_info` VALUES ('27', '6', '2', '购买产品', '1', '2016-04-23', '1');
INSERT INTO `integral_info` VALUES ('28', '6', '1', '评价商品', '1', '2016-04-23', '1');
INSERT INTO `integral_info` VALUES ('29', '6', '1', '评价商品', '1', '2016-04-23', '1');
INSERT INTO `integral_info` VALUES ('30', '6', '2', '购买产品', '1', '2016-04-23', '1');
INSERT INTO `integral_info` VALUES ('31', '6', '2', '购买产品', '1', '2016-04-23', '1');
INSERT INTO `integral_info` VALUES ('32', '6', '2', '购买产品', '1', '2016-04-23', '1');
INSERT INTO `integral_info` VALUES ('33', '6', '2', '购买产品', '1', '2016-04-23', '1');
INSERT INTO `integral_info` VALUES ('34', '6', '2', '购买产品', '1', '2016-04-23', '1');
INSERT INTO `integral_info` VALUES ('35', '1', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('36', '11', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('37', '11', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('38', '6', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('39', '6', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('40', '6', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('41', '6', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('42', '6', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('43', '6', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('44', '6', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('45', '6', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('46', '6', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('47', '6', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('48', '6', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('49', '6', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('50', '6', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('51', '6', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('52', '11', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('53', '11', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('54', '6', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('55', '11', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('56', '11', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('57', '11', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('58', '11', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('59', '11', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('60', '11', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('61', '11', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('62', '11', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('63', '11', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('64', '11', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('65', '11', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('66', '11', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('67', '11', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('68', '11', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('69', '11', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('70', '6', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('71', '11', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('72', '11', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('73', '11', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('74', '11', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('75', '11', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('76', '11', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('77', '11', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('78', '11', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('79', '11', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('80', '11', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('81', '11', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('82', '11', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('83', '11', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('84', '11', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('85', '11', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('86', '11', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('87', '11', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('88', '11', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('89', '6', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('90', '11', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('91', '11', '2', '购买产品', '1', '2016-04-24', '1');
INSERT INTO `integral_info` VALUES ('92', '6', '1', '评价商品', '1', '2016-04-25', '1');
INSERT INTO `integral_info` VALUES ('93', '11', '2', '购买产品', '1', '2016-04-25', '1');
INSERT INTO `integral_info` VALUES ('94', '11', '2', '购买产品', '1', '2016-04-25', '1');
INSERT INTO `integral_info` VALUES ('95', '6', '2', '购买产品', '1', '2016-04-25', '1');
INSERT INTO `integral_info` VALUES ('96', '6', '2', '购买产品', '1', '2016-04-25', '1');
INSERT INTO `integral_info` VALUES ('97', '6', '2', '购买产品', '1', '2016-04-25', '1');
INSERT INTO `integral_info` VALUES ('98', '6', '2', '购买产品', '1', '2016-04-25', '1');
INSERT INTO `integral_info` VALUES ('99', '6', '2', '购买产品', '1', '2016-04-25', '1');
INSERT INTO `integral_info` VALUES ('100', '6', '2', '购买产品', '1', '2016-04-25', '1');
INSERT INTO `integral_info` VALUES ('101', '6', '2', '购买产品', '1', '2016-04-25', '1');
INSERT INTO `integral_info` VALUES ('102', '6', '2', '购买产品', '1', '2016-04-25', '1');
INSERT INTO `integral_info` VALUES ('103', '6', '2', '购买产品', '1', '2016-04-25', '1');
INSERT INTO `integral_info` VALUES ('104', '6', '2', '购买产品', '1', '2016-04-25', '1');
INSERT INTO `integral_info` VALUES ('105', '6', '2', '购买产品', '1', '2016-04-25', '1');
INSERT INTO `integral_info` VALUES ('106', '6', '2', '购买产品', '1', '2016-04-25', '1');
INSERT INTO `integral_info` VALUES ('107', '6', '2', '购买产品', '1', '2016-04-25', '1');
INSERT INTO `integral_info` VALUES ('108', '6', '1', '评价商品', '1', '2016-04-25', '1');
INSERT INTO `integral_info` VALUES ('109', '1', '1', '分享信息', '1', '2016-04-25', '1');
INSERT INTO `integral_info` VALUES ('110', '11', '2', '购买产品', '1', '2016-04-25', '1');
INSERT INTO `integral_info` VALUES ('111', '11', '2', '购买产品', '1', '2016-04-25', '1');
INSERT INTO `integral_info` VALUES ('112', '6', '1', '评价商品', '1', '2016-04-25', '1');
INSERT INTO `integral_info` VALUES ('113', '6', '1', '评价商品', '1', '2016-04-25', '1');
INSERT INTO `integral_info` VALUES ('114', '6', '1', '评价商品', '1', '2016-04-25', '1');
INSERT INTO `integral_info` VALUES ('115', '6', '1', '评价商品', '1', '2016-04-25', '1');
INSERT INTO `integral_info` VALUES ('116', '6', '1', '评价商品', '1', '2016-04-25', '1');
INSERT INTO `integral_info` VALUES ('117', '6', '1', '评价商品', '1', '2016-04-25', '1');
INSERT INTO `integral_info` VALUES ('118', '6', '2', '购买产品', '1', '2016-04-25', '1');
INSERT INTO `integral_info` VALUES ('119', '6', '2', '购买产品', '1', '2016-04-25', '1');
INSERT INTO `integral_info` VALUES ('120', '6', '2', '购买产品', '1', '2016-04-26', '1');
INSERT INTO `integral_info` VALUES ('121', '6', '2', '购买产品', '1', '2016-04-26', '1');
INSERT INTO `integral_info` VALUES ('122', '6', '2', '购买产品', '1', '2016-04-26', '1');
INSERT INTO `integral_info` VALUES ('123', '1', '1', '分享信息', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('124', '11', '1', '分享信息', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('125', '11', '1', '分享信息', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('126', '11', '1', '分享信息', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('127', '11', '1', '分享信息', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('128', '6', '2', '购买产品', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('129', '11', '2', '购买产品', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('130', '6', '2', '购买产品', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('131', '1', '2', '购买产品', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('132', '1', '2', '购买产品', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('133', '1', '2', '购买产品', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('134', '1', '2', '购买产品', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('135', '6', '2', '购买产品', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('136', '1', '2', '兑换优惠券', '2', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('137', '11', '2', '兑换优惠券', '2', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('138', '6', '2', '兑换优惠券', '2', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('139', '11', '2', '购买产品', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('140', '11', '2', '购买产品', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('141', '6', '2', '购买产品', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('142', '6', '2', '购买产品', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('143', '11', '2', '购买产品', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('144', '6', '150', '兑换优惠券', '2', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('145', '6', '2', '购买产品', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('146', '6', '100', '兑换优惠券', '2', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('147', '6', '10', '兑换配送券', '2', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('148', '6', '10', '兑换配送券', '2', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('149', '6', '10', '兑换配送券', '2', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('150', '6', '10', '兑换配送券', '2', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('151', '6', '10', '兑换配送券', '2', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('152', '6', '10', '兑换配送券', '2', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('153', '6', '10', '兑换配送券', '2', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('154', '6', '10', '兑换配送券', '2', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('155', '6', '100', '兑换配送券', '2', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('156', '11', '2', '购买产品', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('157', '11', '2', '购买产品', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('158', '11', '2', '购买产品', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('159', '11', '2', '购买产品', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('160', '11', '2', '购买产品', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('161', '11', '2', '购买产品', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('162', '11', '2', '购买产品', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('163', '11', '2', '购买产品', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('164', '11', '1', '分享信息', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('165', '11', '1', '分享信息', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('166', '11', '1', '分享信息', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('167', '11', '1', '分享信息', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('168', '11', '1', '分享信息', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('169', '11', '1', '分享信息', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('170', '11', '1', '分享信息', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('171', '11', '1', '分享信息', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('172', '11', '1', '分享信息', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('173', '11', '1', '分享信息', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('174', '11', '1', '分享信息', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('175', '11', '1', '分享信息', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('176', '11', '1', '分享信息', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('177', '11', '1', '分享信息', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('178', '11', '1', '分享信息', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('179', '11', '1', '分享信息', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('180', '11', '1', '分享信息', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('181', '11', '1', '分享信息', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('182', '11', '1', '分享信息', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('183', '11', '1', '分享信息', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('184', '11', '1', '分享信息', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('185', '11', '1', '分享信息', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('186', '11', '1', '分享信息', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('187', '11', '1', '分享信息', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('188', '11', '1', '分享信息', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('189', '11', '1', '分享信息', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('190', '11', '1', '分享信息', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('191', '11', '1', '分享信息', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('192', '11', '1', '分享信息', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('193', '11', '1', '分享信息', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('194', '11', '1', '分享信息', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('195', '11', '1', '分享信息', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('196', '11', '1', '分享信息', '1', '2016-04-27', '1');
INSERT INTO `integral_info` VALUES ('197', '11', '10', '兑换配送券', '2', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('198', '11', '10', '兑换配送券', '2', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('199', '11', '10', '兑换配送券', '2', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('200', '11', '10', '兑换配送券', '2', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('201', '11', '2', '兑换优惠券', '2', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('202', '11', '2', '兑换优惠券', '2', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('203', '11', '2', '兑换优惠券', '2', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('204', '11', '10', '兑换配送券', '2', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('205', '11', '10', '兑换配送券', '2', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('206', '11', '10', '兑换配送券', '2', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('207', '11', '10', '兑换配送券', '2', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('208', '1', '2', '兑换优惠券', '2', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('209', '1', '2', '兑换优惠券', '2', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('210', '11', '2', '兑换优惠券', '2', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('211', '11', '2', '兑换优惠券', '2', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('212', '11', '2', '兑换优惠券', '2', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('213', '6', '2', '兑换优惠券', '2', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('214', '6', '2', '兑换优惠券', '2', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('215', '6', '10', '兑换配送券', '2', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('216', '6', '10', '兑换配送券', '2', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('217', '11', '2', '兑换优惠券', '2', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('218', '1', '2', '兑换优惠券', '2', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('219', '1', '2', '兑换优惠券', '2', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('220', '11', '2', '兑换优惠券', '2', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('221', '11', '10', '兑换配送券', '2', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('222', '6', '2', '兑换优惠券', '2', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('223', '6', '2', '兑换优惠券', '2', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('224', '6', '2', '兑换优惠券', '2', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('225', '6', '10', '兑换配送券', '2', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('226', '6', '10', '兑换配送券', '2', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('227', '6', '10', '兑换配送券', '2', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('228', '6', '10', '兑换配送券', '2', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('229', '11', '2', '兑换优惠券', '2', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('230', '11', '2', '兑换优惠券', '2', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('231', '11', '2', '兑换优惠券', '2', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('232', '1', '2', '兑换优惠券', '2', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('233', '6', '2', '兑换优惠券', '2', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('234', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('235', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('236', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('237', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('238', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('239', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('240', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('241', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('242', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('243', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('244', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('245', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('246', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('247', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('248', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('249', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('250', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('251', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('252', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('253', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('254', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('255', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('256', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('257', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('258', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('259', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('260', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('261', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('262', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('263', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('264', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('265', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('266', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('267', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('268', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('269', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('270', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('271', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('272', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('273', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('274', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('275', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('276', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('277', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('278', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('279', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('280', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('281', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('282', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('283', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('284', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('285', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('286', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('287', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('288', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('289', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('290', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('291', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('292', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('293', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('294', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('295', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('296', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('297', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('298', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('299', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('300', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('301', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('302', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('303', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('304', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('305', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('306', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('307', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('308', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('309', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('310', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('311', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('312', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('313', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('314', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('315', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('316', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('317', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('318', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('319', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('320', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('321', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('322', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('323', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('324', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('325', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('326', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('327', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('328', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('329', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('330', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('331', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('332', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('333', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('334', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('335', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('336', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('337', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('338', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('339', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('340', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('341', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('342', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('343', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('344', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('345', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('346', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('347', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('348', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('349', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('350', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('351', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('352', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('353', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('354', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('355', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('356', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('357', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('358', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('359', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('360', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('361', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('362', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('363', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('364', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('365', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('366', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('367', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('368', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('369', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('370', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('371', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('372', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('373', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('374', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('375', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('376', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('377', '11', '1', '分享信息', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('378', '11', '2', '兑换优惠券', '2', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('379', '11', '10', '兑换配送券', '2', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('380', '11', '2', '购买产品', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('381', '6', '2', '购买产品', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('382', '11', '2', '购买产品', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('383', '11', '2', '购买产品', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('384', '6', '2', '购买产品', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('385', '11', '1', '评价商品', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('386', '11', '2', '购买产品', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('387', '11', '2', '购买产品', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('388', '6', '2', '购买产品', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('389', '6', '2', '购买产品', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('390', '11', '2', '购买产品', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('391', '11', '2', '购买产品', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('392', '11', '2', '购买产品', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('393', '11', '2', '购买产品', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('394', '11', '2', '购买产品', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('395', '11', '2', '购买产品', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('396', '11', '2', '购买产品', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('397', '11', '2', '购买产品', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('398', '6', '2', '购买产品', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('399', '6', '2', '购买产品', '1', '2016-04-28', '1');
INSERT INTO `integral_info` VALUES ('400', '6', '1', '评价商品', '1', '2016-05-01', '1');
INSERT INTO `integral_info` VALUES ('401', '6', '2', '购买产品', '1', '2016-05-01', '1');
INSERT INTO `integral_info` VALUES ('402', '6', '2', '购买产品', '1', '2016-05-01', '1');
INSERT INTO `integral_info` VALUES ('403', '11', '1', '评价商品', '1', '2016-05-01', '1');
INSERT INTO `integral_info` VALUES ('404', '11', '1', '评价商品', '1', '2016-05-01', '1');
INSERT INTO `integral_info` VALUES ('405', '11', '2', '购买产品', '1', '2016-05-01', '1');
INSERT INTO `integral_info` VALUES ('406', '11', '2', '购买产品', '1', '2016-05-01', '1');
INSERT INTO `integral_info` VALUES ('407', '11', '2', '购买产品', '1', '2016-05-01', '1');
INSERT INTO `integral_info` VALUES ('408', '11', '2', '购买产品', '1', '2016-05-01', '1');
INSERT INTO `integral_info` VALUES ('409', '11', '2', '购买产品', '1', '2016-05-01', '1');
INSERT INTO `integral_info` VALUES ('410', '11', '2', '购买产品', '1', '2016-05-01', '1');
INSERT INTO `integral_info` VALUES ('411', '11', '2', '购买产品', '1', '2016-05-01', '1');
INSERT INTO `integral_info` VALUES ('412', '11', '2', '购买产品', '1', '2016-05-01', '1');
INSERT INTO `integral_info` VALUES ('413', '11', '2', '购买产品', '1', '2016-05-01', '1');
INSERT INTO `integral_info` VALUES ('414', '11', '2', '购买产品', '1', '2016-05-01', '1');
INSERT INTO `integral_info` VALUES ('415', '11', '2', '购买产品', '1', '2016-05-01', '1');
INSERT INTO `integral_info` VALUES ('416', '11', '2', '购买产品', '1', '2016-05-01', '1');
INSERT INTO `integral_info` VALUES ('417', '11', '2', '购买产品', '1', '2016-05-01', '1');
INSERT INTO `integral_info` VALUES ('418', '11', '2', '购买产品', '1', '2016-05-01', '1');
INSERT INTO `integral_info` VALUES ('419', '11', '2', '购买产品', '1', '2016-05-01', '1');
INSERT INTO `integral_info` VALUES ('420', '11', '1', '评价商品', '1', '2016-05-01', '1');
INSERT INTO `integral_info` VALUES ('421', '11', '1', '评价商品', '1', '2016-05-01', '1');
INSERT INTO `integral_info` VALUES ('422', '11', '2', '购买产品', '1', '2016-05-01', '1');
INSERT INTO `integral_info` VALUES ('423', '6', '2', '购买产品', '1', '2016-05-02', '1');
INSERT INTO `integral_info` VALUES ('424', '6', '2', '购买产品', '1', '2016-05-02', '1');
INSERT INTO `integral_info` VALUES ('425', '6', '2', '购买产品', '1', '2016-05-02', '1');
INSERT INTO `integral_info` VALUES ('426', '11', '1', '评价商品', '1', '2016-05-02', '1');
INSERT INTO `integral_info` VALUES ('427', '11', '2', '购买产品', '1', '2016-05-02', '1');
INSERT INTO `integral_info` VALUES ('428', '11', '2', '购买产品', '1', '2016-05-02', '1');
INSERT INTO `integral_info` VALUES ('429', '11', '1', '评价商品', '1', '2016-05-02', '1');
INSERT INTO `integral_info` VALUES ('430', '11', '2', '购买产品', '1', '2016-05-02', '1');
INSERT INTO `integral_info` VALUES ('431', '11', '2', '购买产品', '1', '2016-05-02', '1');
INSERT INTO `integral_info` VALUES ('432', '11', '2', '购买产品', '1', '2016-05-02', '1');
INSERT INTO `integral_info` VALUES ('433', '11', '2', '购买产品', '1', '2016-05-02', '1');
INSERT INTO `integral_info` VALUES ('434', '11', '2', '购买产品', '1', '2016-05-02', '1');
INSERT INTO `integral_info` VALUES ('435', '11', '2', '购买产品', '1', '2016-05-02', '1');
INSERT INTO `integral_info` VALUES ('436', '11', '2', '购买产品', '1', '2016-05-02', '1');
INSERT INTO `integral_info` VALUES ('437', '11', '2', '购买产品', '1', '2016-05-02', '1');
INSERT INTO `integral_info` VALUES ('438', '11', '2', '购买产品', '1', '2016-05-02', '1');
INSERT INTO `integral_info` VALUES ('439', '11', '2', '购买产品', '1', '2016-05-02', '1');
INSERT INTO `integral_info` VALUES ('440', '11', '2', '购买产品', '1', '2016-05-02', '1');
INSERT INTO `integral_info` VALUES ('441', '11', '2', '购买产品', '1', '2016-05-02', '1');
INSERT INTO `integral_info` VALUES ('442', '11', '2', '购买产品', '1', '2016-05-02', '1');
INSERT INTO `integral_info` VALUES ('443', '11', '2', '购买产品', '1', '2016-05-02', '1');
INSERT INTO `integral_info` VALUES ('444', '11', '2', '购买产品', '1', '2016-05-02', '1');
INSERT INTO `integral_info` VALUES ('445', '11', '2', '购买产品', '1', '2016-05-02', '1');
INSERT INTO `integral_info` VALUES ('446', '11', '2', '购买产品', '1', '2016-05-02', '1');
INSERT INTO `integral_info` VALUES ('447', '11', '2', '购买产品', '1', '2016-05-02', '1');
INSERT INTO `integral_info` VALUES ('448', '11', '2', '购买产品', '1', '2016-05-02', '1');
INSERT INTO `integral_info` VALUES ('449', '11', '2', '购买产品', '1', '2016-05-02', '1');
INSERT INTO `integral_info` VALUES ('450', '11', '2', '购买产品', '1', '2016-05-02', '1');
INSERT INTO `integral_info` VALUES ('451', '11', '2', '购买产品', '1', '2016-05-02', '1');
INSERT INTO `integral_info` VALUES ('452', '11', '2', '购买产品', '1', '2016-05-02', '1');
INSERT INTO `integral_info` VALUES ('453', '11', '2', '购买产品', '1', '2016-05-02', '1');
INSERT INTO `integral_info` VALUES ('454', '11', '2', '购买产品', '1', '2016-05-02', '1');
INSERT INTO `integral_info` VALUES ('455', '11', '2', '购买产品', '1', '2016-05-02', '1');
INSERT INTO `integral_info` VALUES ('456', '11', '2', '购买产品', '1', '2016-05-02', '1');
INSERT INTO `integral_info` VALUES ('457', '11', '2', '购买产品', '1', '2016-05-02', '1');
INSERT INTO `integral_info` VALUES ('458', '11', '2', '购买产品', '1', '2016-05-02', '1');
INSERT INTO `integral_info` VALUES ('459', '11', '2', '购买产品', '1', '2016-05-02', '1');
INSERT INTO `integral_info` VALUES ('460', '11', '2', '购买产品', '1', '2016-05-02', '1');
INSERT INTO `integral_info` VALUES ('461', '11', '10', '兑换配送券', '2', '2016-05-02', '1');
INSERT INTO `integral_info` VALUES ('462', '11', '2', '兑换优惠券', '2', '2016-05-02', '1');
INSERT INTO `integral_info` VALUES ('463', '11', '2', '兑换优惠券', '2', '2016-05-02', '1');
INSERT INTO `integral_info` VALUES ('464', '11', '2', '兑换优惠券', '2', '2016-05-02', '1');
INSERT INTO `integral_info` VALUES ('465', '11', '2', '兑换优惠券', '2', '2016-05-02', '1');
INSERT INTO `integral_info` VALUES ('466', '11', '2', '兑换优惠券', '2', '2016-05-02', '1');
INSERT INTO `integral_info` VALUES ('467', '11', '2', '兑换优惠券', '2', '2016-05-02', '1');
INSERT INTO `integral_info` VALUES ('468', '11', '2', '兑换优惠券', '2', '2016-05-02', '1');
INSERT INTO `integral_info` VALUES ('469', '11', '2', '兑换优惠券', '2', '2016-05-02', '1');
INSERT INTO `integral_info` VALUES ('470', '11', '2', '购买产品', '1', '2016-05-02', '1');
INSERT INTO `integral_info` VALUES ('471', '11', '2', '购买产品', '1', '2016-05-02', '1');
INSERT INTO `integral_info` VALUES ('472', '11', '2', '购买产品', '1', '2016-05-02', '1');
INSERT INTO `integral_info` VALUES ('473', '11', '2', '购买产品', '1', '2016-05-03', '1');
INSERT INTO `integral_info` VALUES ('474', '6', '2', '购买产品', '1', '2016-05-03', '1');

-- ----------------------------
-- Table structure for `logistics`
-- ----------------------------
DROP TABLE IF EXISTS `logistics`;
CREATE TABLE `logistics` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(11) unsigned NOT NULL COMMENT '订单id',
  `shippinguser_id` int(11) unsigned NOT NULL COMMENT '配送员id',
  `status` int(11) unsigned NOT NULL COMMENT '1送货员已抢单2正在配送3已签收',
  `receiving_time` varchar(50) NOT NULL COMMENT '送货员接单时间',
  `shipments_time` varchar(50) NOT NULL COMMENT '发货时间',
  `sign_for_time` varchar(50) NOT NULL COMMENT '签收时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of logistics
-- ----------------------------
INSERT INTO `logistics` VALUES ('22', '269', '214', '3', '2016-04-28 18:29:30', '2016-04-28 18:32:32', '2016-04-28 18:32:50');
INSERT INTO `logistics` VALUES ('23', '272', '214', '3', '2016-04-28 18:52:13', '2016-04-28 18:59:00', '2016-04-28 19:00:05');
INSERT INTO `logistics` VALUES ('24', '271', '214', '3', '2016-04-28 18:52:20', '2016-04-28 19:10:57', '2016-05-01 09:29:15');
INSERT INTO `logistics` VALUES ('25', '270', '214', '3', '2016-04-28 18:58:52', '2016-04-28 18:59:21', '2016-04-28 19:00:03');
INSERT INTO `logistics` VALUES ('26', '273', '214', '3', '2016-05-01 09:46:44', '2016-05-01 09:47:09', '2016-05-01 09:47:13');
INSERT INTO `logistics` VALUES ('27', '274', '214', '3', '2016-05-01 09:46:55', '2016-05-01 09:47:21', '2016-05-01 09:47:22');
INSERT INTO `logistics` VALUES ('28', '277', '214', '3', '2016-05-01 13:53:21', '2016-05-01 13:53:24', '2016-05-01 14:57:38');
INSERT INTO `logistics` VALUES ('30', '288', '214', '3', '2016-05-01 16:35:53', '2016-05-01 16:35:58', '2016-05-02 09:58:04');
INSERT INTO `logistics` VALUES ('31', '287', '214', '3', '2016-05-01 16:35:55', '2016-05-01 16:36:00', '2016-05-02 09:58:05');
INSERT INTO `logistics` VALUES ('41', '290', '34', '1', '2016-05-02 16:13:32', '', '');
INSERT INTO `logistics` VALUES ('46', '283', '34', '1', '2016-05-02 16:27:03', '', '');
INSERT INTO `logistics` VALUES ('47', '275', '34', '1', '2016-05-02 16:30:50', '', '');
INSERT INTO `logistics` VALUES ('48', '305', '214', '3', '2016-05-02 17:13:26', '2016-05-02 17:13:38', '2016-05-02 17:13:39');
INSERT INTO `logistics` VALUES ('49', '304', '214', '2', '2016-05-02 17:13:27', '2016-05-02 17:13:41', '');
INSERT INTO `logistics` VALUES ('50', '302', '214', '2', '2016-05-02 17:13:29', '2016-05-02 17:13:42', '');
INSERT INTO `logistics` VALUES ('51', '301', '214', '2', '2016-05-02 17:13:30', '2016-05-02 17:13:44', '');
INSERT INTO `logistics` VALUES ('52', '300', '214', '2', '2016-05-02 17:13:32', '2016-05-02 17:13:47', '');
INSERT INTO `logistics` VALUES ('53', '298', '214', '1', '2016-05-02 17:13:35', '', '');

-- ----------------------------
-- Table structure for `order`
-- ----------------------------
DROP TABLE IF EXISTS `order`;
CREATE TABLE `order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` varchar(50) NOT NULL COMMENT '订单号',
  `shop_id` int(11) NOT NULL COMMENT '店铺id',
  `member_id` int(10) unsigned NOT NULL COMMENT '会员id',
  `addtime` int(10) unsigned NOT NULL COMMENT '下单时间',
  `shr_name` varchar(30) NOT NULL COMMENT '收货人姓名',
  `shr_tel` varchar(30) NOT NULL COMMENT '收货人电话',
  `pay_time` int(11) NOT NULL COMMENT '付款时间',
  `shr_address` varchar(255) NOT NULL COMMENT '收货人地址',
  `total_price` decimal(10,2) NOT NULL COMMENT '定单总价',
  `order_status` tinyint(1) NOT NULL COMMENT '订单状态 0未支付 1待发货 3已发货 4已收货 5已取消',
  `pay_method` tinyint(1) NOT NULL COMMENT '支付方式 1,货到付款 2微信支付 3支付宝 4在线支付',
  `remark` varchar(255) NOT NULL COMMENT '备注',
  `send_time` varchar(60) NOT NULL COMMENT '送达时间',
  `admin_read` tinyint(4) NOT NULL DEFAULT '0' COMMENT '总管理员订单提醒',
  `shop_read` tinyint(4) NOT NULL DEFAULT '0' COMMENT '店铺管理员订单提醒',
  `coupon_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '优惠id',
  `delivery_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '配送券id',
  `postage` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '邮费',
  `is_make` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否接单  1是接单',
  `is_return` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否退款  0否  1订单商品有申请退款',
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`)
) ENGINE=MyISAM AUTO_INCREMENT=332 DEFAULT CHARSET=utf8 COMMENT='定单基本信息';

-- ----------------------------
-- Records of order
-- ----------------------------
INSERT INTO `order` VALUES ('293', '1462159146806823', '3', '6', '1462159146', '张飞', '12345678901', '1462159153', '普天小区7号楼301室', '99999999.99', '3', '2', '', '尽快送达', '1', '1', '0', '0', '10.00', '1', '1');
INSERT INTO `order` VALUES ('292', '1462159140305694', '3', '6', '1462159140', '张飞', '12345678901', '0', '普天小区7号楼301室', '94.00', '3', '1', '', '尽快送达', '1', '1', '0', '0', '10.00', '1', '0');
INSERT INTO `order` VALUES ('291', '1462154209893707', '3', '6', '1462154209', '张飞', '12345678901', '0', '普天小区7号楼301室', '26.00', '4', '1', '', '今天 10:00-10:15', '1', '1', '0', '0', '10.00', '1', '0');
INSERT INTO `order` VALUES ('296', '1462179528442779', '3', '11', '1462179528', '大明', '18710962367', '1462179588', '陕西省西安火炬路东新世纪广场205室', '28.00', '1', '2', '', '', '0', '1', '0', '0', '10.00', '0', '0');
INSERT INTO `order` VALUES ('295', '1462179241354583', '3', '11', '1462179241', '大明', '18710962367', '1462179328', '陕西省西安火炬路东新世纪广场205室', '11.00', '4', '2', '', '', '0', '1', '0', '0', '10.00', '0', '0');
INSERT INTO `order` VALUES ('287', '1462085074411407', '3', '11', '1462085074', '大明', '18710962367', '0', '陕西省西安火炬路东新世纪广场205室', '46.00', '4', '1', '', '', '1', '1', '0', '0', '10.00', '1', '0');
INSERT INTO `order` VALUES ('297', '146217963861584', '3', '11', '1462179638', '大明', '18710962367', '1462179659', '陕西省西安火炬路东新世纪广场205室', '11.00', '1', '2', '', '', '0', '1', '0', '0', '10.00', '0', '0');
INSERT INTO `order` VALUES ('286', '1462085047126464', '3', '11', '1462085047', '大明', '18710962367', '1462086552', '陕西省西安火炬路东新世纪广场205室', '34.00', '4', '3', '', '', '1', '1', '0', '0', '10.00', '1', '0');
INSERT INTO `order` VALUES ('299', '1462179820509857', '3', '11', '1462179820', '大明', '18710962367', '1462182992', '陕西省西安火炬路东新世纪广场205室', '10.00', '1', '2', '', '', '0', '1', '0', '0', '10.00', '0', '0');
INSERT INTO `order` VALUES ('274', '1462067199565734', '3', '6', '1462067199', '赵子龙', '17793850692', '1462067211', '普天小区18楼709室', '49.00', '4', '2', '', '明天 09:45-10:00', '1', '1', '81', '0', '10.00', '1', '0');
INSERT INTO `order` VALUES ('273', '1462067128208404', '3', '6', '1462067128', '赵子龙', '17793850692', '1462067138', '普天小区18楼709室', '58.00', '4', '2', '', '尽快送达', '1', '1', '0', '0', '10.00', '1', '0');
INSERT INTO `order` VALUES ('271', '1461839731803436', '3', '6', '1461839731', '赵子龙', '17793850692', '1461839761', '普天小区18楼709室', '14.00', '4', '2', '', '尽快送达', '1', '1', '0', '0', '10.00', '1', '0');
INSERT INTO `order` VALUES ('298', '1462179810804260', '3', '11', '1462179810', '大明', '18710962367', '1462180127', '陕西省西安火炬路东新世纪广场205室', '46.00', '1', '2', '', '', '0', '1', '0', '0', '10.00', '1', '0');
INSERT INTO `order` VALUES ('331', '1462242067980651', '3', '6', '1462242067', '张飞', '12345678901', '0', '普天小区7号楼301室', '136.00', '0', '4', '', '尽快送达', '0', '0', '0', '0', '10.00', '0', '0');
INSERT INTO `order` VALUES ('300', '1462179826779296', '3', '11', '1462179826', '大明', '18710962367', '1462180112', '陕西省西安火炬路东新世纪广场205室', '10.00', '3', '2', '', '', '0', '1', '0', '0', '10.00', '1', '0');
INSERT INTO `order` VALUES ('301', '1462179835320098', '3', '11', '1462179835', '大明', '18710962367', '1462180106', '陕西省西安火炬路东新世纪广场205室', '11.00', '4', '2', '', '', '0', '1', '0', '0', '10.00', '1', '0');
INSERT INTO `order` VALUES ('302', '14621798451892', '3', '11', '1462179845', '大明', '18710962367', '1462179897', '陕西省西安火炬路东新世纪广场205室', '11.00', '4', '2', '', '', '0', '1', '0', '0', '10.00', '1', '0');
INSERT INTO `order` VALUES ('303', '1462180267354492', '3', '11', '1462180267', '大明', '18710962367', '1462180544', '陕西省西安火炬路东新世纪广场205室', '28.00', '1', '2', '', '', '0', '1', '0', '0', '10.00', '0', '0');
INSERT INTO `order` VALUES ('304', '1462180280516387', '3', '11', '1462180280', '大明', '18710962367', '1462180357', '陕西省西安火炬路东新世纪广场205室', '11.50', '4', '2', '', '', '0', '1', '0', '0', '10.00', '1', '0');
INSERT INTO `order` VALUES ('305', '1462180299897399', '3', '11', '1462180299', '大明', '18710962367', '1462180341', '陕西省西安火炬路东新世纪广场205室', '97.00', '4', '2', '', '', '0', '1', '0', '0', '10.00', '1', '0');
INSERT INTO `order` VALUES ('306', '1462180574808197', '3', '11', '1462180574', '大明', '18710962367', '1462180787', '陕西省西安火炬路东新世纪广场205室', '11.00', '1', '2', '', '', '0', '1', '0', '0', '10.00', '0', '0');
INSERT INTO `order` VALUES ('307', '1462180602548339', '3', '11', '1462180602', '大明', '18710962367', '1462180814', '陕西省西安火炬路东新世纪广场205室', '10.50', '1', '2', '', '', '0', '1', '0', '0', '10.00', '0', '0');
INSERT INTO `order` VALUES ('308', '1462180615364227', '3', '11', '1462180615', '大明', '18710962367', '1462180782', '陕西省西安火炬路东新世纪广场205室', '28.00', '1', '2', '', '', '0', '1', '0', '0', '10.00', '0', '0');
INSERT INTO `order` VALUES ('309', '1462181162968139', '3', '11', '1462181162', '大明', '18710962367', '1462181507', '陕西省西安火炬路东新世纪广场205室', '28.00', '1', '2', '', '', '0', '1', '0', '0', '10.00', '0', '0');
INSERT INTO `order` VALUES ('310', '1462181168716461', '3', '11', '1462181168', '大明', '18710962367', '1462181324', '陕西省西安火炬路东新世纪广场205室', '50.00', '1', '2', '', '', '0', '1', '0', '0', '10.00', '0', '0');
INSERT INTO `order` VALUES ('311', '1462181177767883', '3', '11', '1462181177', '大明', '18710962367', '1462181317', '陕西省西安火炬路东新世纪广场205室', '10.50', '1', '2', '', '', '0', '1', '0', '0', '10.00', '0', '0');
INSERT INTO `order` VALUES ('330', '1462239846591339', '3', '11', '1462239846', '大明', '18710962367', '0', '陕西省西安火炬路东新世纪广场205室', '120.00', '0', '4', '', '', '0', '0', '0', '0', '10.00', '0', '0');
INSERT INTO `order` VALUES ('313', '1462181827742187', '3', '11', '1462181827', '大明', '18710962367', '1462182788', '陕西省西安火炬路东新世纪广场205室', '11.00', '1', '2', '', '', '0', '1', '0', '0', '10.00', '0', '0');
INSERT INTO `order` VALUES ('314', '1462181832275177', '3', '11', '1462181832', '大明', '18710962367', '1462181853', '陕西省西安火炬路东新世纪广场205室', '28.00', '1', '2', '', '', '0', '1', '0', '0', '10.00', '0', '0');
INSERT INTO `order` VALUES ('315', '1462181839105407', '3', '11', '1462181839', '大明', '18710962367', '1462181848', '陕西省西安火炬路东新世纪广场205室', '10.50', '1', '2', '', '', '0', '1', '0', '0', '10.00', '0', '0');
INSERT INTO `order` VALUES ('316', '1462182889725067', '3', '11', '1462182889', '大明', '18710962367', '0', '陕西省西安火炬路东新世纪广场205室', '28.00', '0', '4', '', '', '0', '1', '0', '0', '10.00', '0', '0');
INSERT INTO `order` VALUES ('317', '1462182895549682', '3', '11', '1462182895', '大明', '18710962367', '1462182987', '陕西省西安火炬路东新世纪广场205室', '10.50', '1', '2', '', '', '0', '1', '0', '0', '10.00', '0', '0');
INSERT INTO `order` VALUES ('318', '1462182899354644', '3', '11', '1462182899', '大明', '18710962367', '1462182963', '陕西省西安火炬路东新世纪广场205室', '11.00', '1', '2', '', '', '0', '1', '0', '0', '10.00', '0', '0');
INSERT INTO `order` VALUES ('319', '1462183091807373', '3', '11', '1462183091', '大明', '18710962367', '1462183369', '陕西省西安火炬路东新世纪广场205室', '11.00', '1', '2', '', '', '0', '1', '0', '0', '10.00', '0', '0');
INSERT INTO `order` VALUES ('320', '14621830955401', '3', '11', '1462183095', '大明', '18710962367', '1462183205', '陕西省西安火炬路东新世纪广场205室', '28.00', '1', '2', '', '', '0', '1', '0', '0', '10.00', '0', '0');
INSERT INTO `order` VALUES ('321', '1462183099824890', '3', '11', '1462183099', '大明', '18710962367', '1462183125', '陕西省西安火炬路东新世纪广场205室', '10.50', '1', '2', '', '', '0', '1', '0', '0', '10.00', '0', '0');
INSERT INTO `order` VALUES ('322', '1462183233696014', '3', '11', '1462183233', '大明', '18710962367', '1462183368', '陕西省西安火炬路东新世纪广场205室', '65.00', '1', '2', '', '', '0', '1', '0', '0', '10.00', '0', '0');
INSERT INTO `order` VALUES ('323', '1462183238257934', '3', '11', '1462183238', '大明', '18710962367', '1462183254', '陕西省西安火炬路东新世纪广场205室', '355.00', '1', '2', '', '', '0', '1', '0', '0', '10.00', '0', '0');
INSERT INTO `order` VALUES ('324', '1462183377737640', '3', '11', '1462183377', '大明', '18710962367', '0', '陕西省西安火炬路东新世纪广场205室', '65.00', '0', '4', '', '', '0', '1', '0', '0', '10.00', '0', '0');
INSERT INTO `order` VALUES ('325', '1462183382982666', '3', '11', '1462183382', '大明', '18710962367', '1462183422', '陕西省西安火炬路东新世纪广场205室', '355.00', '1', '2', '', '', '0', '1', '0', '0', '10.00', '0', '0');
INSERT INTO `order` VALUES ('326', '1462183386362335', '3', '11', '1462183386', '大明', '18710962367', '1462183395', '陕西省西安火炬路东新世纪广场205室', '53.00', '1', '2', '', '', '0', '1', '0', '0', '10.00', '0', '0');
INSERT INTO `order` VALUES ('327', '1462184146363159', '3', '11', '1462184146', '大明', '18710962367', '1462185781', '陕西省西安火炬路东新世纪广场205室', '120.00', '1', '2', '', '', '0', '0', '0', '0', '10.00', '0', '0');
INSERT INTO `order` VALUES ('328', '1462184178589569', '3', '11', '1462184178', '大明', '18710962367', '1462184218', '陕西省西安火炬路东新世纪广场205室', '110.00', '1', '2', '', '尽快送达', '0', '0', '91', '0', '10.00', '0', '0');
INSERT INTO `order` VALUES ('329', '1462184430929107', '3', '11', '1462184430', '大明', '18710962367', '1462185873', '陕西省西安火炬路东新世纪广场205室', '110.00', '1', '2', '', '尽快送达', '0', '0', '90', '0', '10.00', '0', '1');

-- ----------------------------
-- Table structure for `order_goods`
-- ----------------------------
DROP TABLE IF EXISTS `order_goods`;
CREATE TABLE `order_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` varchar(50) NOT NULL COMMENT '定单id',
  `member_id` int(10) unsigned NOT NULL COMMENT '会员id',
  `goods_id` int(10) unsigned NOT NULL COMMENT '商品ID',
  `goods_price` decimal(10,2) NOT NULL COMMENT '商品的价格',
  `goods_weight` varchar(60) NOT NULL COMMENT '规格',
  `goods_number` int(10) unsigned NOT NULL COMMENT '购买的数量',
  `goods_name` varchar(50) NOT NULL COMMENT '商品',
  `shop_id` int(11) NOT NULL COMMENT '商铺',
  `goods_zj` decimal(10,2) NOT NULL COMMENT '该商品的总价',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT ' 0待评价  1已评价  2申请/客服  3客服完成/待配送员 4配送员完成/待财务 5完成退款 6客服驳回  7配送员驳回',
  `goods_thumb` varchar(100) NOT NULL COMMENT '商品缩略图',
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `goods_id` (`goods_id`),
  KEY `member_id` (`member_id`)
) ENGINE=MyISAM AUTO_INCREMENT=532 DEFAULT CHARSET=utf8 COMMENT='定单商品';

-- ----------------------------
-- Records of order_goods
-- ----------------------------
INSERT INTO `order_goods` VALUES ('531', '331', '6', '11', '43.00', '500ml', '2', '好吃4', '3', '86.00', '0', './Public/upload/goods/thumb157074e0e0bdc5.jpg');
INSERT INTO `order_goods` VALUES ('530', '331', '6', '218', '40.00', '12g', '1', '键盘侠专用', '3', '40.00', '0', './Public/upload/goods/thumb1571c56ffda605.jpg');
INSERT INTO `order_goods` VALUES ('456', '271', '6', '10', '4.00', '350ml', '1', '好吃3', '3', '4.00', '1', './Public/upload/goods/thumb15706320d34590.jpg');
INSERT INTO `order_goods` VALUES ('458', '273', '6', '212', '12.00', '12g', '4', '幸福', '3', '48.00', '5', './Public/upload/goods/thumb157255f5c0e865.jpg');
INSERT INTO `order_goods` VALUES ('459', '274', '6', '212', '12.00', '12g', '4', '幸福', '3', '48.00', '5', './Public/upload/goods/thumb157255f5c0e865.jpg');
INSERT INTO `order_goods` VALUES ('460', '274', '6', '172', '1.00', '1g', '1', '也仍然发挥好广大人民的', '3', '1.00', '2', './Public/upload/goods/thumb15718360d42827.jpg');
INSERT INTO `order_goods` VALUES ('494', '300', '11', '100', '0.00', '', '1', '好吃', '3', '0.00', '0', './Public/upload/goods/thumb1570f22f82349b.jpg');
INSERT INTO `order_goods` VALUES ('495', '301', '11', '172', '1.00', '1g', '1', '渣渣', '3', '1.00', '0', './Public/upload/goods/thumb15718360d42827.jpg');
INSERT INTO `order_goods` VALUES ('475', '286', '11', '212', '12.00', '12g', '2', '幸福', '3', '24.00', '0', './Public/upload/goods/thumb157255f5c0e865.jpg');
INSERT INTO `order_goods` VALUES ('476', '287', '11', '212', '12.00', '12g', '3', '幸福', '3', '36.00', '0', './Public/upload/goods/thumb157255f5c0e865.jpg');
INSERT INTO `order_goods` VALUES ('493', '299', '11', '101', '0.00', 'df', '1', '大白菜', '3', '0.00', '0', './Public/upload/goods/thumb1570f236911db1.jpg');
INSERT INTO `order_goods` VALUES ('491', '297', '11', '172', '1.00', '1g', '1', '渣渣', '3', '1.00', '0', './Public/upload/goods/thumb15718360d42827.jpg');
INSERT INTO `order_goods` VALUES ('492', '298', '11', '153', '18.00', '1', '2', '花', '3', '36.00', '0', './Public/upload/goods/thumb1571715294d91e.jpg');
INSERT INTO `order_goods` VALUES ('489', '295', '11', '172', '1.00', '1g', '1', '渣渣', '3', '1.00', '0', './Public/upload/goods/thumb15718360d42827.jpg');
INSERT INTO `order_goods` VALUES ('490', '296', '11', '153', '18.00', '1', '1', '花', '3', '18.00', '0', './Public/upload/goods/thumb1571715294d91e.jpg');
INSERT INTO `order_goods` VALUES ('496', '302', '11', '165', '0.00', '', '2', '测试', '3', '0.00', '0', './Public/upload/goods/thumb157172a4c3553e.jpg');
INSERT INTO `order_goods` VALUES ('482', '291', '6', '212', '12.00', '12g', '1', '幸福', '3', '12.00', '0', './Public/upload/goods/thumb157255f5c0e865.jpg');
INSERT INTO `order_goods` VALUES ('483', '291', '6', '10', '4.00', '350ml', '1', '好吃3', '3', '4.00', '0', './Public/upload/goods/thumb15706320d34590.jpg');
INSERT INTO `order_goods` VALUES ('484', '292', '6', '218', '40.00', '12g', '2', '键盘侠专用', '3', '80.00', '0', './Public/upload/goods/thumb1571c56ffda605.jpg');
INSERT INTO `order_goods` VALUES ('485', '292', '6', '10', '4.00', '350ml', '1', '好吃3', '3', '4.00', '0', './Public/upload/goods/thumb15706320d34590.jpg');
INSERT INTO `order_goods` VALUES ('486', '293', '6', '7', '11.10', '100g', '2', '好吃1', '3', '22.20', '0', './Public/upload/goods/thumb1570b6dedb9fd3.png');
INSERT INTO `order_goods` VALUES ('487', '293', '6', '212', '99999999.99', '12g', '2', '幸福', '3', '99999999.99', '0', './Public/upload/goods/thumb157255f5c0e865.jpg');
INSERT INTO `order_goods` VALUES ('497', '302', '11', '107', '0.50', '15g', '2', '泡椒牛蛙和我', '3', '1.00', '0', './Public/upload/goods/thumb1570f2671afe75.png');
INSERT INTO `order_goods` VALUES ('498', '303', '11', '153', '18.00', '1', '1', '花', '3', '18.00', '0', './Public/upload/goods/thumb1571715294d91e.jpg');
INSERT INTO `order_goods` VALUES ('499', '304', '11', '107', '0.50', '15g', '1', '泡椒牛蛙和我', '3', '0.50', '0', './Public/upload/goods/thumb1570f2671afe75.png');
INSERT INTO `order_goods` VALUES ('500', '304', '11', '172', '1.00', '1g', '1', '渣渣', '3', '1.00', '0', './Public/upload/goods/thumb15718360d42827.jpg');
INSERT INTO `order_goods` VALUES ('501', '305', '11', '165', '0.00', '', '1', '测试', '3', '0.00', '0', './Public/upload/goods/thumb157172a4c3553e.jpg');
INSERT INTO `order_goods` VALUES ('502', '305', '11', '10', '4.00', '350ml', '1', '好吃3', '3', '4.00', '0', './Public/upload/goods/thumb15706320d34590.jpg');
INSERT INTO `order_goods` VALUES ('503', '305', '11', '11', '43.00', '500ml', '1', '好吃4', '3', '43.00', '0', './Public/upload/goods/thumb157074e0e0bdc5.jpg');
INSERT INTO `order_goods` VALUES ('504', '305', '11', '218', '40.00', '12g', '1', '键盘侠专用', '3', '40.00', '0', './Public/upload/goods/thumb1571c56ffda605.jpg');
INSERT INTO `order_goods` VALUES ('505', '306', '11', '172', '1.00', '1g', '1', '渣渣', '3', '1.00', '0', './Public/upload/goods/thumb15718360d42827.jpg');
INSERT INTO `order_goods` VALUES ('506', '307', '11', '107', '0.50', '15g', '1', '泡椒牛蛙和我', '3', '0.50', '0', './Public/upload/goods/thumb1570f2671afe75.png');
INSERT INTO `order_goods` VALUES ('507', '308', '11', '153', '18.00', '1', '1', '花', '3', '18.00', '0', './Public/upload/goods/thumb1571715294d91e.jpg');
INSERT INTO `order_goods` VALUES ('508', '309', '11', '153', '18.00', '1', '1', '花', '3', '18.00', '0', './Public/upload/goods/thumb1571715294d91e.jpg');
INSERT INTO `order_goods` VALUES ('509', '310', '11', '218', '40.00', '12g', '1', '键盘侠专用', '3', '40.00', '0', './Public/upload/goods/thumb1571c56ffda605.jpg');
INSERT INTO `order_goods` VALUES ('510', '311', '11', '107', '0.50', '15g', '1', '泡椒牛蛙和我', '3', '0.50', '0', './Public/upload/goods/thumb1570f2671afe75.png');
INSERT INTO `order_goods` VALUES ('529', '330', '11', '141', '55.00', '55kg', '2', '这样一来我', '3', '110.00', '0', './Public/upload/goods/thumb157170b7c6b2c0.png');
INSERT INTO `order_goods` VALUES ('512', '313', '11', '172', '1.00', '1g', '1', '渣渣', '3', '1.00', '0', './Public/upload/goods/thumb15718360d42827.jpg');
INSERT INTO `order_goods` VALUES ('513', '314', '11', '153', '18.00', '1', '1', '花', '3', '18.00', '0', './Public/upload/goods/thumb1571715294d91e.jpg');
INSERT INTO `order_goods` VALUES ('514', '315', '11', '107', '0.50', '15g', '1', '泡椒牛蛙和我', '3', '0.50', '0', './Public/upload/goods/thumb1570f2671afe75.png');
INSERT INTO `order_goods` VALUES ('515', '316', '11', '153', '18.00', '1', '1', '花', '3', '18.00', '0', './Public/upload/goods/thumb1571715294d91e.jpg');
INSERT INTO `order_goods` VALUES ('516', '317', '11', '107', '0.50', '15g', '1', '泡椒牛蛙和我', '3', '0.50', '0', './Public/upload/goods/thumb1570f2671afe75.png');
INSERT INTO `order_goods` VALUES ('517', '318', '11', '172', '1.00', '1g', '1', '渣渣', '3', '1.00', '0', './Public/upload/goods/thumb15718360d42827.jpg');
INSERT INTO `order_goods` VALUES ('518', '319', '11', '172', '1.00', '1g', '1', '渣渣', '3', '1.00', '0', './Public/upload/goods/thumb15718360d42827.jpg');
INSERT INTO `order_goods` VALUES ('519', '320', '11', '153', '18.00', '1', '1', '花', '3', '18.00', '0', './Public/upload/goods/thumb1571715294d91e.jpg');
INSERT INTO `order_goods` VALUES ('520', '321', '11', '107', '0.50', '15g', '1', '泡椒牛蛙和我', '3', '0.50', '0', './Public/upload/goods/thumb1570f2671afe75.png');
INSERT INTO `order_goods` VALUES ('521', '322', '11', '141', '55.00', '55kg', '1', '这样一来我', '3', '55.00', '0', './Public/upload/goods/thumb157170b7c6b2c0.png');
INSERT INTO `order_goods` VALUES ('522', '323', '11', '135', '345.00', '345g', '1', '花海', '3', '345.00', '0', './Public/upload/goods/thumb15716f014dcaae.png');
INSERT INTO `order_goods` VALUES ('523', '324', '11', '141', '55.00', '55kg', '1', '这样一来我', '3', '55.00', '0', './Public/upload/goods/thumb157170b7c6b2c0.png');
INSERT INTO `order_goods` VALUES ('524', '325', '11', '135', '345.00', '345g', '1', '花海', '3', '345.00', '0', './Public/upload/goods/thumb15716f014dcaae.png');
INSERT INTO `order_goods` VALUES ('525', '326', '11', '11', '43.00', '500ml', '1', '好吃4', '3', '43.00', '0', './Public/upload/goods/thumb157074e0e0bdc5.jpg');
INSERT INTO `order_goods` VALUES ('526', '327', '11', '141', '55.00', '55kg', '2', '这样一来我', '3', '110.00', '0', './Public/upload/goods/thumb157170b7c6b2c0.png');
INSERT INTO `order_goods` VALUES ('527', '328', '11', '141', '55.00', '55kg', '2', '这样一来我', '3', '110.00', '0', './Public/upload/goods/thumb157170b7c6b2c0.png');
INSERT INTO `order_goods` VALUES ('528', '329', '11', '141', '55.00', '55kg', '2', '这样一来我', '3', '110.00', '5', './Public/upload/goods/thumb157170b7c6b2c0.png');

-- ----------------------------
-- Table structure for `order_sales`
-- ----------------------------
DROP TABLE IF EXISTS `order_sales`;
CREATE TABLE `order_sales` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) unsigned NOT NULL COMMENT '商铺id',
  `price` decimal(10,2) NOT NULL,
  `time` varchar(60) CHARACTER SET utf8 NOT NULL,
  `sales` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of order_sales
-- ----------------------------
INSERT INTO `order_sales` VALUES ('1', '3', '10.00', '2016-04-08', '1');
INSERT INTO `order_sales` VALUES ('2', '4', '20.00', '2016-04-02', '2');
INSERT INTO `order_sales` VALUES ('3', '5', '30.00', '2016-04-03', '3');
INSERT INTO `order_sales` VALUES ('4', '3', '40.00', '2016-04-04', '4');
INSERT INTO `order_sales` VALUES ('5', '3', '20.00', '2016-04-11', '1');
INSERT INTO `order_sales` VALUES ('7', '3', '22.20', '2016-04-13', '2');
INSERT INTO `order_sales` VALUES ('10', '3', '22.20', '2016-04-14', '2');
INSERT INTO `order_sales` VALUES ('11', '4', '20.00', '2016-04-14', '2');
INSERT INTO `order_sales` VALUES ('15', '3', '88.80', '2016-04-15', '4');
INSERT INTO `order_sales` VALUES ('16', '6', '34.00', '2016-04-15', '1');
INSERT INTO `order_sales` VALUES ('17', '4', '44.00', '2016-04-15', '2');
INSERT INTO `order_sales` VALUES ('18', '3', '111.00', '2016-04-16', '5');
INSERT INTO `order_sales` VALUES ('19', '6', '102.00', '2016-04-16', '2');
INSERT INTO `order_sales` VALUES ('20', '3', '410.00', '2016-04-18', '15');
INSERT INTO `order_sales` VALUES ('21', '3', '1683.70', '2016-04-19', '55');
INSERT INTO `order_sales` VALUES ('22', '3', '455.10', '2016-04-20', '13');
INSERT INTO `order_sales` VALUES ('23', '3', '320.00', '2016-04-21', '21');
INSERT INTO `order_sales` VALUES ('26', '3', '2170.20', '2016-04-22', '51');
INSERT INTO `order_sales` VALUES ('27', '3', '61.00', '2016-04-23', '9');
INSERT INTO `order_sales` VALUES ('28', '3', '1217.50', '2016-04-24', '143');
INSERT INTO `order_sales` VALUES ('29', '3', '516.80', '2016-04-25', '50');
INSERT INTO `order_sales` VALUES ('30', '4', '10.00', '2016-04-25', '1');
INSERT INTO `order_sales` VALUES ('31', '6', '68.00', '2016-04-25', '3');
INSERT INTO `order_sales` VALUES ('32', '3', '90.00', '2016-04-26', '3');
INSERT INTO `order_sales` VALUES ('33', '3', '676.70', '2016-04-27', '78');
INSERT INTO `order_sales` VALUES ('34', '3', '578.20', '2016-04-28', '86');
INSERT INTO `order_sales` VALUES ('35', '3', '633.50', '2016-05-01', '70');
INSERT INTO `order_sales` VALUES ('36', '3', '99999999.99', '2016-05-02', '56');
INSERT INTO `order_sales` VALUES ('37', '3', '193.00', '2016-05-03', '5');

-- ----------------------------
-- Table structure for `province`
-- ----------------------------
DROP TABLE IF EXISTS `province`;
CREATE TABLE `province` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `provinceid` int(11) NOT NULL,
  `province` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=gbk;

-- ----------------------------
-- Records of province
-- ----------------------------
INSERT INTO `province` VALUES ('1', '110000', '北京市');
INSERT INTO `province` VALUES ('2', '120000', '天津市');
INSERT INTO `province` VALUES ('3', '130000', '河北省');
INSERT INTO `province` VALUES ('4', '140000', '山西省');
INSERT INTO `province` VALUES ('5', '150000', '内蒙古自治区');
INSERT INTO `province` VALUES ('6', '210000', '辽宁省');
INSERT INTO `province` VALUES ('7', '220000', '吉林省');
INSERT INTO `province` VALUES ('8', '230000', '黑龙江省');
INSERT INTO `province` VALUES ('9', '310000', '上海市');
INSERT INTO `province` VALUES ('10', '320000', '江苏省');
INSERT INTO `province` VALUES ('11', '330000', '浙江省');
INSERT INTO `province` VALUES ('12', '340000', '安徽省');
INSERT INTO `province` VALUES ('13', '350000', '福建省');
INSERT INTO `province` VALUES ('14', '360000', '江西省');
INSERT INTO `province` VALUES ('15', '370000', '山东省');
INSERT INTO `province` VALUES ('16', '410000', '河南省');
INSERT INTO `province` VALUES ('17', '420000', '湖北省');
INSERT INTO `province` VALUES ('18', '430000', '湖南省');
INSERT INTO `province` VALUES ('19', '440000', '广东省');
INSERT INTO `province` VALUES ('20', '450000', '广西壮族自治区');
INSERT INTO `province` VALUES ('21', '460000', '海南省');
INSERT INTO `province` VALUES ('22', '500000', '重庆市');
INSERT INTO `province` VALUES ('23', '510000', '四川省');
INSERT INTO `province` VALUES ('24', '520000', '贵州省');
INSERT INTO `province` VALUES ('25', '530000', '云南省');
INSERT INTO `province` VALUES ('26', '540000', '西藏自治区');
INSERT INTO `province` VALUES ('27', '610000', '陕西省');
INSERT INTO `province` VALUES ('28', '620000', '甘肃省');
INSERT INTO `province` VALUES ('29', '630000', '青海省');
INSERT INTO `province` VALUES ('30', '640000', '宁夏回族自治区');
INSERT INTO `province` VALUES ('31', '650000', '新疆维吾尔自治区');
INSERT INTO `province` VALUES ('32', '710000', '台湾省');
INSERT INTO `province` VALUES ('33', '810000', '香港特别行政区');
INSERT INTO `province` VALUES ('34', '820000', '澳门特别行政区');

-- ----------------------------
-- Table structure for `ps_pv`
-- ----------------------------
DROP TABLE IF EXISTS `ps_pv`;
CREATE TABLE `ps_pv` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '配送员id',
  `time` varchar(12) NOT NULL COMMENT '时间',
  `str_time` varchar(80) NOT NULL,
  `endNum` int(11) NOT NULL COMMENT '已配送',
  `notNum` int(11) NOT NULL COMMENT '未配送',
  `allNum` int(11) NOT NULL COMMENT '总数',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ps_pv
-- ----------------------------
INSERT INTO `ps_pv` VALUES ('1', '34', '1461686400', '2016-04-29', '0', '1', '1');
INSERT INTO `ps_pv` VALUES ('2', '35', '1461686400', '2016-04-29', '1', '0', '1');
INSERT INTO `ps_pv` VALUES ('3', '214', '1461772800', '2016-04-29', '22', '0', '22');
INSERT INTO `ps_pv` VALUES ('4', '214', '1462032000', '2016-04-30', '5', '0', '5');
INSERT INTO `ps_pv` VALUES ('5', '214', '1462118400', '2016-05-01', '0', '3', '3');
INSERT INTO `ps_pv` VALUES ('10', '34', '1462118400', '2016-05-02', '1', '2', '2');

-- ----------------------------
-- Table structure for `refund_info`
-- ----------------------------
DROP TABLE IF EXISTS `refund_info`;
CREATE TABLE `refund_info` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_goods_id` int(11) NOT NULL COMMENT '订单商品id',
  `apply_time` varchar(50) NOT NULL COMMENT '申请时间',
  `service_time` varchar(50) NOT NULL COMMENT '客服处理时间',
  `delivery_time` varchar(50) NOT NULL COMMENT '配送成时间',
  `money_back_time` varchar(50) NOT NULL COMMENT '退款时间',
  `finish_time` varchar(50) NOT NULL COMMENT '完成时间',
  `status` tinyint(3) NOT NULL COMMENT '1申请/客服  2客服完成/待配送员 3配送员完成/待财务 4完成退款  5客服驳回 6配送员驳回',
  `return_pic` varchar(255) NOT NULL COMMENT '退款图片',
  `return_info` varchar(255) NOT NULL COMMENT '退款说明',
  `kefu_info` varchar(255) NOT NULL COMMENT '客服驳回原因',
  `peisong_info` varchar(255) NOT NULL COMMENT '配送员驳回原因',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of refund_info
-- ----------------------------
INSERT INTO `refund_info` VALUES ('1', '241', '2016-04-25 13:09:07', '', '', '', '', '1', '', '', '', '');
INSERT INTO `refund_info` VALUES ('19', '407', '2016-04-28 16:41:47', '', '', '', '', '1', '', '', '', '');
INSERT INTO `refund_info` VALUES ('20', '417', '2016-04-28 17:06:02', '', '', '', '', '1', '', '', '', '');
INSERT INTO `refund_info` VALUES ('21', '421', '2016-04-28 17:24:03', '', '', '', '', '1', '', '', '', '');
INSERT INTO `refund_info` VALUES ('22', '457', '2016-05-01 09:40:48', '', '', '', '', '1', '', '', '', '');
INSERT INTO `refund_info` VALUES ('23', '459', '2016-05-01 09:48:06', '', '', '', '', '5', '', '', '', '');
INSERT INTO `refund_info` VALUES ('24', '460', '2016-05-01 09:48:34', '', '', '', '', '1', '', '', '', '');
INSERT INTO `refund_info` VALUES ('25', '458', '2016-05-01 09:48:51', '', '', '', '', '5', '', '', '', '');
INSERT INTO `refund_info` VALUES ('26', '464', '2016-05-02 09:15:32', '', '', '', '', '1', '', '', '', '');
INSERT INTO `refund_info` VALUES ('27', '481', '2016-05-02 09:47:17', '', '', '', '', '1', './Public/upload/20160502/thumb_5726b1a595380.png*./Public/upload/20160502/thumb_5726b1a595768.png*', '这件衣服大号了', '', '');
INSERT INTO `refund_info` VALUES ('28', '481', '2016-05-02 09:53:28', '', '', '', '', '1', '', '', '', '');
INSERT INTO `refund_info` VALUES ('29', '528', '2016-05-03 09:31:38', '', '', '', '', '5', './Public/upload/20160503/thumb_5727ff7ac0047.png*./Public/upload/20160503/thumb_5727ff7ac042f.png*', '这件衣服大号了', '', '');
INSERT INTO `refund_info` VALUES ('30', '528', '2016-05-03 09:34:09', '', '', '', '', '5', './Public/upload/20160503/thumb_57280011a661f.png*./Public/upload/20160503/thumb_57280011a6def.png*', '这件衣服大号了', '', '');

-- ----------------------------
-- Table structure for `reply`
-- ----------------------------
DROP TABLE IF EXISTS `reply`;
CREATE TABLE `reply` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `content` varchar(1000) NOT NULL COMMENT '回复的内容',
  `addtime` int(10) unsigned NOT NULL COMMENT '回复时间',
  `member_id` int(10) unsigned NOT NULL COMMENT '会员ID',
  `comment_id` int(10) unsigned NOT NULL COMMENT '评论的ID',
  PRIMARY KEY (`id`),
  KEY `comment_id` (`comment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='回复';

-- ----------------------------
-- Records of reply
-- ----------------------------

-- ----------------------------
-- Table structure for `shippinguser`
-- ----------------------------
DROP TABLE IF EXISTS `shippinguser`;
CREATE TABLE `shippinguser` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) NOT NULL COMMENT '商铺id',
  `s_name` varchar(50) NOT NULL COMMENT '配送员姓名',
  `s_sfz` char(18) NOT NULL COMMENT '省份证号码',
  `sex` tinyint(2) unsigned NOT NULL COMMENT '性别1男2女',
  `age` tinyint(2) NOT NULL COMMENT '年龄',
  `job_number` varchar(20) NOT NULL COMMENT '工号',
  `password` varchar(32) NOT NULL COMMENT '登录密码',
  `birth_date` date NOT NULL COMMENT '出生日期',
  `status` tinyint(4) NOT NULL COMMENT '0,冻结  1,正常',
  `user_logo` varchar(255) NOT NULL COMMENT '配送员头像',
  `phone` varchar(255) NOT NULL COMMENT '联系方式',
  `account_num` varchar(255) NOT NULL COMMENT '配送员账号',
  `facilityId` varchar(255) NOT NULL COMMENT '设备id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=249 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of shippinguser
-- ----------------------------
INSERT INTO `shippinguser` VALUES ('2', '4', '阿飞', '610327199540044645', '1', '20', '285512', '123456', '2016-04-06', '1', './Public/upload/20160406/thumb_5704cba97b1ef.jpg', '18392643921', 'liyong', '121');
INSERT INTO `shippinguser` VALUES ('214', '3', '刘明', '610327199540044645', '2', '12', '12', '12345678', '1970-01-01', '1', './Public/upload/dianpu/thumb572562bdb3bd5.jpg', '13028364976', '', '170976fa8a83cc6c8d5');
INSERT INTO `shippinguser` VALUES ('34', '3', '张东', '610327199540044645', '0', '0', '', '', '0000-00-00', '1', '', '', '', '');
INSERT INTO `shippinguser` VALUES ('247', '3', '哈哈', '452187', '1', '34', '1', '123456', '1970-01-01', '0', './Public/upload/dianpu/thumb572579ac476ad.jpg', '13462574836', '', '');
INSERT INTO `shippinguser` VALUES ('248', '3', '我自己', '15841554554444', '1', '44', '4555', '44', '1970-04-30', '0', './Public/upload/dianpu/thumb572702654a965.png', '4454', '', '');

-- ----------------------------
-- Table structure for `shop`
-- ----------------------------
DROP TABLE IF EXISTS `shop`;
CREATE TABLE `shop` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '店铺id',
  `shop_name` char(40) NOT NULL COMMENT '店铺名称',
  `shop_address` varchar(100) NOT NULL COMMENT '商铺地址',
  `xq_id` int(11) NOT NULL DEFAULT '0' COMMENT '小区id',
  `status` tinyint(4) NOT NULL COMMENT '店铺状态 0-申请中,1-ok',
  `username` varchar(30) NOT NULL COMMENT '店铺负责人',
  `shop_phone` char(15) NOT NULL COMMENT '店铺电话',
  `shop_logo` varchar(255) NOT NULL COMMENT '店铺logo',
  `start_price` decimal(10,0) NOT NULL DEFAULT '0' COMMENT '起送价',
  `cat_id` int(11) unsigned NOT NULL COMMENT '栏目id',
  `add_time` int(11) NOT NULL COMMENT '添加时间',
  `shop_desc` text NOT NULL COMMENT '店铺简介',
  `lng` text NOT NULL,
  `lnt` text NOT NULL,
  `sj_id` int(11) NOT NULL COMMENT '商家id',
  `shop_x` varchar(100) NOT NULL COMMENT '经纬度x',
  `shop_y` varchar(100) NOT NULL COMMENT '经纬度y',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='店铺';

-- ----------------------------
-- Records of shop
-- ----------------------------
INSERT INTO `shop` VALUES ('3', '赵日天', 'af', '3', '1', 'aa', '12345678000', './Public/upload/dianpu/thumb5726b05005529.png', '34', '4', '1459851034', 'adfadfa', '', '', '3', '', '');
INSERT INTO `shop` VALUES ('8', '王记超市', '西安太白立交', '4', '1', '王东', '15894578965', './Public/upload/dianpu/thumb57183fe829cf6.png', '5', '4', '1461207016', '天天好', '108.947047/108.952111/108.952111/108.94636/108.938919/108.94012/108.94424', '34.274314/34.272257/34.267647/34.266938/34.271548/34.271832/34.272399', '4', '108.932919', '34.237467');
INSERT INTO `shop` VALUES ('9', '代理商商铺', '黄土高坡one号？', '3', '1', 'admin', '18888148409', './Public/upload/dianpu/thumb571d75edd578a.png', '10', '8', '1461548525', '这是一段简介', '108.806833/108.367379/109.784616/109.938424', '35.067364/34.372048/33.908306/34.616525', '6', '108.955448', '34.271246');

-- ----------------------------
-- Table structure for `shopadmin`
-- ----------------------------
DROP TABLE IF EXISTS `shopadmin`;
CREATE TABLE `shopadmin` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) NOT NULL COMMENT '1,总后台  2商家',
  `phone` char(11) CHARACTER SET latin1 NOT NULL COMMENT '商家',
  `shop_id` tinyint(4) NOT NULL DEFAULT '0' COMMENT '商家id',
  `pwd` char(32) CHARACTER SET latin1 NOT NULL COMMENT '登录密码',
  `name` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `balance` decimal(10,2) unsigned NOT NULL COMMENT '余额',
  `withdraw` decimal(10,2) NOT NULL COMMENT '已经提现',
  `header_logo` varchar(100) NOT NULL COMMENT '商家logo',
  `nickname` varchar(30) NOT NULL COMMENT '昵称',
  `qq` varchar(20) NOT NULL COMMENT 'qq号',
  `sign` varchar(200) NOT NULL COMMENT '个性签名',
  `sex` tinyint(1) NOT NULL COMMENT '1男  2女 0未知',
  `addtime` char(10) NOT NULL COMMENT '添加时间',
  `facilityId` varchar(255) NOT NULL COMMENT '设备id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of shopadmin
-- ----------------------------
INSERT INTO `shopadmin` VALUES ('1', '1', '', '3', 'e10adc3949ba59abbe56e057f20f883e', 'admin', '1', '300.00', '200.00', '', '', '', '', '0', '', '');
INSERT INTO `shopadmin` VALUES ('3', '2', '15891766569', '3', 'e10adc3949ba59abbe56e057f20f883e', '好好', '1', '3854.50', '0.00', './Public/upload/dianpu/thumb571c21fdcc28b.png', '青藏高原', '5273643891', '一直想念你的生活', '0', '1460789790', '');
INSERT INTO `shopadmin` VALUES ('4', '2', '15891722676', '8', 'e10adc3949ba59abbe56e057f20f883e', '阿旺', '1', '0.00', '0.00', './Public/upload/dianpu/thumb57183e73a4808.jpg', '阿旺', '', '阳光总在风雨后', '1', '1461206643', '');
INSERT INTO `shopadmin` VALUES ('5', '1', '', '8', 'e10adc3949ba59abbe56e057f20f883e', 'kefu1', '1', '0.00', '0.00', '', '', '', '', '0', '', '');
INSERT INTO `shopadmin` VALUES ('6', '2', '18392643921', '9', 'e10adc3949ba59abbe56e057f20f883e', '这是一个流下眼泪的传说', '1', '0.00', '0.00', './Public/upload/dianpu/thumb571d75b8a967c.png', '昵称', '', '我很好你呢', '1', '1461548472', '');

-- ----------------------------
-- Table structure for `shop_pv`
-- ----------------------------
DROP TABLE IF EXISTS `shop_pv`;
CREATE TABLE `shop_pv` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) unsigned NOT NULL COMMENT '商铺id',
  `time` char(50) NOT NULL COMMENT '时间',
  `pv` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=158 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of shop_pv
-- ----------------------------
INSERT INTO `shop_pv` VALUES ('1', '3', '2016-04-08', '5');
INSERT INTO `shop_pv` VALUES ('156', '3', '2016-04-09', '1250');
INSERT INTO `shop_pv` VALUES ('157', '3', '2016-04-14', '343');

-- ----------------------------
-- Table structure for `slide`
-- ----------------------------
DROP TABLE IF EXISTS `slide`;
CREATE TABLE `slide` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `slide_name` varchar(30) NOT NULL COMMENT '标题',
  `slide_pic` varchar(50) NOT NULL COMMENT '图片',
  `url` varchar(50) NOT NULL,
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序值',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of slide
-- ----------------------------
INSERT INTO `slide` VALUES ('1', '番茄', './Public/upload/slide/thumb570f5d5b71f68.jpg', 'http://www.baidu.com', '34', '1');
INSERT INTO `slide` VALUES ('2', '薯片', './Public/upload/slide/thumb570f5da64945c.jpg', 'www.baidu.com', '2', '1');
INSERT INTO `slide` VALUES ('4', '啊打发', './Public/upload/slide/thumb570f5d9b1fdc8.jpg', '', '34', '1');

-- ----------------------------
-- Table structure for `system_config`
-- ----------------------------
DROP TABLE IF EXISTS `system_config`;
CREATE TABLE `system_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL COMMENT '名称如优惠劵',
  `code` varchar(20) NOT NULL COMMENT '字母名称',
  `value` varchar(150) NOT NULL COMMENT '值',
  `sort` tinyint(4) NOT NULL COMMENT '排序值',
  `comment` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of system_config
-- ----------------------------
INSERT INTO `system_config` VALUES ('1', '分享积分', 'share', '1', '1', '分享赠送积分');
INSERT INTO `system_config` VALUES ('2', '好评积分', 'goodComment', '1', '2', '好评赠送积分');
INSERT INTO `system_config` VALUES ('3', '签到积分', 'sign', '2', '3', '签到积分');
INSERT INTO `system_config` VALUES ('4', '购买积分', 'buy', '2', '4', '购买商品1员兑换积分比率');
INSERT INTO `system_config` VALUES ('5', '公告', 'notice', 'hello word', '4', 'app店铺公告');
INSERT INTO `system_config` VALUES ('6', '优惠券', 'discount', '100', '5', '积分兑换优惠券比例(如100积分/优惠券)');
INSERT INTO `system_config` VALUES ('7', '配送券', 'shipping', '100', '6', '积分兑换配送券比例(如100积分/配送券)');
INSERT INTO `system_config` VALUES ('8', '优惠券抵用价格', 'coupon_price', '2', '7', '一张优惠券能抵用多少钱');
INSERT INTO `system_config` VALUES ('9', '优惠券使用最低消费额', 'use_coupon', '200', '8', '订单金额必须大于最低消费额才可使用');
INSERT INTO `system_config` VALUES ('10', '兑换优惠券可用天数', 'deadline', '7', '9', '优惠券的使用日期(如：7天 2016-04-20/2016-');
INSERT INTO `system_config` VALUES ('11', '统一配送价', 'delivery_price', '10', '10', '配送价格');

-- ----------------------------
-- Table structure for `user`
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(120) NOT NULL,
  `password` varchar(80) NOT NULL,
  `openid` varchar(150) NOT NULL COMMENT '微信端id',
  `nickname` varchar(150) NOT NULL DEFAULT '昵称' COMMENT '昵称',
  `icon` varchar(150) NOT NULL COMMENT '头像',
  `sex` int(11) unsigned NOT NULL DEFAULT '1' COMMENT '性别1男2女',
  `integral` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '积分',
  `birth_date` varchar(80) NOT NULL COMMENT '出生日期',
  `time` varchar(80) NOT NULL,
  `status` tinyint(11) unsigned NOT NULL DEFAULT '1' COMMENT '状态0冻结1正常',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES ('1', '18888148409', 'e10adc3949ba59abbe56e057f20f883es', '', 'Dreams', './Public/upload/20160419/5715d95f4ee21.jpg', '1', '11', '1995-10-04', '2016-04-05 17:31:33', '1');
INSERT INTO `user` VALUES ('2', 'user2', 'e10adc3949ba59abbe56e057f20f883e', '', '阿飞', './Public/upload/20160407/thumb_57060661d4abe.jpg', '2', '0', '1995-10-04', '2016-04-05 13:31:33', '1');
INSERT INTO `user` VALUES ('6', '17791376024', '25d55ad283aa400af464c76d713c07ad', '', '萨斯给', './Public/upload/20160427/57204ed596cfb.png', '1', '525', '1990-10-13', '', '1');
INSERT INTO `user` VALUES ('7', '18888148408', 'e10adc3949ba59abbe56e057f20f883e', '', '昵称', '', '1', '0', '', '', '1');
INSERT INTO `user` VALUES ('8', '18888148404', 'e10adc3949ba59abbe56e057f20f883e', '', '昵称', '', '1', '0', '', '', '1');
INSERT INTO `user` VALUES ('9', '18888148402', 'e10adc3949ba59abbe56e057f20f883e', '', '昵称', '', '1', '0', '', '', '1');
INSERT INTO `user` VALUES ('10', '13567775544', '73C18C59A39B18382081EC00BB456D43', '', '昵称', '', '1', '0', '', '', '1');
INSERT INTO `user` VALUES ('11', '18710962367', 'EE188C1537BE6D7E6BA86ACE577E70D0', '', 'Saab', './Public/upload/20160421/5718abf869b94.jpg', '1', '246', 'boys', '', '1');
INSERT INTO `user` VALUES ('12', '18888148400', 'e10adc3949ba59abbe56e057f20f883e', '', '昵称', '', '1', '0', '', '', '1');
INSERT INTO `user` VALUES ('13', '13186078987', '25d55ad283aa400af464c76d713c07ad', '', '谢吉仓', './Public/upload/20160425/571d74d4c93dc.png', '1', '0', '', '', '1');

-- ----------------------------
-- Table structure for `withdraw`
-- ----------------------------
DROP TABLE IF EXISTS `withdraw`;
CREATE TABLE `withdraw` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `shopadmin_id` int(11) unsigned NOT NULL COMMENT '商家id',
  `username` varchar(60) NOT NULL COMMENT '户名',
  `card_num` varchar(30) NOT NULL COMMENT '银行卡号',
  `link_phone` char(15) NOT NULL COMMENT '联系电话',
  `opening_bank` varchar(255) NOT NULL COMMENT '开户行名称',
  `money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '提现金额',
  `status` int(10) unsigned NOT NULL COMMENT '1已经提现0未体现2驳回',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of withdraw
-- ----------------------------
INSERT INTO `withdraw` VALUES ('7', '2', '5555', '62777777777777777777', '18888148407', '工商', '199.00', '2');
INSERT INTO `withdraw` VALUES ('8', '2', 'A飞', '622228484000462', '18888148409', '建设银行', '20.00', '0');
