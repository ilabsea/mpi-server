ALTER TABLE `field_log` ADD `pat_id` VARCHAR(20) NOT NULL AFTER `application_name`;
ALTER TABLE `field_log` CHANGE `application_name` `application_name` INT(11) NULL DEFAULT NULL;
ALTER TABLE `field_log` CHANGE `application_id` `application_id` INT(11) NULL DEFAULT NULL;
