-- The password for this password hash is "tester_pass"
INSERT INTO ordayna_main_db.users (id, display_name, email, phone_number, password_hash) VALUE (200000, "asd", "asd@asd.com", "36301234567", "$2a$10$lHTqGEfVCdw1J22lcgeYneqZlOSNbbk6rjilNGgb1hyLzGjtCwR2y");

INSERT INTO ordayna_main_db.revoked_refresh_tokens (uuid, duration) VALUE (UUID_v4(), '0 0:0:30');
