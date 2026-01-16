DELIMITER //
CREATE OR REPLACE PROCEDURE ordayna_main_db.detach_intezmeny(
	intezmeny_id_to_detach INT UNSIGNED
) BEGIN
	DELETE FROM foadatbazis.intezmeny_ids
	WHERE intezmeny_ids.intezmeny_id = intezmeny_id_to_detach
	;
END;
//
DELIMITER ;

