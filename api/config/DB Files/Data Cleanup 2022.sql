-- /////////////////////////////////////////////
-- // To populate missing clean up gender allocation
-- ////////////////////////////////////////////
select * from devotee where (devotee_gender = '' or devotee_gender is null) ;
update devotee set devotee_gender = 'F' where (devotee_gender = '' or devotee_gender is null ) and right(devotee_first_name, 2) in ('la', 'ru', 'a+', 'ta', 'ri', 'na','ma', 'hi', 'pa', 'ya', 'ti', 'ha','ni', 'di', 'tu', 'ja', 'va') and right(devotee_first_name,4) not in ('adri', 'puri', 'dutt', 'datt','anti', 'shna');
update devotee set devotee_gender = 'F' where (devotee_gender = '' or devotee_gender is null) and devotee_first_name in ('Suman', 'Madhu', 'Neelam', 'Devki'); 

-- /////////////////////////////////////////////
-- // To clean up white spaces before and after devotee names
-- ////////////////////////////////////////////
update devotee set devotee_first_name = trim(devotee_first_name), devotee_last_name = trim(devotee_last_name) 

-- /////////////////////////////////////////////
-- // To reassign devotee status as per new standard
-- ////////////////////////////////////////////
update devotee set devotee_status = 'G' where devotee_status = 'A';
update devotee set devotee_status = 'A' where devotee_status = 'I';
update devotee set devotee_status = 'S' where devotee_status is null;

-- /////////////////////////////////////////////
-- // To clean up incomplete devotee records
-- ////////////////////////////////////////////
select * from devotee_seva where devotee_key in (select devotee_key  from devotee where devotee_first_name is NULL or devotee_first_name like '%delete%' or Devotee_Last_Name like '%delete%');
select * from devotee_accomodation where devotee_key in (select devotee_key  from devotee where devotee_first_name is NULL or devotee_first_name like '%delete%' or Devotee_Last_Name like '%delete%');
select * from devotee_remarks where devotee_key in (select devotee_key  from devotee where devotee_first_name is NULL or devotee_first_name like '%delete%' or Devotee_Last_Name like '%delete%');
select * from devotee_amenities_allocation where devotee_key in (select devotee_key  from devotee where devotee_first_name is NULL or devotee_first_name like '%delete%' or Devotee_Last_Name like '%delete%');
select * from devotee_photo where devotee_key in (select devotee_key  from devotee where devotee_first_name is NULL or devotee_first_name like '%delete%' or Devotee_Last_Name like '%delete%');
select * from devotee where devotee_first_name is NULL or devotee_first_name like '%delete%' or Devotee_Last_Name like '%delete%';

-- /////////////////////////////////////////////
-- // To remove devotee_seva records with non-existent sevas
-- ////////////////////////////////////////////
delete from devotee_seva where seva_id in ('PH1', '','B')
-- check management list
select * from devotee where devotee_key in (select devotee_key from devotee_seva where seva_id in (select seva_id from seva_master where seva_description like '%manage%' ))

-- /////////////////////////////////////////////
-- // To randomly populate missing Date of Births = TEST ENV. ONLY
-- ******** DO NOT USE IN PRODUCTION ************
-- ////////////////////////////////////////////
Update devotee SET devotee_dob = 
(CASE 
	WHEN LEFT(devotee_last_name,1) = 'a' THEN '1940-04-01' 
	WHEN LEFT(devotee_last_name,1) =  'b' THEN '1950-05-01' 
	WHEN  LEFT(devotee_last_name,1) = 'c' THEN '1960-06-01' 
    WHEN  LEFT(devotee_last_name,1) = 'd' THEN '1970-07-01' 
	WHEN  LEFT(devotee_last_name,1) = 'e' THEN '1980-08-01' 
	WHEN  LEFT(devotee_last_name,1) = 'f' THEN '1985-08-01' 
	WHEN  LEFT(devotee_last_name,1) = 'g' THEN '1990-08-01' 
    WHEN  LEFT(devotee_last_name,1) = 'h' THEN '1995-09-01' 
    WHEN  LEFT(devotee_last_name,1) = 'i' THEN '1996-08-01' 
    WHEN  LEFT(devotee_last_name,1) = 'j' THEN '2000-01-01' 
    WHEN  LEFT(devotee_last_name,1) = 'k' THEN '2005-01-01' 
    WHEN  LEFT(devotee_last_name,1) = 'l' THEN '2010-02-01' 
    WHEN  LEFT(devotee_last_name,1) = 'm' THEN '2015-03-01' 
    WHEN  LEFT(devotee_last_name,1) = 'n' THEN '2018-05-01' 
    WHEN  LEFT(devotee_last_name,1) = 'o' THEN '2019-08-01' 
    WHEN  LEFT(devotee_last_name,1) = 'p' THEN '2001-05-01' 
    WHEN  LEFT(devotee_last_name,1) = 'q' THEN '2004-04-01' 
    WHEN  LEFT(devotee_last_name,1) = 'r' THEN '2007-07-01' 
    WHEN  LEFT(devotee_last_name,1) = 's' THEN '1992-02-01' 
    WHEN  LEFT(devotee_last_name,1) = 'u' THEN '2011-06-01' 
    WHEN  LEFT(devotee_last_name,1) = 'v' THEN '2012-03-01' 
    WHEN  LEFT(devotee_last_name,1) = 'w' THEN '2014-08-01' 
    WHEN  LEFT(devotee_last_name,1) = 'x' THEN '1945-08-01' 
    WHEN  LEFT(devotee_last_name,1) = 'y' THEN '1984-08-01' 
    WHEN  LEFT(devotee_last_name,1) = '+' THEN '1988-08-01' 
    WHEN  LEFT(devotee_last_name,1) = ' ' THEN '1993-08-01' 
    WHEN  LEFT(devotee_last_name,1) = '.' THEN '1973-10-05' 
    WHEN  LEFT(devotee_last_name,1) = '-' THEN '1999-09-21' 
    WHEN  LEFT(devotee_last_name,1) = 't' THEN '2002-11-30' 
END ) 
 WHERE devotee_dob IS NULL OR devotee_dob = '1900-01-01' ;

