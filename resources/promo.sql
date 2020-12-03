CREATE TABLE IF NOT EXISTS promo (
  promo_name TEXT PRIMARY KEY,
  promo_maxcount INTEGER NOT NULL,
  promo_count INTEGER DEFAULT 0,
  promo_time TEXT NOT NULL,
  promo_value INTEGER NOT NULL
);
