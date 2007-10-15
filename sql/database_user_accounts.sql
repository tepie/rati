--
-- Create User Access 
--

-- Drop the existing users
DROP USER 'rati@localhost';

DROP USER 'ratiimport@localhost';

CREATE USER 'rati'@'localhost' IDENTIFIED BY 'Vwd4raDuPA3vyLVN';

GRANT USAGE ON * . * TO 'rati'@'localhost' IDENTIFIED BY 'Vwd4raDuPA3vyLVN';

GRANT SELECT ON `rati` . * TO 'rati'@'localhost';

CREATE USER 'ratiimport'@'localhost' IDENTIFIED BY 'SzrbwJxNxwVxvHW9';

GRANT USAGE ON * . * TO 'ratiimport'@'localhost' IDENTIFIED BY 'SzrbwJxNxwVxvHW9';
	
GRANT SELECT , INSERT , UPDATE , DELETE ON `rati` . * TO 'ratiimport'@'localhost';