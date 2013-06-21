ALTER TABLE `class_schedule` ADD `active` INT( 1 ) NOT NULL ;

 ALTER TABLE `class_enrollment` ADD FOREIGN KEY ( `class_id` ) REFERENCES `famefit`.`class_schedule` (
`id`
) ON DELETE RESTRICT ON UPDATE RESTRICT ;

ALTER TABLE `trainer_appointments` ADD `lost_credit` INT( 1 ) NOT NULL;

ALTER TABLE `trainer_appointments` ADD `consultation` BOOLEAN NOT NULL;