<?php

declare(strict_types=1);

$databasePath = __DIR__ . '/database/database.db';

try {

    $db = new PDO('sqlite:' . $databasePath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Temporarily disable foreign key checks
    $db->exec('PRAGMA foreign_keys = OFF;');

    $tables = ['bookings_features', 'bookings', 'features', 'rooms', 'guests'];
    foreach ($tables as $table) {
        $db->exec("DROP TABLE IF EXISTS $table;");
    }

    // Re-enable foreign key checks
    $db->exec('PRAGMA foreign_keys = ON;');

    $db->exec("
        CREATE TABLE guests (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(40) NOT NULL,
            visits INTEGER NOT NULL DEFAULT 0
        );

        CREATE TABLE rooms (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            tier VARCHAR(20) NOT NULL UNIQUE,
            price_per_night INTEGER NOT NULL,
            description VARCHAR(100)
        );

        CREATE TABLE features (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            category VARCHAR(50),
            tier VARCHAR(50),
            feature_name VARCHAR(50),
            price INTEGER
        );

        CREATE TABLE bookings (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            guest_id INTEGER NOT NULL,
            room_id INTEGER NOT NULL,
            arrival_date DATE NOT NULL,
            departure_date DATE NOT NULL,
            total_price DECIMAL(10,2),
            transfercode VARCHAR NOT NULL UNIQUE,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (guest_id) REFERENCES guests(id),
            FOREIGN KEY (room_id) REFERENCES rooms(id)
        );

        CREATE TABLE bookings_features (
            booking_id INTEGER,
            feature_id INTEGER,
            PRIMARY KEY (booking_id, feature_id),
            FOREIGN KEY (booking_id) REFERENCES bookings(id),
            FOREIGN KEY (feature_id) REFERENCES features(id)
        );
    ");

    $db->exec("
        INSERT INTO rooms (tier, price_per_night, description) VALUES
        ('economy', 4, 'Basic room, don''t let the bed bugs bite!'),
        ('standard', 6, 'A bit more comfy, and hey.. you even get a mosquito net'),
        ('luxury', 8, 'Our most luxurious tent, enjoy your holiday in comfort and style');

        INSERT INTO features (category, tier, feature_name, price) VALUES
        ('water', 'economy', 'pool', 2),
        ('water', 'basic', 'scuba diving', 5),
        ('water', 'premium', 'olympic pool', 10),
        ('water', 'superior', 'waterpark with fire and minibar', 17),

        ('games', 'economy', 'yahtzee', 2),
        ('games', 'basic', 'ping pong table', 5),
        ('games', 'premium', 'PS5', 10),
        ('games', 'superior', 'casino', 17),

        ('wheels', 'economy', 'unicycle', 2),
        ('wheels', 'basic', 'bicycle', 5),
        ('wheels', 'premium', 'trike', 10),
        ('wheels', 'superior', 'four-wheeled motorized beast', 17),

        ('hotel-specific', 'economy', 'Marshmallow roasting over lava stream', 2),
        ('hotel-specific', 'basic', 'Access to marked island trails', 5),
        ('hotel-specific', 'premium', 'Volcano tour with expert guide', 10),
        ('hotel-specific', 'superior', 'Skydiving over volcano', 17);
    ");

    echo "✅ Database reset successfully.\n";
} catch (PDOException $e) {
    echo "❌ Database reset failed: " . $e->getMessage() . "\n";
    exit(1);
}
