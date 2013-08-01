
CREATE TABLE users (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  email CHARACTER VARYING(500) NOT NULL,
  username CHARACTER VARYING(255) NOT NULL,
  password CHARACTER VARYING(255) NOT NULL,
  created_at DATETIME
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ALTER TABLE users ADD UNIQUE INDEX (email);
ALTER TABLE users ADD UNIQUE INDEX (username);

-- XXX: Case-insensitive unique-constraint doesn't seem to work in MySQL. :(
-- ALTER TABLE users ADD UNIQUE INDEX(LOWER(email));


-- This table for (temporarily) representing subscriptions to (1) BitcoinChipin.com
-- and (2) MemoryDealers.com news updates.
CREATE TABLE subscriptions (
  user_id INTEGER UNSIGNED NOT NULL,
  chipin BOOLEAN,
  memorydealers BOOLEAN,
  FOREIGN KEY (user_id) REFERENCES users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE widgets (
  id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
  owner_id INTEGER UNSIGNED NOT NULL,
  title CHARACTER VARYING(255),
  ending DATETIME,
  currency CHARACTER VARYING(3),
  goal DECIMAL(10,5) UNSIGNED,
  raised DECIMAL(10,5) UNSIGNED DEFAULT 0,
  progress SMALLINT UNSIGNED DEFAULT 0,
  width SMALLINT UNSIGNED,
  height SMALLINT UNSIGNED,
  color CHARACTER VARYING(25),
  address CHARACTER VARYING(255),
  about TEXT,
  country CHARACTER VARYING(2),
  created DATETIME,
  FOREIGN KEY (owner_id) REFERENCES users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ALTER TABLE widgets ADD CONSTRAINT widgets_users_fk FOREIGN KEY (owner_id) REFERENCES users (id);


CREATE TABLE confirmation_codes (
  user_id INTEGER UNSIGNED NOT NULL,
  code character varying(64),
  created_at DATETIME,
  expires DATETIME
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE confirmation_codes ADD UNIQUE INDEX (code);


CREATE TABLE bitcoin_addresses (
  address CHARACTER VARYING(255),
  satoshis INTEGER UNSIGNED NOT NULL,
  updated_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE bitcoin_addresses ADD UNIQUE INDEX (address);


CREATE TABLE ticker_data (
  currency CHARACTER VARYING(3),
  last_price DECIMAL(10,5) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE ticker_data ADD UNIQUE INDEX (currency);
