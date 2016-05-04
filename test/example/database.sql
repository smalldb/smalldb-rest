BEGIN TRANSACTION;
CREATE TABLE "blogpost" (
	`id`	INTEGER NOT NULL,
	`title`	TEXT NOT NULL,
	`publishTime`	INTEGER NOT NULL,
	`isDeleted`	INTEGER NOT NULL DEFAULT 0,
	PRIMARY KEY(id)
) WITHOUT ROWID;
INSERT INTO `blogpost` VALUES (1,'About ...','2016-02-20',0);
INSERT INTO `blogpost` VALUES (2,'Once upon a ...','2016-01-10',0);
INSERT INTO `blogpost` VALUES (3,'How to ...','2016-03-30',0);
COMMIT;
