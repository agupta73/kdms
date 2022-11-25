DELIMITER $$
CREATE DEFINER=`kdms`@`%` PROCEDURE `PROC_MANAGE_AMENITY`(
    IN `p_Devotee_Key` VARCHAR(10),
    IN `p_Amenity_Key` VARCHAR(10),
    IN `p_Allocation_Event` VARCHAR(10),
    IN `p_Amenity_Quantity` INT,
    IN `p_Amenity_Managed_By` VARCHAR(10)
)
BEGIN
    DECLARE v_past_amenity_quantity INT;
	DECLARE v_amenity_action VARCHAR(20) ;


SELECT  IFNULL(SUM(Amenity_Quantity),0) INTO v_past_amenity_quantity
	FROM    Devotee_Amenities_Allocation
	WHERE   Devotee_Key = p_Devotee_Key AND Amenity_Key = p_Amenity_Key AND Amenity_Allocation_Status = 'Allocated' AND Allocation_Event = p_Allocation_Event ;

REPLACE INTO `Devotee_Amenities_Allocation`
(
    `Amenity_Key`,
    `Devotee_Key`,
    `Amenity_Quantity`,
    `Allocation_Event`,
    `Amenity_Allocation_Status`,
    `Amenity_Allocation_Date_Time`,
    `Amenity_Allocated_By`
)
VALUES
(
    p_Amenity_Key,
    p_Devotee_Key,
    v_past_amenity_quantity + p_Amenity_Quantity,
    p_Allocation_Event,
	'Allocated', 
    NOW(), 
    p_Amenity_Managed_By
);

UPDATE
    Amenities_Availability
SET
    Allocated_Count = Allocated_Count + p_Amenity_Quantity,
    Available_Count = Available_Count - p_Amenity_Quantity
WHERE
    Amenity_Key = p_Amenity_Key
    AND Allocation_Event = p_Allocation_Event ;


IF p_Amenity_Quantity > 0 THEN
	SET    v_amenity_action = 'Allocated' ; 
ELSE
	SET    v_amenity_action = 'Returned' ;
END IF ;
    
INSERT INTO `Amenities_Allocation_Log`(
	`Amenity_Key`,
	`Devotee_Key`,
    `Amenity_Quantity`,
    `Amenity_Action`,
    `Allocation_Event`,
    `Amenity_Action_By`,
    `Amenity_Action_Date_Time`
)
VALUES
(
    p_Amenity_Key,
    p_Devotee_Key,
    p_Amenity_Quantity,
    v_amenity_action,
    p_Allocation_Event,
    p_Amenity_Managed_By,
    NOW()) ;
END$$
DELIMITER ;
