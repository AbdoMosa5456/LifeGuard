<?php
try {
    $db = new SQLite3('database.sqlite');
    
    // إضافة حقول إعادة تعيين كلمة المرور
    $db->exec('ALTER TABLE users ADD COLUMN reset_token TEXT');
    $db->exec('ALTER TABLE users ADD COLUMN reset_expires DATETIME');
    
    echo "تم تحديث قاعدة البيانات بنجاح!";
} catch (Exception $e) {
    echo "حدث خطأ: " . $e->getMessage();
}
?> 