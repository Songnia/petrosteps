/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_admin` (
  `id` int NOT NULL AUTO_INCREMENT,
  `surname` varchar(50) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `user_type` enum('SuperAdmin','Admin','Participant','Trainer') NOT NULL,
  `created_at` date NOT NULL,
  `activated` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `pwd` varchar(50) NOT NULL,
  `country_id` int NOT NULL,
  `license_expiry_date` date NOT NULL,
  `trainer_id` int NOT NULL,
  `active_session` varchar(100) NOT NULL,
  `last_access_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_block` (
  `bid` bigint NOT NULL AUTO_INCREMENT,
  `Block_Name` varchar(100) NOT NULL,
  `Block_Surface` varchar(100) NOT NULL,
  `Block_Description` varchar(100) NOT NULL,
  `Block_License_Cost_Exp` float NOT NULL,
  `Block_License_cost_Prod` float NOT NULL,
  `Probability_to_find` varchar(100) NOT NULL,
  `Signature_Bonus` float NOT NULL,
  `Block_Survey_Cost` float NOT NULL,
  `Block_Survey_interpretation_cost` float NOT NULL,
  `Block_License_Status` enum('True','False') NOT NULL,
  `Block_Survey_Status` enum('True','False') NOT NULL,
  `Block_Survey_Interpretation_Status` enum('True','False') NOT NULL,
  `Field` bigint NOT NULL,
  PRIMARY KEY (`bid`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_countries` (
  `cid` int NOT NULL AUTO_INCREMENT,
  `cname` varchar(255) NOT NULL,
  PRIMARY KEY (`cid`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_field` (
  `fid` int NOT NULL AUTO_INCREMENT,
  `Field_Name` varchar(100) NOT NULL,
  `Field_Average_TD` int NOT NULL,
  `Field_Road_Cost` float NOT NULL,
  `Field_Accommodation_Cost` float NOT NULL,
  `Field_Drilling_Cost` float NOT NULL,
  `Field_Formation_Eval_cost_Exp` float NOT NULL,
  `Field_Formation_Eval_cost_Dev` varchar(100) NOT NULL,
  `Field_Casing_Cement_cost` float NOT NULL,
  `Field_Testing_cost` float NOT NULL,
  `Field_Completion_Cost` float NOT NULL,
  `Other_Gen_Sup_Cost` float NOT NULL,
  `Other_Tech_Sup_Cost` float NOT NULL,
  `Field_Abandonment_cost` float NOT NULL,
  `Field_Water_Saturation` varchar(100) NOT NULL,
  `Field_Field_Porosity` varchar(100) NOT NULL,
  `Field_BO` varchar(100) NOT NULL,
  `Field_Reservoir_Volume` int NOT NULL,
  `Field_Recovery_Factor` float NOT NULL,
  `Field_ExpWell1` bigint NOT NULL,
  `Field_ExpWell2` bigint NOT NULL,
  `Field_AppWell1` bigint NOT NULL,
  `Field_AppWell2` bigint NOT NULL,
  `Field_DevWell1` bigint NOT NULL,
  `Field_DevWell2` bigint NOT NULL,
  `Field_DevWell3` bigint NOT NULL,
  PRIMARY KEY (`fid`),
  UNIQUE KEY `fid` (`fid`),
  UNIQUE KEY `Field_Name` (`Field_Name`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_parameters` (
  `Pid` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `value` varchar(100) NOT NULL,
  `display_name` varchar(100) NOT NULL,
  `unit` varchar(100) NOT NULL,
  PRIMARY KEY (`Pid`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_production_facility` (
  `pfid` bigint NOT NULL AUTO_INCREMENT,
  `Prod_Facilities_Name` varchar(100) NOT NULL,
  `Prod_Facilities_Capacity` varchar(100) NOT NULL,
  `Prod_Facilities_Cost` float NOT NULL,
  `Production_Facility_Satus` enum('On','Off') NOT NULL,
  `Prod_Facilities_decommissioning_cost` float NOT NULL,
  PRIMARY KEY (`pfid`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_project` (
  `Pid` bigint NOT NULL AUTO_INCREMENT,
  `Project_Name` varchar(100) NOT NULL,
  `Project_year` int NOT NULL DEFAULT '0',
  `Production_year` int NOT NULL,
  `Project_Budget` float NOT NULL,
  `Project_Block` varchar(100) NOT NULL,
  `Project_Field` varchar(100) NOT NULL,
  `Project_Spending` float NOT NULL,
  `Project_License_Cost` float NOT NULL,
  `Project_Survey_Cost` float NOT NULL,
  `Project_Interpretation_cost` float NOT NULL,
  `Project_road_Cost` float NOT NULL,
  `Project_Road_Status` enum('False','True') NOT NULL,
  `Project_Accomodation_cost` float NOT NULL,
  `Project_Accommodation_Status` enum('False','True') NOT NULL,
  `Project_ExpWell_Drilling_Cost` int NOT NULL,
  `Project_ExpWell_FE_Cost` int NOT NULL,
  `Project_ExpWell_Casing_Cement_Cost` int NOT NULL,
  `Project_ExpWell_Testing_Cost` int NOT NULL,
  `Project_ExpWell_Cost` int NOT NULL,
  `Project_AppWell_Cost` int NOT NULL,
  `Project_Finding_Cost` float NOT NULL,
  `Project_DevWell_Cost` int NOT NULL,
  `Project_Assess_Cost` int NOT NULL,
  `Project_RWP_Cost` int NOT NULL,
  `Project_Abandonment_Cost` int NOT NULL,
  `Project_Decomissionning_Cost` int NOT NULL,
  `Project_Recoverable_Volume` int NOT NULL,
  `Project_Oil_in_Place` varchar(100) NOT NULL,
  `Project_Projected_Revenue` varchar(100) NOT NULL,
  `Project_Total_Flowrate` float NOT NULL,
  `Project_Cumulative_Flow` varchar(100) NOT NULL DEFAULT '0',
  `Project_Actual_Revenue` float NOT NULL,
  `Project_Production_Facility` varchar(100) NOT NULL,
  `Project_Task11_Status` varchar(100) NOT NULL,
  `Project_Task21_Status` varchar(100) NOT NULL,
  `Project_Task22_Status` varchar(100) NOT NULL,
  `Project_Task31_Status` varchar(100) NOT NULL,
  `Project_Task32_Status` varchar(100) NOT NULL,
  `Project_Task33_Status` varchar(100) NOT NULL,
  `Project_Task34_Status` varchar(100) NOT NULL,
  `Project_Task35_Status` varchar(100) NOT NULL,
  `Project_Task36_Status` varchar(100) NOT NULL,
  `Project_Task37_Status` varchar(100) NOT NULL,
  `Project_Task41_Status` varchar(100) NOT NULL,
  `Project_Task42_Status` varchar(100) NOT NULL,
  `Project_Task51_Status` varchar(100) NOT NULL,
  `Project_Task52_Status` varchar(100) NOT NULL,
  `Project_Task53_Status` varchar(100) NOT NULL,
  `Project_Task54_Status` varchar(100) NOT NULL,
  `Project_Task61_Status` varchar(100) NOT NULL,
  `Project_Task62_Status` varchar(100) NOT NULL,
  `Project_Task71_Status` varchar(100) NOT NULL,
  `Project_Task72_Status` varchar(100) NOT NULL,
  `Project_Task81_Status` varchar(100) NOT NULL,
  `Project_Task82_Status` varchar(100) NOT NULL,
  `user_id` bigint NOT NULL,
  `steps_completed` int NOT NULL DEFAULT '0',
  `tasks_completed` int NOT NULL,
  PRIMARY KEY (`Pid`)
) ENGINE=InnoDB AUTO_INCREMENT=95 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_project_step` (
  `id` int NOT NULL AUTO_INCREMENT,
  `Project_Id` int NOT NULL,
  `Project_year` int NOT NULL,
  `Project_Actual_Revenue` float NOT NULL,
  `Project_Spending` float NOT NULL,
  `Project_Cumulative_Flow` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `Project_Id` (`Project_Id`)
) ENGINE=InnoDB AUTO_INCREMENT=730 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_project_years` (
  `id` int NOT NULL AUTO_INCREMENT,
  `step` int NOT NULL,
  `project_year` int NOT NULL,
  `step_name` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `step` (`step`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_state` (
  `id` int NOT NULL,
  `name` varchar(30) NOT NULL,
  `country_id` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_well` (
  `Wid` bigint NOT NULL AUTO_INCREMENT,
  `Well_Name` varchar(100) NOT NULL,
  `Well_Depth` varchar(100) NOT NULL,
  `Well_Flowrate` varchar(100) NOT NULL,
  `Well_Production_Start` date NOT NULL,
  `Well_Production_Stop` date NOT NULL,
  `Well_Drilling_Status` enum('True','False') NOT NULL,
  `Well_FE_Status` enum('True','False') NOT NULL,
  `Well_Casing_Cement_Status` enum('True','False') NOT NULL,
  `Well_Testing_Status` enum('True','False') NOT NULL,
  `Well_Completion_Status` enum('True','False') NOT NULL,
  `Well_Production_Status` enum('Closed','Open') NOT NULL,
  `Well_Abandonment_Status` enum('True','False') NOT NULL,
  PRIMARY KEY (`Wid`),
  UNIQUE KEY `Well_Name` (`Well_Name`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
