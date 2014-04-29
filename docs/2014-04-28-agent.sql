ALTER TYPE epelia_users_type ADD VALUE 'agent';
ALTER TABLE epelia_users ADD woma_id INTEGER DEFAULT NULL REFERENCES epelia_womas(id) ON DELETE SET NULL;
CREATE TABLE epelia_womas_shops(woma_id INTEGER NOT NULL REFERENCES epelia_womas(id) ON DELETE CASCADE, shop_id INTEGER NOT NULL REFERENCES epelia_shops(id) ON DELETE CASCADE);
