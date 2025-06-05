-- データベース設定
SET client_encoding = 'UTF8';

-- 仕入先テーブル
CREATE TABLE vendors (
    id SERIAL PRIMARY KEY,
    vendor_code INTEGER UNIQUE NOT NULL,
    vendor_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 商品テーブル
CREATE TABLE products (
    id SERIAL PRIMARY KEY,
    product_code INTEGER UNIQUE NOT NULL,
    product_name VARCHAR(50) NOT NULL,
    price INTEGER NOT NULL CHECK (price >= 0),
    stock_quantity INTEGER NOT NULL CHECK (stock_quantity >= 0),
    vendor_code INTEGER NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vendor_code) REFERENCES vendors(vendor_code)
);

-- サンプルの仕入先データ
INSERT INTO vendors (vendor_code, vendor_name) VALUES 
(1001, '株式会社サンプル'),
(1002, '有限会社テスト'),
(1003, '合同会社デモ'),
(1004, '株式会社例示'),
(1005, '有限会社見本');

-- サンプルの商品データ
INSERT INTO products (product_code, product_name, price, stock_quantity, vendor_code) VALUES 
(10001, 'サンプル商品A', 1500, 50, 1001),
(10002, 'テスト商品B', 2800, 30, 1002),
(10003, 'デモ商品C', 980, 100, 1003),
(10004, '例示商品D', 3200, 25, 1004),
(10005, '見本商品E', 750, 80, 1005);

-- updated_atカラムを自動更新するトリガー関数
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ language 'plpgsql';

-- トリガーの設定
CREATE TRIGGER update_vendors_updated_at 
    BEFORE UPDATE ON vendors 
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_products_updated_at 
    BEFORE UPDATE ON products 
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
