PRAGMA foreign_keys=OFF;
BEGIN TRANSACTION;
CREATE TABLE "blogpost" (
	`id`	INTEGER NOT NULL,
	`title`	TEXT NOT NULL,
	`publishTime`	INTEGER NOT NULL,
	`isDeleted`	INTEGER NOT NULL DEFAULT 0,
	PRIMARY KEY(id)
) WITHOUT ROWID;
INSERT INTO "blogpost" VALUES(1,'About ...','2016-02-20',0);
INSERT INTO "blogpost" VALUES(2,'Once upon a ...','2016-01-10',0);
INSERT INTO "blogpost" VALUES(3,'How to ...','2016-03-30',0);
CREATE TABLE "session" (
	`id`	TEXT NOT NULL,
	`token`	TEXT NOT NULL,
	`user_id`	TEXT NOT NULL,
	PRIMARY KEY(id)
);
CREATE TABLE "user" (
	`user_id`	INTEGER NOT NULL,
	`email`	TEXT NOT NULL UNIQUE,
	`password`	TEXT,
	`name`	TEXT NOT NULL,
	`note`	TEXT,
	PRIMARY KEY(user_id)
) WITHOUT ROWID;
INSERT INTO "user" VALUES(1,'alice','$2y$10$uE8wdvWTyQlI7qpJgHwG3OUPEzUrm63qjwdotkluOueyLWS7sCJfu','Alice','Password is "123".');
INSERT INTO "user" VALUES(2,'bob','$2y$10$l2SQZzSinF.M3yUl5QyXiuGi.nvk0h27yp0xN7xjQG75vHFsyrOum','Bob','Password is "abc".');
COMMIT;
