ALTER TABLE epelia_products ADD main_picture_id INTEGER DEFAULT NULL REFERENCES epelia_pictures(id) ON DELETE SET NULL;
ALTER TABLE epelia_shops DROP CONSTRAINT epelia_shops_url_key;
