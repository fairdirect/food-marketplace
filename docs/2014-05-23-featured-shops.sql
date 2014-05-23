ALTER TABLE epelia_shops ADD featured_home BOOLEAN NOT NULL DEFAULT false;
CREATE TABLE epelia_shops_featured_products_home(shop_id INTEGER NOT NULL REFERENCES epelia_shops(id) ON DELETE CASCADE, product_id INTEGER NOT NULL REFERENCES epelia_products(id) ON DELETE CASCADE);
