PRAGMA synchronous = OFF;
PRAGMA journal_mode = MEMORY;
BEGIN TRANSACTION;
CREATE TABLE "profile" (
  "user_id" INTEGER NOT NULL,
  "name" varchar(255) DEFAULT NULL,
  "public_email" varchar(255) DEFAULT NULL,
  "gravatar_email" varchar(255) DEFAULT NULL,
  "gravatar_id" varchar(32) DEFAULT NULL,
  "location" varchar(255) DEFAULT NULL,
  "website" varchar(255) DEFAULT NULL,
  "bio" text,
  PRIMARY KEY ("user_id"),
  CONSTRAINT "fk_user_profile" FOREIGN KEY ("user_id") REFERENCES "user" ("id") ON DELETE CASCADE
);
INSERT INTO "profile" VALUES (1,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO "profile" VALUES (2,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
CREATE TABLE "social_account" (
  "id" INTEGER NOT NULL ,
  "user_id" int(11) DEFAULT NULL,
  "provider" varchar(255) NOT NULL,
  "client_id" varchar(255) NOT NULL,
  "data" text,
  "code" varchar(32) DEFAULT NULL,
  "created_at" int(11) DEFAULT NULL,
  "email" varchar(255) DEFAULT NULL,
  "username" varchar(255) DEFAULT NULL,
  PRIMARY KEY ("id")
  CONSTRAINT "fk_user_account" FOREIGN KEY ("user_id") REFERENCES "user" ("id") ON DELETE CASCADE
);
CREATE TABLE "token" (
  "user_id" INTEGER NOT NULL,
  "code" varchar(32) NOT NULL,
  "created_at" int(11) NOT NULL,
  "type" smallint(6) NOT NULL
  CONSTRAINT "fk_user_token" FOREIGN KEY ("user_id") REFERENCES "user" ("id") ON DELETE CASCADE
);
CREATE TABLE "user" (
  "id" INTEGER AUTOINCREMENT NOT NULL ,
  "username" varchar(255) NOT NULL,
  "email" varchar(255) NOT NULL,
  "password_hash" varchar(60) NOT NULL,
  "auth_key" varchar(32) NOT NULL,
  "confirmed_at" int(11) DEFAULT NULL,
  "unconfirmed_email" varchar(255) DEFAULT NULL,
  "blocked_at" int(11) DEFAULT NULL,
  "registration_ip" varchar(45) DEFAULT NULL,
  "created_at" int(11) NOT NULL,
  "updated_at" int(11) NOT NULL,
  "flags" int(11) NOT NULL DEFAULT '0',
  "vmlist" varchar(255) DEFAULT NULL,
  PRIMARY KEY ("id")
);
INSERT INTO "user" VALUES (1,'admin','support@serveur-express.com','$2y$13$UKX8bZZOnOHsoQxRTZm4MOn.F61lK1zMs5h.GSwXWTuPS/mqXRNua','dd8e7wGDQ4giY-kMs9TY1XITuApqgppl',1418745915,NULL,NULL,NULL,1418745915,1441289654,0,'t134,t135');
INSERT INTO "user" VALUES (2,'demo','thomas@serveur-express.com','$2y$10$2si6VYh2Psz480TT5TfPEuWb24/3Bs4FzTapzmHh7lU.wmHaUcz6q','NMA-ZUj_kvL8vIjZkbsCO-T5eWbZ4jPE',1441287825,NULL,NULL,'212.198.137.246',1441287825,1441287825,0,NULL);
CREATE INDEX "user_user_unique_email" ON "user" ("email");
CREATE INDEX "user_user_unique_username" ON "user" ("username");
CREATE INDEX "token_token_unique" ON "token" ("user_id","code","type");
CREATE INDEX "social_account_account_unique" ON "social_account" ("provider","client_id");
CREATE INDEX "social_account_account_unique_code" ON "social_account" ("code");
CREATE INDEX "social_account_fk_user_account" ON "social_account" ("user_id");
END TRANSACTION;
