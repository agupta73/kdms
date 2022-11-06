-- ///////////////////
-- // Proc_insert_devotee
-- //////////////////
DELIMITER $
$
CREATE DEFINER=`root`@`localhost` PROCEDURE `PROC_INSERT_DEVOTEE`
(IN `p_Devotee_Key` VARCHAR
(10), IN `p_Devotee_Type` VARCHAR
(30), IN `p_Devotee_First_Name` VARCHAR
(50), IN `p_Devotee_Last_Name` VARCHAR
(50), IN `p_Devotee_Gender` VARCHAR
(6), IN `p_Devotee_ID_Type` VARCHAR
(10), IN `p_Devotee_ID_Number` VARCHAR
(50), IN `p_Devotee_Station` VARCHAR
(50), IN `p_Devotee_Cell_Phone_Number` VARCHAR
(15), IN `p_Devotee_Status` VARCHAR
(20), IN `p_Devotee_Remarks` VARCHAR
(250), IN `p_Devotee_Record_Updated_By` VARCHAR
(10), IN `p_Devotee_Accommodation_ID` VARCHAR
(10), IN `p_Devotee_Accomodation_Status` VARCHAR
(10))
BEGIN

    INSERT INTO devotee
        (
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
        Devotee_Record_Update_Date_Time,
        Devotee_Record_Updated_By
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
            NOW(),
            p_Devotee_Record_Updated_By
);

    INSERT INTO Devotee_Accomodation
        (
        Accomodation_Key,
        Devotee_Key,
        Accomodation_Year,
        Arrival_Date_Time,
        Departure_Date_Time,
        Accomodation_Status,
        Devotee_Accomodation_Update_Date_Time,
        Devotee_Accomodation_Updated_By
        )
    VALUES(
            p_Devotee_Accommodation_ID,
            p_Devotee_Key,
            YEAR(NOW()),
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
	Accomodation_Key = p_Devotee_Accommodation_ID;

    END$$
DELIMITER
;

-- ///////////////
-- // Proc_Manage_Amenity
-- ///////////////
DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `PROC_MANAGE_AMENITY`
(
    IN `p_Devotee_Key` VARCHAR
(10),
    IN `p_Amenity_Key` VARCHAR
(10),
    IN `p_Amenity_Quantity` INT,
    IN `p_Amenity_Managed_By` VARCHAR
(10)
)
BEGIN
    DECLARE
        v_past_amenity_quantity INT
;
DECLARE v_amenity_action VARCHAR
(20) ;


SELECT
    IFNULL(SUM(Amenity_Quantity),0)
INTO v_past_amenity_quantity
FROM
    Devotee_Amenities_Allocation
WHERE
    Devotee_Key = p_Devotee_Key AND Amenity_Key = p_Amenity_Key AND Amenity_Allocation_Status = 'Allocated' AND Amenity_Allocation_Year = YEAR(NOW())
;
REPLACE
INTO
	`Devotee_Amenities_Allocation`
(
    `Amenity_Key`,
    `Devotee_Key`,
    `Amenity_Quantity`,
    `Amenity_Allocation_Year`,
    `Amenity_Allocation_Status`,
    `Amenity_Allocation_Date_Time`,
    `Amenity_Allocated_By`
)
VALUES
(
    p_Amenity_Key,
    p_Devotee_Key,
    v_past_amenity_quantity + p_Amenity_Quantity,
    YEAR
(NOW()), 'Allocated', NOW(), p_Amenity_Managed_By);

UPDATE
    Amenities_Availability
SET
    Allocated_Count = Allocated_Count + p_Amenity_Quantity,
    Available_Count = Available_Count - p_Amenity_Quantity
WHERE
    Amenity_Key = p_Amenity_Key
;


IF p_Amenity_Quantity > 0 THEN
SET
    v_amenity_action
= 'Allocated' ; ELSE
SET
    v_amenity_action
= 'Returned' ;
END
IF ;
    
INSERT INTO `Amenities_Allocation_Log`(
    `
Amenity_Key`,
`Devotee_Key
`,
    `Amenity_Quantity`,
    `Amenity_Action`,
    `Amenity_Action_Year`,
    `Amenity_Action_By`,
    `Amenity_Action_Date_Time`
)
VALUES
(
    p_Amenity_Key,
    p_Devotee_Key,
    p_Amenity_Quantity,
    v_amenity_action,
    YEAR
(NOW()),
    p_Amenity_Managed_By,
    NOW()) ;
END$$
DELIMITER ;
-- ///////////////////
-- // Proc_refresh_acco_count
-- //////////////////
DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `PROC_REFRESH_ACCO_COUNT`
()
BEGIN

    DECLARE v_finished INTEGER DEFAULT 0
;
DECLARE v_accommodation_key VARCHAR
(10) DEFAULT "" ;
DECLARE v_accommodation_count INTEGER DEFAULT 0
;
DECLARE v_accommodation_capacity INTEGER DEFAULT 0 ;

DECLARE csr_accomodation CURSOR FOR
      SELECT
    am.accomodation_key,
    COUNT(da.Accomodation_Key),
    am.accomodation_capacity
FROM
    Accommodation_Master am
    LEFT OUTER JOIN Devotee_Accomodation da ON
    am.Accomodation_Key = da.Accomodation_Key AND da.Accomodation_Year = YEAR(NOW()) AND da.Accomodation_Status = 'Allocated'
GROUP BY
    am.Accomodation_Key;

DECLARE
CONTINUE
HANDLER FOR NOT FOUND
SET v_finished
= 1 ;

OPEN csr_accomodation ;

WHILE v_finished = 0 DO

FETCH csr_accomodation
INTO v_accommodation_key, v_accommodation_count, v_accommodation_capacity ;

IF v_finished = 0 THEN
UPDATE
                    Accommodation_Availability
                SET
                    allocated_count = v_accommodation_count,
                    available_count = v_accommodation_capacity - (reserved_count + out_of_availability_count + v_accommodation_count)
                WHERE
                    accomodation_key = v_accommodation_key
;
END
IF ;
        	
		END
WHILE ;
    
    CLOSE csr_accomodation ;
END$$
DELIMITER ;
-- ////////////////
-- // Proc_refresh_amenity_Count
-- ////////////////
DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `PROC_REFRESH_AMENITY_COUNT`
()
BEGIN

    DECLARE v_finished INTEGER DEFAULT 0
;
DECLARE v_amenity_key VARCHAR
(10) DEFAULT "" ;
DECLARE v_amenity_count INTEGER DEFAULT 0
;
DECLARE v_amenity_quantity INTEGER DEFAULT 0 ;

DECLARE csr_amenity CURSOR FOR
      SELECT
    am.amenity_key,
    IFNULL(SUM(da.Amenity_Quantity), 0),
    am.amenity_quantity
FROM
    Amenity_Master am
    LEFT OUTER JOIN Devotee_Amenities_Allocation da ON
    am.Amenity_Key = da.Amenity_Key AND da.Amenity_Allocation_Year = YEAR(NOW()) AND da.Amenity_Allocation_Status = 'Allocated'
GROUP BY
    am.Amenity_Key;

DECLARE
CONTINUE
HANDLER FOR NOT FOUND
SET v_finished
= 1 ;

OPEN csr_amenity ;

WHILE v_finished = 0 DO

FETCH csr_amenity
INTO v_amenity_key, v_amenity_count, v_amenity_quantity ;

IF v_finished = 0 THEN
UPDATE
                    Amenities_Availability
                SET
                    allocated_count = v_amenity_count,
                    available_count = v_amenity_quantity - (reserved_count + out_of_availability_count + v_amenity_count)
                WHERE
                    amenity_key = v_amenity_key
;
END
IF ;
        	
		END
WHILE ;
    
    CLOSE csr_amenity ;
END$$
DELIMITER ;
-- //////////////////
-- // Proc_replace_devotee
-- /////////////////
DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `PROC_REPLACE_DEVOTEE`
(IN `p_Devotee_Key` VARCHAR
(10), IN `p_Devotee_Type` VARCHAR
(30), IN `p_Devotee_First_Name` VARCHAR
(50), IN `p_Devotee_Last_Name` VARCHAR
(50), IN `p_Devotee_Gender` VARCHAR
(6), IN `p_Devotee_ID_Type` VARCHAR
(10), IN `p_Devotee_ID_Number` VARCHAR
(50), IN `p_Devotee_Station` VARCHAR
(50), IN `p_Devotee_Cell_Phone_Number` VARCHAR
(15), IN `p_Devotee_Status` VARCHAR
(20), IN `p_Devotee_Remarks` VARCHAR
(250), IN `p_Devotee_Record_Updated_By` VARCHAR
(10), IN `p_Devotee_Accommodation_ID` VARCHAR
(10), IN `p_Devotee_Accomodation_Status` VARCHAR
(10))
BEGIN

    DECLARE v_past_accomodation varchar
    (10);
DECLARE v_past_accomodation_Count varchar
(10);    

       REPLACE INTO devotee
(
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
        Devotee_Record_Update_Date_Time,
        Devotee_Record_Updated_By
    )
VALUES
(
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
    NOW(),
    p_Devotee_Record_Updated_By
);


SELECT count(*)
INTO v_past_accomodation_count
FROM Devotee_Accomodation
WHERE
Devotee_Key = p_Devotee_Key AND
    Accomodation_Status = 'Allocated' AND
    Accomodation_Year = YEAR(NOW()) AND
    Accomodation_key = p_Devotee_Accommodation_ID;

IF (v_past_accomodation_Count = 0) THEN

SELECT accomodation_key
INTO v_past_accomodation
FROM Devotee_Accomodation
WHERE
Devotee_Key = p_Devotee_Key AND
    Accomodation_Status = 'Allocated' AND
    Accomodation_Year = YEAR(NOW())
ORDER BY 
Devotee_Accomodation_Update_Date_Time DESC
LIMIT 1;

UPDATE Devotee_Accomodation
SET Accomodation_Status
= 'Departed' ,  Devotee_Accomodation_Updated_By = p_Devotee_Record_Updated_By, Departure_date_time = NOW() WHERE Devotee_Key = p_Devotee_Key;

UPDATE Accommodation_Availability SET
	Allocated_Count = Allocated_Count - 1,
    Available_Count = Available_Count + 1
WHERE	
	Accomodation_Key = v_past_accomodation;

INSERT INTO Devotee_Accomodation
    (
    Accomodation_Key,
    Devotee_Key,
    Accomodation_Year,
    Arrival_Date_Time,
    Departure_Date_Time,
    Accomodation_Status,
    Devotee_Accomodation_Update_Date_Time,
    Devotee_Accomodation_Updated_By
    )
VALUES(
        p_Devotee_Accommodation_ID,
        p_Devotee_Key,
        YEAR(NOW()),
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
	Accomodation_Key = p_Devotee_Accommodation_ID;
END
IF;


END$$
DELIMITER ;
-- ////////////////////
-- // Proc_update_devotee
-- ///////////////////
DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `PROC_UPDATE_DEVOTEE`
(IN `p_Devotee_Key` VARCHAR
(10), IN `p_Devotee_Type` VARCHAR
(30), IN `p_Devotee_First_Name` VARCHAR
(50), IN `p_Devotee_Last_Name` VARCHAR
(50), IN `p_Devotee_Gender` VARCHAR
(6), IN `p_Devotee_ID_Type` VARCHAR
(10), IN `p_Devotee_ID_Number` VARCHAR
(50), IN `p_Devotee_Station` VARCHAR
(50), IN `p_Devotee_Cell_Phone_Number` VARCHAR
(15), IN `p_Devotee_Status` VARCHAR
(20), IN `p_Devotee_Remarks` VARCHAR
(250), IN `p_Devotee_Record_Updated_By` VARCHAR
(10), IN `p_Devotee_Accommodation_ID` VARCHAR
(10), IN `p_Devotee_Accomodation_Status` VARCHAR
(10))
BEGIN

    DECLARE v_past_accomodation varchar
    (10);
DECLARE v_past_accomodation_Count varchar
(10);

UPDATE devotee SET
        Devotee_Type = p_Devotee_Type,
        Devotee_First_Name = p_Devotee_First_Name,
        Devotee_Last_Name = p_Devotee_Last_Name,
        Devotee_Gender = p_Devotee_Gender,
        Devotee_ID_Type = p_Devotee_ID_Type,
        Devotee_ID_Number = p_Devotee_ID_Number,
        Devotee_Station = p_Devotee_Station,
        Devotee_Cell_Phone_Number = p_Devotee_Cell_Phone_Number,
        Devotee_Status = p_Devotee_Status,
        Devotee_Remarks = p_Devotee_Remarks,
        Devotee_Record_Update_Date_Time = NOW(),
        Devotee_Record_Updated_By = p_Devotee_Record_Updated_By
     WHERE Devotee_Key = p_Devotee_Key;

SELECT count(*)
INTO v_past_accomodation_count
FROM Devotee_Accomodation
WHERE
Devotee_Key = p_Devotee_Key AND
    Accomodation_Status = 'Allocated' AND
    Accomodation_Year = YEAR(NOW()) AND
    Accomodation_key = p_Devotee_Accommodation_ID;

IF (v_past_accomodation_Count = 0) THEN

SELECT accomodation_key
INTO v_past_accomodation
FROM Devotee_Accomodation
WHERE
Devotee_Key = p_Devotee_Key AND
    Accomodation_Status = 'Allocated' AND
    Accomodation_Year = YEAR(NOW())
ORDER BY 
Devotee_Accomodation_Update_Date_Time DESC
LIMIT 1;

UPDATE Devotee_Accomodation
SET Accomodation_Status
= 'Departed' ,  Devotee_Accomodation_Updated_By = p_Devotee_Record_Updated_By, Departure_date_time = NOW() WHERE Devotee_Key = p_Devotee_Key;

UPDATE Accommodation_Availability SET
	Allocated_Count = Allocated_Count - 1,
    Available_Count = Available_Count + 1
WHERE	
	Accomodation_Key = v_past_accomodation;

INSERT INTO Devotee_Accomodation
    (
    Accomodation_Key,
    Devotee_Key,
    Accomodation_Year,
    Arrival_Date_Time,
    Departure_Date_Time,
    Accomodation_Status,
    Devotee_Accomodation_Update_Date_Time,
    Devotee_Accomodation_Updated_By
    )
VALUES(
        p_Devotee_Accommodation_ID,
        p_Devotee_Key,
        YEAR(NOW()),
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
	Accomodation_Key = p_Devotee_Accommodation_ID;
END
IF;


END$$
DELIMITER ;
-- /////////////////
-- // Proc_upsert_acco
-- /////////////////
DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `PROC_UPSERT_ACCO`
(IN `p_Accomodation_Key` VARCHAR
(5), IN `p_Accomodation_Name` VARCHAR
(100), IN `p_Accomodation_Capacity` INT
(11), IN `p_Reserved_Count` INT
(11), IN `p_Out_of_Availability_Count` INT
(11), IN `p_Accomodation_Updated_By` VARCHAR
(10))
BEGIN
    REPLACE
INTO `Accommodation_Master`
(
    `Accomodation_Key`,
    `Accomodation_Name`,
    `Accomodation_Capacity`,
    `Accomodation_Update_Date_Time`,
    `Accomodation_Updated_By`
)
VALUES
(
    p_Accomodation_Key,
    p_Accomodation_Name,
    p_Accomodation_Capacity,
    NOW(), p_Accomodation_Updated_By) ;
    
REPLACE
INTO `Accommodation_Availability`
(
    `Accomodation_Key`,
    `Reserved_Count`,
    `Out_of_Availability_Count`,
    `Availability_Update_Date_Time`,
    `Availability_Updated_By`
)
VALUES
(
    p_Accomodation_Key,
    p_Reserved_Count,
    p_Out_of_Availability_Count,
    NOW(),
    p_Accomodation_Updated_By
    );

CALL PROC_REFRESH_ACCO_COUNT
();

END$$
-- DELIMITER ;
-- ///////////////////
-- // Proc_upsert_Amenity
-- /////////////////////
DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `PROC_UPSERT_AMENITY`
(IN `p_Amenity_Key` VARCHAR
(5), IN `p_Amenity_Name` VARCHAR
(100), IN `p_Amenity_Status` VARCHAR
(20), IN `p_Amenity_Quantity` INT
(11), IN `p_Reserved_Count` INT
(11), IN `p_Out_of_Availability_Count` INT
(11), IN `p_Amenity_Updated_By` VARCHAR
(10))
BEGIN
    REPLACE
INTO `Amenity_Master`
(
    `Amenity_Key`,
    `Amenity_Name`,
    `Amenity_Status`,
    `Amenity_Quantity`,
    `Amenity_Record_Update_Date_Time`,
    `Amenity_Record_Upated_by`
)
VALUES
(
    p_Amenity_Key,
    p_Amenity_Name,
    p_Amenity_Status,
    p_Amenity_Quantity,
    NOW(), p_Amenity_Updated_By) ;
    
REPLACE
INTO `Amenities_Availability`
(
    `Amenity_Key`,
    `Reserved_Count`,
    `Out_of_Availability_Count`,
    `Availability_Update_Date_Time`,
    `Availability_Updated_By`
)
VALUES
(
    p_Amenity_Key,
    p_Reserved_Count,
    p_Out_of_Availability_Count,
    NOW(), p_Amenity_Updated_By) ;
CALL
    PROC_REFRESH_AMENITY_COUNT
() ;
    END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`kdms`@`%` PROCEDURE `PROC_UPSERT_EVENT`(IN `p_Event_Id` VARCHAR(10), IN `p_Event_Description` VARCHAR(50), IN `p_Event_Status` VARCHAR(10))
BEGIN
    REPLACE
INTO `Event_Master`(
    `Event_ID`,
    `Event_Description`,
    `Event_Status`,
    `Event_Updated_On_Date_Time`,
    `Event_Update_by`
)
VALUES(
    p_Event_Id,
    p_Event_Description,
    p_Event_Status,
    NOW(), 'Anil') ;



    END$$
DELIMITER ;

    -- DELIMITER ;
-- //////////////////////////////////////
-- // Proc_upsert_Acco_W_Event
-- ////////////////////////////////////////
drop procedure `PROC_UPSERT_ACCO_W_EVENT`;
DELIMITER $$
CREATE DEFINER=`kdms`@`%` PROCEDURE `PROC_UPSERT_ACCO_W_EVENT`(
IN `p_Accomodation_Key` VARCHAR(5),
IN `p_Accommodation_Event` VARCHAR(10),
IN `p_Accomodation_Name` VARCHAR(100),
IN `p_Accomodation_Capacity` INT(11),
IN `p_Reserved_Count` INT(11),
IN `p_Out_of_Availability_Count` INT(11),
IN `p_Accomodation_Updated_By` VARCHAR(10)
)
BEGIN
    REPLACE
INTO `Accommodation_Master`
(
    `Accomodation_Key`,
    `Accomodation_Name`,
    `Accomodation_Capacity`,
    `Accomodation_Update_Date_Time`,
    `Accomodation_Updated_By`
)
VALUES
(
    p_Accomodation_Key,
    p_Accomodation_Name,
    p_Accomodation_Capacity,
    NOW(), p_Accomodation_Updated_By) ;

REPLACE
INTO `Accommodation_Availability`
(
    `Accomodation_Key`,
    `Accommodation_Event`,
    `Reserved_Count`,
    `Out_of_Availability_Count`,
    `Availability_Update_Date_Time`,
    `Availability_Updated_By`
)
VALUES
(
    p_Accomodation_Key,
    p_Accommodation_Event,
    p_Reserved_Count,
    p_Out_of_Availability_Count,
    NOW(),
    p_Accomodation_Updated_By
    )
    ;

CALL PROC_REFRESH_ACCO_COUNT_W_EVENT(p_Accommodation_Event);

END$$
DELIMITER ;


-- //////////////////////////////////////
-- // PROC_REFRESH_ACCO_COUNT_W_EVENT
-- ////////////////////////////////////////
drop procedure `PROC_REFRESH_ACCO_COUNT_W_EVENT`;

DELIMITER $$
CREATE DEFINER=`kdms`@`%` PROCEDURE `PROC_REFRESH_ACCO_COUNT_W_EVENT`(
IN `p_accommodation_event` VARCHAR(10))
BEGIN

    DECLARE v_finished INTEGER DEFAULT 0;
	DECLARE v_accommodation_key VARCHAR(10) DEFAULT "" ;
    DECLARE v_accommodation_count INTEGER DEFAULT 0;
	DECLARE v_accommodation_capacity INTEGER DEFAULT 0 ;

	DECLARE DEBUG bool DEFAULT false;

DECLARE csr_accomodation CURSOR FOR
SELECT
    am.accomodation_key,
    COUNT(da.Accomodation_Key),
    am.accomodation_capacity
FROM
    Accommodation_Master am
        LEFT OUTER JOIN Devotee_Accomodation da ON
                am.Accomodation_Key = da.Accomodation_Key AND da.Accommodation_Event = p_accommodation_event AND da.Accomodation_Status = 'Allocated'
GROUP BY
    am.Accomodation_Key;

DECLARE
CONTINUE
HANDLER FOR NOT FOUND
SET v_finished = 1 ;

OPEN csr_accomodation ;

WHILE v_finished = 0 DO

FETCH csr_accomodation
INTO v_accommodation_key, v_accommodation_count, v_accommodation_capacity ;

IF v_finished = 0 THEN
UPDATE
    Accommodation_Availability
SET
    allocated_count = v_accommodation_count,
    available_count = v_accommodation_capacity - (reserved_count + out_of_availability_count + v_accommodation_count)

WHERE
        accomodation_key = v_accommodation_key AND
        Accommodation_Event = p_accommodation_event
;

IF DEBUG = true THEN
		CALL logIt(concat('PROC_REFRESH_ACCO_COUNT_W_EVENT: v_accommodation_capacity is: ', v_accommodation_capacity, ' and v_accommodation_event is: ', p_accommodation_event));
END IF;
END IF ;

END WHILE ;

CLOSE csr_accomodation ;
END$$
DELIMITER ;



-- //////////////////////////////////////
-- // logIt
-- ////////////////////////////////////////
DELIMITER $$
CREATE DEFINER=`kdms`@`%` PROCEDURE `logIt`(
IN `p_msg` VARCHAR(1024) )
BEGIN
insert into sp_logs select 0, `p_msg`;
END$$
DELIMITER ;

-- //////////////////////////////////////
-- // PROC_REPLACE_DEVOTEE_W_SEVA_I
-- ////////////////////////////////////////
DROP procedure PROC_REPLACE_DEVOTEE_W_SEVA_I;
DELIMITER $$
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

    DECLARE v_past_accomodation varchar(10);
    DECLARE v_past_accomodation_Count varchar(10);
    DECLARE v_past_accomodation_Count1 int;
    DECLARE v_past_demographics_count int;
    DECLARE v_past_seva varchar(10);
	DECLARE v_past_seva_count varchar(10);
    DECLARE DEBUG bool DEFAULT false;

-- Upsert Devotee Record
       REPLACE INTO devotee(
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

-- Log Entry
IF DEBUG = true THEN
		CALL logIt(concat('PROC_REPLACE_DEVOTEE_W_SEVA_I: Devotee record replaced. Devotee ID: ', p_Devotee_Key));
END IF;

/* =========== Removing use of demographics table for now.. adding fields directly into devotee table =========================
-- Demographics table Update - simply replace the record
IF  (p_Devotee_Address_1 <> "" OR p_Devotee_State <> "" OR p_Devotee_Country <> "") THEN
	-- SELECT count(*) INTO v_past_demographics_count  FROM Devotee_Demographics WHERE devotee_key = p_Devotee_Key AND Devotee_Demographics_Status = 'Current';

    

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
 =========== Removing use of demographics table for now.. adding fields directly into devotee table ========================= */
 -- ========================================================================================================================= --
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


-- /////////////////////////////////////////////
-- // Testing of PROC_REPLACE_DEVOTEE_W_SEVA_I
-- ////////////////////////////////////////////
/*CALL `kdms2022`.`PROC_REPLACE_DEVOTEE_W_SEVA_I`
('P181022956',
'P',
'Stored',
'Procedure Test',
'M',
'2003-12-21',
'DL',
'235235ed',
'address one field',
'address two field',
'Pleasanton',
'CA',
'94566',
'USA',
'test@test.com',
'42343234',
'A',
'2003',
'Anil',
'This is testing of stored procedure',
'no comments for now',
'anil',
'DKS',
'Assigned',
'RK',
'Allocated',
'2022NR');

select * from devotee where devotee_key = 'P181022956';
select * from devotee_accomodation where devotee_key = 'P181022956';
select * from accommodation_availability where accomodation_key in ('RK','DS9');
select * from devotee_seva where devotee_key = 'P181022956';
select * from seva_availability where seva_id in ('DKS', 'REG')
*/

-- //////////////////////////////////////
-- // PROC_INSERT_DEVOTEE_W_SEVA_I
-- ////////////////////////////////////////

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `PROC_INSERT_DEVOTEE_W_SEVA_I`(
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
    IN `p_Devotee_Country` VARCHAR(20),
    IN `p_Comments`  VARCHAR(250),
    IN `p_Joined_Since`  VARCHAR(4)
	)
BEGIN

INSERT INTO devotee(
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
    Devotee_Address_1,
    Devotee_Address_2,
    Devotee_State,
    Devotee_Zip,
    Devotee_Country,
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
          p_Devotee_Address_1,
          p_Devotee_Address_2,
          p_Devotee_State,
          p_Devotee_Zip,
          p_Devotee_Country,
          p_Comments,
          p_Joined_Since
      );

INSERT INTO Devotee_Accomodation(
    Accomodation_Key,
    Devotee_Key,
    Accomodation_Year,
    Arrival_Date_Time,
    Departure_Date_Time,
    Accomodation_Status,
    Devotee_Accomodation_Update_Date_Time,
    Devotee_Accomodation_Updated_By
)
VALUES(
          p_Devotee_Accommodation_ID,
          p_Devotee_Key,
          YEAR(NOW()),
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
        Accomodation_Key = p_Devotee_Accommodation_ID;

INSERT INTO `Devotee_Seva`(
    `Seva_ID`,
    `Devotee_Key`,
    `Seva_Year`,
    `Assignment_Date_Time`,
    `Release_Date_Time`,
    `Seva_Status`,
    `Devotee_Seva_Update_Date_Time`,
    `Devotee_Seva_Updated_By`)
VALUES (
           p_Devotee_Seva_Id,
           p_Devotee_Key,
           YEAR(NOW()),
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
WHERE Seva_Id = p_Devotee_Seva_ID;



END$$
DELIMITER ;

-- //////////////////////////////////////
-- // PROC_UPSERT_SEVA_W_AVAIL_UPDATE_I
-- ////////////////////////////////////////
drop procedure PROC_UPSERT_SEVA_W_AVAIL_UPDATE_I;
DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `PROC_UPSERT_SEVA_W_AVAIL_UPDATE_I`(
	IN `p_Seva_Id` VARCHAR(6),
    IN `p_Seva_Description` VARCHAR(100),
    IN `p_Event_Id` VARCHAR(10)
    )
BEGIN
    REPLACE
INTO `Seva_Master`(
    `Seva_Id`,
    `Seva_Description`,
    `Seva_Updated_On_Date_Time`,
    `Seva_Update_by`
)
VALUES(
    p_Seva_Id,
    p_Seva_Description,
    NOW(), 'Anil') ;

 REPLACE INTO `Seva_Availability`(
	`Seva_Id`,
    `Seva_Event`,
	`Assigned_Count`,
	`Availability_Update_Date_Time`,
	`Availability_Updated_By`
	)
VALUES (
	p_Seva_Id,
    p_Event_Id,
	0,
	NOW(),
	'Anil'
);

CALL PROC_REFRESH_SEVA_COUNT_I(p_Event_Id);

END$$
DELIMITER ;

-- //////////////////////////////////////
-- // PROC_REFRESH_SEVA_COUNT_I
-- ////////////////////////////////////////
drop procedure `PROC_REFRESH_SEVA_COUNT_I`;

DELIMITER $$
CREATE PROCEDURE `PROC_REFRESH_SEVA_COUNT_I`(
    IN `p_Event_Id` VARCHAR(10)
)
BEGIN

	DECLARE v_finished INTEGER DEFAULT 0 ;
    DECLARE v_seva_id VARCHAR(10) DEFAULT "" ;
    DECLARE v_seva_count INTEGER DEFAULT 0 ;
    DECLARE DEBUG BOOL DEFAULT false ;


	DECLARE csr_seva CURSOR FOR
SELECT
    sm.seva_id,
    COUNT(ds.seva_id)
FROM
    Seva_Master sm
        LEFT OUTER JOIN Devotee_Seva ds ON
                sm.Seva_ID = ds.Seva_Id AND ds.Seva_Event = p_Event_Id AND ds.Seva_Status = 'Assigned'
GROUP BY
    sm.Seva_Id;

DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_finished = 1 ;

-- Update seva_availability set assigned_count = 0 where seva_event = p_Event_Id;

OPEN csr_seva ;

WHILE v_finished = 0 DO

            FETCH csr_seva INTO v_seva_id, v_seva_count ;

            IF v_finished = 0 THEN
UPDATE
    Seva_Availability
SET
    assigned_count = v_seva_count
WHERE
        seva_id = v_seva_id AND
        seva_event = p_Event_Id;

IF DEBUG = true THEN
						CALL logIt(concat('PROC_REFRESH_SEVA_COUNT_I: Seva_ID is: ', v_seva_id, ' and seva_event is: ', p_Event_Id, ' and assigned count is : ', v_seva_count));
END IF;
END IF ;

END WHILE ;

CLOSE csr_seva ;
END$$
DELIMITER ;

-- ////////////////////////////////////////
-- // PROC_INITIALIZE_EVENT
-- ////////////////////////////////////////
DROP procedure PROC_INITIALIZE_EVENT;

DELIMITER $$
CREATE DEFINER=`kdms`@`%` PROCEDURE `PROC_INITIALIZE_EVENT`(
	IN `p_Event_ID` VARCHAR(10)
)
BEGIN
-- ====================================================================================
-- BEGIN TRASACTION
-- ARCHIVAL
-- Archive past data (event ID in closed status, and not in current or future status)
-- Identify accommodation record already archived for the each event identified
-- Sum up counts on the accommodation availability records already archived
-- Insert records for the accommocations not yet archived
-- Remove archived accommodations availabiilty records (both inserted and updated)
-- Identify seva availability records already archived for the each event identified
-- Sum up counts on the seva records already archived
-- Insert seva availability records not yet archived in the seva archive table
-- Remove archived seva availability records (both inserted and updated)

-- INSERT RECORDS
-- Identify all records that are in Accommodation Master table, but not in Accommodation availability table for the current event
-- Insert missing accommodations in the accommodation availability table
-- Identify all Seva records that are in Seva Master but not in Seva Availability Table for the current event
-- Insert missing seva records in the seva availability table

-- COMMIT TRASACTOIN

-- ADJUST COUNTS
-- Call PROC_REFRESH_ACCO_COUNT_WITH_EVENT
-- Call PROC_REFRESH_SEVA_COUNT_I
-- ====================================================================================
-- BEGIN TRASACTION

    DECLARE errno INT;
    DECLARE DEBUG bool DEFAULT false;
    /* DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
		GET CURRENT DIAGNOSTICS CONDITION 1 errno = MYSQL_ERRNO;
		SELECT errno AS MYSQL_ERROR;
		ROLLBACK;
    END;
	*/
START TRANSACTION;
-- ACCOMMODATION ARCHIVAL
-- Create temporary table with records to be archieved (event ID in closed status, and not in current or future status)
CREATE TEMPORARY TABLE t_aa_result
SELECT aa.*
FROM   accommodation_availability aa
           LEFT OUTER JOIN Event_Master em on aa.Accommodation_Event = em.event_ID
           LEFT OUTER JOIN accommodation_availability_archive aaa ON (aa.accomodation_Key = aaa.accomodation_key
    AND aa.accommodation_event = aaa.accommodation_event)
WHERE em.event_Status = 'Closed' AND aa.Accommodation_Event <> p_Event_ID AND aaa.accomodation_key IS NULL ;

-- >>> DEBUG block
IF DEBUG THEN
		CREATE TEMPORARY TABLE t_d_result (
			DebugInfoType varchar(50),
            DebugInfo varchar(50)
        );
END IF;
	IF DEBUG THEN
		insert into t_d_result select distinct 'a1. pre_archival_results_events', IFNULL(accommodation_event, 'No Event') from t_aa_result;
insert into t_d_result select distinct 'a2. pre_archival_aa_events', IFNULL(accommodation_event, 'No Event') from accommodation_availability ;
insert into t_d_result select distinct 'a3. pre_archival_aaa_events', IFNULL(accommodation_event, 'No Event') from accommodation_availability_archive ;
END IF;
   -- || Till here
    -- Archive past data
INSERT INTO accommodation_availability_archive
SELECT * FROM t_aa_result;

-- DELETE archieved records
DELETE aa FROM accommodation_availability aa
    INNER JOIN t_aa_result taar ON taar.accomodation_key = aa.accomodation_key AND taar.accommodation_event = aa.accommodation_event;

    -- >>> DEBUG block
    IF DEBUG THEN
		insert into t_d_result select distinct 'b1. post_archival_acco_results_events', IFNULL(accommodation_event, 'No Event') from t_aa_result;
insert into t_d_result select distinct 'b2. post_archival_aa_events', IFNULL(accommodation_event, 'No Event') from accommodation_availability ;
insert into t_d_result select distinct 'b3. post_archival_aaa_events', IFNULL(accommodation_event, 'No Event') from accommodation_availability_archive ;
END IF;
   -- || Till here
    -- || ACCOMMODATION ARCHIVAL COMPLETE

	-- SEVA ARCHIVAL
    -- Create temporary table with records to be archieved (event ID in closed status, and not in current or future status)
	CREATE TEMPORARY TABLE t_sa_result
SELECT sa.*
FROM   seva_availability sa
           LEFT OUTER JOIN Event_Master em on sa.Seva_Event = em.event_ID
           LEFT OUTER JOIN seva_availability_archive saa ON (sa.seva_id = saa.seva_id
    AND sa.seva_event = saa.seva_event)
WHERE em.event_Status = 'Closed' AND sa.Seva_Event <> p_Event_ID AND saa.seva_id IS NULL ;

-- >>> DEBUG block
IF DEBUG THEN
		insert into t_d_result select distinct 'c1. pre_archival_seva_results_events',  IFNULL(seva_event, 'No Event') from t_sa_result;
insert into t_d_result select distinct 'c2. pre_archival_sa_events', IFNULL(seva_event, 'No Event') from seva_availability ;
insert into t_d_result select distinct 'c3. pre_archival_aaa_events', IFNULL(seva_event, 'No Event') from seva_availability_archive ;
END IF;
   -- || Till here
    -- Archive past data
INSERT INTO seva_availability_archive
SELECT * FROM t_sa_result;

-- DELETE archieved records
DELETE sa FROM seva_availability sa
    INNER JOIN t_sa_result saar ON saar.seva_id = sa.seva_id AND saar.seva_event = sa.seva_event;

    -- >>> DEBUG block
    IF DEBUG THEN
		insert into t_d_result select distinct 'd1. post_archival_seva_results_events', IFNULL(seva_event, 'No Event') from t_sa_result;
insert into t_d_result select distinct 'd2. post_archival_sa_events', IFNULL(seva_event, 'No Event') from seva_availability ;
insert into t_d_result select distinct 'd3. post_archival_saa_events', IFNULL(seva_event, 'No Event') from seva_availability_archive ;
END IF;
   -- || Till here

	-- || SEVA ARCHIVAL COMPLETE

	-- ACCOMMODATION INITIALIZE
      -- >>> DEBUG block
    IF DEBUG THEN
		insert into t_d_result select distinct 'e1. pre_retrieval_aa_events', IFNULL(accommodation_event, 'No Event') from accommodation_availability ;
insert into t_d_result select distinct 'e2. pre_retrieval_aaa_events', IFNULL(accommodation_event, 'No Event') from accommodation_availability_archive ;
insert into t_d_result select 'e3. pre_retrieval_accommodations_missing_in_aa', am.accomodation_key from accommodation_master am left outer join accommodation_availability aa on am.Accomodation_Key = aa.Accomodation_Key AND aa.accommodation_event = p_Event_ID WHERE  aa.Accomodation_Key is  null;
END IF;
   -- || Till here

    -- Move accommodation availability records that may have been archived
    REPLACE INTO accommodation_availability
SELECT * FROM accommodation_availability_archive aaa
WHERE aaa.accommodation_event = p_Event_ID;

DELETE FROM accommodation_availability_archive aaa
WHERE aaa.accommodation_event = p_Event_ID;

-- Add accommodation master records that are missing in accommodation availability table
INSERT INTO accommodation_availability
SELECT am.accomodation_key, p_Event_ID, 0, 0, 0, am.Accomodation_Capacity,NOW(), 'Script'
FROM accommodation_master am
         LEFT OUTER JOIN accommodation_availability aa on am.Accomodation_Key = aa.Accomodation_Key AND aa.accommodation_event = p_Event_ID
WHERE  aa.Accomodation_Key is null;

-- Call refresh accommodation counts procedure to true up the counts
CALL `PROC_REFRESH_ACCO_COUNT_W_EVENT`(p_Event_ID);

-- >>> DEBUG block
IF DEBUG THEN
		insert into t_d_result select distinct 'f1. post_retrieval_aa_events', IFNULL(accommodation_event, 'No Event') from accommodation_availability ;
insert into t_d_result select distinct 'f2. post_retrieval_aaa_events', IFNULL(accommodation_event, 'No Event') from accommodation_availability_archive ;
insert into t_d_result select 'f3. post_retrieval_accommodations_missing_in_aa', am.accomodation_key from accommodation_master am left outer join accommodation_availability aa on am.Accomodation_Key = aa.Accomodation_Key AND aa. accommodation_event = p_Event_ID WHERE  aa.Accomodation_Key is  null;
END IF;
   -- || Till here

	-- || ACCOMMODATION INITIALIZE COMPLETE

    -- SEVA INITIALIZE

     -- >>> DEBUG block
    IF DEBUG THEN
		insert into t_d_result select distinct 'g1. pre_retrieval_sa_events', IFNULL(seva_event, 'No Event') from seva_availability ;
insert into t_d_result select distinct 'g2. pre_retrieval_saa_events', IFNULL(seva_event, 'No Event') from seva_availability_archive ;
insert into t_d_result select 'g3. pre_retrieval_seva_missing_in_sa', sm.seva_id from seva_master sm left outer join seva_availability sa on sm.seva_id = sa.seva_id AND sa.seva_event = p_Event_ID WHERE  sa.seva_id is  null;
END IF;
   -- || Till here

   -- Move Seva availability records from archive table, that may have been archived in the past
	REPLACE INTO seva_availability
SELECT * FROM seva_availability_archive saa
WHERE saa.seva_event = p_Event_ID;

DELETE FROM seva_availability_archive saa
WHERE saa.seva_event = p_Event_ID;

-- Add seva master records that are missing in seva availability table
INSERT INTO seva_availability
SELECT sm.seva_id, p_Event_ID, 0,NOW(), 'Script'
FROM seva_master sm
         LEFT OUTER JOIN seva_availability sa on sm.seva_id = sa.seva_id AND sa.seva_event = p_Event_ID
WHERE  sa.seva_id is null;

-- Call refresh seva counts procedure to true up the counts
CALL `PROC_REFRESH_SEVA_COUNT_I`(p_Event_ID);

-- >>> DEBUG block
IF DEBUG THEN
		insert into t_d_result select distinct 'h1. post_retrieval_sa_events', IFNULL(seva_event, 'No Event') from seva_availability ;
insert into t_d_result select distinct 'h2. post_retrieval_saa_events', IFNULL(seva_event, 'No Event') from seva_availability_archive ;
insert into t_d_result select 'h3. post_retrieval_seva_missing_in_sa', sm.seva_id from seva_master sm left outer join seva_availability sa on sm.seva_id = sa.seva_id AND sa.seva_event = p_Event_ID WHERE  sa.seva_id is  null;
END IF;
   -- || Till here

	-- || ACCOMMODATION INITIALIZE COMPLETE

    DROP TEMPORARY TABLE t_aa_result;
    DROP TEMPORARY TABLE t_sa_result;
    IF DEBUG THEN
select DebugInfoType, group_concat(DebugInfo) from t_d_result group by DebugInfoType order by DebugInfoType;
DROP TEMPORARY TABLE t_d_result;
END IF;

COMMIT;


-- ================================ TESTING BLOCK =================================
/*
-- Refresh tables
insert into accommodation_availability select * from accommodation_availability_archive ;
delete from accommodation_availability_archive;

insert into seva_availability select * from seva_availability_archive ;
delete from seva_availability_archive;
-- till here


select 'accommodation_availability', count(*) from accommodation_availability
UNION select 'accommodation_availability_archive', count(*) from accommodation_availability_archive
UNION select 'seva_availability', count(*) from seva_availability
UNION select 'seva_availability_archive', count(*) from seva_availability_archive;

CALL `PROC_INITIALIZE_EVENT`('2022JB');

select 'accommodation_availability', count(*) from accommodation_availability
UNION select 'accommodation_availability_archive', count(*) from accommodation_availability_archive
UNION select 'seva_availability', count(*) from seva_availability
UNION select 'seva_availability_archive', count(*) from seva_availability_archive;
*/
-- ============================= END TESTING BLOCK ================================
END$$
DELIMITER ;
