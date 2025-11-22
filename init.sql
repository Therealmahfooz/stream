-- init.sql (UPDATED FOR POSTGRESQL)

-- 1. Create Tables
CREATE TABLE IF NOT EXISTS sync_state (
  room VARCHAR(64) PRIMARY KEY,
  video_url TEXT,
  -- Use VARCHAR instead of ENUM, which is not standard in Postgres
  status VARCHAR(10) NOT NULL DEFAULT 'pause', 
  current_time NUMERIC(10, 2) NOT NULL DEFAULT 0, -- Use NUMERIC instead of DOUBLE
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS chats (
    -- Use SERIAL for auto-increment in Postgres
    id SERIAL PRIMARY KEY, 
    room VARCHAR(64) NOT NULL,
    username VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS room_users (
    room VARCHAR(64) NOT NULL,
    user_id VARCHAR(50) PRIMARY KEY,
    last_seen TIMESTAMP NOT NULL,
    KEY (room)
);

-- 2. Seed default room
INSERT INTO sync_state (room, video_url, status, current_time)
VALUES ('default', NULL, 'pause', 0)
ON CONFLICT (room) DO NOTHING; -- Postgres equivalent of ON DUPLICATE KEY UPDATE