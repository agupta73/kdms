-- //////////////////////////////////////
-- // create table devotee_demographics
-- ////////////////////////////////////////
CREATE TABLE `devotee_demographics` (
                                        `Devotee_Key` varchar(10) NOT NULL,
                                        `Devotee_Address_1` varchar(45) DEFAULT NULL,
                                        `Devotee_Address_2` varchar(45) DEFAULT NULL,
                                        `Devotee_State` varchar(25) DEFAULT NULL,
                                        `Devotee_Zip` varchar(15) DEFAULT NULL,
                                        `Devotee_Country` varchar(15) DEFAULT NULL,
                                        `Devotee_Address_Status` varchar(10) DEFAULT 'Current',
                                        `Devotee_Email` varchar(45) DEFAULT NULL,
                                        `Devotee_Demographics_Status` varchar(10) DEFAULT NULL,
                                        `Devotee_Record_Updated_By` varchar(10) DEFAULT NULL,
                                        `Devotee_Record_Update_Date_Time` datetime DEFAULT NULL,
                                        PRIMARY KEY (`Devotee_Key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- //////////////////////////////////////
-- // Added column comment and Joined Since in table devotee
-- ////////////////////////////////////////
ALTER TABLE `devotee`
    ADD COLUMN `Comments` VARCHAR(250) NULL DEFAULT NULL AFTER `Devotee_Remarks`;

ALTER TABLE `devotee`
    ADD COLUMN `Joined_Since` VARCHAR(15) NULL DEFAULT NULL AFTER `Devotee_Record_Updated_By`;

ALTER TABLE `devotee`
CHANGE COLUMN `Joined_Since` `Joined_Since` VARCHAR(15) NULL DEFAULT NULL AFTER `Devotee_Status`;

-- //////////////////////////////////////
-- // Modified devotee seva table for seva_event
-- ////////////////////////////////////////
ALTER TABLE `devotee_seva`
CHANGE COLUMN `Seva_Year` `Seva_Event` VARCHAR(10) NULL COMMENT 'Year/festival for which seva was assigned' ;

-- //////////////////////////////////////
-- // Modified devotee seva availability table for seva_event
-- ////////////////////////////////////////
ALTER TABLE `seva_availability`
ADD COLUMN `Seva_Event` VARCHAR(10) NOT NULL AFTER `Seva_Id`;

ALTER TABLE `kdms2022`.`seva_availability`
ADD PRIMARY KEY (`Seva_Id`, `Seva_Event`),
DROP INDEX `UniqueSevaID` ;
;

-- ////////////////////////////////////////////////////////////////////////////
-- // Changed duty_year to duty_event (as well as some other changes) in office_duty table
-- //////////////////////////////////////////////////////////////////////////////
ALTER TABLE `office_duty` 
CHANGE COLUMN `Officer_Key` `Officer_Key` VARCHAR(10) NOT NULL ,
CHANGE COLUMN `Duty_Position_Key` `Duty_Position_Key` VARCHAR(10) NULL ,
CHANGE COLUMN `Duty_Year` `Duty_Event` VARCHAR(50) NOT NULL ,
ADD PRIMARY KEY (`Officer_Key`);
;

-- ////////////////////////////////////////////////////////////////////////////
-- // Added column officers_required in duty_location_master table
-- //////////////////////////////////////////////////////////////////////////////
ALTER TABLE `kdms2022`.`duty_location_master` 
ADD COLUMN `Officers_Required` INT NULL AFTER `Duty_Location_Description`;
-- ////////////////////////////////////////////////////////////////////////////
-- // Increase length of column duty_location_name in duty_location_master table
-- //////////////////////////////////////////////////////////////////////////////
ALTER TABLE `kdms2022`.`duty_location_master` 
CHANGE COLUMN `Duty_Location_Name` `Duty_Location_Name` VARCHAR(50) NOT NULL ;

-- ////////////////////////////////////////////////////////////////////////////
-- // Increase length of column officer_key and fixed lenght of duty_event in office_duty table
-- //////////////////////////////////////////////////////////////////////////////
ALTER TABLE `kdms2022`.`office_duty` 
CHANGE COLUMN `Officer_Key` `Officer_Key` VARCHAR(25) NOT NULL ,
CHANGE COLUMN `Duty_Event` `Duty_Event` VARCHAR(10) NOT NULL ;

-- ////////////////////////////////////////////////////////////////////////////
-- // Added allocation_event column in amenity_availability table
-- //////////////////////////////////////////////////////////////////////////////
ALTER TABLE `kdms2022`.`amenities_availability` 
ADD COLUMN `Allocation_event` VARCHAR(10) NOT NULL AFTER `Amenity_Key`,
DROP PRIMARY KEY,
ADD PRIMARY KEY (`Amenity_Key`, `Allocation_event`);

-- ////////////////////////////////////////////////////////////////////////////
-- //Created amenity_availability_archive table
-- //////////////////////////////////////////////////////////////////////////////
CREATE TABLE `amenities_availability_archive` (
  `Amenity_Key` varchar(5) NOT NULL,
  `Allocation_event` varchar(10) NOT NULL,
  `Allocated_Count` int NOT NULL DEFAULT '0' COMMENT 'Number of pieces are allocated',
  `Reserved_Count` int NOT NULL DEFAULT '0' COMMENT 'Number of pieces reserved/blocked',
  `Out_of_Availability_Count` int NOT NULL DEFAULT '0' COMMENT 'Number of pieces that cannot be allocated for any reason (lost/damaged)',
  `Available_Count` int NOT NULL DEFAULT '0' COMMENT 'Pieces available to allocate',
  `Availability_Update_Date_Time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Date/Time of Creation/modification of availability record',
  `Availability_Updated_By` varchar(10) DEFAULT NULL COMMENT 'UserID of the logged in user at the time of availability record creation/modification',
  PRIMARY KEY (`Amenity_Key`,`Allocation_event`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
-- // Added allocation_event column in devotee_amenities_allocation table and removed allocation year from it
-- //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
ALTER TABLE `kdms2022`.`devotee_amenities_allocation` 
CHANGE COLUMN `Amenity_Allocation_Year` `Allocation_Event` VARCHAR(10) NOT NULL COMMENT 'Year/festival for which Amenity was allocated' ;

-- ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
-- // Changed Amenity_Record_Upated_by column in amenity_master table to Varchar(10)
-- //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
ALTER TABLE `kdms2022`.`amenity_master` 
CHANGE COLUMN `Amenity_Record_Upated_by` `Amenity_Record_Upated_by` VARCHAR(10) NULL DEFAULT NULL COMMENT 'UserID of the logged in user at the time of amenity creation/modification' ;
