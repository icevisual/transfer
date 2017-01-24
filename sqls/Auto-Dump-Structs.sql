-- ----------------------------
-- Date: 2017-01-24 21:10:24
-- ----------------------------

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for __temp1
-- ----------------------------
DROP TABLE IF EXISTS `__temp1`;
CREATE TABLE `__temp1` (
  `thumb` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '文件路径'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for chinese_col
-- ----------------------------
DROP TABLE IF EXISTS `chinese_col`;
CREATE TABLE `chinese_col` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL COMMENT 'email',
  `昵称` varchar(255) DEFAULT NULL COMMENT '昵称',
  `邮箱状态` varchar(255) DEFAULT NULL COMMENT '邮箱状态',
  `视频认证` varchar(255) DEFAULT NULL COMMENT '视频认证',
  `好友数` varchar(255) DEFAULT NULL COMMENT '好友数',
  `回帖数` varchar(255) DEFAULT NULL COMMENT '回帖数',
  `主题数` varchar(255) DEFAULT NULL COMMENT '主题数',
  `性别` varchar(255) DEFAULT NULL COMMENT '性别',
  `学历` varchar(255) DEFAULT NULL COMMENT '学历',
  `管理组` varchar(255) DEFAULT NULL COMMENT '管理组',
  `用户组` varchar(255) DEFAULT NULL COMMENT '用户组',
  `在线时间` varchar(255) DEFAULT NULL COMMENT '在线时间',
  `注册时间` varchar(255) DEFAULT NULL COMMENT '注册时间',
  `最后访问` varchar(255) DEFAULT NULL COMMENT '最后访问',
  `上次活动时间` varchar(255) DEFAULT NULL COMMENT '上次活动时间',
  `上次发表时间` varchar(255) DEFAULT NULL COMMENT '上次发表时间',
  `所在时区` varchar(255) DEFAULT NULL COMMENT '所在时区',
  `已用空间` varchar(255) DEFAULT NULL COMMENT '已用空间',
  `积分` varchar(255) DEFAULT NULL COMMENT '积分',
  `威望` varchar(255) DEFAULT NULL COMMENT '威望',
  `U币` varchar(255) DEFAULT NULL COMMENT 'U币',
  `贡献` varchar(255) DEFAULT NULL COMMENT '贡献',
  `个人签名` varchar(255) DEFAULT NULL COMMENT '个人签名',
  `个人主页` varchar(255) DEFAULT NULL COMMENT '个人主页',
  `扩展用户组` varchar(255) DEFAULT NULL COMMENT '扩展用户组',
  `星座` varchar(255) DEFAULT NULL COMMENT '星座',
  `出生地` varchar(255) DEFAULT NULL COMMENT '出生地',
  `居住地` varchar(255) DEFAULT NULL COMMENT '居住地',
  `公司` varchar(255) DEFAULT NULL COMMENT '公司',
  `真实姓名` varchar(255) DEFAULT NULL COMMENT '真实姓名',
  `职业` varchar(255) DEFAULT NULL COMMENT '职业',
  `职位` varchar(255) DEFAULT NULL COMMENT '职位',
  `交友目的` varchar(255) DEFAULT NULL COMMENT '交友目的',
  `毕业学校` varchar(255) DEFAULT NULL COMMENT '毕业学校',
  `兴趣爱好` varchar(255) DEFAULT NULL COMMENT '兴趣爱好',
  `自定义头衔` varchar(255) DEFAULT NULL COMMENT '自定义头衔',
  `注册 IP` varchar(255) DEFAULT NULL COMMENT '注册 IP',
  `上次访问 IP` varchar(255) DEFAULT NULL COMMENT '上次访问 IP',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19092 DEFAULT CHARSET=utf8 COMMENT='用户账单表';

-- ----------------------------
-- Table structure for mqtt_acl
-- ----------------------------
DROP TABLE IF EXISTS `mqtt_acl`;
CREATE TABLE `mqtt_acl` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `allow` int(1) DEFAULT NULL COMMENT '0: deny, 1: allow',
  `ipaddr` varchar(60) DEFAULT NULL COMMENT 'IpAddress',
  `username` varchar(100) DEFAULT NULL COMMENT 'Username',
  `clientid` varchar(100) DEFAULT NULL COMMENT 'ClientId',
  `access` int(2) NOT NULL COMMENT '1: subscribe, 2: publish, 3: pubsub',
  `topic` varchar(100) NOT NULL DEFAULT '' COMMENT 'Topic Filter',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for mqtt_user
-- ----------------------------
DROP TABLE IF EXISTS `mqtt_user`;
CREATE TABLE `mqtt_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `salt` varchar(20) DEFAULT NULL,
  `is_superuser` tinyint(1) DEFAULT '0',
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mqtt_username` (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for sm_request_log
-- ----------------------------
DROP TABLE IF EXISTS `sm_request_log`;
CREATE TABLE `sm_request_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(100) NOT NULL COMMENT 'IP',
  `uri` varchar(100) DEFAULT NULL COMMENT '路由',
  `params` text COMMENT '参数',
  `time_usage` decimal(10,4) NOT NULL DEFAULT '0.0000' COMMENT '时间使用量S',
  `memory_usage` decimal(10,4) NOT NULL DEFAULT '0.0000' COMMENT '内存使用量MB',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8945 DEFAULT CHARSET=utf8 COMMENT='请求日志';

-- ----------------------------
-- Table structure for sm_smell
-- ----------------------------
DROP TABLE IF EXISTS `sm_smell`;
CREATE TABLE `sm_smell` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `smell_sn` varchar(20) NOT NULL COMMENT '自定义气味编号',
  `en_name` varchar(50) NOT NULL DEFAULT '' COMMENT '气味英文名',
  `cn_name` varchar(50) NOT NULL DEFAULT '' COMMENT '气味中文名',
  `tags` varchar(50) NOT NULL DEFAULT '' COMMENT '标签id 冒号分隔',
  `source` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '气味来源 0:未知 1:配方 2:采购',
  `source_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '气味关联id(配方表or采购材料表)',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0',
  `deleted_at` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5274 DEFAULT CHARSET=utf8 COMMENT='气味表';

-- ----------------------------
-- Table structure for sm_smell_pc
-- ----------------------------
DROP TABLE IF EXISTS `sm_smell_pc`;
CREATE TABLE `sm_smell_pc` (
  `id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `smell_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `thumb` varchar(100) DEFAULT NULL,
  `width` smallint(5) DEFAULT '0' COMMENT 'width',
  `height` smallint(5) DEFAULT '0' COMMENT 'height',
  `category_id` int(1) NOT NULL DEFAULT '0',
  `user_id` int(1) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for sm_smell_pc2
-- ----------------------------
DROP TABLE IF EXISTS `sm_smell_pc2`;
CREATE TABLE `sm_smell_pc2` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `smell_id` mediumint(8) NOT NULL COMMENT '分类名称',
  `probably_name` varchar(50) NOT NULL COMMENT '图片查询失败，可能正确的名字',
  `thumb` varchar(255) NOT NULL COMMENT '文件路径',
  `height` smallint(5) DEFAULT '0' COMMENT 'height',
  `width` smallint(5) DEFAULT '0' COMMENT 'width',
  `category_id` smallint(6) unsigned DEFAULT NULL COMMENT '分类id',
  `user_id` int(10) unsigned DEFAULT NULL COMMENT '创建人',
  `created_at` int(10) NOT NULL COMMENT '创建时间',
  `updated_at` int(10) NOT NULL COMMENT '更新时间',
  `deleted_at` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33874 DEFAULT CHARSET=utf8 COMMENT='气味分类管理';

-- ----------------------------
-- Table structure for sm_smell_pc_copy
-- ----------------------------
DROP TABLE IF EXISTS `sm_smell_pc_copy`;
CREATE TABLE `sm_smell_pc_copy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `smell_id` mediumint(8) NOT NULL COMMENT '分类名称',
  `probably_name` varchar(50) NOT NULL COMMENT '图片查询失败，可能正确的名字',
  `thumb` varchar(255) NOT NULL COMMENT '文件路径',
  `height` smallint(5) DEFAULT '0' COMMENT 'height',
  `width` smallint(5) DEFAULT '0' COMMENT 'width',
  `category_id` smallint(6) unsigned DEFAULT NULL COMMENT '分类id',
  `user_id` int(10) unsigned DEFAULT NULL COMMENT '创建人',
  `created_at` int(10) NOT NULL COMMENT '创建时间',
  `updated_at` int(10) NOT NULL COMMENT '更新时间',
  `deleted_at` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35403 DEFAULT CHARSET=utf8 COMMENT='气味分类管理';

-- ----------------------------
-- Table structure for sm_smell_pc_copy1
-- ----------------------------
DROP TABLE IF EXISTS `sm_smell_pc_copy1`;
CREATE TABLE `sm_smell_pc_copy1` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `smell_id` mediumint(8) NOT NULL COMMENT '分类名称',
  `probably_name` varchar(50) NOT NULL COMMENT '图片查询失败，可能正确的名字',
  `thumb` varchar(255) NOT NULL COMMENT '文件路径',
  `height` smallint(5) DEFAULT '0' COMMENT 'height',
  `width` smallint(5) DEFAULT '0' COMMENT 'width',
  `category_id` smallint(6) unsigned DEFAULT NULL COMMENT '分类id',
  `user_id` int(10) unsigned DEFAULT NULL COMMENT '创建人',
  `created_at` int(10) NOT NULL COMMENT '创建时间',
  `updated_at` int(10) NOT NULL COMMENT '更新时间',
  `deleted_at` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35383 DEFAULT CHARSET=utf8 COMMENT='气味分类管理';

-- ----------------------------
-- Table structure for sm_smell_pc_copy2
-- ----------------------------
DROP TABLE IF EXISTS `sm_smell_pc_copy2`;
CREATE TABLE `sm_smell_pc_copy2` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `smell_id` mediumint(8) NOT NULL COMMENT '分类名称',
  `probably_name` varchar(50) NOT NULL COMMENT '图片查询失败，可能正确的名字',
  `thumb` varchar(255) NOT NULL COMMENT '文件路径',
  `height` smallint(5) DEFAULT '0' COMMENT 'height',
  `width` smallint(5) DEFAULT '0' COMMENT 'width',
  `category_id` smallint(6) unsigned DEFAULT NULL COMMENT '分类id',
  `user_id` int(10) unsigned DEFAULT NULL COMMENT '创建人',
  `created_at` int(10) NOT NULL COMMENT '创建时间',
  `updated_at` int(10) NOT NULL COMMENT '更新时间',
  `deleted_at` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35413 DEFAULT CHARSET=utf8 COMMENT='气味分类管理';

-- ----------------------------
-- Table structure for sm_smell_pc_copy3
-- ----------------------------
DROP TABLE IF EXISTS `sm_smell_pc_copy3`;
CREATE TABLE `sm_smell_pc_copy3` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `smell_id` mediumint(8) NOT NULL COMMENT '分类名称',
  `probably_name` varchar(50) NOT NULL COMMENT '图片查询失败，可能正确的名字',
  `thumb` varchar(255) NOT NULL COMMENT '文件路径',
  `height` smallint(5) DEFAULT '0' COMMENT 'height',
  `width` smallint(5) DEFAULT '0' COMMENT 'width',
  `category_id` smallint(6) unsigned DEFAULT NULL COMMENT '分类id',
  `user_id` int(10) unsigned DEFAULT NULL COMMENT '创建人',
  `created_at` int(10) NOT NULL COMMENT '创建时间',
  `updated_at` int(10) NOT NULL COMMENT '更新时间',
  `deleted_at` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=37485 DEFAULT CHARSET=utf8 COMMENT='气味分类管理';

-- ----------------------------
-- Table structure for sm_smell_pc_copy4
-- ----------------------------
DROP TABLE IF EXISTS `sm_smell_pc_copy4`;
CREATE TABLE `sm_smell_pc_copy4` (
  `id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `smell_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `thumb` varchar(100) DEFAULT NULL,
  `category_id` int(1) NOT NULL DEFAULT '0',
  `user_id` int(1) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for sm_smell_pc_copy5
-- ----------------------------
DROP TABLE IF EXISTS `sm_smell_pc_copy5`;
CREATE TABLE `sm_smell_pc_copy5` (
  `id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `smell_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `thumb` varchar(100) DEFAULT NULL,
  `category_id` int(1) NOT NULL DEFAULT '0',
  `user_id` int(1) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for sm_smell_pc_copy6
-- ----------------------------
DROP TABLE IF EXISTS `sm_smell_pc_copy6`;
CREATE TABLE `sm_smell_pc_copy6` (
  `id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `smell_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `thumb` varchar(100) DEFAULT NULL,
  `category_id` int(1) NOT NULL DEFAULT '0',
  `user_id` int(1) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for sm_smell_pc_pass
-- ----------------------------
DROP TABLE IF EXISTS `sm_smell_pc_pass`;
CREATE TABLE `sm_smell_pc_pass` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `smell_id` mediumint(8) NOT NULL COMMENT '分类名称',
  `probably_name` varchar(50) NOT NULL COMMENT '图片查询失败，可能正确的名字',
  `thumb` varchar(255) NOT NULL COMMENT '文件路径',
  `height` smallint(5) DEFAULT '0' COMMENT 'height',
  `width` smallint(5) DEFAULT '0' COMMENT 'width',
  `category_id` smallint(6) unsigned DEFAULT NULL COMMENT '分类id',
  `user_id` int(10) unsigned DEFAULT NULL COMMENT '创建人',
  `created_at` int(10) NOT NULL COMMENT '创建时间',
  `updated_at` int(10) NOT NULL COMMENT '更新时间',
  `deleted_at` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35403 DEFAULT CHARSET=utf8 COMMENT='气味分类管理';

-- ----------------------------
-- Table structure for sm_smell_pc_pass_copy
-- ----------------------------
DROP TABLE IF EXISTS `sm_smell_pc_pass_copy`;
CREATE TABLE `sm_smell_pc_pass_copy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `smell_id` mediumint(8) NOT NULL COMMENT '分类名称',
  `probably_name` varchar(50) NOT NULL COMMENT '图片查询失败，可能正确的名字',
  `thumb` varchar(255) NOT NULL COMMENT '文件路径',
  `height` smallint(5) DEFAULT '0' COMMENT 'height',
  `width` smallint(5) DEFAULT '0' COMMENT 'width',
  `category_id` smallint(6) unsigned DEFAULT NULL COMMENT '分类id',
  `user_id` int(10) unsigned DEFAULT NULL COMMENT '创建人',
  `created_at` int(10) NOT NULL COMMENT '创建时间',
  `updated_at` int(10) NOT NULL COMMENT '更新时间',
  `deleted_at` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35403 DEFAULT CHARSET=utf8 COMMENT='气味分类管理';

-- ----------------------------
-- Table structure for sm_smell_pc_processed
-- ----------------------------
DROP TABLE IF EXISTS `sm_smell_pc_processed`;
CREATE TABLE `sm_smell_pc_processed` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `smell_id` mediumint(8) NOT NULL COMMENT '分类名称',
  `probably_name` varchar(50) NOT NULL COMMENT '图片查询失败，可能正确的名字',
  `thumb` varchar(255) NOT NULL COMMENT '文件路径',
  `height` smallint(5) DEFAULT '0' COMMENT 'height',
  `width` smallint(5) DEFAULT '0' COMMENT 'width',
  `category_id` smallint(6) unsigned DEFAULT NULL COMMENT '分类id',
  `user_id` int(10) unsigned DEFAULT NULL COMMENT '创建人',
  `created_at` int(10) NOT NULL COMMENT '创建时间',
  `updated_at` int(10) NOT NULL COMMENT '更新时间',
  `deleted_at` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35403 DEFAULT CHARSET=utf8 COMMENT='气味分类管理';

-- ----------------------------
-- Table structure for sm_smell_thumb
-- ----------------------------
DROP TABLE IF EXISTS `sm_smell_thumb`;
CREATE TABLE `sm_smell_thumb` (
  `id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `cn_name` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `thumb` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `localpath` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `down_status` tinyint(4) NOT NULL DEFAULT '0',
  `height` smallint(6) DEFAULT NULL,
  `width` smallint(6) DEFAULT NULL,
  `mime` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for sm_smell_thumb_copy
-- ----------------------------
DROP TABLE IF EXISTS `sm_smell_thumb_copy`;
CREATE TABLE `sm_smell_thumb_copy` (
  `id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `cn_name` varchar(50) NOT NULL DEFAULT '' COMMENT '气味中文名',
  `thumb` varchar(255) NOT NULL COMMENT '文件路径',
  `localpath` varchar(100) DEFAULT NULL COMMENT '本地地址',
  `down_status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '下载状态0未处理1已下载，2下失败',
  `height` smallint(5) DEFAULT '0' COMMENT 'height',
  `width` smallint(5) DEFAULT '0' COMMENT 'width',
  `mime` varchar(50) NOT NULL DEFAULT '0' COMMENT 'mime type'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for sm_smell_thumb_copy1
-- ----------------------------
DROP TABLE IF EXISTS `sm_smell_thumb_copy1`;
CREATE TABLE `sm_smell_thumb_copy1` (
  `id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `cn_name` varchar(50) NOT NULL DEFAULT '' COMMENT '气味中文名',
  `thumb` varchar(255) NOT NULL COMMENT '文件路径',
  `localpath` varchar(100) DEFAULT NULL COMMENT '本地地址',
  `down_status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '下载状态0未处理1已下载，2下失败',
  `height` smallint(5) DEFAULT '0' COMMENT 'height',
  `width` smallint(5) DEFAULT '0' COMMENT 'width',
  `mime` varchar(50) NOT NULL DEFAULT '0' COMMENT 'mime type'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for sm_smell_thumb_part02
-- ----------------------------
DROP TABLE IF EXISTS `sm_smell_thumb_part02`;
CREATE TABLE `sm_smell_thumb_part02` (
  `id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `cn_name` varchar(50) NOT NULL DEFAULT '' COMMENT '气味中文名',
  `thumb` varchar(255) NOT NULL COMMENT '文件路径',
  `localpath` varchar(100) DEFAULT NULL COMMENT '本地地址',
  `down_status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '下载状态0未处理1已下载，2下失败',
  `height` smallint(5) DEFAULT '0' COMMENT 'height',
  `width` smallint(5) DEFAULT '0' COMMENT 'width',
  `mime` varchar(50) NOT NULL DEFAULT '0' COMMENT 'mime type'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for x_bill
-- ----------------------------
DROP TABLE IF EXISTS `x_bill`;
CREATE TABLE `x_bill` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL COMMENT '姓名',
  `type` tinyint(4) NOT NULL COMMENT '金额类别，1需付，1已付',
  `desc` varchar(160) NOT NULL COMMENT '描述',
  `amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '金额',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '时间',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1128 DEFAULT CHARSET=utf8 COMMENT='用户账单表';

-- ----------------------------
-- Table structure for x_request_log
-- ----------------------------
DROP TABLE IF EXISTS `x_request_log`;
CREATE TABLE `x_request_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(100) NOT NULL COMMENT 'IP',
  `uri` varchar(100) DEFAULT NULL COMMENT '路由',
  `params` text COMMENT '参数',
  `time_usage` decimal(10,4) NOT NULL DEFAULT '0.0000' COMMENT '时间使用量S',
  `memory_usage` decimal(10,4) NOT NULL DEFAULT '0.0000' COMMENT '内存使用量MB',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4358 DEFAULT CHARSET=utf8 COMMENT='请求日志';

-- ----------------------------
-- Table structure for x_topic_msg
-- ----------------------------
DROP TABLE IF EXISTS `x_topic_msg`;
CREATE TABLE `x_topic_msg` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `message` varchar(255) NOT NULL COMMENT 'message',
  `topic` varchar(255) DEFAULT NULL COMMENT 'topic',
  `sign` varchar(255) DEFAULT NULL COMMENT 'sign= md5_32(productKey+(message)+productSecret)',
  `messageId` varchar(255) DEFAULT NULL COMMENT 'messageId',
  `appKey` varchar(255) DEFAULT NULL COMMENT 'appKey',
  `deviceId` varchar(255) DEFAULT NULL COMMENT 'deviceId',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Topic消息';

-- ----------------------------
-- Table structure for x_transfer
-- ----------------------------
DROP TABLE IF EXISTS `x_transfer`;
CREATE TABLE `x_transfer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(50) DEFAULT NULL COMMENT 'contentuid',
  `eng` text COMMENT '英原文',
  `chi` text COMMENT '中译文',
  `status` tinyint(2) DEFAULT '0' COMMENT '是否被翻译',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27933 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for x_transfer_copy
-- ----------------------------
DROP TABLE IF EXISTS `x_transfer_copy`;
CREATE TABLE `x_transfer_copy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(50) DEFAULT NULL COMMENT 'contentuid',
  `eng` text COMMENT '英原文',
  `chi` text COMMENT '中译文',
  `status` tinyint(2) DEFAULT '0' COMMENT '是否被翻译',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27933 DEFAULT CHARSET=utf8;

