CREATE TABLE `qso` (
  `id` int(11) NOT NULL AUTO_INCREMENT primary key,
  `utc` datetime NOT NULL,
  `band` varchar(10) NOT NULL,
  txmode varchar(4) NOT NULL,
  `call1` varchar(16) NOT NULL,
  `call2` varchar(16) NOT NULL,
  contest varchar(16),
  `status` CHAR(1) NOT NULL DEFAULT '*',
  `dok` varchar(16) ,
  `dxcc` varchar(8) 
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
ALTER TABLE `dcl3`.`qso` ADD UNIQUE `uqso` (`call1`, `call2`, `band`, `txmode`, `utc`);
alter table qso add column loc char(6);



CREATE TABLE `dcl3`.`op` (
	`id` INT NOT NULL AUTO_INCREMENT , 
	`callsign` VARCHAR(16) NOT NULL DEFAULT 'NOCALL' , 
	`email` VARCHAR(64) NOT NULL DEFAULT 'dl6mhw@darc.de' , 
	`pswd` VARCHAR(64) NOT NULL DEFAULT 'nix' , 
	`name` VARCHAR(64) NOT NULL DEFAULT 'YL/OM' , 
	`att1` INT(1) NULL , 
	`ldate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
	`status` CHAR(1) NOT NULL DEFAULT '+' , 
	PRIMARY KEY (`id`)
) ENGINE = InnoDB;
ALTER TABLE `dcl3`.`op` ADD UNIQUE `uop` (`callsign`);

create table call2dok (
	id INT NOT NULL AUTO_INCREMENT primary key, 
	contest VARCHAR(16) NOT NULL DEFAULT 'NO' , 
	jahr VARCHAR(4) NOT NULL DEFAULT '1967' , 
	callsign VARCHAR(16) NOT NULL DEFAULT 'NOCALL' , 
    dok varchar(16) ,
	ldate TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	start date
);