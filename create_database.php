<?php
try {
    $pdo = new PDO("sqlite:health_monitoring.db");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // إنشاء جدول المستخدمين
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL,
        reset_token TEXT,
        reset_expires DATETIME,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    echo "تم إنشاء قاعدة البيانات وجدول المستخدمين بنجاح!\n";
} catch(PDOException $e) {
    echo "خطأ: " . $e->getMessage() . "\n";
}
?> 