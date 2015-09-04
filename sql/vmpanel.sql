----
-- phpLiteAdmin database dump (https://bitbucket.org/phpliteadmin/public)
-- phpLiteAdmin version: 1.9.6
-- Exported: 5:37pm on September 3, 2015 (CEST)
-- database file: /etc/vmpanel/vmpanel.db
----
BEGIN TRANSACTION;

----
-- Drop table for user
----
DROP TABLE "user";

----
-- Table structure for user
----
CREATE TABLE `user` (
`id` int(11) NOT NULL PRIMARY KEY AUTOINCREMENT,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(60) NOT NULL,
  `auth_key` varchar(32) NOT NULL,
  `confirmed_at` int(11) DEFAULT NULL,
  `unconfirmed_email` varchar(255) DEFAULT NULL,
  `blocked_at` int(11) DEFAULT NULL,
  `registration_ip` varchar(45) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `flags` int(11) NOT NULL DEFAULT '0',
  `vmlist` varchar(255) DEFAULT NULL
);

----
-- Data dump for user, a total of 2 rows
----
INSERT INTO "user" ("id","username","email","password_hash","auth_key","confirmed_at","unconfirmed_email","blocked_at","registration_ip","created_at","updated_at","flags","vmlist") VALUES ('1','admin','support@serveur-express.com','$2y$13$UKX8bZZOnOHsoQxRTZm4MOn.F61lK1zMs5h.GSwXWTuPS/mqXRNua','dd8e7wGDQ4giY-kMs9TY1XITuApqgppl','1418745915',NULL,NULL,NULL,'1418745915','1441289654','0','t134,t135');
INSERT INTO "user" ("id","username","email","password_hash","auth_key","confirmed_at","unconfirmed_email","blocked_at","registration_ip","created_at","updated_at","flags","vmlist") VALUES ('2','demo','thomas@serveur-express.com','$2y$10$2si6VYh2Psz480TT5TfPEuWb24/3Bs4FzTapzmHh7lU.wmHaUcz6q','NMA-ZUj_kvL8vIjZkbsCO-T5eWbZ4jPE','1441287825',NULL,NULL,'212.198.137.246','1441287825','1441294590','0','');

----
-- Drop index for sqlite_autoindex_user_1
----
DROP INDEX "sqlite_autoindex_user_1";

----
-- structure for index sqlite_autoindex_user_1 on table user
----
;
COMMIT;