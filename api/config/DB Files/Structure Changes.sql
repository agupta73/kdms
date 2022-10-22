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
ALTER TABLE devotee`
    ADD COLUMN `Comments` VARCHAR(250) NULL DEFAULT NULL AFTER `Devotee_Remarks`;

ALTER TABLE `devotee`
    ADD COLUMN `Joined_Since` VARCHAR(15) NULL DEFAULT NULL AFTER `Devotee_Record_Updated_By`;

ALTER TABLE `devotee`
CHANGE COLUMN `Joined_Since` `Joined_Since` VARCHAR(15) NULL DEFAULT NULL AFTER `Devotee_Status`;
