DROP TABLE "fieldData";

CREATE TABLE "fieldData" (
	"id"	integer NOT NULL,
	"fieldId"	integer NOT NULL,
	"itemId"	integer NOT NULL,
	"intNeg"	integer DEFAULT NULL,
	"intPos"	integer DEFAULT NULL,
	"intNegPos"	integer DEFAULT NULL,
	"floatNeg"	double DEFAULT NULL,
	"floatPos"	double DEFAULT NULL,
	"floatNegPos"	double DEFAULT NULL,
	"string"	varchar(256) DEFAULT NULL,
	"selection"	varchar(1280) DEFAULT NULL,
	"mselection"	varchar(1280) DEFAULT NULL,
	"qrcode"	char(256) DEFAULT NULL,
	"datetime"	DATETIME NOT NULL DEFAULT NULL,
	PRIMARY KEY("id" AUTOINCREMENT)
);


ALTER TABLE `database_rev` ADD `customfieldrev` integer NOT NULL DEFAULT 1;
UPDATE `customfields` SET `dataType` = 9 WHERE `customfields`.`dataType` = 8;
UPDATE `customfields` SET `dataType` = 8 WHERE `customfields`.`dataType` = 7;
UPDATE `customfields` SET `dataType` = 7 WHERE `customfields`.`dataType` = 6;
UPDATE `customfields` SET `dataType` = 6 WHERE `customfields`.`dataType` = 5;
UPDATE `database_rev` SET `customfieldrev` = 2;