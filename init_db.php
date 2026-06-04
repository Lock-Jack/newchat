<?php
function getDB() {
    $db = new SQLite3(__DIR__ . '/chat.db');
    $db->exec("PRAGMA journal_mode=WAL;");
    $db->exec("PRAGMA foreign_keys=ON;");

    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        pseudo TEXT NOT NULL UNIQUE,
        token TEXT NOT NULL UNIQUE,
        avatar_color TEXT NOT NULL DEFAULT '#c0392b',
        last_seen INTEGER DEFAULT 0
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS messages (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        sender_id INTEGER NOT NULL,
        receiver_id INTEGER DEFAULT NULL,
        room TEXT DEFAULT 'general',
        content TEXT,
        file_path TEXT,
        file_name TEXT,
        file_type TEXT,
        type TEXT DEFAULT 'text',
        created_at INTEGER NOT NULL,
        FOREIGN KEY(sender_id) REFERENCES users(id)
    )");

    return $db;
}
