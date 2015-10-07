ALTER TABLE  `flat` ADD  `quick_views` INT( 5 ) NOT NULL DEFAULT  '0';

ALTER TABLE  `user` ADD  `login` VARCHAR( 24 ) NOT NULL AFTER  `email` ,
ADD INDEX (  `login`) 

ALTER TABLE  `tenement` ADD  `user_id` INT( 11 ) NOT NULL DEFAULT 