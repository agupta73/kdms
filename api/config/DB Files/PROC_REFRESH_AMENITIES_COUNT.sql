DROP PROCEDURE PROC_REFRESH_AMENITIES_COUNT;

DELIMITER $$
CREATE DEFINER=`kdms`@`%` PROCEDURE `PROC_REFRESH_AMENITIES_COUNT`(
	IN `p_Event_Id` VARCHAR(10)
)
BEGIN

	DECLARE v_finished INTEGER DEFAULT 0 ;
    DECLARE v_amenity_key VARCHAR(10) DEFAULT "" ;
    DECLARE v_amenity_count INTEGER DEFAULT 0 ;
    DECLARE v_amenity_quantity INTEGER DEFAULT 0 ;
    DECLARE DEBUG BOOL DEFAULT false ;
    

	DECLARE csr_amenity CURSOR FOR
      SELECT
    am.amenity_key,
    COUNT(daa.amenity_key),
    am.amenity_quantity
FROM
    Amenity_Master am
LEFT OUTER JOIN devotee_amenities_allocation daa ON
    am.amenity_key = daa.amenity_key AND daa.Allocation_Event = p_Event_Id AND daa.Amenity_Allocation_Status = 'Allocated'
GROUP BY
    am.amenity_key;

	DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_finished = 1 ;
    

	OPEN csr_amenity ;
    
		WHILE v_finished = 0 DO
        	
            FETCH csr_amenity INTO v_amenity_key, v_amenity_count, v_amenity_quantity ;
            
            IF v_finished = 0 THEN
            	UPDATE
                    amenities_availability
                SET
                    allocated_count = v_amenity_count,
                    available_count = v_amenity_quantity - (reserved_count + out_of_availability_count + v_amenity_count)
                WHERE
                    amenity_key = v_amenity_key AND
                    allocation_event = p_Event_Id;
                    
                    IF DEBUG = true THEN
						CALL logIt(concat('PROC_REFRESH_AMENITY_COUNT: AMENITY_KEY is: ', v_amenity_key, ' and allocation_event is: ', p_Event_Id, ' and assigned count is : ', v_amenity_count));
                    END IF;
                END IF ;
        	
		END WHILE ;
    
    CLOSE csr_amenity ;
END$$
DELIMITER ;
