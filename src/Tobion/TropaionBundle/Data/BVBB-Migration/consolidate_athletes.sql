/*

# Scheinbar wurde die Pass-Nr von manchen Mitgliedern über die Saisons hinweg geändert.
# Z.B. bei Vereinswechsel, zwischenzeitlichem Austritt, anderer Name durch Heirat oder einfach falsche Schreibweise
# Dabei wurden Ausstelldatum und Aenderungsdatum nicht übernommen.

select * 
from athletes a
join athletes a2
where a.id <> a2.id and a.first_name = a2.first_name and a.birthday = a2.birthday
order by a.first_name, a.birthday


select a.*, count(distinct(a.id)) AS number_duplicates, GROUP_CONCAT( DISTINCT a.id ORDER BY a.updated_at DESC, a.id DESC SEPARATOR ',') AS duplicate_ids
from athletes a
join athletes a2
where a.id <> a2.id and a.first_name = a2.first_name and a.birthday = a2.birthday
group by a.first_name, a.birthday
order by number_duplicates DESC




# Bspw. sind 809929 und 810451 wohl die gleiche Person
# Dann muessen die Referenzen in clubs (contact_person_id), matches und lineups geaendert werden.
# Bsp.:

SELECT *
FROM lineups
WHERE athlete_id = 809929 OR  athlete_id = 810451 

*/

# Duplikate anhand von untersch. last_name, gleicher first_name und birthday
SELECT a.first_name, a.birthday,
    COUNT(DISTINCT(a.id)) AS number_duplicates, 
    SUBSTRING_INDEX(GROUP_CONCAT(DISTINCT a.id ORDER BY a.updated_at DESC, a.id DESC SEPARATOR ','), ',', 1) AS newest_id,
    SUBSTRING(
        GROUP_CONCAT(DISTINCT a.id ORDER BY a.updated_at DESC, a.id DESC SEPARATOR ','), 
        1 + LOCATE(',', GROUP_CONCAT(DISTINCT a.id ORDER BY a.updated_at DESC, a.id DESC SEPARATOR ','))
    ) AS old_duplicate_ids
FROM athletes a
JOIN athletes a2
WHERE 
    a.id <> a2.id AND 
    a.last_name <> a2.last_name AND
    a.first_name = a2.first_name AND 
    a.birthday = a2.birthday
GROUP BY a.first_name, a.birthday
ORDER BY number_duplicates DESC, a.first_name, a.birthday;
/*  
# ->
# folgende newest_id scheinen wirklich Duplikate zu sein
811254
811090
(811842 - Heirat?, falsches Geschlecht)
811035
(811624)
811302
810678
(811250 - Heirat)
811631 - Kind von Heirat
811585 - Heirat
810995
810462
811278
811396 - Kind von Heirat
811613 - Heirat
811959
809897
(1401975 - Kind von Heirat)
811591 - Heirat
811641 - Heirat
811305
*/

# Duplikate anhand von gleicher last_name, first_name und birthday
SELECT a.last_name, a.first_name, a.birthday,
    COUNT(DISTINCT(a.id)) AS number_duplicates, 
    CAST(SUBSTRING_INDEX(GROUP_CONCAT(DISTINCT a.id ORDER BY a.updated_at DESC, a.id DESC SEPARATOR ','), ',', 1) AS UNSIGNED) AS newest_id,
    SUBSTRING(
        GROUP_CONCAT(DISTINCT a.id ORDER BY a.updated_at DESC, a.id DESC SEPARATOR ','), 
        1 + LOCATE(',', GROUP_CONCAT(DISTINCT a.id ORDER BY a.updated_at DESC, a.id DESC SEPARATOR ','))
    ) AS old_duplicate_ids
FROM athletes a
JOIN athletes a2
WHERE 
    a.id <> a2.id AND 
    a.last_name = a2.last_name AND
    a.first_name = a2.first_name AND 
    a.birthday = a2.birthday
GROUP BY a.last_name, a.first_name, a.birthday
ORDER BY number_duplicates DESC, a.last_name, a.first_name;  


# Duplikate anhand von gleicher last_name, first_name und club_id
SELECT a.last_name, a.first_name, a.club_id,
    COUNT(DISTINCT(a.id)) AS number_duplicates, 
    CAST(SUBSTRING_INDEX(GROUP_CONCAT(DISTINCT a.id ORDER BY a.updated_at DESC, a.id DESC SEPARATOR ','), ',', 1) AS UNSIGNED) AS newest_id,
    SUBSTRING(
        GROUP_CONCAT(DISTINCT a.id ORDER BY a.updated_at DESC, a.id DESC SEPARATOR ','), 
        1 + LOCATE(',', GROUP_CONCAT(DISTINCT a.id ORDER BY a.updated_at DESC, a.id DESC SEPARATOR ','))
    ) AS old_duplicate_ids
FROM athletes a
JOIN athletes a2
WHERE 
    a.id <> a2.id AND 
    a.last_name = a2.last_name AND
    a.first_name = a2.first_name AND 
    a.club_id = a2.club_id
GROUP BY a.club_id, a.last_name, a.first_name
ORDER BY number_duplicates DESC, a.club_id, a.last_name, a.first_name;  
    
    
# Fremdschluessel-Constraints mit automatischer Cascade beim Updaten des Primaerschluessels in einer transaktionalen Engine, wie InnoDB, reichen hier nicht aus,
# da es weder ein Update, noch ein Delete ist, sondern eine Zusammenfuehrung
    
DROP PROCEDURE IF EXISTS replace_athlete //  
/* 
# alte fehlerhafte version, da dergleichen nicht funktioniert:
SET @id_list_as_string = '2,1,3';
DELETE FROM `errormessages`
WHERE `id` IN (SELECT @id_list_as_string);

CREATE PROCEDURE replace_athlete(IN current_id INT UNSIGNED, IN old_ids VARCHAR(255))
BEGIN 
    UPDATE `clubs`
    SET `contact_person_id` = current_id
    WHERE `contact_person_id` IN (old_ids);
    
    UPDATE `matches`
    SET `team1_player_id` = current_id
    WHERE `team1_player_id` IN (old_ids);
    
    UPDATE `matches`
    SET `team1_partner_id` = current_id
    WHERE `team1_partner_id` IN (old_ids);
    
    UPDATE `matches`
    SET `team2_player_id` = current_id
    WHERE `team2_player_id` IN (old_ids);
    
    UPDATE `matches`
    SET `team2_partner_id` = current_id
    WHERE `team2_partner_id` IN (old_ids);
    
    UPDATE `lineups`
    SET `athlete_id` = current_id
    WHERE `athlete_id` IN (old_ids);
    
    DELETE FROM `athletes`
    WHERE `id` IN (old_ids);
END
//    
*/

# http://stackoverflow.com/questions/595371/pass-array-into-a-stored-procedure
# http://forums.mysql.com/read.php?98,50521,164995#msg-164995
CREATE PROCEDURE replace_athlete(IN current_id INT UNSIGNED, IN old_ids VARCHAR(255))
BEGIN 
    SET @query = CONCAT('
    UPDATE `clubs`
    SET `contact_person_id` = ',current_id,'
    WHERE `contact_person_id` IN (',old_ids,');'
    );
    PREPARE stmt FROM @query;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
    
    SET @query = CONCAT('
    UPDATE `matches`
    SET `team1_player_id` = ',current_id,'
    WHERE `team1_player_id` IN (',old_ids,');'
    );
    PREPARE stmt FROM @query;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;

    SET @query = CONCAT('
    UPDATE `matches`
    SET `team1_partner_id` = ',current_id,'
    WHERE `team1_partner_id` IN (',old_ids,');'
    );
    PREPARE stmt FROM @query;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
    
    SET @query = CONCAT('
    UPDATE `matches`
    SET `team2_player_id` = ',current_id,'
    WHERE `team2_player_id` IN (',old_ids,');'
    );
    PREPARE stmt FROM @query;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
    
    SET @query = CONCAT('
    UPDATE `matches`
    SET `team2_partner_id` = ',current_id,'
    WHERE `team2_partner_id` IN (',old_ids,');'
    );
    PREPARE stmt FROM @query;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
    
    SET @query = CONCAT('
    UPDATE `lineups`
    SET `athlete_id` = ',current_id,'
    WHERE `athlete_id` IN (',old_ids,');'
    );
    PREPARE stmt FROM @query;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;

    SET @query = CONCAT('
    DELETE FROM `athletes`
    WHERE `id` IN (',old_ids,');'
    );
    PREPARE stmt FROM @query;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
    
END
//    


DROP PROCEDURE IF EXISTS consolidate_athletes //  

CREATE PROCEDURE consolidate_athletes()
BEGIN 
    DECLARE done TINYINT DEFAULT 0;   
    
    DECLARE current_id INT UNSIGNED;
    DECLARE old_ids VARCHAR(255);  
    
    DECLARE duplicate_cursor CURSOR FOR 
        SELECT 
            # a.last_name, a.first_name, a.birthday,
            # COUNT(DISTINCT(a.id)) AS number_duplicates, 
            CAST(SUBSTRING_INDEX(GROUP_CONCAT(DISTINCT a.id ORDER BY a.updated_at DESC, a.id DESC SEPARATOR ','), ',', 1) AS UNSIGNED) AS newest_id,
            SUBSTRING(
                GROUP_CONCAT(DISTINCT a.id ORDER BY a.updated_at DESC, a.id DESC SEPARATOR ','), 
                1 + LOCATE(',', GROUP_CONCAT(DISTINCT a.id ORDER BY a.updated_at DESC, a.id DESC SEPARATOR ','))
            ) AS old_duplicate_ids
        FROM athletes a
        JOIN athletes a2
        WHERE 
            a.id <> a2.id AND 
            a.last_name = a2.last_name AND
            a.first_name = a2.first_name AND 
            a.birthday = a2.birthday
        GROUP BY a.last_name, a.first_name, a.birthday
        # ORDER BY number_duplicates DESC, a.last_name, a.first_name
        ; 
    
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;
    
    OPEN duplicate_cursor;
       
    REPEAT
        FETCH duplicate_cursor INTO current_id, old_ids;
            
        IF NOT done THEN
            CALL replace_athlete(current_id, old_ids);
        END IF;
    UNTIL done END REPEAT;

    CLOSE duplicate_cursor;
END
//   


SET profiling = 1;
CALL consolidate_athletes();
SHOW profiles;