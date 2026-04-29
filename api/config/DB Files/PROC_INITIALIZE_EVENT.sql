DROP PROCEDURE PROC_INITIALIZE_EVENT;
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
	LEFT OUTER JOIN event_master em on aa.Accommodation_Event = em.`Event_ID`  
    LEFT OUTER JOIN accommodation_availability_archive aaa ON (aa.accomodation_Key = aaa.accomodation_key 
		AND aa.accommodation_event = aaa.accommodation_event)
	WHERE em.`Event_Status` = 'Closed' AND aa.Accommodation_Event <> p_Event_ID AND aaa.accomodation_key IS NULL ;
    
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
	LEFT OUTER JOIN event_master em on sa.Seva_Event = em.`Event_ID`  
    LEFT OUTER JOIN seva_availability_archive saa ON (sa.seva_id = saa.seva_id 
		AND sa.seva_event = saa.seva_event)
	WHERE em.`Event_Status` = 'Closed' AND sa.Seva_Event <> p_Event_ID AND saa.seva_id IS NULL ;
    
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
    
    -- AMENITIES ARCHIVAL
    -- Create temporary table with records to be archieved (event ID in closed status, and not in current or future status)
	CREATE TEMPORARY TABLE t_ama_result
    SELECT aa.* 
	FROM   amenities_availability aa
	LEFT OUTER JOIN event_master em on aa.Allocation_event = em.`Event_ID`  
    LEFT OUTER JOIN amenities_availability_archive aaa ON (aa.amenity_key = aaa.amenity_key 
		AND aa.allocation_event = aaa.allocation_event)
	WHERE em.`Event_Status` = 'Closed' AND aa.Allocation_event <> p_Event_ID AND aaa.Amenity_Key IS NULL ;
    
     -- >>> DEBUG block
    IF DEBUG THEN
		insert into t_d_result select distinct 'cc1. pre_archival_amenity_results_events',  IFNULL(seva_event, 'No Event') from t_ama_result;
		insert into t_d_result select distinct 'cc2. pre_archival_amenity_events', IFNULL(seva_event, 'No Event') from amenities_availability ;
		insert into t_d_result select distinct 'cc3. pre_archival_amenity_archival_events', IFNULL(seva_event, 'No Event') from amenities_availability_archive ;
    END IF;
   -- || Till here
    -- Archive past data 
    INSERT INTO amenities_availability_archive
    SELECT * FROM t_ama_result;
    
    -- DELETE archieved records
    DELETE aa FROM amenities_availability aa
    INNER JOIN t_ama_result saar ON saar.amenity_key = aa.amenity_key AND saar.allocation_event = aa.allocation_event;
    
    -- >>> DEBUG block
    IF DEBUG THEN
		insert into t_d_result select distinct 'dd1. post_archival_amenity_results_events', IFNULL(seva_event, 'No Event') from t_ama_result;
		insert into t_d_result select distinct 'dd2. post_archival_amenity_availability_events', IFNULL(seva_event, 'No Event') from amenities_availability ;
		insert into t_d_result select distinct 'dd3. post_archival_amenity_a_arc_events', IFNULL(seva_event, 'No Event') from amenities_availability_archive ;
    END IF;
   -- || Till here
   
	-- || AMENITEIES ARCHIVAL COMPLETE
    
    
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
    
-- || SEVA INITIALIZE COMPLETE

 -- AMENITIES INITIALIZE 
    
     -- >>> DEBUG block
    IF DEBUG THEN
		insert into t_d_result select distinct 'gg1. pre_retrieval_amenities_a_events', IFNULL(allocation_event, 'No Event') from amenities_availability ;
		insert into t_d_result select distinct 'gg2. pre_retrieval_amenities_aa_events', IFNULL(allocation_event, 'No Event') from amenities_availability_archive ;
        insert into t_d_result select 'gg3. pre_retrieval_amenities_missing_in_amenities_avail', am.amenity_key from amenity_master am left outer join amenities_availability aa on am.amenity_key = aa.amenity_key AND aa.allocation_event = p_Event_ID WHERE  aa.am.amenity_key is null;
    END IF;
   -- || Till here
   
   -- Move amenities availability records from archive table, that may have been archived in the past
	REPLACE INTO amenities_availability 
    SELECT * FROM amenities_availability_archive aaa
    WHERE aaa.allocation_event = p_Event_ID;
    
    DELETE FROM amenities_availability_archive aaa
    WHERE aaa.allocation_event = p_Event_ID;
    
    -- Add amenity master records that are missing in amenities availability table
	INSERT INTO amenities_availability 
    SELECT am.amenity_key, p_Event_ID, 0,0, 0, am.amenity_quantity, NOW(), 'Script'
    FROM amenity_master am 
    LEFT OUTER JOIN amenities_availability aa on am.amenity_key = aa.amenity_key AND aa.allocation_event = p_Event_ID
    WHERE  aa.amenity_key is null;
    
    -- Call refresh seva counts procedure to true up the counts
	CALL `PROC_REFRESH_AMENITY_COUNT`(p_Event_ID);

    -- >>> DEBUG block
       IF DEBUG THEN
		insert into t_d_result select distinct 'hh1. post_retrieval_amanities_a_events', IFNULL(allocation_event, 'No Event') from seva_availability ;
		insert into t_d_result select distinct 'hh2. post_retrieval_amanities_aa_events', IFNULL(allocation_event, 'No Event') from seva_availability_archive ;
        insert into t_d_result select 'hh3. post_retrieval_amanities_missing_in_amanities_a', am.amenity_key from amenity_master am left outer join amenities_availability aa on am.amenity_key = aa.amenity_key  AND aa.allocation_event = p_Event_ID WHERE  aa.amenity_key  is  null;
    END IF;
   -- || Till here
    
	-- || AMENITIES INITIALIZE COMPLETE

    DROP TEMPORARY TABLE t_aa_result;
    DROP TEMPORARY TABLE t_sa_result;
    DROP TEMPORARY TABLE t_ama_result;
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
