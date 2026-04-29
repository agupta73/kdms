DROP PROCEDURE IF EXISTS `PROC_REFRESH_AMENITIES_COUNT`;

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
    am.`Amenity_Key`,
    COUNT(daa.`Amenity_Key`),
    am.`Amenity_Quantity`
FROM
    `amenity_master` am
LEFT OUTER JOIN `devotee_amenities_allocation` daa ON
    am.`Amenity_Key` = daa.`Amenity_Key` AND daa.`Allocation_Event` = p_Event_Id AND daa.`Amenity_Allocation_Status` = 'Allocated'
GROUP BY
    am.`Amenity_Key`;

	DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_finished = 1 ;
    

	OPEN csr_amenity ;
    
		WHILE v_finished = 0 DO
        	
            FETCH csr_amenity INTO v_amenity_key, v_amenity_count, v_amenity_quantity ;
            
            IF v_finished = 0 THEN
            	UPDATE
                    `amenities_availability`
                SET
                    `Allocated_Count` = v_amenity_count,
                    `Available_Count` = v_amenity_quantity - (`Reserved_Count` + `Out_of_Availability_Count` + v_amenity_count)
                WHERE
                    `Amenity_Key` = v_amenity_key AND
                    `Allocation_event` = p_Event_Id;
                    
                    IF DEBUG = true THEN
						CALL logIt(concat('PROC_REFRESH_AMENITY_COUNT: AMENITY_KEY is: ', v_amenity_key, ' and allocation_event is: ', p_Event_Id, ' and assigned count is : ', v_amenity_count));
                    END IF;
                END IF ;
        	
		END WHILE ;
    
    CLOSE csr_amenity ;
END$$
DELIMITER ;
