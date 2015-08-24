ALTER TABLE epelia_shops ADD woma_id INTEGER DEFAULT NULL REFERENCES epelia_womas(id) ON DELETE SET NULL;
UPDATE epelia_shops SET woma_id = (SELECT w.id FROM epelia_womas w JOIN epelia_womas_shops ON woma_id = w.id AND shop_id = epelia_shops.id);
DROP TABLE epelia_womas_shops;
