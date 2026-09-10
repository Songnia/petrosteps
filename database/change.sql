ALTER TABLE `tbl_admin` CHANGE `user_type` `user_type` ENUM( 'SuperAdmin', 'Admin', 'Participant', 'Trainer' ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ;


CREATE TABLE IF NOT EXISTS `tbl_countries` (
  `cid` int(11) NOT NULL AUTO_INCREMENT,
  `cname` varchar(255) NOT NULL,
  PRIMARY KEY (`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;



INSERT INTO `tbl_countries` (`cid`, `cname`) VALUES
(1, 'India'),
(2, 'Shrilanka'),
(3, 'Australia '),
(4, 'New Zealand');

ALTER TABLE `tbl_admin` ADD `country_id` INT( 11 ) NOT NULL ;


ALTER TABLE `tbl_admin` ADD `license_expiry_date` DATE NOT NULL ;

ALTER TABLE `tbl_admin` ADD `trainer_id` INT( 11 ) NOT NULL ;

----------------------------- New Changes
ALTER TABLE  `tbl_admin` ADD  `active_session` VARCHAR( 100 ) NOT NULL ;
----------------------------- New Changes graph
ALTER TABLE  `tbl_project` ADD  `Production_year` INT( 11 ) NOT NULL AFTER  `Project_year` ;
ALTER TABLE  `tbl_project` CHANGE  `Project_year`  `Project_year` INT( 10 ) NOT NULL DEFAULT  '0';