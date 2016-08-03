/*
Navicat MySQL Data Transfer

Source Server         : renrenfenqi_pro
Source Server Version : 50518
Source Host           : rdsqafeiqvaavni.mysql.rds.aliyuncs.com:3306
Source Database       : renrenfenqi_pro

Target Server Type    : MYSQL
Target Server Version : 50518
File Encoding         : 65001

Date: 2016-08-03 14:41:16
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for ecs_account
-- ----------------------------
DROP TABLE IF EXISTS `ecs_account`;
CREATE TABLE `ecs_account` (
  `account_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL COMMENT '用户名',
  `password` varchar(32) NOT NULL COMMENT '密码',
  `nikename` varchar(10) DEFAULT NULL COMMENT '昵称',
  `token` varchar(32) DEFAULT NULL COMMENT '唯一标识符',
  `last_sign_time` timestamp NULL DEFAULT NULL COMMENT '最后一次登录时间',
  `expire_time` int(11) DEFAULT NULL COMMENT '过期时间',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `account_group_id` varchar(16) DEFAULT NULL COMMENT '对应 ecs_group_permission 的 user_group_id',
  `flag_valid` int(11) NOT NULL DEFAULT '1' COMMENT '用户是否有效 1：有效 0 ：无效',
  `status` int(11) NOT NULL DEFAULT '1' COMMENT '状态',
  `email` varchar(50) DEFAULT NULL COMMENT '邮箱',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`account_id`)
) ENGINE=InnoDB AUTO_INCREMENT=136 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 COMMENT='Crm 后台账号表';

-- ----------------------------
-- Table structure for ecs_account_group
-- ----------------------------
DROP TABLE IF EXISTS `ecs_account_group`;
CREATE TABLE `ecs_account_group` (
  `id` varchar(24) NOT NULL COMMENT '分组ID',
  `group_name` varchar(45) NOT NULL COMMENT '分组名称',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_account_school
-- ----------------------------
DROP TABLE IF EXISTS `ecs_account_school`;
CREATE TABLE `ecs_account_school` (
  `as_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int(11) NOT NULL,
  `angel_id` int(11) NOT NULL,
  `angel` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`as_id`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_activity
-- ----------------------------
DROP TABLE IF EXISTS `ecs_activity`;
CREATE TABLE `ecs_activity` (
  `ac_id` int(11) NOT NULL AUTO_INCREMENT,
  `ac_name` varchar(50) NOT NULL COMMENT '活动名',
  `temp_id` int(11) NOT NULL COMMENT '模板id',
  `managers` varchar(20) DEFAULT NULL COMMENT '负责人',
  `banner` varchar(250) DEFAULT NULL COMMENT '广告',
  `goods_id` char(30) NOT NULL DEFAULT '0' COMMENT '商品关联ID',
  `ac_laud` int(11) NOT NULL DEFAULT '0' COMMENT '活动点赞总数',
  `html_url` varchar(255) DEFAULT NULL COMMENT '模板URL',
  `ctime` int(10) NOT NULL,
  PRIMARY KEY (`ac_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='活动表';

-- ----------------------------
-- Table structure for ecs_activity_apple_watch
-- ----------------------------
DROP TABLE IF EXISTS `ecs_activity_apple_watch`;
CREATE TABLE `ecs_activity_apple_watch` (
  `activity_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL COMMENT '用户Uid',
  `phone` varchar(11) NOT NULL COMMENT '用户手机号',
  `name` varchar(255) DEFAULT NULL COMMENT '购买的watch 名称',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`activity_id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8 COMMENT='apple_watch 活动记录';

-- ----------------------------
-- Table structure for ecs_activity_appraise
-- ----------------------------
DROP TABLE IF EXISTS `ecs_activity_appraise`;
CREATE TABLE `ecs_activity_appraise` (
  `appraise_id` int(11) NOT NULL AUTO_INCREMENT,
  `activity_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `content` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`appraise_id`),
  UNIQUE KEY `appraise_id` (`appraise_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_activity_ip6_record
-- ----------------------------
DROP TABLE IF EXISTS `ecs_activity_ip6_record`;
CREATE TABLE `ecs_activity_ip6_record` (
  `activity_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL COMMENT '用户uid',
  `phone` varchar(11) NOT NULL COMMENT '手机号',
  `name` varchar(255) DEFAULT NULL COMMENT '名称',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`activity_id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=2999 DEFAULT CHARSET=utf8 COMMENT='iPhone 6 秒杀活动 记录';

-- ----------------------------
-- Table structure for ecs_activity_laud
-- ----------------------------
DROP TABLE IF EXISTS `ecs_activity_laud`;
CREATE TABLE `ecs_activity_laud` (
  `laud_id` int(11) NOT NULL AUTO_INCREMENT,
  `activity_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime NOT NULL,
  PRIMARY KEY (`laud_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_activity_template
-- ----------------------------
DROP TABLE IF EXISTS `ecs_activity_template`;
CREATE TABLE `ecs_activity_template` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `cname` varchar(30) NOT NULL COMMENT '名称',
  `is_type` int(1) NOT NULL DEFAULT '0' COMMENT '类型 1/手机 2/PC',
  `is_attr` int(1) NOT NULL DEFAULT '0' COMMENT '属性 1/图文混编 2/秒杀 3/专题',
  `page_type` int(1) DEFAULT '0' COMMENT '0/不分享 1/分享',
  `page_url` varchar(255) DEFAULT NULL COMMENT '分享缩略图',
  `page_back` varchar(50) DEFAULT NULL COMMENT 'CSS分享背景',
  `page_back_img` varchar(255) DEFAULT NULL COMMENT '分享背景图片',
  `page_back_way` varchar(100) DEFAULT NULL COMMENT '背景重复方式',
  `page_custom` text COMMENT '页面样式自定义',
  `g_img_width` varchar(100) DEFAULT NULL COMMENT '商品图片宽度',
  `g_img_height` varchar(100) DEFAULT NULL COMMENT '商品图片高度',
  `g_img_back_color` varchar(100) DEFAULT NULL COMMENT '商品图片背景色 ',
  `g_img_custom` text COMMENT '商品图片自定义',
  `g_title_size` varchar(100) DEFAULT NULL COMMENT '商品名称字体大小',
  `g_title_color` varchar(50) DEFAULT NULL COMMENT '商品名称字体颜色',
  `g_title_custom` text COMMENT '商品名称自定义',
  `g_price_size` varchar(100) DEFAULT NULL COMMENT '商品价格字体大小',
  `g_price_color` varchar(50) DEFAULT NULL COMMENT '商品价格字体颜色',
  `g_price_custom` varchar(100) DEFAULT NULL COMMENT '商品价格自定义',
  `g_oprice_size` varchar(100) DEFAULT NULL COMMENT '商品原价格字体大小',
  `g_oprice_color` varchar(50) DEFAULT NULL COMMENT '商品原价格字体颜色 ',
  `g_oprice_line` int(1) DEFAULT '0' COMMENT '商品原价格是否需要删除线 0/no 1/ok',
  `g_oprice_custom` varchar(100) DEFAULT NULL COMMENT '商品原价格自定义',
  `g_layout_type` varchar(50) DEFAULT 'ul' COMMENT '排版方式（可选项：表格／无需列表）(table, ul)',
  `g_layout_column` int(5) DEFAULT NULL COMMENT '列数（一共有多少列）',
  `g_layout_custom` varchar(100) DEFAULT NULL COMMENT '自定义',
  `btn_img` varchar(255) DEFAULT NULL COMMENT '按钮图片',
  `btn_bg_color` varchar(50) DEFAULT NULL COMMENT '背景颜色',
  `btn_size` varchar(100) DEFAULT NULL COMMENT '字体大小',
  `btn_color` varchar(50) DEFAULT NULL COMMENT '字体颜色 ',
  `btn_custom` text COMMENT '自定义',
  `layout_html` text COMMENT '排版',
  `layout_css` text COMMENT 'CSS',
  `layout_js` text COMMENT 'JS',
  `ctime` int(10) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='活动模板表';

-- ----------------------------
-- Table structure for ecs_app
-- ----------------------------
DROP TABLE IF EXISTS `ecs_app`;
CREATE TABLE `ecs_app` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0',
  `appid` varchar(100) NOT NULL DEFAULT '""',
  `client_id` varchar(100) NOT NULL DEFAULT '""' COMMENT '设备标识码',
  `client_type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '设备类别，是ios还是安卓',
  `mode` int(1) DEFAULT '1' COMMENT '1/校园经理  2/仁仁分期',
  `user_id` int(11) DEFAULT NULL COMMENT '学生uid',
  `type` varchar(5) NOT NULL DEFAULT 'XY',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=75597 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_apply
-- ----------------------------
DROP TABLE IF EXISTS `ecs_apply`;
CREATE TABLE `ecs_apply` (
  `apply_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `is_type` int(1) DEFAULT '1' COMMENT '1/分期 2/兼职 3/白条',
  `business_no` bigint(20) unsigned NOT NULL COMMENT '业务号，根据一个规则生成',
  `goods_id` int(11) unsigned NOT NULL COMMENT '商品ID',
  `goods_name` varchar(150) DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '商品总价（无活动、兼职时等于分期价）',
  `goods_price` decimal(11,2) DEFAULT '0.00' COMMENT '商品价格（要分期付款的价）',
  `pay_percent` int(11) NOT NULL DEFAULT '0' COMMENT '首付百分比',
  `pay_first_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `name` varchar(10) NOT NULL COMMENT '申请人称呼',
  `phone` varchar(11) NOT NULL COMMENT '申请学生的手机号',
  `stu_id` int(11) NOT NULL DEFAULT '0',
  `uid` int(11) NOT NULL DEFAULT '0',
  `cid` int(11) NOT NULL DEFAULT '0' COMMENT '邀请者id',
  `cname` varchar(15) DEFAULT NULL,
  `first_price` tinyint(4) NOT NULL DEFAULT '1' COMMENT '针对4000以上，是否有首付 1有 、2无',
  `periods` tinyint(3) unsigned NOT NULL COMMENT '分多少期 单位 月',
  `attr_val_list` varchar(20) DEFAULT NULL COMMENT '商品属性',
  `custom_id` int(11) unsigned DEFAULT NULL COMMENT '客服ID，自动分配的',
  `custom_name` varchar(255) DEFAULT NULL COMMENT '客服名字',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态 0 ： 客服未联系 2：客服联系未果 3：已联系，不需要  4 已联系 已分配',
  `stock_meg` varchar(25) DEFAULT NULL,
  `buy_meg` varchar(25) DEFAULT NULL,
  `angel_id` int(11) DEFAULT NULL COMMENT '处理校园大使名字',
  `city_manager_id` int(11) DEFAULT NULL COMMENT '分派城市经理',
  `dorm_id` int(11) NOT NULL DEFAULT '0',
  `job_state` tinyint(4) NOT NULL DEFAULT '0' COMMENT '订单兼职状态',
  `job_operation` tinyint(4) NOT NULL DEFAULT '0' COMMENT '订单兼职操作行为',
  `goods_job_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '商品兼职金额',
  `client_type` varchar(10) DEFAULT NULL COMMENT '客户端类型android、web、ios',
  `remarks` text COMMENT '备注',
  `dis_amount` decimal(10,2) NOT NULL COMMENT '优惠金额',
  `dis_type` varchar(15) NOT NULL DEFAULT 'NONE' COMMENT '优惠类型',
  `dis_id` varchar(150) DEFAULT NULL COMMENT '优惠序号',
  `allot_time` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `version` tinyint(4) NOT NULL DEFAULT '2',
  PRIMARY KEY (`apply_id`),
  UNIQUE KEY `business_no` (`business_no`)
) ENGINE=InnoDB AUTO_INCREMENT=39605 DEFAULT CHARSET=utf8 COMMENT='大学生提交申请表';

-- ----------------------------
-- Table structure for ecs_audit_log
-- ----------------------------
DROP TABLE IF EXISTS `ecs_audit_log`;
CREATE TABLE `ecs_audit_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `uid` int(10) unsigned NOT NULL COMMENT '用户ID',
  `business_no` bigint(20) unsigned NOT NULL COMMENT '业务号',
  `mode` int(1) unsigned NOT NULL COMMENT '1/学生审核 2/资料审核 3/订单审核 10/v3版本订单审核',
  `client` int(1) DEFAULT '1' COMMENT '1/web 2/app 3/crm',
  `url_log` varchar(255) DEFAULT NULL COMMENT '访问URL',
  `ip` varchar(20) DEFAULT NULL COMMENT '访问IP',
  `uname` varchar(20) DEFAULT NULL COMMENT '操作人姓名',
  `audit_note` varchar(600) DEFAULT NULL COMMENT '审核备注',
  `edit_note` varchar(600) DEFAULT NULL COMMENT '修改备注',
  `unusual_note` varchar(600) DEFAULT NULL COMMENT '异常日记',
  `is_state` int(1) NOT NULL COMMENT '1/审核 2/修改 3/异常',
  `etime` int(10) DEFAULT NULL COMMENT '修改时间',
  `ctime` int(10) DEFAULT NULL COMMENT '操作时间戳',
  `state_log` int(1) DEFAULT '0' COMMENT '记录状态',
  `failure_select` text COMMENT '错误原因选择',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`) USING BTREE,
  KEY `mode` (`mode`) USING BTREE,
  KEY `client` (`client`) USING BTREE,
  KEY `business_no` (`business_no`) USING BTREE,
  KEY `ctime` (`ctime`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=90175 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for ecs_audit_qq
-- ----------------------------
DROP TABLE IF EXISTS `ecs_audit_qq`;
CREATE TABLE `ecs_audit_qq` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `audit_qq` varchar(20) NOT NULL DEFAULT '' COMMENT '审核qq',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0未启用 1启用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for ecs_banner
-- ----------------------------
DROP TABLE IF EXISTS `ecs_banner`;
CREATE TABLE `ecs_banner` (
  `b_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `img_link` varchar(255) NOT NULL COMMENT '图片连接',
  `img_path` varchar(255) NOT NULL COMMENT '图片地址',
  `img_suffix` varchar(255) DEFAULT NULL COMMENT '图片后缀',
  `is_show` tinyint(255) unsigned DEFAULT '0' COMMENT '是否显示',
  `position` tinyint(4) unsigned NOT NULL COMMENT '位置',
  `sort` tinyint(4) unsigned DEFAULT '0' COMMENT '排序',
  `source` tinyint(4) unsigned NOT NULL COMMENT '来源 1:PC 2:APP 3:Mobile',
  `goods_map_id` int(10) unsigned DEFAULT NULL COMMENT '商品关系ID',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`b_id`)
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_bus
-- ----------------------------
DROP TABLE IF EXISTS `ecs_bus`;
CREATE TABLE `ecs_bus` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `cname` varchar(50) DEFAULT NULL COMMENT '商户名称',
  `mobile` varchar(15) NOT NULL COMMENT '手机号码',
  `pass` char(64) NOT NULL COMMENT '密码',
  `reg_time` int(10) NOT NULL COMMENT '注册时间',
  `examine` int(1) NOT NULL COMMENT '审核 ''1''=>''自营'',''2''=>''已认证'',''3''=>''未认证''',
  `is_state` int(1) NOT NULL DEFAULT '1' COMMENT '0/关闭  1/开启',
  `ctime` int(10) DEFAULT NULL COMMENT '添加时间',
  `etime` int(10) DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1089 DEFAULT CHARSET=utf8 COMMENT='商户基础表';

-- ----------------------------
-- Table structure for ecs_campus_angel
-- ----------------------------
DROP TABLE IF EXISTS `ecs_campus_angel`;
CREATE TABLE `ecs_campus_angel` (
  `angel_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL,
  `password` varchar(32) NOT NULL,
  `token` varchar(32) NOT NULL,
  `angel_name` varchar(10) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `QQ` varchar(20) DEFAULT NULL,
  `flag` tinyint(4) NOT NULL DEFAULT '1',
  `account_id` int(11) NOT NULL,
  `client_sn` varchar(120) DEFAULT NULL COMMENT '客户端编码',
  `carate_time` datetime NOT NULL,
  PRIMARY KEY (`angel_id`),
  UNIQUE KEY `angel_name` (`angel_name`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=99 DEFAULT CHARSET=utf8 COMMENT='校园经理帐号表';

-- ----------------------------
-- Table structure for ecs_category
-- ----------------------------
DROP TABLE IF EXISTS `ecs_category`;
CREATE TABLE `ecs_category` (
  `cat_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类ID',
  `cat_name` varchar(255) NOT NULL DEFAULT '' COMMENT '分类名称',
  `cat_pid` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '父id',
  `cat_level` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '分类层级',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0启用 1禁用',
  `sort` int(6) DEFAULT NULL COMMENT '排序',
  `code` varchar(50) DEFAULT NULL COMMENT '代号',
  `created` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated` timestamp NULL DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`cat_id`),
  UNIQUE KEY `code` (`code`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=182 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for ecs_college
-- ----------------------------
DROP TABLE IF EXISTS `ecs_college`;
CREATE TABLE `ecs_college` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `mode` int(1) DEFAULT '1' COMMENT '1/学院 2/专业',
  `mode_id` int(10) NOT NULL COMMENT '上级ID',
  `school_id` int(10) NOT NULL COMMENT '学校id',
  `province_id` int(10) NOT NULL COMMENT '省级ID',
  `city_id` int(10) NOT NULL COMMENT '城市ID',
  `area_id` int(10) NOT NULL COMMENT '区域ID',
  `address` varchar(120) NOT NULL COMMENT '学院地址',
  `latitude` varchar(20) NOT NULL COMMENT '纬度',
  `longitude` varchar(20) NOT NULL COMMENT '经度',
  `college_name` varchar(50) NOT NULL COMMENT '学院名称',
  `professional_name` varchar(50) NOT NULL COMMENT '专业名称',
  `info` text COMMENT '备注',
  `is_state` int(1) DEFAULT '1' COMMENT '0/不显示 1/显示',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mode` (`mode`) USING BTREE,
  KEY `mode_id` (`mode_id`) USING BTREE,
  KEY `school_id` (`school_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for ecs_contacts
-- ----------------------------
DROP TABLE IF EXISTS `ecs_contacts`;
CREATE TABLE `ecs_contacts` (
  `contact_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` int(11) unsigned NOT NULL COMMENT '学生ID',
  `relation` varchar(255) NOT NULL COMMENT '与学生关系',
  `name` varchar(255) NOT NULL COMMENT '名字',
  `phone` varchar(15) DEFAULT NULL,
  `workunit` varchar(255) DEFAULT NULL COMMENT '工作单位',
  `post` varchar(20) DEFAULT NULL COMMENT '职务',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `uid` int(10) DEFAULT NULL COMMENT '核心ID',
  `mode` int(1) NOT NULL COMMENT '1/父亲 2/母亲',
  PRIMARY KEY (`contact_id`)
) ENGINE=InnoDB AUTO_INCREMENT=102816 DEFAULT CHARSET=utf8 COMMENT='联系人表';

-- ----------------------------
-- Table structure for ecs_credit
-- ----------------------------
DROP TABLE IF EXISTS `ecs_credit`;
CREATE TABLE `ecs_credit` (
  `credit_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL COMMENT '用户uid',
  `credit` int(10) unsigned NOT NULL COMMENT '用户信用额度',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`credit_id`),
  UNIQUE KEY `uid` (`uid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8 COMMENT='用户信用额度';

-- ----------------------------
-- Table structure for ecs_credit_record
-- ----------------------------
DROP TABLE IF EXISTS `ecs_credit_record`;
CREATE TABLE `ecs_credit_record` (
  `credit_re_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL,
  `operate` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '操作 1： 增加  2 ：减少',
  `numerical` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '操作的数值',
  `remark` text COMMENT '备注',
  `from` varchar(10) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`credit_re_id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8 COMMENT='信用额度记录';

-- ----------------------------
-- Table structure for ecs_crm_operate_log
-- ----------------------------
DROP TABLE IF EXISTS `ecs_crm_operate_log`;
CREATE TABLE `ecs_crm_operate_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(255) DEFAULT NULL COMMENT 'crm后台帐号',
  `name` varchar(255) DEFAULT NULL COMMENT 'crm后台姓名',
  `data` text COMMENT '操作的数据',
  `type` int(10) unsigned DEFAULT '0' COMMENT '操作类型',
  `req` text COMMENT 'req请求的内容',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6931 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for ecs_crm_sms_log
-- ----------------------------
DROP TABLE IF EXISTS `ecs_crm_sms_log`;
CREATE TABLE `ecs_crm_sms_log` (
  `sms_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `message` varchar(255) DEFAULT NULL COMMENT '短信内容',
  `uname` varchar(255) DEFAULT NULL COMMENT '谁发的',
  `data` text COMMENT '发送的人 json格式',
  `created_at` timestamp NULL DEFAULT NULL,
  `type` tinyint(4) DEFAULT '1' COMMENT '类型 1：兼职 2：风控',
  PRIMARY KEY (`sms_id`)
) ENGINE=InnoDB AUTO_INCREMENT=910 DEFAULT CHARSET=utf8 COMMENT='crm 后台发送短信log';

-- ----------------------------
-- Table structure for ecs_custom_plan
-- ----------------------------
DROP TABLE IF EXISTS `ecs_custom_plan`;
CREATE TABLE `ecs_custom_plan` (
  `cid` int(11) NOT NULL AUTO_INCREMENT,
  `custom_id` int(11) NOT NULL COMMENT '客服id',
  `flag` int(11) NOT NULL DEFAULT '0' COMMENT '标记,1为排到者，轮流',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `state` int(11) NOT NULL DEFAULT '1' COMMENT '是否排单',
  PRIMARY KEY (`cid`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8 COMMENT='客服处理订单排版表';

-- ----------------------------
-- Table structure for ecs_delivery_adress
-- ----------------------------
DROP TABLE IF EXISTS `ecs_delivery_adress`;
CREATE TABLE `ecs_delivery_adress` (
  `ad_id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_name` varchar(15) NOT NULL,
  `ad_phone` bigint(12) NOT NULL,
  `ad_uid` int(11) NOT NULL,
  `ad_status` int(11) NOT NULL DEFAULT '0' COMMENT '默认收货地址 1默认',
  `ad_auth` int(11) NOT NULL DEFAULT '1' COMMENT '认证，1：不可修改 2：可修改',
  `ad_remark` varchar(50) DEFAULT NULL,
  `school_id` int(11) NOT NULL,
  `ad_dorm_address` varchar(50) NOT NULL COMMENT '寝室地址',
  `school_address` varchar(50) NOT NULL DEFAULT '' COMMENT '学校详细地址',
  PRIMARY KEY (`ad_id`)
) ENGINE=InnoDB AUTO_INCREMENT=24792 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_depot
-- ----------------------------
DROP TABLE IF EXISTS `ecs_depot`;
CREATE TABLE `ecs_depot` (
  `did` int(11) NOT NULL AUTO_INCREMENT,
  `dname` varchar(20) NOT NULL COMMENT '仓库名',
  `dhead_id` int(11) NOT NULL COMMENT '负责人id',
  `dhead_name` varchar(10) NOT NULL COMMENT '负责人名字',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态',
  `anchor` tinyint(4) DEFAULT '0',
  `create_time` datetime NOT NULL,
  PRIMARY KEY (`did`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_device
-- ----------------------------
DROP TABLE IF EXISTS `ecs_device`;
CREATE TABLE `ecs_device` (
  `dev_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `token` varchar(100) NOT NULL,
  `client` varchar(10) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`dev_id`),
  KEY `token` (`token`)
) ENGINE=InnoDB AUTO_INCREMENT=18199 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_dorm
-- ----------------------------
DROP TABLE IF EXISTS `ecs_dorm`;
CREATE TABLE `ecs_dorm` (
  `dorm_id` int(11) NOT NULL AUTO_INCREMENT,
  `dorm_name` varchar(30) NOT NULL COMMENT '宿舍名',
  `dorm_type` int(11) NOT NULL COMMENT '宿舍类型 1男 2女 4混合',
  `people_num` int(11) NOT NULL COMMENT '宿舍人数',
  `bedroom_num` int(11) NOT NULL COMMENT '寝室个数',
  `school_id` int(11) NOT NULL,
  `ceo_id` int(11) NOT NULL COMMENT '校园ceoid',
  `create_at` datetime NOT NULL,
  PRIMARY KEY (`dorm_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for ecs_event_520_goods
-- ----------------------------
DROP TABLE IF EXISTS `ecs_event_520_goods`;
CREATE TABLE `ecs_event_520_goods` (
  `event_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `phone` varchar(11) DEFAULT NULL,
  `goods_id` int(10) unsigned DEFAULT NULL,
  `goods_name` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `type` tinyint(1) unsigned DEFAULT NULL,
  `time` tinyint(3) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`event_id`),
  KEY `uid` (`uid`,`type`)
) ENGINE=InnoDB AUTO_INCREMENT=97 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_event_520_sms
-- ----------------------------
DROP TABLE IF EXISTS `ecs_event_520_sms`;
CREATE TABLE `ecs_event_520_sms` (
  `event_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `phone` varchar(11) DEFAULT NULL COMMENT '电话',
  `type` tinyint(1) unsigned DEFAULT NULL,
  `time` timestamp NULL DEFAULT NULL COMMENT '时间点',
  `message` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`event_id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=995 DEFAULT CHARSET=utf8 COMMENT='20150520清仓活动短信提醒';

-- ----------------------------
-- Table structure for ecs_fraudmetrix
-- ----------------------------
DROP TABLE IF EXISTS `ecs_fraudmetrix`;
CREATE TABLE `ecs_fraudmetrix` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stu_id` int(11) DEFAULT NULL,
  `status` char(10) DEFAULT NULL,
  `name` char(40) DEFAULT NULL,
  `sex` char(10) DEFAULT NULL,
  `school` char(50) DEFAULT NULL,
  `phone` bigint(20) DEFAULT NULL,
  `email` char(40) DEFAULT NULL,
  `pay_way` char(20) DEFAULT NULL,
  `card` char(40) DEFAULT NULL,
  `identity` char(40) DEFAULT NULL,
  `data` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stu_id` (`stu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5726 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_fraudmetrix_copy
-- ----------------------------
DROP TABLE IF EXISTS `ecs_fraudmetrix_copy`;
CREATE TABLE `ecs_fraudmetrix_copy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stu_id` int(11) DEFAULT NULL,
  `status` char(10) DEFAULT NULL,
  `name` char(40) DEFAULT NULL,
  `sex` char(10) DEFAULT NULL,
  `school` char(50) DEFAULT NULL,
  `phone` bigint(20) DEFAULT NULL,
  `email` char(40) DEFAULT NULL,
  `pay_way` char(20) DEFAULT NULL,
  `card` char(40) DEFAULT NULL,
  `identity` char(40) DEFAULT NULL,
  `data` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stu_id` (`stu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14421 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_goods
-- ----------------------------
DROP TABLE IF EXISTS `ecs_goods`;
CREATE TABLE `ecs_goods` (
  `goods_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `goods_map_id` int(11) unsigned NOT NULL COMMENT '商品关系主表ID',
  `attr_val_list` varchar(255) NOT NULL,
  `goods_name` varchar(255) NOT NULL COMMENT '商品名称',
  `goods_sn` varchar(60) NOT NULL COMMENT '商品编号',
  `shop_price` decimal(10,2) unsigned NOT NULL COMMENT '本店售价',
  `show_price` decimal(10,2) DEFAULT NULL COMMENT '本店显示价',
  `market_price` decimal(10,2) unsigned DEFAULT NULL COMMENT '市场价格',
  `purchase_price` decimal(10,2) DEFAULT NULL COMMENT '进货价格',
  `formula` tinyint(2) DEFAULT '0' COMMENT '0 手动计算 ；1~5 公式1~公式5',
  `inventory` int(10) unsigned DEFAULT '0' COMMENT '库存',
  `is_on_sell` tinyint(1) DEFAULT '0' COMMENT '是否上架销售',
  `cat_id` smallint(5) unsigned NOT NULL COMMENT '类别',
  `brand_id` smallint(5) unsigned NOT NULL COMMENT '品牌',
  `support_periods` varchar(255) DEFAULT NULL COMMENT '支持分期数 跟主关系表数值一致',
  `first_pay` varchar(255) DEFAULT NULL COMMENT '首付比例列表',
  `goods_brief` varchar(255) DEFAULT NULL COMMENT '商品的简短描述',
  `goods_desc` text COMMENT '商品的详细描述',
  `is_job` tinyint(1) unsigned DEFAULT '0' COMMENT '是否支持兼职，0：不支持， 1：支持',
  `is_default` tinyint(1) unsigned DEFAULT '0' COMMENT '0，非默认；1，默认',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`goods_id`),
  UNIQUE KEY `goods_sn` (`goods_sn`),
  KEY `goods_map_id` (`goods_map_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11807 DEFAULT CHARSET=utf8 COMMENT='商品表';

-- ----------------------------
-- Table structure for ecs_goods_appraise
-- ----------------------------
DROP TABLE IF EXISTS `ecs_goods_appraise`;
CREATE TABLE `ecs_goods_appraise` (
  `appraise_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL COMMENT '用户UID',
  `star` tinyint(4) unsigned NOT NULL DEFAULT '10' COMMENT '总评分数 1-10',
  `goods_star` tinyint(4) unsigned DEFAULT NULL COMMENT '商品评价分数 1-10',
  `service_star` tinyint(4) unsigned DEFAULT NULL COMMENT '服务评价',
  `content` varchar(255) DEFAULT NULL COMMENT '内容',
  `order_id` int(10) unsigned NOT NULL COMMENT '订单ID',
  `goods_map_id` int(10) unsigned NOT NULL COMMENT '商品关系ID',
  `goods_id` int(10) unsigned DEFAULT NULL COMMENT '商品ID',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`appraise_id`),
  UNIQUE KEY `order_goods_uid` (`uid`,`order_id`,`goods_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1203 DEFAULT CHARSET=utf8 COMMENT='商品评价表';

-- ----------------------------
-- Table structure for ecs_goods_appraise_false
-- ----------------------------
DROP TABLE IF EXISTS `ecs_goods_appraise_false`;
CREATE TABLE `ecs_goods_appraise_false` (
  `appraise_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned DEFAULT '0' COMMENT '用户UID',
  `name` varchar(20) NOT NULL,
  `star` tinyint(4) unsigned NOT NULL DEFAULT '10' COMMENT '总评分数 1-10',
  `goods_star` tinyint(4) unsigned DEFAULT NULL COMMENT '商品评价分数 1-10',
  `service_star` tinyint(4) unsigned DEFAULT NULL COMMENT '服务评价',
  `content` varchar(255) DEFAULT NULL COMMENT '内容',
  `order_id` int(10) unsigned DEFAULT NULL COMMENT '订单ID',
  `goods_map_id` int(10) unsigned NOT NULL COMMENT '商品关系ID',
  `goods_id` int(10) unsigned DEFAULT NULL COMMENT '商品ID',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`appraise_id`),
  UNIQUE KEY `order_goods_uid` (`uid`,`order_id`,`goods_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=155 DEFAULT CHARSET=utf8 COMMENT='商品评价表';

-- ----------------------------
-- Table structure for ecs_goods_app_navigation
-- ----------------------------
DROP TABLE IF EXISTS `ecs_goods_app_navigation`;
CREATE TABLE `ecs_goods_app_navigation` (
  `app_na_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) unsigned DEFAULT '0' COMMENT '父节点id ，默认0',
  `level` tinyint(3) unsigned DEFAULT '1' COMMENT '1：一级导航 2：二级导航,目前只有2级',
  `type` varchar(10) DEFAULT NULL COMMENT '类型 category 分类  brand 品牌',
  `display_name` varchar(30) DEFAULT NULL COMMENT '显示的名字',
  `sort` tinyint(4) unsigned DEFAULT '0' COMMENT '排序',
  `img_path` varchar(255) DEFAULT NULL COMMENT '图片链接',
  `real_id` smallint(5) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`app_na_id`)
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8 COMMENT='APP 分类管理';

-- ----------------------------
-- Table structure for ecs_goods_attr
-- ----------------------------
DROP TABLE IF EXISTS `ecs_goods_attr`;
CREATE TABLE `ecs_goods_attr` (
  `attr_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `goods_map_id` int(11) unsigned NOT NULL COMMENT '商品类型',
  `attr_name` varchar(255) NOT NULL COMMENT '属性名称',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`attr_id`),
  KEY `goods_map_id` (`goods_map_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2598 DEFAULT CHARSET=utf8 COMMENT='商品类型属性表';

-- ----------------------------
-- Table structure for ecs_goods_attr_val
-- ----------------------------
DROP TABLE IF EXISTS `ecs_goods_attr_val`;
CREATE TABLE `ecs_goods_attr_val` (
  `attr_val_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goods_map_id` int(11) unsigned NOT NULL COMMENT '商品关系主表ID',
  `attr_id` int(11) NOT NULL COMMENT '属性ID',
  `attr_value` varchar(255) NOT NULL COMMENT '属性值',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`attr_val_id`),
  KEY `attr_id` (`attr_id`) USING BTREE,
  KEY `goods_map_id` (`goods_map_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8087 DEFAULT CHARSET=utf8 COMMENT='商品属性值表';

-- ----------------------------
-- Table structure for ecs_goods_brand
-- ----------------------------
DROP TABLE IF EXISTS `ecs_goods_brand`;
CREATE TABLE `ecs_goods_brand` (
  `brand_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `brand_name` varchar(60) NOT NULL DEFAULT '' COMMENT '品牌名字',
  `brand_logo` varchar(80) DEFAULT '',
  `brand_desc` text COMMENT '描述',
  `brand_code` varchar(10) DEFAULT NULL,
  `site_url` varchar(255) DEFAULT '' COMMENT '网址',
  `sort_order` tinyint(3) unsigned DEFAULT '0' COMMENT '排序顺序',
  `is_show` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`brand_id`),
  UNIQUE KEY `brand_name` (`brand_name`) USING BTREE,
  KEY `is_show` (`is_show`)
) ENGINE=InnoDB AUTO_INCREMENT=207 DEFAULT CHARSET=utf8 COMMENT='商品品牌';

-- ----------------------------
-- Table structure for ecs_goods_category
-- ----------------------------
DROP TABLE IF EXISTS `ecs_goods_category`;
CREATE TABLE `ecs_goods_category` (
  `cat_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `fid` int(11) DEFAULT '0' COMMENT '父ID',
  `lid` int(11) DEFAULT NULL COMMENT '级别ID',
  `cat_name` varchar(255) NOT NULL COMMENT '类型名字',
  `cat_desc` varchar(255) DEFAULT NULL COMMENT '类别描述',
  `is_show` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `sort` tinyint(4) unsigned DEFAULT '0' COMMENT 'sort',
  `cname` varchar(10) NOT NULL COMMENT '别名',
  `cat_code` varchar(10) DEFAULT NULL,
  `m_icon` varchar(255) DEFAULT NULL COMMENT '类别货号ID',
  `p_icon` varchar(255) DEFAULT NULL,
  `banner_1_image` varchar(255) DEFAULT NULL,
  `banner_1_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`cat_id`),
  UNIQUE KEY `cname` (`cname`) USING BTREE,
  UNIQUE KEY `cat_name` (`cat_name`),
  KEY `is_show` (`is_show`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8 COMMENT='商品类别表';

-- ----------------------------
-- Table structure for ecs_goods_img
-- ----------------------------
DROP TABLE IF EXISTS `ecs_goods_img`;
CREATE TABLE `ecs_goods_img` (
  `img_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(3) unsigned NOT NULL COMMENT '相应类型ID 1：map 2：goods',
  `type_id` int(10) unsigned NOT NULL,
  `img_path` varchar(255) NOT NULL COMMENT '实际图片',
  `img_desc` varchar(255) DEFAULT NULL COMMENT '描述',
  `img_suffix` varchar(255) DEFAULT NULL COMMENT '图片后缀',
  `img_type` varchar(255) NOT NULL COMMENT '图片类型 1：商品轮播图片 2: 商品相册  3：新品左 4：新品右  5：列表',
  `thumb_path` varchar(255) DEFAULT NULL COMMENT '微缩图片',
  `storage` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `background` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`img_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9090 DEFAULT CHARSET=utf8 COMMENT='商品图片';

-- ----------------------------
-- Table structure for ecs_goods_jobs
-- ----------------------------
DROP TABLE IF EXISTS `ecs_goods_jobs`;
CREATE TABLE `ecs_goods_jobs` (
  `goods_job_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `goods_id` int(11) unsigned NOT NULL COMMENT '商品ID,goods_id',
  `period` tinyint(11) unsigned NOT NULL DEFAULT '0' COMMENT '分期数',
  `max_price` int(10) unsigned DEFAULT NULL COMMENT '最大兼职金额 100 的整数倍',
  `min_price` int(10) unsigned DEFAULT '0' COMMENT '最小兼职金额 100 的整数倍',
  `default_price` int(10) unsigned DEFAULT NULL COMMENT '默认兼职金额 100 的整数倍',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`goods_job_id`),
  UNIQUE KEY `goods_period` (`goods_id`,`period`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=9287 DEFAULT CHARSET=utf8 COMMENT='商品对应兼职表';

-- ----------------------------
-- Table structure for ecs_goods_jobs_edition
-- ----------------------------
DROP TABLE IF EXISTS `ecs_goods_jobs_edition`;
CREATE TABLE `ecs_goods_jobs_edition` (
  `goods_job_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `goods_map_id` int(11) unsigned NOT NULL COMMENT '商品ID,goods_id',
  `default_price` int(10) unsigned DEFAULT NULL COMMENT '默认兼职金额 100 的整数倍',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`goods_job_id`),
  UNIQUE KEY `goods_period` (`goods_map_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for ecs_goods_map
-- ----------------------------
DROP TABLE IF EXISTS `ecs_goods_map`;
CREATE TABLE `ecs_goods_map` (
  `goods_map_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goods_map_name` varchar(255) NOT NULL COMMENT '商品名字',
  `cat_id` smallint(5) unsigned NOT NULL COMMENT '类别',
  `brand_id` smallint(5) unsigned NOT NULL COMMENT '品牌',
  `support_periods` varchar(255) DEFAULT NULL COMMENT '支持分期数  用 | 隔开',
  `first_pay` varchar(255) DEFAULT NULL COMMENT '首付比例列表',
  `goods_desc` text COMMENT '商品描述',
  `goods_spec` text COMMENT '商品规格参数',
  `market_price` decimal(10,2) DEFAULT NULL,
  `shop_price` decimal(10,2) DEFAULT NULL,
  `goods_brief` varchar(255) DEFAULT NULL,
  `recommend` tinyint(1) unsigned DEFAULT '0',
  `available` tinyint(1) unsigned DEFAULT '1' COMMENT '1 : 有货，0：无货',
  `is_job` tinyint(1) unsigned DEFAULT '0' COMMENT '0  不提供 1 提供',
  `is_first` tinyint(4) unsigned DEFAULT '1' COMMENT '是否在前面显示首付，0：不显示，1：显示',
  `is_credit` tinyint(1) DEFAULT '0' COMMENT '是否信用钱包类型 0：不是  1：是',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`goods_map_id`),
  KEY `map_cat` (`cat_id`),
  CONSTRAINT `map_cat` FOREIGN KEY (`cat_id`) REFERENCES `ecs_goods_category` (`cat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1407 DEFAULT CHARSET=utf8 COMMENT='商品关系主表';

-- ----------------------------
-- Table structure for ecs_goods_navigation
-- ----------------------------
DROP TABLE IF EXISTS `ecs_goods_navigation`;
CREATE TABLE `ecs_goods_navigation` (
  `navigation_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) unsigned DEFAULT '0' COMMENT '父节点id ，默认0',
  `level` tinyint(3) unsigned DEFAULT '1' COMMENT '1：一级导航 2：二级导航,目前只有2级',
  `type` varchar(10) DEFAULT NULL COMMENT '类型 category 分类  brand 品牌',
  `display_name` varchar(30) DEFAULT NULL COMMENT '显示的名字',
  `sort` tinyint(4) unsigned DEFAULT '0' COMMENT '排序',
  `img_path` varchar(255) DEFAULT NULL COMMENT '图片链接',
  `real_id` smallint(5) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`navigation_id`)
) ENGINE=InnoDB AUTO_INCREMENT=98 DEFAULT CHARSET=utf8 COMMENT='PC 导航管理';

-- ----------------------------
-- Table structure for ecs_goods_price
-- ----------------------------
DROP TABLE IF EXISTS `ecs_goods_price`;
CREATE TABLE `ecs_goods_price` (
  `goods_price_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goods_id` int(10) unsigned NOT NULL,
  `period` tinyint(3) unsigned NOT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`goods_price_id`),
  UNIQUE KEY `goods_period` (`goods_id`,`period`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=14936 DEFAULT CHARSET=utf8 COMMENT='商品不同分期的价格';

-- ----------------------------
-- Table structure for ecs_group_permission
-- ----------------------------
DROP TABLE IF EXISTS `ecs_group_permission`;
CREATE TABLE `ecs_group_permission` (
  `id` varchar(16) NOT NULL,
  `sys_module_id` varchar(16) NOT NULL COMMENT '对应 ecs_module 的id',
  `user_group_id` varchar(16) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `un` (`sys_module_id`,`user_group_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_guide_img
-- ----------------------------
DROP TABLE IF EXISTS `ecs_guide_img`;
CREATE TABLE `ecs_guide_img` (
  `gid` int(11) NOT NULL AUTO_INCREMENT,
  `img_src` varchar(255) DEFAULT NULL COMMENT '路径',
  `status` int(11) NOT NULL DEFAULT '1' COMMENT '上下架',
  `remark` int(11) NOT NULL COMMENT '描述',
  `type` int(11) NOT NULL COMMENT '类型 1安卓 2 ios',
  PRIMARY KEY (`gid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_indepot_orders
-- ----------------------------
DROP TABLE IF EXISTS `ecs_indepot_orders`;
CREATE TABLE `ecs_indepot_orders` (
  `ioid` int(11) NOT NULL AUTO_INCREMENT,
  `serial_no` varchar(100) NOT NULL,
  `poid` int(11) NOT NULL,
  `goods_name` varchar(250) NOT NULL,
  `goods_id` int(11) NOT NULL,
  `goods_map_id` int(11) NOT NULL,
  `goods_price` decimal(10,2) NOT NULL,
  `goods_num` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `pay_way` int(11) NOT NULL COMMENT '付款方式(到付、已付、月结)',
  `depot_id` int(11) NOT NULL COMMENT '仓库id',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `remark` varchar(50) NOT NULL,
  `create_time` datetime NOT NULL,
  `purchase_time` datetime NOT NULL,
  `invoice` int(11) NOT NULL COMMENT '发票情况',
  `invoice_no` varchar(50) NOT NULL,
  `update_time` datetime NOT NULL,
  `busniess_no` varchar(32) NOT NULL,
  `client` int(11) NOT NULL DEFAULT '1' COMMENT '1：采购入库 2：总仓-分仓 4：退换货入库',
  `handler` int(11) NOT NULL,
  PRIMARY KEY (`ioid`)
) ENGINE=InnoDB AUTO_INCREMENT=3000281 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_intergral
-- ----------------------------
DROP TABLE IF EXISTS `ecs_intergral`;
CREATE TABLE `ecs_intergral` (
  `itid` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  PRIMARY KEY (`itid`)
) ENGINE=InnoDB AUTO_INCREMENT=20801 DEFAULT CHARSET=utf8 COMMENT='积分表';

-- ----------------------------
-- Table structure for ecs_intergral_detail
-- ----------------------------
DROP TABLE IF EXISTS `ecs_intergral_detail`;
CREATE TABLE `ecs_intergral_detail` (
  `itdid` int(11) NOT NULL AUTO_INCREMENT,
  `itid` int(11) NOT NULL,
  `num` int(11) NOT NULL COMMENT '积分数',
  `status` int(11) NOT NULL DEFAULT '1' COMMENT '1:加 2：减',
  `type` int(11) NOT NULL DEFAULT '0',
  `source` int(11) NOT NULL COMMENT '1：收货地址 2：学生认证 3：商品评评 4：签到',
  `create_time` datetime NOT NULL,
  PRIMARY KEY (`itdid`)
) ENGINE=InnoDB AUTO_INCREMENT=47583 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_iphone6
-- ----------------------------
DROP TABLE IF EXISTS `ecs_iphone6`;
CREATE TABLE `ecs_iphone6` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `phone` varchar(20) NOT NULL,
  `client_type` varchar(10) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  `custom_id` int(11) NOT NULL DEFAULT '0',
  `period` tinyint(4) NOT NULL DEFAULT '1',
  `desc` varchar(50) DEFAULT NULL,
  `create_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1836 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_job
-- ----------------------------
DROP TABLE IF EXISTS `ecs_job`;
CREATE TABLE `ecs_job` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `mode` int(1) DEFAULT '0' COMMENT '1/新模式',
  `bus_id` int(10) DEFAULT NULL COMMENT '商户ID,自主发布为0',
  `type_id` int(10) NOT NULL COMMENT '类型ID',
  `province_id` int(5) DEFAULT NULL COMMENT '省级',
  `city_id` int(5) DEFAULT '0' COMMENT '城市ID',
  `area_id` int(5) DEFAULT '0' COMMENT '县级ID',
  `title` varchar(80) NOT NULL COMMENT '标题',
  `job_num` int(10) NOT NULL COMMENT '招聘人数',
  `wage` varchar(10) NOT NULL COMMENT '工资待遇',
  `address` varchar(255) DEFAULT NULL COMMENT '工作地址',
  `region` varchar(80) NOT NULL COMMENT '工作地区',
  `work_date` varchar(30) DEFAULT NULL COMMENT '工作日期',
  `work_time` varchar(50) NOT NULL COMMENT '工作时间',
  `settlement` varchar(30) NOT NULL COMMENT '结算方式',
  `info` text COMMENT '兼职描述',
  `uname` varchar(255) NOT NULL COMMENT '联系人',
  `tel` char(15) NOT NULL COMMENT '联系电话',
  `job_state` int(1) NOT NULL DEFAULT '1' COMMENT '岗位状态 1/报名 2/结束',
  `is_state` int(1) NOT NULL DEFAULT '0' COMMENT '状态 0/未结束 1/结束',
  `browse_count` int(10) NOT NULL COMMENT '浏览次数',
  `is_sort` int(5) NOT NULL DEFAULT '0' COMMENT '推荐SORT',
  `source` varchar(20) DEFAULT NULL COMMENT '来源',
  `is_del` int(1) DEFAULT '1' COMMENT '1/正常 0/关闭',
  `ctime` int(10) DEFAULT NULL,
  `utime` int(10) DEFAULT NULL,
  `close_time` int(10) NOT NULL COMMENT '截止时间',
  `location` varchar(60) NOT NULL COMMENT '经纬度',
  PRIMARY KEY (`id`),
  KEY `bus_id` (`bus_id`),
  KEY `type_id` (`type_id`),
  KEY `is_state` (`is_state`),
  KEY `is_sort` (`is_sort`)
) ENGINE=InnoDB AUTO_INCREMENT=2565 DEFAULT CHARSET=utf8 COMMENT='岗位详情';

-- ----------------------------
-- Table structure for ecs_job_apply
-- ----------------------------
DROP TABLE IF EXISTS `ecs_job_apply`;
CREATE TABLE `ecs_job_apply` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `job_id` int(10) NOT NULL COMMENT '岗位ID',
  `students_id` int(10) NOT NULL COMMENT '学生ID',
  `title` varchar(80) DEFAULT NULL COMMENT '兼职标题',
  `school` varchar(50) DEFAULT NULL COMMENT '学校',
  `uname` varchar(50) DEFAULT NULL,
  `mobile` char(11) DEFAULT NULL COMMENT '手机号码',
  `info` varchar(255) NOT NULL COMMENT '描述',
  `is_state` int(1) DEFAULT '1' COMMENT '1/申请 2/确认工作 3/完成工作',
  `note_a` varchar(255) DEFAULT NULL COMMENT '申请状态日记',
  `note_b` varchar(255) DEFAULT NULL COMMENT '指派日记',
  `source` int(1) DEFAULT '1' COMMENT '1/主动申请 2/兼职购物',
  `job_order_id` int(10) DEFAULT NULL COMMENT '兼职购物工单ID',
  `orders_id` int(10) DEFAULT '0' COMMENT '申请apply ID',
  `orders_num` varchar(30) DEFAULT NULL COMMENT '订单号',
  `is_pay` int(1) DEFAULT '0' COMMENT '默认0/未支付   1/已支付',
  `apply_time` int(10) NOT NULL COMMENT '申请时间',
  PRIMARY KEY (`id`),
  KEY `job_id` (`job_id`),
  KEY `member_id` (`students_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4517 DEFAULT CHARSET=utf8 COMMENT='岗位申请';

-- ----------------------------
-- Table structure for ecs_job_apply_new
-- ----------------------------
DROP TABLE IF EXISTS `ecs_job_apply_new`;
CREATE TABLE `ecs_job_apply_new` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `job_id` int(10) NOT NULL COMMENT '岗位ID',
  `uid` int(10) NOT NULL COMMENT '学生ID',
  `is_state` int(1) DEFAULT '1' COMMENT '1/申请 2/成功 3/取消 4/报名后取消 5/用户取消报名',
  `succeed_note` varchar(255) DEFAULT NULL COMMENT '成功备注',
  `cancel_note` varchar(255) DEFAULT NULL COMMENT '取消备注',
  `source` int(1) DEFAULT '1' COMMENT '1/主动申请 2/兼职购物',
  `stu_cancel_why` int(1) NOT NULL COMMENT '取消原因 1/商户 2/自己',
  `stu_cancel_info` varchar(510) DEFAULT NULL,
  `stu_cancel_time` int(10) DEFAULT NULL,
  `apply_time` int(10) NOT NULL COMMENT '申请时间',
  `succeed_time` int(10) NOT NULL COMMENT '成功时间',
  `cancel_time` int(10) NOT NULL COMMENT '取消时间',
  `renege_time` int(10) DEFAULT NULL COMMENT '违约时间',
  `ctime` int(10) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `job_id` (`job_id`) USING BTREE,
  KEY `uid` (`uid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1072 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for ecs_job_click
-- ----------------------------
DROP TABLE IF EXISTS `ecs_job_click`;
CREATE TABLE `ecs_job_click` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `job_id` int(10) NOT NULL COMMENT '岗位ID',
  `uid` int(10) NOT NULL COMMENT '学生ID',
  `platform` int(1) NOT NULL COMMENT '平台/1-5代表几星',
  `merchants` int(1) NOT NULL COMMENT '商户/1-5代表几星',
  `comments` varchar(255) DEFAULT NULL COMMENT '用户评论',
  `click` int(1) DEFAULT '0' COMMENT '0/没打 1/有打',
  `click_time` int(10) NOT NULL COMMENT '打卡时间',
  `comments_time` int(10) NOT NULL COMMENT '评论时间/包含平星时间',
  `ctime` int(10) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`) USING BTREE,
  KEY `job_id` (`job_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=117 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for ecs_job_order
-- ----------------------------
DROP TABLE IF EXISTS `ecs_job_order`;
CREATE TABLE `ecs_job_order` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `orders_id` int(10) NOT NULL COMMENT '订单ID',
  `orders_num` varchar(30) NOT NULL COMMENT '订单号',
  `uid` int(10) DEFAULT NULL COMMENT '用户ID',
  `uname` varchar(25) DEFAULT NULL COMMENT '用户名称',
  `mobile` char(15) DEFAULT NULL COMMENT '手机号',
  `goods_price` decimal(10,2) NOT NULL COMMENT '商品总价',
  `job_price` decimal(10,2) NOT NULL COMMENT '兼职金额',
  `money_total` decimal(10,2) NOT NULL COMMENT '已还兼职金额',
  `pay_type` int(1) NOT NULL DEFAULT '1' COMMENT ' 默认兼职还款  1/ 兼职还款  2 / 现金还款',
  `position` varchar(20) DEFAULT NULL COMMENT '意向职位',
  `is_state` int(1) NOT NULL DEFAULT '0' COMMENT '默认未还清 0/ 未还清 1/已还清',
  `ctime` int(10) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_num` (`orders_num`),
  KEY `uid` (`uid`),
  KEY `orders_id` (`orders_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1835 DEFAULT CHARSET=utf8 COMMENT='兼职工单';

-- ----------------------------
-- Table structure for ecs_job_position_goods
-- ----------------------------
DROP TABLE IF EXISTS `ecs_job_position_goods`;
CREATE TABLE `ecs_job_position_goods` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `goods_id` int(10) NOT NULL COMMENT '商品ID',
  `cname` varchar(80) NOT NULL COMMENT '名称',
  `prices` decimal(10,0) DEFAULT NULL COMMENT '价格',
  `info` varchar(50) DEFAULT NULL COMMENT '兼职描述',
  `position_param` varchar(255) NOT NULL COMMENT '职位参数json',
  `ctime` int(10) NOT NULL COMMENT '创建',
  `utime` int(10) NOT NULL COMMENT '更新',
  PRIMARY KEY (`id`),
  KEY `goods_id` (`goods_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COMMENT='职位关联商品';

-- ----------------------------
-- Table structure for ecs_job_report
-- ----------------------------
DROP TABLE IF EXISTS `ecs_job_report`;
CREATE TABLE `ecs_job_report` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `is_type` int(1) NOT NULL DEFAULT '0' COMMENT '类型',
  `job_id` int(10) NOT NULL COMMENT '岗位ID',
  `students_id` int(10) DEFAULT '0' COMMENT '用户ID 默认为0',
  `info` varchar(255) NOT NULL COMMENT '描述',
  `ctime` int(10) DEFAULT NULL,
  `utime` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `job_id` (`job_id`),
  KEY `member_id` (`students_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='举报岗位';

-- ----------------------------
-- Table structure for ecs_job_type
-- ----------------------------
DROP TABLE IF EXISTS `ecs_job_type`;
CREATE TABLE `ecs_job_type` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `cname` varchar(30) NOT NULL COMMENT '过期时间',
  `info` varchar(255) NOT NULL COMMENT '描述',
  `ice` varchar(255) DEFAULT NULL COMMENT '图标',
  `goods_ice` varchar(255) DEFAULT NULL COMMENT '兼职购物图标',
  `is_sort` int(3) DEFAULT NULL,
  `latitude_param` text COMMENT '纬度参数',
  `days_time` varchar(250) DEFAULT NULL COMMENT '兼职天数',
  `is_del` int(1) DEFAULT '1' COMMENT '0/关闭 1/开启',
  `ctime` int(10) DEFAULT NULL,
  `utime` int(10) DEFAULT NULL,
  `font_color` varchar(30) NOT NULL COMMENT '字体颜色',
  PRIMARY KEY (`id`),
  KEY `cname` (`cname`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COMMENT='岗位类型';

-- ----------------------------
-- Table structure for ecs_latitude_param
-- ----------------------------
DROP TABLE IF EXISTS `ecs_latitude_param`;
CREATE TABLE `ecs_latitude_param` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `fid` int(10) DEFAULT '0' COMMENT '父ID',
  `lid` int(5) DEFAULT '1' COMMENT '层级ID',
  `cname` varchar(30) NOT NULL COMMENT '名称',
  `info` varchar(255) DEFAULT NULL COMMENT '描述',
  `is_del` int(1) DEFAULT '1' COMMENT '1/开启 0/关闭',
  `ctime` int(10) NOT NULL COMMENT '创建',
  `utime` int(10) NOT NULL COMMENT '更新',
  PRIMARY KEY (`id`),
  KEY `fid` (`fid`),
  KEY `lid` (`lid`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='纬度参数';

-- ----------------------------
-- Table structure for ecs_limit_activities
-- ----------------------------
DROP TABLE IF EXISTS `ecs_limit_activities`;
CREATE TABLE `ecs_limit_activities` (
  `act_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) unsigned NOT NULL COMMENT '活动类型 ，1：信用钱包',
  `goods_map_id` int(10) unsigned NOT NULL,
  `is_show_list` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否现在是商品列表中 0 ：不显示 1：显示',
  `start_time` timestamp NULL DEFAULT NULL COMMENT '开始时间',
  `end_time` timestamp NULL DEFAULT NULL COMMENT '结束时间',
  `number` int(10) unsigned NOT NULL COMMENT '活动数量',
  `borrow` decimal(10,2) unsigned DEFAULT NULL COMMENT '借多少，只在type = 1时获取',
  `repay` decimal(10,2) unsigned DEFAULT NULL COMMENT '还，只在type = 1时获取',
  `enable` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否生效 1 生效 0 不生效',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`act_id`),
  KEY `goods` (`goods_map_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for ecs_migrations
-- ----------------------------
DROP TABLE IF EXISTS `ecs_migrations`;
CREATE TABLE `ecs_migrations` (
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_module
-- ----------------------------
DROP TABLE IF EXISTS `ecs_module`;
CREATE TABLE `ecs_module` (
  `id` varchar(16) NOT NULL,
  `module_icon` varchar(20) DEFAULT NULL,
  `module_name` varchar(20) NOT NULL,
  `module_parent_id` varchar(16) DEFAULT NULL,
  `module_type` varchar(10) DEFAULT NULL,
  `module_resource` varchar(60) DEFAULT NULL,
  `module_order` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_money
-- ----------------------------
DROP TABLE IF EXISTS `ecs_money`;
CREATE TABLE `ecs_money` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `is_type` int(1) NOT NULL DEFAULT '1' COMMENT '1/现金',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '学生ID',
  `apply_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '申请ID',
  `job_id` int(10) DEFAULT '0' COMMENT '岗位ID',
  `job_order_id` int(10) DEFAULT '0' COMMENT '兼职购物工单ID',
  `water_num` char(30) DEFAULT NULL COMMENT '流水编号',
  `wage` decimal(10,2) DEFAULT '0.00' COMMENT '工资',
  `orders_num` varchar(30) DEFAULT NULL,
  `action_id` int(10) DEFAULT NULL COMMENT '操作者ID',
  `action_name` varchar(30) DEFAULT NULL COMMENT '操作者姓名',
  `is_pay` int(1) DEFAULT '1' COMMENT '1/普通打工  2/兼职购物',
  `liu_id` int(10) DEFAULT NULL COMMENT '返回流水号',
  `liu_time` int(10) DEFAULT NULL COMMENT '返回请求时间',
  `note` varchar(250) NOT NULL COMMENT '备注',
  `ctime` int(10) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `is_type` (`is_type`),
  KEY `students_id` (`uid`),
  KEY `job_apply_id` (`apply_id`)
) ENGINE=InnoDB AUTO_INCREMENT=486 DEFAULT CHARSET=utf8 COMMENT='虚拟币记录';

-- ----------------------------
-- Table structure for ecs_new_activity
-- ----------------------------
DROP TABLE IF EXISTS `ecs_new_activity`;
CREATE TABLE `ecs_new_activity` (
  `act_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned DEFAULT NULL COMMENT '用户uid',
  `type` int(10) unsigned NOT NULL COMMENT '不同的类型',
  `phone` varchar(255) DEFAULT NULL COMMENT '手机号码',
  `theme` varchar(255) DEFAULT NULL COMMENT '主题内容',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`act_id`),
  KEY `type` (`type`),
  KEY `phone` (`phone`),
  KEY `t,p` (`type`,`phone`)
) ENGINE=InnoDB AUTO_INCREMENT=15283 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_new_edition_orders
-- ----------------------------
DROP TABLE IF EXISTS `ecs_new_edition_orders`;
CREATE TABLE `ecs_new_edition_orders` (
  `order_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `is_type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1/分期 2/兼职 3/白条',
  `business_no` bigint(20) unsigned NOT NULL COMMENT '业务号',
  `uid` int(11) unsigned NOT NULL,
  `goods_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订单商品ID',
  `goods_name` varchar(255) NOT NULL COMMENT '订单商品名称',
  `goods_price` decimal(10,2) NOT NULL COMMENT '订单商品价格',
  `job_state` int(11) NOT NULL DEFAULT '0' COMMENT '是否兼职',
  `job_price` decimal(10,2) NOT NULL COMMENT '兼职价格',
  `periods` tinyint(4) unsigned NOT NULL COMMENT '选择分期数',
  `pay_percent` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '首付百分比',
  `pay_first_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '分期首付金额',
  `monthly_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '月供金额',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0：提交审核',
  `dis_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '优惠金额',
  `dis_type` varchar(15) DEFAULT NULL COMMENT '优惠类型',
  `dis_id` varchar(150) DEFAULT NULL COMMENT '优惠序号',
  `client_type` varchar(20) DEFAULT NULL COMMENT '客户端类型android、web、ios',
  `is_act` tinyint(1) unsigned DEFAULT '0' COMMENT '是否活动，0：不是活动 1 : 是活动',
  `create_orders_time` timestamp NULL DEFAULT NULL COMMENT '下订单时间，预防漏单',
  `verify_time` timestamp NULL DEFAULT NULL COMMENT '审核时间',
  `finish_orders_time` timestamp NULL DEFAULT NULL COMMENT '订单完成时间',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`order_id`),
  UNIQUE KEY `bus` (`business_no`),
  KEY `uid` (`uid`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=104556 DEFAULT CHARSET=utf8 COMMENT='v3.0 新版本订单表 20150903';

-- ----------------------------
-- Table structure for ecs_ngd_examine
-- ----------------------------
DROP TABLE IF EXISTS `ecs_ngd_examine`;
CREATE TABLE `ecs_ngd_examine` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `phone` bigint(12) DEFAULT NULL,
  `status` tinyint(2) DEFAULT '0' COMMENT '0 未处理 1 已处理',
  `type` tinyint(2) DEFAULT '0' COMMENT '0 工薪购，1工薪贷',
  `remarks` varchar(100) DEFAULT NULL COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=496 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_orders
-- ----------------------------
DROP TABLE IF EXISTS `ecs_orders`;
CREATE TABLE `ecs_orders` (
  `order_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `is_type` tinyint(1) unsigned DEFAULT NULL COMMENT '1/分期 2/兼职 3/白条',
  `business_no` bigint(20) unsigned NOT NULL COMMENT '业务号',
  `stu_id` int(11) NOT NULL COMMENT '分期学生id',
  `uid` int(11) DEFAULT NULL,
  `goods_id` int(11) unsigned NOT NULL COMMENT '订单商品ID',
  `goods_name` varchar(255) NOT NULL COMMENT '订单商品名称',
  `goods_price` decimal(10,2) NOT NULL COMMENT '订单商品价格',
  `job_state` int(11) NOT NULL DEFAULT '0' COMMENT '是否兼职',
  `job_price` decimal(10,2) NOT NULL COMMENT '兼职价格',
  `periods` tinyint(4) unsigned NOT NULL COMMENT '选择分期数',
  `pay_first_price` decimal(10,2) unsigned DEFAULT NULL COMMENT '分期首付金额',
  `monthly_price` decimal(10,2) DEFAULT NULL COMMENT '月供金额',
  `refund_day` varchar(6) DEFAULT NULL COMMENT '每月还款日',
  `pay_student_id` int(11) NOT NULL DEFAULT '0',
  `city_manager_id` int(11) unsigned DEFAULT NULL COMMENT '签约的城市经理ID',
  `city_manager_name` varchar(255) DEFAULT NULL COMMENT '签约的城市经理名字',
  `angel_id` int(11) NOT NULL,
  `custom_id` int(11) NOT NULL DEFAULT '0' COMMENT '售后人员',
  `cid` int(11) NOT NULL DEFAULT '0',
  `remarks` text COMMENT '备注',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0审核中 1审核成功 2审核失败 3 再次审核',
  `serial_number` varchar(50) DEFAULT NULL,
  `IMEI` varchar(50) DEFAULT NULL,
  `allot_time` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`order_id`),
  UNIQUE KEY `buss` (`business_no`),
  KEY `stu_id_ind` (`stu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8033 DEFAULT CHARSET=utf8 COMMENT='订单表';

-- ----------------------------
-- Table structure for ecs_order_goods
-- ----------------------------
DROP TABLE IF EXISTS `ecs_order_goods`;
CREATE TABLE `ecs_order_goods` (
  `business_no` bigint(20) unsigned NOT NULL COMMENT '订单号',
  `uid` int(10) unsigned NOT NULL COMMENT '用户UID',
  `goods_id` int(10) unsigned NOT NULL COMMENT '订单商品ID',
  `goods_name` varchar(255) DEFAULT NULL COMMENT '订单商品名称',
  `goods_price` decimal(10,2) unsigned DEFAULT NULL COMMENT '订单商品价格(总价)',
  `serial_number` varchar(50) DEFAULT NULL,
  `IMEI` varchar(50) DEFAULT NULL,
  `goods_status` tinyint(4) unsigned DEFAULT NULL COMMENT '货物状态',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`business_no`),
  KEY `uid` (`uid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for ecs_order_ious
-- ----------------------------
DROP TABLE IF EXISTS `ecs_order_ious`;
CREATE TABLE `ecs_order_ious` (
  `business_no` bigint(20) unsigned NOT NULL COMMENT '订单号',
  `uid` int(10) unsigned NOT NULL COMMENT '用户UID',
  `money` decimal(10,2) unsigned NOT NULL COMMENT '白条金额(元)',
  `ispay` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否财务支付 0：没有  非 1： 已经支付了',
  `pay_day` timestamp NULL DEFAULT NULL COMMENT '财务 打款时间',
  `pay_way` varchar(20) DEFAULT NULL COMMENT '财务打款方式  ',
  `remark` varchar(255) DEFAULT NULL COMMENT '财务打款备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`business_no`),
  KEY `uid` (`uid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for ecs_order_log
-- ----------------------------
DROP TABLE IF EXISTS `ecs_order_log`;
CREATE TABLE `ecs_order_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `mode` int(1) unsigned NOT NULL COMMENT '1/订单 2/货物 3/审核',
  `uid` int(10) unsigned NOT NULL COMMENT '用户ID',
  `business_no` bigint(20) unsigned NOT NULL COMMENT '业务号',
  `info` varchar(250) DEFAULT NULL COMMENT '描述',
  `state_log` int(4) NOT NULL COMMENT '业务记录',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`) USING BTREE,
  KEY `mode` (`mode`) USING BTREE,
  KEY `business_no` (`business_no`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=510 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for ecs_order_main
-- ----------------------------
DROP TABLE IF EXISTS `ecs_order_main`;
CREATE TABLE `ecs_order_main` (
  `order_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '订单自增id',
  `is_type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1/商品分析  2/兼职购物 3/白条商品',
  `business_no` bigint(25) unsigned NOT NULL COMMENT '订单号',
  `uid` int(10) unsigned NOT NULL COMMENT '用户UID',
  `order_price` decimal(10,2) unsigned DEFAULT NULL COMMENT '订单价格',
  `custom_id` int(10) unsigned DEFAULT NULL COMMENT '客服ID，自动分配的',
  `angel_id` int(10) unsigned DEFAULT NULL COMMENT '校园经理ID',
  `status` tinyint(4) unsigned DEFAULT NULL COMMENT '订单状态',
  `goods_status` tinyint(4) unsigned DEFAULT NULL COMMENT '货物状态',
  `periods` tinyint(2) unsigned DEFAULT NULL COMMENT '分期数',
  `first_pay_percent` tinyint(3) unsigned DEFAULT NULL COMMENT '首付比例',
  `first_pay_price` decimal(10,2) unsigned DEFAULT NULL COMMENT '首付金额 由goods_price * first_pay_percent 计算而来',
  `monthly_price` decimal(10,2) unsigned DEFAULT NULL COMMENT '月供金额',
  `dis_amount` decimal(10,2) unsigned DEFAULT NULL COMMENT '优惠金额',
  `dis_type` varchar(30) DEFAULT NULL COMMENT '优惠类型',
  `dis_data` text COMMENT '优惠信息的存储',
  `from_client` varchar(255) DEFAULT NULL COMMENT '下单来源',
  `create_orders_time` timestamp NULL DEFAULT NULL COMMENT '下订单时间，预防漏单',
  `finish_orders_time` timestamp NULL DEFAULT NULL COMMENT '订单完成时间',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `city_manager_id` int(10) DEFAULT NULL COMMENT '城市经理ID',
  `city_manager_name` varchar(20) DEFAULT NULL COMMENT '城市经理姓名',
  `dis_id` int(10) DEFAULT NULL COMMENT '红包ID',
  `allocate_time` timestamp NULL DEFAULT NULL COMMENT '订单分配时间',
  `invite_code_id` int(10) DEFAULT NULL COMMENT '邀请码_推荐人ID',
  `audit_state` int(4) DEFAULT '20' COMMENT '审核状态',
  `blacklist` varchar(25) DEFAULT NULL COMMENT '黑名称',
  `is_up` int(1) DEFAULT '0' COMMENT '是否申请更新',
  `up_data` text NOT NULL COMMENT '更新内容',
  `is_job` int(1) DEFAULT '0' COMMENT '是否兼职',
  `job_price` decimal(10,2) NOT NULL COMMENT '兼职金额',
  `cancel_time` timestamp NULL DEFAULT NULL COMMENT '取消时间',
  PRIMARY KEY (`order_id`),
  UNIQUE KEY `business_no` (`business_no`) USING BTREE,
  KEY `uid` (`uid`) USING BTREE,
  KEY `status` (`status`) USING BTREE,
  KEY `is_type` (`is_type`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=30268 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for ecs_order_track
-- ----------------------------
DROP TABLE IF EXISTS `ecs_order_track`;
CREATE TABLE `ecs_order_track` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `business_no` varchar(32) NOT NULL,
  `status` int(11) NOT NULL,
  `create_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=72038 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_outdepot_orders
-- ----------------------------
DROP TABLE IF EXISTS `ecs_outdepot_orders`;
CREATE TABLE `ecs_outdepot_orders` (
  `odid` int(11) NOT NULL AUTO_INCREMENT,
  `goods_name` varchar(250) NOT NULL,
  `goods_num` int(11) NOT NULL COMMENT '数量',
  `goods_id` int(11) NOT NULL,
  `serial_no` varchar(250) NOT NULL COMMENT '序列号',
  `out_depot` int(11) NOT NULL COMMENT '出库仓库',
  `in_depot` int(11) NOT NULL COMMENT '借调仓',
  `business_no` varchar(35) NOT NULL,
  `header` int(11) NOT NULL COMMENT '领货人',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `client` int(11) NOT NULL DEFAULT '1' COMMENT '来源 1：总仓-借调仓 2：分仓-借调仓 4：分仓-领货人 8：总仓-领货人',
  `way` int(11) NOT NULL DEFAULT '1' COMMENT '1：自动出库 2：手动出库',
  `create_time` datetime NOT NULL,
  `handler` int(11) NOT NULL DEFAULT '0' COMMENT '操作者',
  PRIMARY KEY (`odid`)
) ENGINE=InnoDB AUTO_INCREMENT=5000048 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_pay_bill
-- ----------------------------
DROP TABLE IF EXISTS `ecs_pay_bill`;
CREATE TABLE `ecs_pay_bill` (
  `bill_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL COMMENT '用户UID',
  `order_id` int(11) unsigned NOT NULL COMMENT '订单ID',
  `bill_no` bigint(20) unsigned NOT NULL COMMENT '账单流水号',
  `repbus` bigint(20) unsigned NOT NULL COMMENT '首付流水号-在信用账户版本增加字段',
  `period` tinyint(3) unsigned DEFAULT NULL COMMENT '第几期',
  `repayment_day` date DEFAULT NULL COMMENT '应还款日期',
  `repayment_money` int(11) unsigned NOT NULL COMMENT '应还款金额(分)',
  `real_pay_day` timestamp NULL DEFAULT NULL COMMENT '实际还款日期',
  `real_pay_money` int(10) unsigned DEFAULT NULL COMMENT '实际还款金额(分)',
  `late_fee` int(10) unsigned DEFAULT NULL COMMENT '滞纳金',
  `status` tinyint(4) unsigned NOT NULL COMMENT '状态 0：学生正常待还款，1： 学生还款延期  2：等待学生付款 3：付款成功',
  `pay_way` varchar(15) DEFAULT NULL COMMENT '还款途径 onlineBank：网上银行，withholding：银行代扣，alipay：支付宝',
  `from` varchar(20) DEFAULT NULL COMMENT '通知来源，notice  异步，return 同步',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`bill_id`),
  UNIQUE KEY `bill_no` (`bill_no`),
  KEY `order_id` (`order_id`),
  KEY `uid` (`uid`) USING BTREE,
  KEY `period` (`period`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=83200 DEFAULT CHARSET=utf8 COMMENT='账单表';

-- ----------------------------
-- Table structure for ecs_pay_bill_job
-- ----------------------------
DROP TABLE IF EXISTS `ecs_pay_bill_job`;
CREATE TABLE `ecs_pay_bill_job` (
  `bill_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL COMMENT '用户UID',
  `business_no` bigint(20) unsigned NOT NULL COMMENT '订单号',
  `bill_no` bigint(20) unsigned NOT NULL COMMENT '账单流水号',
  `period` tinyint(3) unsigned DEFAULT NULL COMMENT '第几期',
  `repayment_day` date DEFAULT NULL COMMENT '应还款日期',
  `repayment_money` int(11) unsigned NOT NULL COMMENT '应还款金额(分)',
  `real_pay_day` timestamp NULL DEFAULT NULL COMMENT '实际还款日期',
  `real_pay_money` int(10) unsigned DEFAULT NULL COMMENT '实际还款金额(分)',
  `late_fee` int(10) unsigned DEFAULT NULL COMMENT '滞纳金(分)',
  `status` tinyint(4) unsigned NOT NULL COMMENT '状态 0：学生正常待还款，1： 学生还款延期  2：等待学生付款 3：付款成功',
  `pay_way` varchar(15) DEFAULT NULL COMMENT '还款途径 onlineBank：网上银行，withholding：银行代扣，alipay：支付宝',
  `from` varchar(20) DEFAULT NULL COMMENT '通知来源，notice  异步，return 同步',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`bill_id`),
  UNIQUE KEY `bill_no` (`bill_no`),
  KEY `business_no` (`business_no`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=15461 DEFAULT CHARSET=utf8 COMMENT='订单-兼职分期-账单表';

-- ----------------------------
-- Table structure for ecs_pay_job_detail
-- ----------------------------
DROP TABLE IF EXISTS `ecs_pay_job_detail`;
CREATE TABLE `ecs_pay_job_detail` (
  `job_detail_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL COMMENT '用户UID',
  `order_id` int(11) unsigned NOT NULL COMMENT '订单ID',
  `business_no` bigint(20) unsigned NOT NULL COMMENT '订单流水号',
  `repaybusinessno` bigint(20) unsigned NOT NULL COMMENT '交易流水号',
  `water_num` bigint(20) unsigned DEFAULT NULL COMMENT '//兼职系统流水号,只是用于记录',
  `pay_day` timestamp NULL DEFAULT NULL COMMENT '还款时间',
  `pay_money` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '还的钱(分)',
  `pay_way` varchar(15) DEFAULT NULL COMMENT '支付方式',
  `from` varchar(20) DEFAULT NULL COMMENT '通知来源，notice  异步，return 同步',
  `status` tinyint(4) unsigned DEFAULT '0' COMMENT '2:等待付款  3：付款成功',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`job_detail_id`),
  UNIQUE KEY `repaybusinessno` (`repaybusinessno`),
  KEY `business_no` (`business_no`),
  KEY `uid_buss` (`uid`,`business_no`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=549 DEFAULT CHARSET=utf8 COMMENT='兼职还款信息 明细表';

-- ----------------------------
-- Table structure for ecs_pay_job_gather
-- ----------------------------
DROP TABLE IF EXISTS `ecs_pay_job_gather`;
CREATE TABLE `ecs_pay_job_gather` (
  `job_gather_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL COMMENT '用户UID',
  `order_id` int(10) unsigned NOT NULL COMMENT '订单ID',
  `business_no` bigint(20) unsigned NOT NULL COMMENT '订单流水号',
  `need_money` int(10) unsigned NOT NULL COMMENT '订单需要还款兼职金额分)',
  `has_pay_money` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '已经还款兼职金额(分)',
  `status` tinyint(1) unsigned DEFAULT '1' COMMENT '1:后台还款状态  2:在线还款状态  3:还款完成',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`job_gather_id`),
  UNIQUE KEY `uid_order_id` (`uid`,`order_id`),
  KEY `uid_buss` (`uid`,`business_no`)
) ENGINE=InnoDB AUTO_INCREMENT=1483 DEFAULT CHARSET=utf8 COMMENT='兼职还款信息 总表';

-- ----------------------------
-- Table structure for ecs_pay_setting
-- ----------------------------
DROP TABLE IF EXISTS `ecs_pay_setting`;
CREATE TABLE `ecs_pay_setting` (
  `pay_set_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL COMMENT '用户UID',
  `refund_day` varchar(2) NOT NULL COMMENT '还款日',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `uid` (`uid`),
  UNIQUE KEY `pay_set_id` (`pay_set_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19350 DEFAULT CHARSET=utf8 COMMENT='用户还款设置';

-- ----------------------------
-- Table structure for ecs_pay_student
-- ----------------------------
DROP TABLE IF EXISTS `ecs_pay_student`;
CREATE TABLE `ecs_pay_student` (
  `pay_student_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pay_way` varchar(15) NOT NULL COMMENT '还款途径 onlineBank：网上银行，withholding：银行代扣，alipay：支付宝',
  `uid` int(10) unsigned NOT NULL,
  `bank_select` varchar(255) NOT NULL COMMENT '选择的银行',
  `card` varchar(255) NOT NULL COMMENT '卡号，对应的支付宝帐号',
  `data` text COMMENT '附加字段，现在给 代扣使用',
  `status` tinyint(3) unsigned DEFAULT '0' COMMENT '0  无意义， 1： 代扣签约审核中 2：代扣签约成功 3:解约',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`pay_student_id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=13828 DEFAULT CHARSET=utf8 COMMENT='学生付款方式表';

-- ----------------------------
-- Table structure for ecs_pay_way
-- ----------------------------
DROP TABLE IF EXISTS `ecs_pay_way`;
CREATE TABLE `ecs_pay_way` (
  `pay_id` int(11) NOT NULL AUTO_INCREMENT,
  `pay_name` varchar(20) NOT NULL,
  PRIMARY KEY (`pay_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_pay_way_alipay
-- ----------------------------
DROP TABLE IF EXISTS `ecs_pay_way_alipay`;
CREATE TABLE `ecs_pay_way_alipay` (
  `pay_alipay_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `out_trade_no` bigint(20) NOT NULL COMMENT '账单流水号',
  `buyer_email` varchar(255) NOT NULL COMMENT '买家支付宝帐号',
  `buyer_id` bigint(16) NOT NULL COMMENT '买家支付宝ID',
  `total_fee` decimal(10,2) DEFAULT NULL COMMENT '交易金额(元) 支付宝支持',
  `trade_no` bigint(16) NOT NULL COMMENT '支付宝交易号',
  `trade_status` varchar(20) DEFAULT NULL COMMENT '交易状态',
  `gmt_create` timestamp NULL DEFAULT NULL COMMENT '交易创建时间',
  `gmt_payment` timestamp NULL DEFAULT NULL COMMENT '交易付款时间',
  `data` text COMMENT '整体数据记录',
  `from` varchar(20) DEFAULT NULL COMMENT '通知来源，notice  异步，return 同步',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`pay_alipay_id`),
  UNIQUE KEY `out_trade_no` (`out_trade_no`)
) ENGINE=InnoDB AUTO_INCREMENT=66967 DEFAULT CHARSET=utf8 COMMENT='支付宝付款 详细信息记录';

-- ----------------------------
-- Table structure for ecs_pay_way_online
-- ----------------------------
DROP TABLE IF EXISTS `ecs_pay_way_online`;
CREATE TABLE `ecs_pay_way_online` (
  `pay_online_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) NOT NULL COMMENT '账单流水号',
  `trade_state` varchar(20) NOT NULL COMMENT '交易状态说明',
  `amount` int(10) NOT NULL COMMENT '付款金额  以分为单位',
  `trade_no` bigint(20) NOT NULL COMMENT 'U付交易号',
  `pay_type` varchar(20) DEFAULT NULL,
  `amt_type` varchar(20) DEFAULT NULL,
  `pay_date` timestamp NULL DEFAULT NULL COMMENT '支付日期',
  `settle_date` timestamp NULL DEFAULT NULL COMMENT '对账日期',
  `data` text COMMENT '整体数据记录',
  `from` varchar(20) DEFAULT NULL COMMENT '通知来源，notice  异步，return 同步',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`pay_online_id`),
  UNIQUE KEY `order_id` (`order_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2076 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_phone_contacts
-- ----------------------------
DROP TABLE IF EXISTS `ecs_phone_contacts`;
CREATE TABLE `ecs_phone_contacts` (
  `contact_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL,
  `data` mediumtext COMMENT '联系人信息',
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`contact_id`),
  KEY `uid` (`uid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=38633 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for ecs_purchase_delivery
-- ----------------------------
DROP TABLE IF EXISTS `ecs_purchase_delivery`;
CREATE TABLE `ecs_purchase_delivery` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned DEFAULT NULL,
  `business_no` bigint(20) unsigned NOT NULL,
  `purchase_price` decimal(10,2) unsigned DEFAULT NULL COMMENT '采购单价',
  `supplier_id` int(10) unsigned DEFAULT NULL COMMENT '供应商id',
  `express_id` int(10) unsigned DEFAULT NULL COMMENT '快递id',
  `waybill_no` varchar(255) DEFAULT NULL COMMENT '运单编号',
  `serial_number` varchar(255) DEFAULT NULL COMMENT '产品序列号',
  `delivery_time` timestamp NULL DEFAULT NULL COMMENT '发货时间',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0：未发货 1：已发货 2：已发起采购',
  `stream` text COMMENT '物流信息',
  `stream_from` varchar(255) DEFAULT NULL COMMENT '通过什么接口查询快递信息',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `business_no` (`business_no`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=856 DEFAULT CHARSET=utf8 COMMENT='v3.0 采购管理-发货列表 20150826';

-- ----------------------------
-- Table structure for ecs_purchase_express
-- ----------------------------
DROP TABLE IF EXISTS `ecs_purchase_express`;
CREATE TABLE `ecs_purchase_express` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `express_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='v3.0 采购管理-快递列表 20150826';

-- ----------------------------
-- Table structure for ecs_purchase_orders
-- ----------------------------
DROP TABLE IF EXISTS `ecs_purchase_orders`;
CREATE TABLE `ecs_purchase_orders` (
  `poid` int(11) NOT NULL AUTO_INCREMENT,
  `goods_name` varchar(200) NOT NULL,
  `goods_id` int(11) NOT NULL,
  `goods_map_id` int(11) NOT NULL,
  `goods_price` decimal(10,2) NOT NULL,
  `goods_num` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `supplier_id` int(11) NOT NULL COMMENT '供应商id',
  `pay_way` int(11) NOT NULL COMMENT '付款方式(到付、已付、月结) ',
  `depot_id` int(11) NOT NULL COMMENT '发起采购仓库',
  `in_depot` int(11) NOT NULL COMMENT '入库的仓库',
  `status` tinyint(4) NOT NULL,
  `remark` varchar(50) NOT NULL,
  `create_time` datetime NOT NULL,
  `invoice` int(11) NOT NULL COMMENT '发票情况',
  `update_time` datetime NOT NULL,
  `busniess_no` varchar(32) NOT NULL,
  `client` varchar(10) DEFAULT NULL,
  `way` tinyint(4) DEFAULT '1' COMMENT '1:订单发起  2：自主发起',
  PRIMARY KEY (`poid`)
) ENGINE=InnoDB AUTO_INCREMENT=1000250 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_purchase_supplier
-- ----------------------------
DROP TABLE IF EXISTS `ecs_purchase_supplier`;
CREATE TABLE `ecs_purchase_supplier` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `supplier_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8 COMMENT='v3.0 采购管理-供货商列表 20150826';

-- ----------------------------
-- Table structure for ecs_push_text
-- ----------------------------
DROP TABLE IF EXISTS `ecs_push_text`;
CREATE TABLE `ecs_push_text` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `is_type` int(1) NOT NULL DEFAULT '1' COMMENT '1/极光推送',
  `cname` varchar(50) DEFAULT NULL COMMENT '文案名称',
  `info` varchar(255) DEFAULT NULL COMMENT '信息',
  `action` char(20) DEFAULT NULL COMMENT '操作对象',
  `is_del` int(1) NOT NULL DEFAULT '1' COMMENT '0/关闭  1/开启',
  `ctime` int(10) DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `is_type` (`is_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='推送文案';

-- ----------------------------
-- Table structure for ecs_receiving_bank
-- ----------------------------
DROP TABLE IF EXISTS `ecs_receiving_bank`;
CREATE TABLE `ecs_receiving_bank` (
  `receiving_bank_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `receiving_type` tinyint(1) unsigned NOT NULL COMMENT '收款方式 1:银行卡',
  `bank_name` varchar(50) NOT NULL COMMENT '银行名称',
  `bank_account` varchar(50) NOT NULL COMMENT '开户名',
  `bank_no` varchar(50) NOT NULL COMMENT '银行卡号',
  `identity` varchar(20) NOT NULL COMMENT '身份证号',
  `phone` varchar(11) NOT NULL COMMENT '手机号码',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态 0:默认状态 1:验证成功 2:验证失败',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`receiving_bank_id`),
  KEY `uid` (`uid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=12577 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for ecs_red_packet
-- ----------------------------
DROP TABLE IF EXISTS `ecs_red_packet`;
CREATE TABLE `ecs_red_packet` (
  `red_id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '用户id',
  `type` tinyint(4) NOT NULL COMMENT '红包类型 ：1:购买红包  2：还款红包',
  `source` tinyint(4) NOT NULL DEFAULT '1' COMMENT '红包来源  1： 积分红包 2转介绍红包',
  `red_money` int(11) NOT NULL COMMENT '红包金额',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1未使用 2已使用 4 已过期',
  `expire_time` datetime NOT NULL COMMENT '过期时间',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `delete_time` datetime DEFAULT NULL COMMENT '删除时间',
  `use_time` datetime DEFAULT NULL COMMENT '使用时间',
  PRIMARY KEY (`red_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5248 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_red_packet_get
-- ----------------------------
DROP TABLE IF EXISTS `ecs_red_packet_get`;
CREATE TABLE `ecs_red_packet_get` (
  `red_get_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `red_key` varchar(50) NOT NULL COMMENT '对应的红包ID',
  `order_id` int(10) unsigned NOT NULL COMMENT '生成了订单  才能领取',
  `is_get` tinyint(4) DEFAULT '0' COMMENT '是否已经领取， 0 是没有领取',
  `is_use` tinyint(4) DEFAULT '0' COMMENT '是否已经被使用了，0是没有被使用',
  `bill_no` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`red_get_id`),
  KEY `red_key` (`red_key`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COMMENT='返佣红包 领取';

-- ----------------------------
-- Table structure for ecs_red_packet_send
-- ----------------------------
DROP TABLE IF EXISTS `ecs_red_packet_send`;
CREATE TABLE `ecs_red_packet_send` (
  `red_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL,
  `red_key` varchar(50) NOT NULL COMMENT '红包key，根据uid.order_id.ymd  MD5 产生',
  `red_money` int(255) NOT NULL COMMENT '红包钱',
  `order_id` int(10) unsigned NOT NULL COMMENT '产生红包的订单，应对，相同的用户不同的订单',
  `expire_time` timestamp NULL DEFAULT NULL COMMENT '红包过期时间，null就是没有过期限制',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`red_id`),
  KEY `red_key` (`red_key`)
) ENGINE=InnoDB AUTO_INCREMENT=299 DEFAULT CHARSET=utf8 COMMENT='红包活动派送表';

-- ----------------------------
-- Table structure for ecs_refund_orders
-- ----------------------------
DROP TABLE IF EXISTS `ecs_refund_orders`;
CREATE TABLE `ecs_refund_orders` (
  `re_id` int(11) NOT NULL AUTO_INCREMENT,
  `business_no` bigint(20) NOT NULL,
  `goods_id` int(11) NOT NULL,
  `goods_name` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1' COMMENT '1:换退货申请 2：换退货申请通过 4：换退货申请驳回 8：换退货中 16：换退货成功',
  `type` int(11) NOT NULL COMMENT '1：换货 2：退货',
  `angel_id` int(11) NOT NULL,
  `city_id` int(11) NOT NULL,
  `create_time` datetime NOT NULL,
  `update_time` datetime NOT NULL,
  `remark` varchar(50) NOT NULL,
  PRIMARY KEY (`re_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_region
-- ----------------------------
DROP TABLE IF EXISTS `ecs_region`;
CREATE TABLE `ecs_region` (
  `region_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned DEFAULT NULL COMMENT '父ID',
  `region_name` varchar(360) DEFAULT NULL COMMENT '区域名字',
  `region_type` tinyint(1) unsigned DEFAULT NULL COMMENT '类型级别 0，1，2，3',
  `agency_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `is_job` int(1) DEFAULT '0' COMMENT '0/未开放 1/已开放',
  `job_sort` int(2) DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`region_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3410 DEFAULT CHARSET=utf8 COMMENT='地区表';

-- ----------------------------
-- Table structure for ecs_reg_ip6
-- ----------------------------
DROP TABLE IF EXISTS `ecs_reg_ip6`;
CREATE TABLE `ecs_reg_ip6` (
  `reg_ip6_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL COMMENT '用户uid',
  `phone` varchar(20) NOT NULL COMMENT '用户帐号',
  `period` tinyint(3) unsigned NOT NULL COMMENT '活动第几期',
  `key` varchar(32) NOT NULL COMMENT '邀请码',
  `nums` int(11) DEFAULT '0' COMMENT '邀请人数',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`reg_ip6_id`),
  UNIQUE KEY `key` (`uid`,`key`,`period`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1757 DEFAULT CHARSET=utf8 COMMENT='iphone 6 注册邀请表';

-- ----------------------------
-- Table structure for ecs_rel_stu_info
-- ----------------------------
DROP TABLE IF EXISTS `ecs_rel_stu_info`;
CREATE TABLE `ecs_rel_stu_info` (
  `rel_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `stu_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `school_id` varchar(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`rel_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6678 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_remind
-- ----------------------------
DROP TABLE IF EXISTS `ecs_remind`;
CREATE TABLE `ecs_remind` (
  `rid` bigint(20) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `remark` varchar(50) DEFAULT NULL,
  `repay_day` bigint(20) NOT NULL,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `create_time` datetime NOT NULL,
  PRIMARY KEY (`rid`)
) ENGINE=InnoDB AUTO_INCREMENT=4575120150716 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_remittance_log
-- ----------------------------
DROP TABLE IF EXISTS `ecs_remittance_log`;
CREATE TABLE `ecs_remittance_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `water_no` varchar(250) DEFAULT NULL COMMENT '流水号',
  `mode` int(1) unsigned NOT NULL COMMENT '1/信用钱包',
  `uid` int(10) unsigned NOT NULL COMMENT '用户ID',
  `business_no` bigint(20) unsigned NOT NULL COMMENT '订单号',
  `info` varchar(250) DEFAULT NULL COMMENT '描述',
  `uname` varchar(20) DEFAULT NULL COMMENT '操作人姓名',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0：未打款 1:已打款',
  `pay_time` timestamp NULL DEFAULT NULL COMMENT '打款时间',
  `receiving_bank_id` int(10) unsigned NOT NULL COMMENT '收款银行表流水id',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `business_no` (`business_no`) USING BTREE,
  KEY `status` (`status`) USING BTREE,
  KEY `created_at` (`created_at`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for ecs_repayment_detail
-- ----------------------------
DROP TABLE IF EXISTS `ecs_repayment_detail`;
CREATE TABLE `ecs_repayment_detail` (
  `repay_de_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `repaybusinessno` bigint(20) unsigned NOT NULL COMMENT '流水号',
  `uid` int(11) unsigned NOT NULL COMMENT '用户UID',
  `year_month` date NOT NULL COMMENT '账单的年月份，天数字段为 01 ',
  `real_pay_day` timestamp NULL DEFAULT NULL COMMENT '实际还款日期',
  `real_pay_money` int(10) unsigned DEFAULT NULL COMMENT '实际还款金额(分)',
  `discount` int(10) unsigned DEFAULT NULL,
  `dicount_data` text COMMENT '折扣组成',
  `status` tinyint(4) unsigned NOT NULL COMMENT '状态：等待学生付款 3：付款成功',
  `pay_way` varchar(15) DEFAULT NULL COMMENT '还款途径 onlineBank：网上银行，withholding：银行代扣，alipay：支付宝',
  `from` varchar(20) DEFAULT NULL COMMENT '通知来源，notice  异步，return 同步',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`repay_de_id`),
  UNIQUE KEY `repaybusinessno` (`repaybusinessno`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=108503 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_repayment_gather
-- ----------------------------
DROP TABLE IF EXISTS `ecs_repayment_gather`;
CREATE TABLE `ecs_repayment_gather` (
  `repay_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL COMMENT '用户UID',
  `year_month` date NOT NULL COMMENT '账单的年月份，天数字段为 01 ',
  `repayment_day` date NOT NULL COMMENT '还款日，不会改变',
  `repayment_money` int(11) unsigned NOT NULL COMMENT '月供(分)，不能为负数，本月不会改变',
  `cal_repayment_day` date NOT NULL COMMENT '计算的还款日，用于滞纳金抵消的计算等等，默认等于还款日',
  `cal_repayment_money` int(11) NOT NULL COMMENT '计算的应还金额(分)，由上期叠加而成，可以为负数',
  `sum_pay_money` int(11) unsigned NOT NULL COMMENT '本月全部还的钱(分)',
  `final_pay_day` timestamp NULL DEFAULT NULL COMMENT '最后还款时间',
  `late_fee` int(11) unsigned DEFAULT NULL COMMENT '滞纳金(分)',
  `status` tinyint(4) unsigned NOT NULL COMMENT '状态 0：学生正常待还款，1： 学生还款延期  2：等待学生付款 3：付款成功',
  `data` text COMMENT '组成单位,每个order_id的和bill_id,json格式',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`repay_id`),
  UNIQUE KEY `uid_year_month` (`uid`,`year_month`)
) ENGINE=InnoDB AUTO_INCREMENT=87418 DEFAULT CHARSET=utf8 COMMENT='用户还款信息表，是所有账单的集合';

-- ----------------------------
-- Table structure for ecs_role
-- ----------------------------
DROP TABLE IF EXISTS `ecs_role`;
CREATE TABLE `ecs_role` (
  `role_id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(60) NOT NULL,
  `desc` varchar(255) NOT NULL DEFAULT '""',
  `status` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_schools
-- ----------------------------
DROP TABLE IF EXISTS `ecs_schools`;
CREATE TABLE `ecs_schools` (
  `school_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `school_name` varchar(30) NOT NULL COMMENT '学校名字',
  `province_id` smallint(6) NOT NULL COMMENT '省份ID',
  `city_id` smallint(6) NOT NULL COMMENT '城市ID',
  `district_id` smallint(6) NOT NULL COMMENT '区域ID',
  `address` varchar(50) DEFAULT NULL,
  `account_id` mediumint(9) NOT NULL,
  `angel_id` mediumint(9) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`school_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3292 DEFAULT CHARSET=utf8 COMMENT='学校表';

-- ----------------------------
-- Table structure for ecs_school_ceo
-- ----------------------------
DROP TABLE IF EXISTS `ecs_school_ceo`;
CREATE TABLE `ecs_school_ceo` (
  `ceo_id` int(11) NOT NULL AUTO_INCREMENT,
  `ceo_name` varchar(10) NOT NULL COMMENT '姓名',
  `school_id` int(11) NOT NULL,
  `create_at` datetime NOT NULL,
  `ceo_phone` varchar(20) NOT NULL COMMENT '手机',
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`ceo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for ecs_seckill_goods
-- ----------------------------
DROP TABLE IF EXISTS `ecs_seckill_goods`;
CREATE TABLE `ecs_seckill_goods` (
  `sg_id` int(11) NOT NULL AUTO_INCREMENT,
  `period` int(11) NOT NULL DEFAULT '1',
  `goods_map_id` int(11) NOT NULL,
  `goods_map_name` varchar(255) NOT NULL,
  `goods_price` decimal(10,2) NOT NULL,
  `goods_id` int(11) NOT NULL,
  `num` tinyint(4) unsigned NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `create_time` datetime NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`sg_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_seckill_orders
-- ----------------------------
DROP TABLE IF EXISTS `ecs_seckill_orders`;
CREATE TABLE `ecs_seckill_orders` (
  `so_id` int(11) NOT NULL AUTO_INCREMENT,
  `goods_id` int(11) NOT NULL,
  `goods_name` varchar(200) DEFAULT NULL,
  `goods_price` decimal(10,2) NOT NULL,
  `uid` int(11) NOT NULL,
  `create_time` datetime NOT NULL,
  `period` int(11) NOT NULL,
  PRIMARY KEY (`so_id`)
) ENGINE=InnoDB AUTO_INCREMENT=87 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_sign_in
-- ----------------------------
DROP TABLE IF EXISTS `ecs_sign_in`;
CREATE TABLE `ecs_sign_in` (
  `sn_id` int(11) NOT NULL AUTO_INCREMENT,
  `sn_uid` int(11) NOT NULL COMMENT '人物',
  `sn_date` int(11) NOT NULL COMMENT '日期',
  `sn_branch` int(11) NOT NULL COMMENT '分数',
  `sn_day` tinyint(4) NOT NULL COMMENT '天数',
  PRIMARY KEY (`sn_id`)
) ENGINE=InnoDB AUTO_INCREMENT=29190 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_stock
-- ----------------------------
DROP TABLE IF EXISTS `ecs_stock`;
CREATE TABLE `ecs_stock` (
  `sid` int(11) NOT NULL AUTO_INCREMENT,
  `did` int(11) NOT NULL COMMENT '所属仓库id',
  `goods_id` int(11) NOT NULL COMMENT '总属商品id',
  `goods_map_id` int(11) NOT NULL COMMENT '详情商品id',
  `goods_num` int(11) NOT NULL COMMENT '商品库存',
  `lock_num` int(11) NOT NULL COMMENT '锁定库存',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '时间',
  PRIMARY KEY (`sid`)
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_students
-- ----------------------------
DROP TABLE IF EXISTS `ecs_students`;
CREATE TABLE `ecs_students` (
  `stu_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL COMMENT ' 核心ID',
  `name` varchar(255) NOT NULL COMMENT '姓名',
  `age` tinyint(3) unsigned NOT NULL COMMENT '年龄',
  `sex` char(1) NOT NULL COMMENT '性别 F：女, M：男',
  `area_id` int(11) unsigned NOT NULL COMMENT '籍贯区域ID',
  `home_address` varchar(255) DEFAULT NULL COMMENT '家庭住址',
  `phone` varchar(11) NOT NULL COMMENT '手机号码',
  `qq` varchar(15) DEFAULT NULL COMMENT 'QQ号/或者微信',
  `email` varchar(20) DEFAULT NULL COMMENT '邮箱',
  `schooling` varchar(20) DEFAULT NULL COMMENT '专业',
  `identity` varchar(20) NOT NULL COMMENT '身份证',
  `school_id` varchar(255) NOT NULL COMMENT '学校ID',
  `dorm_address` varchar(255) DEFAULT NULL COMMENT '宿舍地址',
  `bank_No` varchar(50) DEFAULT NULL COMMENT '银行卡号',
  `pay_way_id` int(11) DEFAULT '1',
  `contacts` varchar(50) DEFAULT NULL COMMENT '其它联系方式',
  `gradion_years` varchar(4) DEFAULT NULL COMMENT '毕业年份',
  `gradion_month` varchar(2) DEFAULT NULL COMMENT '毕业月份',
  `img_sfz_url` varchar(255) DEFAULT NULL COMMENT '身份证正面',
  `img_sfzm_url` varchar(255) DEFAULT NULL COMMENT '身份证反面',
  `img_xsz_url` varchar(255) DEFAULT NULL COMMENT '学生证正面图片地址',
  `img_xszm_url` varchar(255) DEFAULT NULL COMMENT '学生证背面图片地址',
  `img_card_url` varchar(255) DEFAULT NULL COMMENT '校园一卡通 正',
  `img_cardm_url` varchar(255) DEFAULT NULL COMMENT '校园一卡通图片 反',
  `img_bank_url` varchar(255) DEFAULT NULL COMMENT '银行账号正图片地址',
  `img_bankm_url` varchar(255) DEFAULT NULL COMMENT '银行账号反图片地址',
  `office_url` varchar(100) DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL COMMENT '备注',
  `birthday` varchar(30) DEFAULT NULL COMMENT '生日1987,08,01逗号分割',
  `auth` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1:未填写 2：认证中 3：认证成功  4：认证失败',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `college` int(10) DEFAULT NULL COMMENT '学院',
  `professional` int(10) DEFAULT NULL COMMENT '专业',
  `class` varchar(60) DEFAULT NULL COMMENT '班级',
  `provinces_id` int(10) DEFAULT NULL COMMENT '省份ID',
  `city_id` int(10) unsigned DEFAULT NULL COMMENT '城市ID',
  `auth_info` tinyint(1) DEFAULT '1' COMMENT '学生资料-1、未填写 2、认证中 3、认证成功  4、认证失败',
  `auth_refund` tinyint(1) unsigned DEFAULT '1' COMMENT '还款认证-1、未填写 2、认证中 3、认证成功  4、认证失败',
  `auth_eid` tinyint(1) unsigned DEFAULT '1' COMMENT '学生认证-1、未填写 2、认证中 3、认证成功  4、认证失败',
  `auth_time` int(10) NOT NULL COMMENT '申请时间',
  `handle_auth_time` int(10) DEFAULT NULL COMMENT '处理申请时间',
  `auth_msg` int(1) DEFAULT '0' COMMENT '1/通知 0/没通知',
  `client` varchar(20) DEFAULT '0' COMMENT '请求来源',
  `is_order` int(1) DEFAULT '0' COMMENT '判断是否有新订单',
  `auth_video` int(1) DEFAULT '1' COMMENT '视频认证',
  `video_qq` varchar(20) DEFAULT NULL COMMENT '视频预约QQ',
  `video_time` varchar(50) DEFAULT NULL COMMENT '视频预约时间',
  `education_levels` varchar(30) DEFAULT NULL COMMENT '学历层次',
  `type_img_url` varchar(255) DEFAULT NULL COMMENT '不同类型图片',
  PRIMARY KEY (`stu_id`),
  UNIQUE KEY `uid` (`uid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=97193 DEFAULT CHARSET=utf8 COMMENT='学生表';

-- ----------------------------
-- Table structure for ecs_students_auth_notes
-- ----------------------------
DROP TABLE IF EXISTS `ecs_students_auth_notes`;
CREATE TABLE `ecs_students_auth_notes` (
  `stu_notes_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned DEFAULT NULL,
  `mode` tinyint(1) unsigned DEFAULT NULL COMMENT '1：学生认证 2：视频认证',
  `note_start` timestamp NULL DEFAULT NULL COMMENT '填写开始时间',
  `note_end` timestamp NULL DEFAULT NULL COMMENT '填写结束时间',
  `handle_time` timestamp NULL DEFAULT NULL COMMENT '处理时间',
  `handle_res` tinyint(1) unsigned DEFAULT '0' COMMENT '0：没处理  3：通过  4：没通过',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`stu_notes_id`),
  KEY `uid_mode` (`uid`,`mode`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=141545 DEFAULT CHARSET=utf8 COMMENT='学生填写认证时间点记录';

-- ----------------------------
-- Table structure for ecs_students_external_account
-- ----------------------------
DROP TABLE IF EXISTS `ecs_students_external_account`;
CREATE TABLE `ecs_students_external_account` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `mode` int(1) NOT NULL COMMENT '1/学信网 2/教务处',
  `uid` int(10) NOT NULL COMMENT '用户ID',
  `account` varchar(50) DEFAULT NULL COMMENT '帐号',
  `passwd` varchar(100) DEFAULT NULL COMMENT '密码',
  `domain` varchar(255) DEFAULT NULL COMMENT '域名',
  `ctime` int(10) NOT NULL COMMENT '创建',
  PRIMARY KEY (`id`),
  KEY `mode` (`mode`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=54944 DEFAULT CHARSET=utf8 COMMENT='学生外部帐号表';

-- ----------------------------
-- Table structure for ecs_students_job
-- ----------------------------
DROP TABLE IF EXISTS `ecs_students_job`;
CREATE TABLE `ecs_students_job` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `intent` varchar(50) NOT NULL COMMENT '兼职意向',
  `job_time` varchar(50) NOT NULL COMMENT '兼职时间',
  `wx` varchar(30) DEFAULT NULL,
  `ctime` int(10) DEFAULT NULL COMMENT '添加时间',
  `etime` int(10) DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=7072 DEFAULT CHARSET=utf8 COMMENT='学生兼职信息';

-- ----------------------------
-- Table structure for ecs_student_account
-- ----------------------------
DROP TABLE IF EXISTS `ecs_student_account`;
CREATE TABLE `ecs_student_account` (
  `uid` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_name` varchar(20) NOT NULL COMMENT '用户名',
  `password` varchar(32) NOT NULL COMMENT '密码',
  `nikename` varchar(20) DEFAULT NULL COMMENT '昵称',
  `avatar` varchar(255) DEFAULT NULL COMMENT '头像',
  `token` varbinary(32) DEFAULT NULL,
  `status` bigint(20) NOT NULL COMMENT '状态',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `last_sign_time` datetime DEFAULT NULL COMMENT '最后登陆时间',
  `expire_time` datetime DEFAULT NULL COMMENT '过期时间',
  `stu_id` int(11) unsigned NOT NULL,
  `client` varchar(255) DEFAULT NULL COMMENT '注册来源',
  `from` varchar(30) DEFAULT NULL COMMENT '注册根据',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `user_name` (`user_name`),
  KEY `token` (`token`)
) ENGINE=InnoDB AUTO_INCREMENT=139589 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_supplier
-- ----------------------------
DROP TABLE IF EXISTS `ecs_supplier`;
CREATE TABLE `ecs_supplier` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  `phone` varchar(25) NOT NULL,
  `create_time` datetime NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_theme
-- ----------------------------
DROP TABLE IF EXISTS `ecs_theme`;
CREATE TABLE `ecs_theme` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `use_type` int(1) NOT NULL COMMENT '类型 1/PC 2/app',
  `cname` varchar(30) DEFAULT NULL COMMENT 'cname',
  `yname` varchar(25) DEFAULT NULL COMMENT '英文名',
  `sort` int(3) NOT NULL COMMENT '排序',
  `is_state` int(1) NOT NULL COMMENT '状态',
  PRIMARY KEY (`id`),
  KEY `use_type` (`use_type`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='主题';

-- ----------------------------
-- Table structure for ecs_theme_data
-- ----------------------------
DROP TABLE IF EXISTS `ecs_theme_data`;
CREATE TABLE `ecs_theme_data` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `theme_id` int(10) NOT NULL COMMENT '类型',
  `info` text COMMENT 'json格式数据',
  `sort` int(3) NOT NULL COMMENT '排序',
  PRIMARY KEY (`id`),
  KEY `theme_id` (`theme_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='主题数据';

-- ----------------------------
-- Table structure for ecs_ump_commonvalidate
-- ----------------------------
DROP TABLE IF EXISTS `ecs_ump_commonvalidate`;
CREATE TABLE `ecs_ump_commonvalidate` (
  `common_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `type` tinyint(1) unsigned DEFAULT '4' COMMENT '3:三要素验证  4:四要素验证',
  `order_id` bigint(20) unsigned NOT NULL COMMENT '商户订单号',
  `bank_account` varchar(30) NOT NULL COMMENT '银行卡号',
  `account_name` varchar(30) NOT NULL COMMENT '账户姓名',
  `identity_code` varchar(20) NOT NULL COMMENT '身份证号',
  `mobile_id` varchar(11) DEFAULT NULL COMMENT '手机号',
  `isCharge` tinyint(4) DEFAULT NULL COMMENT '0-计费；其他-不计费',
  `ret_code` varchar(30) DEFAULT NULL COMMENT '接口返回状态码',
  `ret_msg` varchar(255) DEFAULT NULL COMMENT '接口返回状态msg',
  `data` text COMMENT '接口返回的内容json格式',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`common_id`),
  UNIQUE KEY `order_id` (`order_id`) USING BTREE,
  KEY `uid` (`uid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=14537 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for ecs_users
-- ----------------------------
DROP TABLE IF EXISTS `ecs_users`;
CREATE TABLE `ecs_users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_web_version
-- ----------------------------
DROP TABLE IF EXISTS `ecs_web_version`;
CREATE TABLE `ecs_web_version` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `version` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '文件版本',
  `url` varchar(50) DEFAULT NULL COMMENT '下载路径',
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `version` (`version`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for ecs_white_info
-- ----------------------------
DROP TABLE IF EXISTS `ecs_white_info`;
CREATE TABLE `ecs_white_info` (
  `white_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned DEFAULT NULL,
  `business_no` bigint(20) NOT NULL COMMENT '订单业务号，关联订单表',
  `money` decimal(10,2) unsigned NOT NULL COMMENT '白条金额(元)',
  `ispay` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否财务支付 0：没有  非 1： 已经支付了',
  `pay_day` timestamp NULL DEFAULT NULL COMMENT '财务 打款时间',
  `pay_way` varchar(20) DEFAULT NULL COMMENT '财务打款方式  ',
  `remark` varchar(255) DEFAULT NULL COMMENT '财务打款备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`white_id`),
  UNIQUE KEY `business_no` (`business_no`)
) ENGINE=InnoDB AUTO_INCREMENT=1213 DEFAULT CHARSET=utf8 COMMENT='白条 订单 信息';

-- ----------------------------
-- Table structure for ecs_wish
-- ----------------------------
DROP TABLE IF EXISTS `ecs_wish`;
CREATE TABLE `ecs_wish` (
  `wish_id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `goods_name` varchar(150) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `client_type` varchar(15) NOT NULL,
  `custom_id` int(11) NOT NULL,
  `create_time` datetime NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1：未联系 2：已联系',
  `laud` int(11) NOT NULL DEFAULT '0',
  `remark` varchar(100) DEFAULT NULL,
  `buy_status` int(11) NOT NULL DEFAULT '0' COMMENT '0:未处理  1：可采购 2：不可采购',
  `buy_remark` varchar(50) DEFAULT NULL,
  `buy_time` datetime DEFAULT NULL,
  `custom_time` datetime DEFAULT NULL,
  PRIMARY KEY (`wish_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6419 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_wish_laud
-- ----------------------------
DROP TABLE IF EXISTS `ecs_wish_laud`;
CREATE TABLE `ecs_wish_laud` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wish_id` int(11) NOT NULL COMMENT '点赞商品',
  `uid` int(11) NOT NULL COMMENT '点赞人员',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11200 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_xyjl_error
-- ----------------------------
DROP TABLE IF EXISTS `ecs_xyjl_error`;
CREATE TABLE `ecs_xyjl_error` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `accept_meg` text,
  `send_meg` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2358 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for pay_pay_admin
-- ----------------------------
DROP TABLE IF EXISTS `pay_pay_admin`;
CREATE TABLE `pay_pay_admin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `email` varchar(320) NOT NULL,
  `password` varchar(64) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- View structure for __tmp_selector
-- ----------------------------
DROP VIEW IF EXISTS `__tmp_selector`;
CREATE ALGORITHM=UNDEFINED DEFINER=`renren_admin`@`%` SQL SECURITY DEFINER VIEW `__tmp_selector` AS select `ecs_student_account`.`uid` AS `uid`,`ecs_students`.`name` AS `name`,`ecs_student_account`.`user_name` AS `account`,`ecs_orders`.`goods_name` AS `goods_name`,`ecs_orders`.`created_at` AS `time`,`ecs_orders`.`business_no` AS `business_no`,`ecs_orders`.`goods_price` AS `goods_price`,`ecs_pay_setting`.`refund_day` AS `refund_day` from (((`ecs_student_account` join `ecs_students` on((`ecs_student_account`.`uid` = `ecs_students`.`uid`))) join `ecs_pay_setting` on((`ecs_pay_setting`.`uid` = `ecs_student_account`.`uid`))) join `ecs_orders` on((`ecs_orders`.`stu_id` = `ecs_students`.`stu_id`))) where ((`ecs_orders`.`status` in (5,15)) and (`ecs_student_account`.`user_name` in ('15228896078','15640231301','15527778002','17879835074','15549075095','13976662262','13307194838','13219071827','18160714127','18279121681','15083530332','13555843973','13163316597','13251071507','13576971174','15568849032','13667132806','18171404440','13856980805','18020144171','15979056745','15527861835','13657088712','17707081460','18646551569','18180824907','18166859351','15926160601','13672233633','13125228048','18379135459','18720989622','15728045948','18507915235','15116458787','13909801021','15870640820','13206670603','15559020786','18752026650','15727667462','15079036671','13588237951','15879075212','15574984696','15270014164','13260551320','18844116105','18643069213','18151737819','15692750707','15551069300','15295549328','13880195142','13204674413','13147244121','13027234331','18643069213','18780035150','18679703326','18679149193','18207410515','15004009762','18046718919','18980415316','18672945020','18349275351','13750056776','18378457745','18742438421','15527532835','13574110023','15855137882','13018056923','15700790046'))) ;

-- ----------------------------
-- View structure for __tmp_selector_02
-- ----------------------------
DROP VIEW IF EXISTS `__tmp_selector_02`;
CREATE ALGORITHM=UNDEFINED DEFINER=`renren_admin`@`%` SQL SECURITY DEFINER VIEW `__tmp_selector_02` AS select `ecs_student_account`.`uid` AS `uid`,`ecs_student_account`.`user_name` AS `account`,round((sum(`g`.`cal_repayment_money`) / 100),2) AS `cal_repayment_money`,round((sum(`g`.`late_fee`) / 100),2) AS `late_fee`,(to_days(from_unixtime(unix_timestamp())) - to_days(`g`.`repayment_day`)) AS `delayDays` from (`ecs_student_account` join `ecs_repayment_gather` `g` on((`ecs_student_account`.`uid` = `g`.`uid`))) where ((`g`.`status` = 1) and (`ecs_student_account`.`user_name` in ('15228896078','15640231301','15527778002','17879835074','15549075095','13976662262','13307194838','13219071827','18160714127','18279121681','15083530332','13555843973','13163316597','13251071507','13576971174','15568849032','13667132806','18171404440','13856980805','18020144171','15979056745','15527861835','13657088712','17707081460','18646551569','18180824907','18166859351','15926160601','13672233633','13125228048','18379135459','18720989622','15728045948','18507915235','15116458787','13909801021','15870640820','13206670603','15559020786','18752026650','15727667462','15079036671','13588237951','15879075212','15574984696','15270014164','13260551320','18844116105','18643069213','18151737819','15692750707','15551069300','15295549328','13880195142','13204674413','13147244121','13027234331','18643069213','18780035150','18679703326','18679149193','18207410515','15004009762','18046718919','18980415316','18672945020','18349275351','13750056776','18378457745','18742438421','15527532835','13574110023','15855137882','13018056923','15700790046'))) group by `g`.`uid` ;

-- ----------------------------
-- Procedure structure for __bill_selector
-- ----------------------------
DROP PROCEDURE IF EXISTS `__bill_selector`;
DELIMITER ;;
CREATE DEFINER=`renren_admin`@`%` PROCEDURE `__bill_selector`(
	_in_phone VARCHAR(20),
	_in_name VARCHAR(40)
)
BEGIN
	DECLARE _uid INT(11);
	SET _uid = 0;
	SELECT uid INTO _uid FROM ecs_students WHERE phone = _in_phone AND `name` = _in_name;  
	IF _uid = 0 THEN 
		SELECT 'User Not Found';
	ELSE
		SELECT * FROM ecs_pay_bill WHERE uid = _uid;
		SELECT * FROM ecs_pay_bill_job WHERE uid = _uid;
		SELECT * FROM ecs_repayment_gather WHERE uid = _uid;
		SELECT * FROM ecs_repayment_detail WHERE uid = _uid AND `status` = 3;
	END IF;
END
;;
DELIMITER ;
