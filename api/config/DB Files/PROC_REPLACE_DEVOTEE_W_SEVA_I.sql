-- Please use the procedures.sql file
DELIMITER $$
CREATE DEFINER=`kdms`@`%` PROCEDURE `PROC_REPLACE_DEVOTEE_W_SEVA_I`(
	IN `p_Devotee_Key` VARCHAR(10),
	IN `p_Devotee_Type` VARCHAR(30),
	IN `p_Devotee_First_Name` VARCHAR(50),
	IN `p_Devotee_Last_Name` VARCHAR(50),
	IN `p_Devotee_Gender` VARCHAR(6),
	IN `p_Devotee_ID_Type` VARCHAR(10),
	IN `p_Devotee_ID_Number` VARCHAR(50),
	IN `p_Devotee_Station` VARCHAR(50),
	IN `p_Devotee_Cell_Phone_Number` VARCHAR(15),
	IN `p_Devotee_Status` VARCHAR(20),
	IN `p_Devotee_Remarks` VARCHAR(250),
	IN `p_Devotee_Referral` VARCHAR(100),
	IN `p_Devotee_Seva_Id` VARCHAR(6),
	IN `p_Devotee_Seva_Status` VARCHAR(10),
	IN `p_Devotee_Record_Updated_By` VARCHAR(10),
	IN `p_Devotee_Accommodation_ID` VARCHAR(10),
	IN `p_Devotee_Accomodation_Status` VARCHAR(10),
	IN `p_Devotee_Address_1` VARCHAR(100),
    IN `p_Devotee_Address_2` VARCHAR(100),
    IN `p_Devotee_State` VARCHAR(25),
    IN `p_Devotee_Zip` VARCHAR(12),
    IN `p_Devotee_Country` VARCHAR(20) ,
    IN `p_Comments`  VARCHAR(250),
    IN `p_Joined_Since`  VARCHAR(4),
    IN `p_Event_ID` VARCHAR(10)
	)
BEGIN

    DECLARE v_past_accomodation varchar(10);
    DECLARE v_past_accomodation_Count varchar(10);
    DECLARE v_past_accomodation_Count1 int;
    DECLARE v_past_demographics_count int;
    DECLARE v_past_seva varchar(10);
	DECLARE v_past_seva_count varchar(10);
    DECLARE DEBUG bool DEFAULT false;
-- Change not needed to be commited
-- Upsert Devotee Record
       REPLACE INTO devotee(
        Devotee_Key,
        Devotee_Type,
        Devotee_First_Name,
        Devotee_Last_Name,
        Devotee_Gender,
        Devotee_ID_Type,
        Devotee_ID_Number,
        Devotee_Station,
        Devotee_Cell_Phone_Number,
        Devotee_Status,
        Devotee_Remarks,
        Devotee_Referral,
        Devotee_Record_Update_Date_Time,
        Devotee_Record_Updated_By,
    	-- Devotee_Address_1,
    	-- Devotee_Address_2,
    	-- Devotee_State,
    	-- Devotee_Zip,
    	-- Devotee_Country,
    	Comments,
    	Joined_Since
    )
VALUES(
    p_Devotee_Key,
    p_Devotee_Type,
    p_Devotee_First_Name,
    p_Devotee_Last_Name,
    p_Devotee_Gender,
    p_Devotee_ID_Type,
    p_Devotee_ID_Number,
    p_Devotee_Station,
    p_Devotee_Cell_Phone_Number,
    p_Devotee_Status,
    p_Devotee_Remarks,
    p_Devotee_Referral,
    NOW(),
    p_Devotee_Record_Updated_By,
	-- p_Devotee_Address_1,
    -- p_Devotee_Address_2,
    -- p_Devotee_State,
    -- p_Devotee_Zip,
    -- p_Devotee_Country,
    p_Comments,
    p_Joined_Since
);

-- Log Entry
IF DEBUG = true THEN
		CALL logIt(concat('PROC_REPLACE_DEVOTEE_W_SEVA_I: Devotee record replaced. Devotee ID: ', p_Devotee_Key));
END IF;

-- Demographics table Update - simply replace the record
IF  (p_Devotee_Address_1 <> "" OR p_Devotee_State <> "" OR p_Devotee_Country <> "") THEN
	-- SELECT count(*) INTO v_past_demographics_count  FROM Devotee_Demographics WHERE devotee_key = p_Devotee_Key AND Devotee_Demographics_Status = 'Current';

    /* IF (v_past_demographics_count > 0) THEN

		IF DEBUG = true THEN
				CALL logIt(concat('PROC_REPLACE_DEVOTEE_W_SEVA_I: Past address Record Found. Devotee ID: ', p_Devotee_Key ), ' counts: ' , v_past_demographics_count);
		END IF;

		 UPDATE devotee_demographics SET Devotee_Demographics_Status = 'Past', Devotee_Record_Updated_By = p_Devotee_Record_Updated_By, Devotee_Record_Update_Date_Time =  NOW()
         WHERE Devotee_Key = p_Devotee_Key AND Devotee_Demographics_Status = 'Current';
        */

        REPLACE INTO `devotee_demographics`
			(`Devotee_Key`,
				`Devotee_Address_1`,
				`Devotee_Address_2`,
				`Devotee_State`,
				`Devotee_Zip`,
				`Devotee_Country`,
				`Devotee_Address_Status`,
				`Devotee_Email`,
				`Devotee_Demographics_Status`,
				`Devotee_Record_Updated_By`,
				`Devotee_Record_Update_Date_Time`)
				VALUES
				(

                p_Devotee_Key,
                p_Devotee_Address_1,
				p_Devotee_Address_2,
				p_Devotee_State,
				p_Devotee_Zip,
				p_Devotee_Country,
				'Current',
				'',
				'',
				p_Devotee_Record_Updated_By,
				NOW()
                );
	-- END IF;
END IF;
--
-- Add accommodation record, if the accommodation is changed within the same event.
-- If accommodation with in the current event is same as old accommodation
-- (meaning accommodation was not changed, other devotee information was updated), skip changing accommodation info

-- Count the accommodations allocated in the same event for the same devotee
SELECT count(*) INTO v_past_accomodation_count  FROM Devotee_Accomodation WHERE
        Devotee_Key = p_Devotee_Key AND
        Accomodation_Status = 'Allocated' AND
        Accommodation_Event = p_Event_ID AND
        Accomodation_key = p_Devotee_Accommodation_ID;

-- If there are no record, meaning, the accommodation allocation is either new or is changing
IF (v_past_accomodation_Count = 0) THEN
-- Log entry
IF DEBUG = true THEN
		CALL logIt(concat('PROC_REPLACE_DEVOTEE_W_SEVA_I: No Past Accommodation Record Found for the current event. Devotee ID: ', p_Devotee_Key, ' accommocation ID: ' , p_Devotee_Accommodation_ID, ' event ID: ', p_Event_ID));
END IF;

-- Find out if its chaning with in the same event for the devotee
SELECT accomodation_key INTO v_past_accomodation  FROM Devotee_Accomodation WHERE
        Devotee_Key = p_Devotee_Key AND
        Accomodation_Status = 'Allocated' AND
        Accommodation_Event = p_Event_ID
ORDER BY
    Devotee_Accomodation_Update_Date_Time DESC
    LIMIT 1;

-- If it is changing (at least one record found), de-allocate it and increase accommodation's availability (because devotee has departed and one more space available now)
IF (SELECT ROW_COUNT() > 0) THEN
UPDATE Devotee_Accomodation SET Accomodation_Status = 'Departed' ,  Devotee_Accomodation_Updated_By = p_Devotee_Record_Updated_By, Departure_date_time = NOW() WHERE Devotee_Key = p_Devotee_Key AND Accommodation_Event = p_Event_ID AND Accomodation_Key = v_past_accomodation;

UPDATE Accommodation_Availability SET
                                      Allocated_Count = Allocated_Count - 1,
                                      Available_Count = Available_Count + 1
WHERE
        Accomodation_Key = v_past_accomodation AND
        Accommodation_Event = p_Event_ID;
-- Long entry
IF DEBUG = true THEN
		CALL logIt(concat('PROC_REPLACE_DEVOTEE_W_SEVA_I: Accommodation Count reduced. Devotee ID: ', p_Devotee_Key, ' accommocation ID: ' , p_Devotee_Accommodation_ID, ' event ID: ', p_Event_ID));
END IF;
END IF;

-- IN case of new allocation, simply insert the record in devotee accommodation table. But first check if that's going ot work because
-- There is a possibility that the accommodation availability record didn't even exist and therefore availability is not reduced.
-- Check if accommodation availability record exists:

SELECT COUNT(*) INTO v_past_accomodation_count1
FROM
    accommodation_availability
WHERE
        Accommodation_Event = p_Event_ID AND
        Accomodation_key = p_Devotee_Accommodation_ID ;

-- If accommodation record not found in accommodation availability table, insert the record first
IF (v_past_accomodation_Count1 = 0) THEN
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
-- Log entry
IF DEBUG = true THEN
		CALL logIt(concat('PROC_REPLACE_DEVOTEE_W_SEVA_I: Accommodation availability record added and allocation increased. Devotee ID: ', p_Devotee_Key, ' accommocation ID: ' , p_Devotee_Accommodation_ID, ' event ID: ', p_Event_ID));
END IF;
END IF;

-- If new record, simply allocate it (insert into devotee accommodation table) and reduce the accommodation availability for the event by one
INSERT INTO Devotee_Accomodation(
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

UPDATE Accommodation_Availability SET
                                      Allocated_Count = Allocated_Count + 1,
                                      Available_Count = Available_Count - 1
WHERE
        Accomodation_Key = p_Devotee_Accommodation_ID AND
        Accommodation_Event = p_Event_ID;

END IF;


SELECT count(*) INTO v_past_seva_count  FROM Devotee_Seva WHERE
        Devotee_Key = p_Devotee_Key AND
        Seva_Status = 'Assigned' AND
        Seva_Event =  p_Event_ID AND
        Seva_Id = p_Devotee_Seva_Id;

IF (v_past_seva_Count = 0) THEN

IF DEBUG = true THEN
		CALL logIt(concat('PROC_REPLACE_DEVOTEE_W_SEVA_I: No past seva found for the current event/seva. Devotee ID: ', p_Devotee_Key, ' Seva ID: ' , p_Devotee_Seva_Id, ' event ID: ', p_Event_ID));
END IF;


SELECT seva_id INTO v_past_seva  FROM Devotee_Seva WHERE
        Devotee_Key = p_Devotee_Key AND
        Seva_Status = 'Assigned' AND
        Seva_Event = p_Event_ID
ORDER BY
    Devotee_Seva_Update_Date_Time DESC
    LIMIT 1;

UPDATE Devotee_Seva SET Seva_Status = 'Released' ,  Devotee_Seva_Updated_By = p_Devotee_Record_Updated_By, Release_Date_Time = NOW() WHERE Devotee_Key = p_Devotee_Key;


UPDATE `Seva_Availability` SET
    Assigned_Count = Assigned_Count - 1
WHERE
        `seva_id` = v_past_seva AND Seva_Event = p_Event_ID;

IF DEBUG = true THEN
		CALL logIt(concat('PROC_REPLACE_DEVOTEE_W_SEVA_I: Released from past seva from the current event and reduced its assigned count. Devotee ID: ', p_Devotee_Key, 'Seva ID: ', v_past_seva, ' event ID: ', p_Event_ID));
END IF;

INSERT INTO `Devotee_Seva`(
    `Seva_ID`,
    `Devotee_Key`,
    `Seva_event`,
    `Assignment_Date_Time`,
    `Release_Date_Time`,
    `Seva_Status`,
    `Devotee_Seva_Update_Date_Time`,
    `Devotee_Seva_Updated_By`)
VALUES (
           p_Devotee_Seva_Id,
           p_Devotee_Key,
           p_Event_id,
           NOW(),
           NULL,
           p_Devotee_Seva_Status,
           NOW(),
           p_Devotee_Record_Updated_By
       );

UPDATE `Seva_Availability`
SET
    `Assigned_Count`= `Assigned_Count` + 1,
    `Availability_Update_Date_Time`= NOW(),
    `Availability_Updated_By`= p_Devotee_Record_Updated_By
WHERE
        Seva_Id = p_Devotee_Seva_ID AND Seva_Event = p_Event_ID;

-- BUG REPORT- code for adding seva availability is not working. Copy the logic from accommodation availability section and refactor that for seva
-- commenting the following block since it breaks the devotee record addition
/* IF (SELECT ROW_COUNT() > 0) THEN
    INSERT INTO `seva_availability`
    (`Seva_Id`,
     `Seva_Event`,
     `Assigned_Count`,
     `Availability_Update_Date_Time`,
     `Availability_Updated_By`)
    VALUES
        (p_Devotee_Seva_ID,
        p_Event_ID,
        1,
        NOW(),
        p_Devotee_Record_Updated_By);

END IF;
*/
END IF;

END$$
DELIMITER ;
