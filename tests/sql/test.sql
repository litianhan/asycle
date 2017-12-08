


CREATE DATABASE test;
GRANT ALL PRIVILEGES ON test.* TO 'test_user'@'%' IDENTIFIED BY '12345678';
FLUSH PRIVILEGES;

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_general_ci NOT NULL COMMENT '用户名',
  `password` varchar(255) NOT NULL DEFAULT '' COMMENT '密码',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP(),
   PRIMARY KEY (`id`),
   KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=UTF8 COMMENT='用户表（用于测试）';

CREATE TABLE IF NOT EXISTS `users_email` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL COMMENT '用户uid',
  `email` varchar(255) COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '用户邮箱',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP(),
   PRIMARY KEY (`id`),
   KEY `uid` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=UTF8 COMMENT='用户邮箱表（用于测试）';
CREATE TABLE IF NOT EXISTS `users_city` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL COMMENT '用户uid',
  `city` varchar(255) COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '用户城市',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP(),
   PRIMARY KEY (`id`),
   KEY `uid` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=UTF8 COMMENT='用户城市表（用于测试）';

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(32) NOT NULL COMMENT '用户uuid',
  `username` varchar(255) COLLATE utf8_general_ci NOT NULL COMMENT '用户名',
  `signature` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci default '' COMMENT '个性签名',
  `email` varchar(255) COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '用户邮箱',
  `password` varchar(255) NOT NULL DEFAULT '' COMMENT '密码',
  `register_time` datetime NOT NULL COMMENT '注册时unix时间戳',
  `register_ip` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '注册时ip',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '用户状态，0正常，1封号',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP(),
   PRIMARY KEY (`id`),
   KEY `uuid` (`uuid`),
   KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='用户表';

