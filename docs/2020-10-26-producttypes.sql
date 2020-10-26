ALTER TABLE epelia_products DROP COLUMN tax;
CREATE TYPE producttype AS ENUM('offer','request');
ALTER TABLE epelia_products ADD producttype producttype DEFAULT 'request' NOT NULL;