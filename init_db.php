<?php
try {
    $db = new SQLite3('database.sqlite');
    
    // إنشاء جدول المستخدمين
    $db->exec('CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL,
        email TEXT UNIQUE NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )');
    
    echo "تم إنشاء قاعدة البيانات بنجاح!";
} catch (Exception $e) {
    echo "حدث خطأ: " . $e->getMessage();
}
?> 