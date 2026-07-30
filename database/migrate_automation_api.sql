USE pulsoangelino_pulsoweb;

ALTER TABLE posts
  ADD COLUMN source_name VARCHAR(180) NULL AFTER image,
  ADD COLUMN source_url VARCHAR(1000) NULL AFTER source_name,
  ADD COLUMN source_hash CHAR(64) NULL AFTER source_url,
  ADD UNIQUE KEY uq_posts_source_hash (source_hash);

CREATE TABLE IF NOT EXISTS automation_publications (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  idempotency_key VARCHAR(128) NOT NULL UNIQUE,
  request_id CHAR(36) NOT NULL UNIQUE,
  post_id BIGINT UNSIGNED NULL,
  source_url VARCHAR(1000) NULL,
  status ENUM('processing','published','failed') NOT NULL DEFAULT 'processing',
  http_status SMALLINT UNSIGNED NULL,
  error_message VARCHAR(500) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  completed_at TIMESTAMP NULL,
  INDEX idx_automation_status_created (status,created_at),
  CONSTRAINT fk_automation_post FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE SET NULL
) ENGINE=InnoDB;
