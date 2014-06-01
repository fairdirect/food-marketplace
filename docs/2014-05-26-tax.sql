ALTER TABLE epelia_products ADD tax INTEGER NOT NULL DEFAULT 19 ;
ALTER TABLE epelia_products_orders ADD product_name TEXT NOT NULL;
ALTER TABLE epelia_products_orders ADD tax INTEGER NOT NULL DEFAULT 19;
ALTER TABLE epelia_products_shopping_carts DROP COLUMN product_price_id;
ALTER TABLE epelia_products_shopping_carts ADD product_name TEXT NOT NULL;
ALTER TABLE epelia_products_shopping_carts ADD tax INTEGER NOT NULL;
ALTER TABLE epelia_products_shopping_carts ADD value NUMERIC(6,2) NOT NULL;
ALTER TABLE epelia_products_shopping_carts ADD unit_type TEXT NOT NULL;
ALTER TABLE epelia_products_shopping_carts ADD content_type TEXT NOT NULL;
ALTER TABLE epelia_products_shopping_carts ADD contents INTEGER NOT NULL;
ALTER TABLE epelia_products_shopping_carts ADD price_quantity INTEGER NOT NULL;

