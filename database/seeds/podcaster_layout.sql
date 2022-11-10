CREATE TABLE `module` (
                          `module_id` int(11) NOT NULL DEFAULT '0',
                          `is_configurable` smallint(1) DEFAULT NULL,
                          `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
                          `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
                          `description` text COLLATE utf8_unicode_ci,
                          `admin_uri` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
                          `icon` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
                          `maintainers` text COLLATE utf8_unicode_ci,
                          `version` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL,
                          `license` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
                          `state` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL,
                          PRIMARY KEY (`module_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `user_cookie` (
                               `usr_id` int(11) NOT NULL,
                               `cookie_name` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
                               `login_time` datetime NOT NULL,
                               KEY `usr_id` (`usr_id`),
                               KEY `cookie_name` (`cookie_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `preference` (
                              `preference_id` int(11) NOT NULL,
                              `name` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
                              `default_value` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
                              PRIMARY KEY (`preference_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `user_preference` (
                                   `user_preference_id` int(11) NOT NULL,
                                   `usr_id` int(11) NOT NULL,
                                   `preference_id` int(11) NOT NULL,
                                   `value` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
                                   PRIMARY KEY (`user_preference_id`),
                                   KEY `usr_user_preference_fk` (`usr_id`),
                                   KEY `preference_user_preference_fk` (`preference_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `role_permission` (
                                   `role_permission_id` int(11) NOT NULL DEFAULT '0',
                                   `role_id` int(11) NOT NULL DEFAULT '0',
                                   `permission_id` int(11) NOT NULL DEFAULT '0',
                                   PRIMARY KEY (`role_permission_id`),
                                   KEY `permission_id` (`permission_id`),
                                   KEY `role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `role` (
                        `role_id` int(11) NOT NULL DEFAULT '-1',
                        `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
                        `description` text COLLATE utf8_unicode_ci,
                        `date_created` datetime DEFAULT NULL,
                        `created_by` int(11) DEFAULT NULL,
                        `last_updated` datetime DEFAULT NULL,
                        `updated_by` int(11) DEFAULT NULL,
                        PRIMARY KEY (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `user_oauth` (
                              `user_oauth_id` int(10) NOT NULL AUTO_INCREMENT,
                              `usr_id` int(10) NOT NULL COMMENT 'User ID',
                              `screen_name` varchar(40) NOT NULL COMMENT 'Twitter usename',
                              `oauth_token` text COMMENT 'OAuth access token',
                              `service` varchar(15) NOT NULL COMMENT 'Name of service',
                              PRIMARY KEY (`user_oauth_id`),
                              UNIQUE KEY `usr_id` (`usr_id`,`screen_name`,`service`)
) ENGINE=InnoDB AUTO_INCREMENT=1565 DEFAULT CHARSET=utf8 COMMENT='OAuth info for services';

CREATE TABLE `user_session` (
                                `session_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                                `last_updated` datetime DEFAULT NULL,
                                `data_value` text COLLATE utf8_unicode_ci,
                                `usr_id` int(11) NOT NULL DEFAULT '0',
                                `username` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
                                `expiry` int(11) NOT NULL DEFAULT '0',
                                PRIMARY KEY (`session_id`),
                                KEY `last_updated` (`last_updated`),
                                KEY `usr_id` (`usr_id`),
                                KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `login` (
                         `login_id` int(11) NOT NULL,
                         `usr_id` int(11) DEFAULT NULL,
                         `date_time` datetime DEFAULT NULL,
                         `remote_ip` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
                         PRIMARY KEY (`login_id`),
                         KEY `usr_login_fk` (`usr_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
