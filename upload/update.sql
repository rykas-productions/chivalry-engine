INSERT INTO `settings` (`setting_id`, `setting_name`, `setting_value`) VALUES (NULL, 'energy_refill_cost', '10');
INSERT INTO `settings` (`setting_id`, `setting_name`, `setting_value`) VALUES (NULL, 'will_refill_cost', '5');
INSERT INTO `settings` (`setting_id`, `setting_name`, `setting_value`) VALUES (NULL, 'brave_refill_cost', '10');
INSERT INTO `settings` (`setting_id`, `setting_name`, `setting_value`) VALUES (NULL, 'iq_per_sec', '5');

CREATE TABLE `sec_market` ( 
	`sec_id` INT(11) UNSIGNED NULL DEFAULT NULL AUTO_INCREMENT , 
	`sec_user` INT(11) UNSIGNED NOT NULL , 
	`sec_cost` INT(11) UNSIGNED NOT NULL , 
	`sec_total` INT(11) UNSIGNED NOT NULL , 
	UNIQUE (`sec_id`)
) ENGINE = MyISAM;