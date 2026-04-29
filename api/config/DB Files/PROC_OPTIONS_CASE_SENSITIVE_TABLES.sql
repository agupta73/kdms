-- Recreate upsert/refresh procedures used by clsOptions / upsertOption for Linux + case-sensitive table names.
-- Canonical table names match kdms schema (e.g. accommodation_master, event_master, seva_availability).
-- Run with MySQL client (required for procedure bodies):
--   mysql -h HOST -u USER -p DATABASE < PROC_OPTIONS_CASE_SENSITIVE_TABLES.sql
--
-- Cloud Console "Run query" may not accept DELIMITER; use Cloud Shell + mysql if needed.

DROP PROCEDURE IF EXISTS `PROC_REFRESH_ACCO_COUNT_W_EVENT`;
DELIMITER $$
CREATE PROCEDURE `PROC_REFRESH_ACCO_COUNT_W_EVENT`(
    IN `p_accommodation_event` VARCHAR(10)
)
BEGIN
    DECLARE v_finished INTEGER DEFAULT 0;
    DECLARE v_accommodation_key VARCHAR(10) DEFAULT '';
    DECLARE v_accommodation_count INTEGER DEFAULT 0;
    DECLARE v_accommodation_capacity INTEGER DEFAULT 0;
    DECLARE DEBUG BOOL DEFAULT FALSE;

    DECLARE csr_accomodation CURSOR FOR
        SELECT
            am.`Accomodation_Key`,
            COUNT(da.`Accomodation_Key`),
            am.`Accomodation_Capacity`
        FROM `accommodation_master` am
        LEFT OUTER JOIN `devotee_accomodation` da ON
            am.`Accomodation_Key` = da.`Accomodation_Key`
            AND da.`Accommodation_Event` = p_accommodation_event
            AND da.`Accomodation_Status` = 'Allocated'
        GROUP BY am.`Accomodation_Key`;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_finished = 1;

    OPEN csr_accomodation;
    WHILE v_finished = 0 DO
        FETCH csr_accomodation INTO v_accommodation_key, v_accommodation_count, v_accommodation_capacity;
        IF v_finished = 0 THEN
            UPDATE `accommodation_availability`
            SET
                `Allocated_Count` = v_accommodation_count,
                `Available_Count` = v_accommodation_capacity
                    - (`Reserved_Count` + `Out_of_Availability_Count` + v_accommodation_count)
            WHERE
                `Accomodation_Key` = v_accommodation_key
                AND `Accommodation_Event` = p_accommodation_event;
            IF DEBUG THEN
                CALL logIt(CONCAT(
                    'PROC_REFRESH_ACCO_COUNT_W_EVENT: v_accommodation_capacity is: ',
                    v_accommodation_capacity,
                    ' and v_accommodation_event is: ',
                    p_accommodation_event
                ));
            END IF;
        END IF;
    END WHILE;
    CLOSE csr_accomodation;
END$$
DELIMITER ;

DROP PROCEDURE IF EXISTS `PROC_UPSERT_ACCO_W_EVENT`;
DELIMITER $$
CREATE PROCEDURE `PROC_UPSERT_ACCO_W_EVENT`(
    IN `p_Accomodation_Key` VARCHAR(5),
    IN `p_Accommodation_Event` VARCHAR(10),
    IN `p_Accomodation_Name` VARCHAR(100),
    IN `p_Accomodation_Capacity` INT,
    IN `p_Reserved_Count` INT,
    IN `p_Out_of_Availability_Count` INT,
    IN `p_Accomodation_Updated_By` VARCHAR(10)
)
BEGIN
    REPLACE INTO `accommodation_master` (
        `Accomodation_Key`,
        `Accomodation_Name`,
        `Accomodation_Capacity`,
        `Accomodation_Update_Date_Time`,
        `Accomodation_Updated_By`
    ) VALUES (
        p_Accomodation_Key,
        p_Accomodation_Name,
        p_Accomodation_Capacity,
        NOW(),
        p_Accomodation_Updated_By
    );

    REPLACE INTO `accommodation_availability` (
        `Accomodation_Key`,
        `Accommodation_Event`,
        `Reserved_Count`,
        `Out_of_Availability_Count`,
        `Availability_Update_Date_Time`,
        `Availability_Updated_By`
    ) VALUES (
        p_Accomodation_Key,
        p_Accommodation_Event,
        p_Reserved_Count,
        p_Out_of_Availability_Count,
        NOW(),
        p_Accomodation_Updated_By
    );

    CALL PROC_REFRESH_ACCO_COUNT_W_EVENT(p_Accommodation_Event);
END$$
DELIMITER ;

DROP PROCEDURE IF EXISTS `PROC_REFRESH_AMENITIES_COUNT`;
DELIMITER $$
CREATE PROCEDURE `PROC_REFRESH_AMENITIES_COUNT`(IN `p_Event_Id` VARCHAR(10))
BEGIN
    DECLARE v_finished INTEGER DEFAULT 0;
    DECLARE v_amenity_key VARCHAR(10) DEFAULT '';
    DECLARE v_amenity_count INTEGER DEFAULT 0;
    DECLARE v_amenity_quantity INTEGER DEFAULT 0;
    DECLARE DEBUG BOOL DEFAULT FALSE;

    DECLARE csr_amenity CURSOR FOR
        SELECT
            am.`Amenity_Key`,
            COUNT(daa.`Amenity_Key`),
            am.`Amenity_Quantity`
        FROM `amenity_master` am
        LEFT OUTER JOIN `devotee_amenities_allocation` daa ON
            am.`Amenity_Key` = daa.`Amenity_Key`
            AND daa.`Allocation_Event` = p_Event_Id
            AND daa.`Amenity_Allocation_Status` = 'Allocated'
        GROUP BY am.`Amenity_Key`;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_finished = 1;

    OPEN csr_amenity;
    WHILE v_finished = 0 DO
        FETCH csr_amenity INTO v_amenity_key, v_amenity_count, v_amenity_quantity;
        IF v_finished = 0 THEN
            UPDATE `amenities_availability`
            SET
                `Allocated_Count` = v_amenity_count,
                `Available_Count` = v_amenity_quantity
                    - (`Reserved_Count` + `Out_of_Availability_Count` + v_amenity_count)
            WHERE
                `Amenity_Key` = v_amenity_key
                AND `Allocation_event` = p_Event_Id;
            IF DEBUG THEN
                CALL logIt(CONCAT(
                    'PROC_REFRESH_AMENITY_COUNT: AMENITY_KEY is: ',
                    v_amenity_key,
                    ' and allocation_event is: ',
                    p_Event_Id,
                    ' and assigned count is : ',
                    v_amenity_count
                ));
            END IF;
        END IF;
    END WHILE;
    CLOSE csr_amenity;
END$$
DELIMITER ;

DROP PROCEDURE IF EXISTS `PROC_UPSERT_AMENITY`;
DELIMITER $$
CREATE PROCEDURE `PROC_UPSERT_AMENITY`(
    IN `p_Amenity_Key` VARCHAR(5),
    IN `p_Allocation_Event` VARCHAR(10),
    IN `p_Amenity_Name` VARCHAR(100),
    IN `p_Amenity_Status` VARCHAR(20),
    IN `p_Amenity_Quantity` INT,
    IN `p_Reserved_Count` INT,
    IN `p_Out_of_Availability_Count` INT,
    IN `p_Amenity_Updated_By` VARCHAR(10)
)
BEGIN
    REPLACE INTO `amenity_master` (
        `Amenity_Key`,
        `Amenity_Name`,
        `Amenity_Status`,
        `Amenity_Quantity`,
        `Amenity_Record_Update_Date_Time`,
        `Amenity_Record_Upated_by`
    ) VALUES (
        p_Amenity_Key,
        p_Amenity_Name,
        p_Amenity_Status,
        p_Amenity_Quantity,
        NOW(),
        p_Amenity_Updated_By
    );

    REPLACE INTO `amenities_availability` (
        `Amenity_Key`,
        `Allocation_event`,
        `Reserved_Count`,
        `Out_of_Availability_Count`,
        `Availability_Update_Date_Time`,
        `Availability_Updated_By`
    ) VALUES (
        p_Amenity_Key,
        p_Allocation_Event,
        p_Reserved_Count,
        p_Out_of_Availability_Count,
        NOW(),
        p_Amenity_Updated_By
    );

    CALL PROC_REFRESH_AMENITIES_COUNT(p_Allocation_Event);
END$$
DELIMITER ;

DROP PROCEDURE IF EXISTS `PROC_REFRESH_SEVA_COUNT_I`;
DELIMITER $$
CREATE PROCEDURE `PROC_REFRESH_SEVA_COUNT_I`(IN `p_Event_Id` VARCHAR(10))
BEGIN
    DECLARE v_finished INTEGER DEFAULT 0;
    DECLARE v_seva_id VARCHAR(10) DEFAULT '';
    DECLARE v_seva_count INTEGER DEFAULT 0;
    DECLARE DEBUG BOOL DEFAULT FALSE;

    DECLARE csr_seva CURSOR FOR
        SELECT
            sm.`Seva_ID`,
            COUNT(ds.`Seva_ID`)
        FROM `seva_master` sm
        LEFT OUTER JOIN `devotee_seva` ds ON
            sm.`Seva_ID` = ds.`Seva_ID`
            AND ds.`Seva_Event` = p_Event_Id
            AND ds.`Seva_Status` = 'Assigned'
        GROUP BY sm.`Seva_ID`;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_finished = 1;

    OPEN csr_seva;
    WHILE v_finished = 0 DO
        FETCH csr_seva INTO v_seva_id, v_seva_count;
        IF v_finished = 0 THEN
            UPDATE `seva_availability`
            SET `Assigned_Count` = v_seva_count
            WHERE `Seva_Id` = v_seva_id AND `Seva_Event` = p_Event_Id;
            IF DEBUG THEN
                CALL logIt(CONCAT(
                    'PROC_REFRESH_SEVA_COUNT_I: Seva_ID is: ',
                    v_seva_id,
                    ' and seva_event is: ',
                    p_Event_Id,
                    ' and assigned count is : ',
                    v_seva_count
                ));
            END IF;
        END IF;
    END WHILE;
    CLOSE csr_seva;
END$$
DELIMITER ;

DROP PROCEDURE IF EXISTS `PROC_UPSERT_SEVA_W_AVAIL_UPDATE_I`;
DELIMITER $$
CREATE PROCEDURE `PROC_UPSERT_SEVA_W_AVAIL_UPDATE_I`(
    IN `p_Seva_Id` VARCHAR(6),
    IN `p_Seva_Description` VARCHAR(100),
    IN `p_Event_Id` VARCHAR(10)
)
BEGIN
    REPLACE INTO `seva_master` (
        `Seva_ID`,
        `Seva_Description`,
        `Seva_Updated_On_Date_Time`,
        `Seva_Update_By`
    ) VALUES (
        p_Seva_Id,
        p_Seva_Description,
        NOW(),
        'Anil'
    );

    REPLACE INTO `seva_availability` (
        `Seva_Id`,
        `Seva_Event`,
        `Assigned_Count`,
        `Availability_Update_Date_Time`,
        `Availability_Updated_By`
    ) VALUES (
        p_Seva_Id,
        p_Event_Id,
        0,
        NOW(),
        'Anil'
    );

    CALL PROC_REFRESH_SEVA_COUNT_I(p_Event_Id);
END$$
DELIMITER ;

DROP PROCEDURE IF EXISTS `PROC_UPSERT_EVENT`;
DELIMITER $$
CREATE PROCEDURE `PROC_UPSERT_EVENT`(
    IN `p_Event_Id` VARCHAR(10),
    IN `p_Event_Description` VARCHAR(50),
    IN `p_Event_Status` VARCHAR(10)
)
BEGIN
    REPLACE INTO `event_master` (
        `Event_ID`,
        `Event_Description`,
        `Event_Status`,
        `Event_Updated_On_Date_Time`,
        `Event_Update_By`
    ) VALUES (
        p_Event_Id,
        p_Event_Description,
        p_Event_Status,
        NOW(),
        'Anil'
    );
END$$
DELIMITER ;
