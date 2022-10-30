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
	-- ARCHIVAL
	-- Archive past data (event ID in closed status, and not in current or future status)
    -- Identify accommodation record already archived for the each event identified
	INSERT INTO accommodation_availability_archive 
	SELECT aa.*
	FROM   accommodation_availability aa
	LEFT OUTER JOIN Event_Master em on aa.Accommodation_Event = em.event_ID  
    LEFT OUTER JOIN accommodation_availability_archive aaa ON (aa.accomodation_Key = aaa.accomodation_key 
		AND aa.accommodation_event = aaa.accommodation_event)
	WHERE em.event_Status = 'Closed' AND aa.Accommodation_Event <> p_Event_ID AND aaa.accomodation_key IS NULL ;
    
    SELECT aa.*
	FROM   accommodation_availability aa
	LEFT OUTER JOIN Event_Master em on aa.Accommodation_Event = em.event_ID  
    LEFT OUTER JOIN accommodation_availability_archive aaa ON (aa.accomodation_Key = aaa.accomodation_key 
		AND aa.accommodation_event = aaa.accommodation_event)
	WHERE em.event_Status = 'Closed' AND aa.Accommodation_Event <> p_Event_ID AND aaa.accomodation_key IS NULL ;
    
    IF DEBUG THEN
		call logIt(CONCAT('PROC_INITIALIZE_EVENT: '));
    END IF;
    COMMIT;

END$$
DELIMITER ;
