CREATE TABLE epelia_settings(id TEXT PRIMARY KEY NOT NULL, value TEXT NOT NULL);
INSERT INTO epelia_settings(id, value) VALUES ('frontpage_content','Herzlich willkommen auf unserem Wochenmarkt!');
ALTER TABLE epelia_shopping_carts ADD is_self_collecting BOOLEAN DEFAULT FALSE;
