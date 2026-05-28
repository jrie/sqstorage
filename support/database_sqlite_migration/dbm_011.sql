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
	"datetime"	DATETIME DEFAULT NULL
);


ALTER TABLE `database_rev` ADD `customfieldrev` integer NOT NULL DEFAULT 1;
UPDATE `customFields` SET `dataType` = 9 WHERE `customFields`.`dataType` = 8;
UPDATE `customFields` SET `dataType` = 8 WHERE `customFields`.`dataType` = 7;
UPDATE `customFields` SET `dataType` = 7 WHERE `customFields`.`dataType` = 6;
UPDATE `customFields` SET `dataType` = 6 WHERE `customFields`.`dataType` = 5;
UPDATE `database_rev` SET `customfieldrev` = 2;
