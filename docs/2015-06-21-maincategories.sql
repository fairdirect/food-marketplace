CREATE TABLE epelia_main_categories (id SERIAL PRIMARY KEY, name TEXT NOT NULL);
INSERT INTO epelia_main_categories(name) VALUES('Lebensmittel');
INSERT INTO epelia_main_categories(name) VALUES('Drogerie');
ALTER TABLE epelia_product_groups ADD main_category INTEGER DEFAULT NULL REFERENCES epelia_main_categories(id) ON DELETE SET NULL;
UPDATE epelia_product_groups SET main_category = 1 WHERE type='groceries';
UPDATE epelia_product_groups SET main_category = 2 WHERE type='drugstore';
ALTER TABLE epelia_product_groups DROP COLUMN type;

