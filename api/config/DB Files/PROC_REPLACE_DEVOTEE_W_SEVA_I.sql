-- Deploy to Cloud SQL / production when PHP calls CALL PROC_REPLACE_DEVOTEE_W_SEVA_I(...).
-- Table names use lowercase as in kdms schema dump (Linux case-sensitive MySQL).
-- Source reference: devotee_accomodation, accommodation_availability, devotee_seva, seva_availability.
--
-- Cloud Console / SQL Studio: do not use DELIMITER — it is only understood by the mysql CLI, not the server.
-- Paste and run the entire file in one execution if the editor allows multiple statements.
-- If the editor runs one statement at a time, execute DROP first, then paste from CREATE through the final "END;".
-- From macOS/Linux mysql CLI, use: scripts/mysql_apply_PROC_REPLACE_DEVOTEE_W_SEVA_I.sh (uses DELIMITER correctly).

DROP PROCEDURE IF EXISTS PROC_REPLACE_DEVOTEE_W_SEVA_I;
CREATE DEFINER=`kdms`@`%` PROCEDURE `PROC_REPLACE_DEVOTEE_W_SEVA_I`(
	IN `p_Devotee_Key` VARCHAR(10),
	IN `p_Devotee_Type` VARCHAR(30),
	IN `p_Devotee_First_Name` VARCHAR(50),
	IN `p_Devotee_Last_Name` VARCHAR(50),
	IN `p_Devotee_Gender` VARCHAR(6),
    IN `p_Devotee_DOB` DATE,
	IN `p_Devotee_ID_Type` VARCHAR(10),
	IN `p_Devotee_ID_Number` VARCHAR(50),
	IN `p_Devotee_Address_1` VARCHAR(100),
    IN `p_Devotee_Address_2` VARCHAR(100),
    IN `p_Devotee_Station` VARCHAR(50),
	IN `p_Devotee_State` VARCHAR(25),
    IN `p_Devotee_Zip` VARCHAR(12),
    IN `p_Devotee_Country` VARCHAR(20) ,
    IN `p_Devotee_Email` VARCHAR(40) ,
    IN `p_Devotee_Cell_Phone_Number` VARCHAR(15),
	IN `p_Devotee_Status` VARCHAR(20),
	IN `p_Joined_Since`  VARCHAR(4),
    IN `p_Devotee_Referral` VARCHAR(50),
    IN `p_Devotee_Remarks` VARCHAR(250),
	IN `p_Comments`  VARCHAR(250),
    IN `p_Devotee_Record_Updated_By`  VARCHAR(10),

	IN `p_Devotee_Seva_Id` VARCHAR(6),
	IN `p_Devotee_Seva_Status` VARCHAR(10),
	IN `p_Devotee_Accommodation_ID` VARCHAR(10),
	IN `p_Devotee_Accomodation_Status` VARCHAR(10),

    IN `p_Event_ID` VARCHAR(10)
	)
BEGIN

    DECLARE v_past_accomodation VARCHAR(10);
    DECLARE v_past_accomodation_count INT DEFAULT 0;
    DECLARE v_past_accomodation_count1 INT DEFAULT 0;
    DECLARE v_past_seva VARCHAR(10);
    DECLARE v_past_seva_count INT DEFAULT 0;
    DECLARE DEBUG BOOL DEFAULT FALSE;

-- Upsert Devotee Record
       REPLACE INTO `devotee`(
        Devotee_Key,
        Devotee_Type,
        Devotee_First_Name,
        Devotee_Last_Name,
        Devotee_Gender,
        Devotee_DOB,
        Devotee_ID_Type,
        Devotee_ID_Number,
        Devotee_Address_1,
    	Devotee_Address_2,
        Devotee_Station,
        Devotee_State,
    	Devotee_Zip,
    	Devotee_Country,
        Devotee_Email,
        Devotee_Cell_Phone_Number,
        Devotee_Status,
        Joined_Since,
        Devotee_Referral,
        Devotee_Remarks,
        Comments,
        Devotee_Record_Update_Date_Time,
        Devotee_Record_Updated_By
    )
VALUES(
   		p_Devotee_Key,
        p_Devotee_Type,
        p_Devotee_First_Name,
        p_Devotee_Last_Name,
        p_Devotee_Gender,
        p_Devotee_DOB,
        p_Devotee_ID_Type,
        p_Devotee_ID_Number,
        p_Devotee_Address_1,
    	p_Devotee_Address_2,
        p_Devotee_Station,
        p_Devotee_State,
    	p_Devotee_Zip,
    	p_Devotee_Country,
        p_Devotee_Email,
        p_Devotee_Cell_Phone_Number,
        p_Devotee_Status,
        p_Joined_Since,
        p_Devotee_Referral,
        p_Devotee_Remarks,
        p_Comments,
        NOW(),
        p_Devotee_Record_Updated_By
);

IF DEBUG THEN
	CALL logIt(CONCAT('PROC_REPLACE_DEVOTEE_W_SEVA_I: Devotee record replaced. Devotee ID: ', p_Devotee_Key));
END IF;

-- Add accommodation record, if the accommodation is changed within the same event.

SELECT COUNT(*) INTO v_past_accomodation_count
FROM `devotee_accomodation`
WHERE Devotee_Key = p_Devotee_Key AND
      Accomodation_Status = 'Allocated' AND
      Accommodation_Event = p_Event_ID AND
      Accomodation_Key = p_Devotee_Accommodation_ID;

IF (v_past_accomodation_count = 0) THEN

IF DEBUG THEN
	CALL logIt(CONCAT('PROC_REPLACE_DEVOTEE_W_SEVA_I: No matching past accommodation allocation for same keys. Devotee ID: ', p_Devotee_Key));
END IF;

SELECT `Accomodation_Key` INTO v_past_accomodation FROM `devotee_accomodation` WHERE
        Devotee_Key = p_Devotee_Key AND
        Accomodation_Status = 'Allocated' AND
        Accommodation_Event = p_Event_ID
ORDER BY
    Devotee_Accomodation_Update_Date_Time DESC
    LIMIT 1;

IF ROW_COUNT() > 0 THEN
UPDATE `devotee_accomodation` SET Accomodation_Status = 'Departed', Devotee_Accomodation_Updated_By = p_Devotee_Record_Updated_By, Departure_Date_Time = NOW() WHERE Devotee_Key = p_Devotee_Key AND Accommodation_Event = p_Event_ID AND Accomodation_Key = v_past_accomodation;

UPDATE `accommodation_availability` SET
                                      Allocated_Count = Allocated_Count - 1,
                                      Available_Count = Available_Count + 1
WHERE
        Accomodation_Key = v_past_accomodation AND
        Accommodation_Event = p_Event_ID;
IF DEBUG THEN
	CALL logIt(CONCAT('PROC_REPLACE_DEVOTEE_W_SEVA_I: Accommodation count reduced. Devotee ID: ', p_Devotee_Key));
END IF;
END IF;

SELECT COUNT(*) INTO v_past_accomodation_count1
FROM
    `accommodation_availability`
WHERE
        Accommodation_Event = p_Event_ID AND
        Accomodation_Key = p_Devotee_Accommodation_ID ;

IF (v_past_accomodation_count1 = 0) THEN
    INSERT INTO `accommodation_availability`
    (`Accomodation_Key`,
     `Accommodation_Event`,
     `Allocated_Count`,
     `Reserved_Count`,
     `Out_of_Availability_Count`,
     `Available_Count`,
     `Availability_Update_Date_Time`,
     `Availability_Updated_By`)
    VALUES
        (p_Devotee_Accommodation_ID,
        p_Event_ID,
        0,
        0,
        0,
        0,
        NOW(),
        p_Devotee_Record_Updated_By);
IF DEBUG THEN
	CALL logIt(CONCAT('PROC_REPLACE_DEVOTEE_W_SEVA_I: accommodation_availability seed row added. Devotee ID: ', p_Devotee_Key));
END IF;
END IF;

INSERT INTO `devotee_accomodation`(
    Accomodation_Key,
    Devotee_Key,
    Accommodation_Event,
    Arrival_Date_Time,
    Departure_Date_Time,
    Accomodation_Status,
    Devotee_Accomodation_Update_Date_Time,
    Devotee_Accomodation_Updated_By
)
VALUES(
          p_Devotee_Accommodation_ID,
          p_Devotee_Key,
          p_Event_ID,
          NOW(),
          NULL,
          p_Devotee_Accomodation_Status,
          NOW(),
          p_Devotee_Record_Updated_By
      );

UPDATE `accommodation_availability` SET
                                      Allocated_Count = Allocated_Count + 1,
                                      Available_Count = Available_Count - 1
WHERE
        Accomodation_Key = p_Devotee_Accommodation_ID AND
        Accommodation_Event = p_Event_ID;

END IF;

SELECT COUNT(*) INTO v_past_seva_count FROM `devotee_seva` WHERE
        Devotee_Key = p_Devotee_Key AND
        Seva_Status = 'Assigned' AND
        Seva_Event =  p_Event_ID AND
        Seva_ID = p_Devotee_Seva_Id;

IF (v_past_seva_count = 0) THEN

IF DEBUG THEN
	CALL logIt(CONCAT('PROC_REPLACE_DEVOTEE_W_SEVA_I: No past seva row for current event/seva. Devotee ID: ', p_Devotee_Key));
END IF;

SELECT Seva_ID INTO v_past_seva FROM `devotee_seva` WHERE
        Devotee_Key = p_Devotee_Key AND
        Seva_Status = 'Assigned' AND
        Seva_Event = p_Event_ID
ORDER BY
    Devotee_Seva_Update_Date_Time DESC
    LIMIT 1;

UPDATE `devotee_seva` SET Seva_Status = 'Released', Devotee_Seva_Updated_By = p_Devotee_Record_Updated_By, Release_Date_Time = NOW() WHERE Devotee_Key = p_Devotee_Key;

UPDATE `seva_availability` SET
    Assigned_Count = Assigned_Count - 1
WHERE
        Seva_Id = v_past_seva AND Seva_Event = p_Event_ID;

IF DEBUG THEN
	CALL logIt(CONCAT('PROC_REPLACE_DEVOTEE_W_SEVA_I: Released past seva. Devotee ID: ', p_Devotee_Key, ' Seva ID: ', v_past_seva));
END IF;

INSERT INTO `devotee_seva`(
    `Seva_ID`,
    `Devotee_Key`,
    `Seva_Event`,
    `Assignment_Date_Time`,
    `Release_Date_Time`,
    `Seva_Status`,
    `Devotee_Seva_Update_Date_Time`,
    `Devotee_Seva_Updated_By`)
VALUES (
           p_Devotee_Seva_Id,
           p_Devotee_Key,
           p_Event_ID,
           NOW(),
           NULL,
           p_Devotee_Seva_Status,
           NOW(),
           p_Devotee_Record_Updated_By
       );

UPDATE `seva_availability`
SET
    `Assigned_Count` = `Assigned_Count` + 1,
    `Availability_Update_Date_Time` = NOW(),
    `Availability_Updated_By` = p_Devotee_Record_Updated_By
WHERE
        Seva_Id = p_Devotee_Seva_Id AND Seva_Event = p_Event_ID;

END IF;

END;

