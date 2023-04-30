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

-- ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
-- // Changed Amenity_Allocated_By column in devotee_amenities_allocation table to Varchar(10)
-- //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
ALTER TABLE `kdms2022`.`devotee_amenities_allocation` 
CHANGE COLUMN `Amenity_Allocated_By` `Amenity_Allocated_By` VARCHAR(10) NULL DEFAULT NULL COMMENT 'UserID of the logged in user at the time of amenity allocation' ;


-- ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
-- // Added allocation_event column in amenities_allocation_log table and removed Amenity_Action_Year from it
-- //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
ALTER TABLE `kdms2022`.`amenities_allocation_log` 
CHANGE COLUMN `Amenity_Action_Year` `Allocation_Event` VARCHAR(10) NOT NULL ;

-- ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
-- // Added table devotee_remarks
-- //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
CREATE TABLE `kdms2022`.`devotee_remarks` (
  `devotee_key` VARCHAR(10) NOT NULL,
  `remark_type` VARCHAR(15) NOT NULL DEFAULT 'MISC',
  `remark_event` VARCHAR(10) NOT NULL,
  `rating` INT NULL,
  `remark` VARCHAR(250) NULL,
  `remark_update_date_time` DATETIME NULL,
  `remark_updated_by` VARCHAR(10) NULL,
  PRIMARY KEY (`devotee_key`, `remark_type`, `remark_event`));

-- ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
-- // Added table devotee_attendance
-- //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
CREATE TABLE `kdms_gold_2022`.`devotee_attendance` (
  `devotee_key` varchar(10) NOT NULL,
  `seva_id` varchar(6) NOT NULL DEFAULT 'UN',
  `attendance_date` date NOT NULL,
  `rating` int DEFAULT 1,
  `remark` varchar(250) DEFAULT NULL,
  `attendance_update_date_time` datetime DEFAULT NULL,
  `attendance_updated_by` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`devotee_key`,`seva_id`,`attendance_date`)
) 
-- ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
-- // Added column seva_event to table devotee_attendance and made it part of primary key
-- //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
ALTER TABLE `kdms2022`.`devotee_attendance` 
ADD COLUMN `seva_event` VARCHAR(10) NOT NULL AFTER `attendance_date`,
DROP PRIMARY KEY,
ADD PRIMARY KEY (`devotee_key`, `seva_id`, `attendance_date`, `seva_event`);
;

-- ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
-- // updated user access table to increase length of user_key column
-- //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
ALTER TABLE `kdms2022`.`user_master` 
CHANGE COLUMN `User_Key` `User_Key` VARCHAR(20) NOT NULL ;

-- ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
-- // Added table user_access
-- //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
CREATE TABLE `user_access` (
  `user_role_key` varchar(20) NOT NULL,
  `asset_key` varchar(10) NOT NULL,
  `access_value` varchar(10) NOT NULL DEFAULT 'NONE',
  `access_updated_by` varchar(10) DEFAULT NULL,
  `access_update_date_time` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_key`,`asset_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
-- // Added table user_permission
-- //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
CREATE TABLE `kdms2022`.`asset_list` (
  `asset_key` VARCHAR(10) NOT NULL,
  `asset_name` VARCHAR(100) NOT NULL,
  `asset_updated_by` VARCHAR(10) NOT NULL,
  `asset_update_date_time` DATETIME NOT NULL,
  PRIMARY KEY (`asset_key`));

-- ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
-- // Added table user_favorite
-- //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
CREATE TABLE `kdms2022`.`user_favorites` (
  `user_key` VARCHAR(10) NOT NULL,
  `fav_name` VARCHAR(100) NOT NULL DEFAULT 'Favorite' 
  `fav_type` VARCHAR(10) NOT NULL DEFAULT 'REPORT',
  `fav_url` VARCHAR(250) NOT NULL DEFAULT 'http://google.com' ,
  `fav_public` VARCHAR(10) NOT NULL DEFAULT 'NO',
  `fav_updated_by` VARCHAR(10) NULL,
  `fav_update_date_time` DATETIME NULL,
  PRIMARY KEY (`user_key`, `fav_name`));

-- ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
-- Modify Blob columns to support storing high quality images (Devotee ID and Devotee Photo).
-- ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
ALTER TABLE Devotee_ID MODIFY COLUMN Devotee_ID_Image LONGBLOB;
ALTER TABLE devotee_photo MODIFY COLUMN Devotee_Photo LONGBLOB;

-- for kdms ocr temprory bucket.
CREATE TABLE `kdms_ocr_image_bucket` (
  `image_name` VARCHAR(500) NOT NULL,
  `image` longblob,
  `status` int DEFAULT '1' COMMENT '0=inactive;1=active;',
  `image_uploaded_at` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'Date/Time of creation/modification of devotee photo',
  PRIMARY KEY (`image_name`)
);
-- ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
-- Create office_duty_archive table
-- ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
CREATE TABLE `office_duty_archive` (
  `Officer_Key` varchar(25) NOT NULL,
  `Devotee_Key` varchar(10) NOT NULL,
  `Duty_Type` varchar(50) NOT NULL,
  `Duty_Position_Key` varchar(10) DEFAULT NULL,
  `Duty_Location_Key` varchar(10) NOT NULL,
  `Duty_Event` varchar(10) NOT NULL,
  `Duty_Status` varchar(10) NOT NULL,
  PRIMARY KEY (`Officer_Key`,`Duty_Event`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
-- Create card_print_archive table
-- ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
CREATE TABLE `card_print_archive` (
  `Devotee_Key` varchar(10) NOT NULL,
  `Print_Status` varchar(1) NOT NULL DEFAULT 'A',
  `Print_Requested_Date_Time` datetime DEFAULT NULL,
  `Print_Requested_By_User` varchar(10) DEFAULT NULL,
  UNIQUE KEY `UniqueDevoteeStatus` (`Devotee_Key`,`Print_Status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
-- ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
-- Create print_log table
-- ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
CREATE TABLE `print_log` (
  `Devotee_Key` varchar(10) NOT NULL,
  `Event_Id` varchar(45) DEFAULT NULL,
  `Print_Requested_By_User` varchar(10) DEFAULT NULL,
  `Print_Date_Time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;