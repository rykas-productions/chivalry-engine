DROP TABLE `academy`;

CREATE TABLE `academy` ( 
	`ac_id` INT(11) UNSIGNED NULL DEFAULT NULL AUTO_INCREMENT , 
	`ac_name` TEXT NOT NULL , 
	`ac_desc` TEXT NOT NULL , 
	`ac_cost` INT(11) UNSIGNED NOT NULL , 
	`ac_level` INT(11) UNSIGNED NOT NULL , 
	`ac_days` INT(11) UNSIGNED NOT NULL , 
	`ac_str` INT(11) UNSIGNED NOT NULL , 
	`ac_agl` INT(11) UNSIGNED NOT NULL , 
	`ac_grd` INT(11) UNSIGNED NOT NULL , 
	`ac_lab` INT(11) UNSIGNED NOT NULL , 
	`ac_iq` INT(11) UNSIGNED NOT NULL , 
	PRIMARY KEY (`ac_id`)
) ENGINE = MyISAM;

ALTER TABLE `users` 
	ADD `course` INT(11) UNSIGNED NOT NULL AFTER `need_verify`, 
	ADD `course_complete` INT(11) UNSIGNED NOT NULL AFTER `course`;
	
CREATE TABLE `academy_done` ( 
	`userid` INT(11) UNSIGNED NOT NULL , 
	`course` INT(11) UNSIGNED NOT NULL 
) ENGINE = MyISAM;

ALTER TABLE `users` 
	ADD `email_optin` BOOLEAN NOT NULL DEFAULT TRUE AFTER `course_complete`;
	
CREATE TABLE `ipban` (
  `ip_id` int(11) UNSIGNED NOT NULL,
  `ip_ip` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
ALTER TABLE `ipban`
  ADD UNIQUE KEY `ip_id` (`ip_id`);
ALTER TABLE `users` 
	ADD `email_optin` BOOLEAN NOT NULL DEFAULT TRUE AFTER `course_complete`;
ALTER TABLE `ipban`
  MODIFY `ip_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;