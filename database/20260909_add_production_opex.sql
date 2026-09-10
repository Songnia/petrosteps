ALTER TABLE `tbl_project`
  ADD COLUMN `Project_Opex` DECIMAL(20,2) NOT NULL DEFAULT 0 AFTER `Project_Spending`;

INSERT INTO `tbl_parameters` (`name`, `value`, `display_name`, `unit`)
SELECT 'Production_Opex_Per_Barrel', '10', 'Production OPEX', '(USD/B)'
WHERE NOT EXISTS (
  SELECT 1 FROM `tbl_parameters` WHERE `name` = 'Production_Opex_Per_Barrel'
);
