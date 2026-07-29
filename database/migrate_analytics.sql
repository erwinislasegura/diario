USE vecinoss;

CREATE TABLE IF NOT EXISTS page_views (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  path VARCHAR(255) NOT NULL,
  page_type VARCHAR(30) NOT NULL,
  content_id BIGINT UNSIGNED NULL,
  page_title VARCHAR(200) NOT NULL,
  visitor_hash CHAR(64) NOT NULL,
  session_hash CHAR(64) NOT NULL,
  referrer VARCHAR(500) NOT NULL DEFAULT '',
  user_agent VARCHAR(500) NOT NULL DEFAULT '',
  viewed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_views_date (viewed_at),
  INDEX idx_views_path_date (path, viewed_at),
  INDEX idx_views_content (page_type, content_id)
) ENGINE=InnoDB;
