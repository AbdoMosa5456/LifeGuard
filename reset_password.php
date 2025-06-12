<?php
session_start();

// إذا كان المستخدم مسجل دخوله بالفعل، قم بتوجيهه إلى لوحة التحكم
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
$success = '';
$token = $_GET['token'] ?? '';

if (empty($token)) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($password) || empty($confirm_password)) {
        $error = 'يرجى ملء جميع الحقول';
    } elseif ($password !== $confirm_password) {
        $error = 'كلمات المرور غير متطابقة';
    } elseif (strlen($password) < 6) {
        $error = 'يجب أن تكون كلمة المرور 6 أحرف على الأقل';
    } else {
        try {
            $db = new SQLite3('database.sqlite');
            
            // التحقق من صلاحية الرمز
            $stmt = $db->prepare('SELECT id FROM users WHERE reset_token = :token AND reset_expires > datetime("now")');
            $stmt->bindValue(':token', $token, SQLITE3_TEXT);
            $result = $stmt->execute();
            $user = $result->fetchArray(SQLITE3_ASSOC);

            if ($user) {
                // تحديث كلمة المرور
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare('UPDATE users SET password = :password, reset_token = NULL, reset_expires = NULL WHERE id = :id');
                $stmt->bindValue(':password', $hashed_password, SQLITE3_TEXT);
                $stmt->bindValue(':id', $user['id'], SQLITE3_INTEGER);
                
                if ($stmt->execute()) {
                    $success = 'تم تغيير كلمة المرور بنجاح! يمكنك الآن تسجيل الدخول';
                } else {
                    $error = 'حدث خطأ أثناء تغيير كلمة المرور';
                }
            } else {
                $error = 'رمز إعادة التعيين غير صالح أو منتهي الصلاحية';
            }
        } catch (Exception $e) {
            $error = 'حدث خطأ في النظام';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LifeGuard - إعادة تعيين كلمة المرور</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to right, #6a11cb, #2575fc);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #333;
            direction: rtl;
            text-align: right;
        }
        .container {
            background-color: #ffffff;
            padding: 35px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 450px;
            box-sizing: border-box;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            font-size: 2em;
            font-weight: 600;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }
        input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 1em;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        input:focus {
            border-color: #2575fc;
            box-shadow: 0 0 0 3px rgba(37, 117, 252, 0.2);
            outline: none;
        }
        button {
            width: 100%;
            padding: 12px 20px;
            background-color: #2575fc;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1.1em;
            font-weight: 600;
            transition: background-color 0.3s ease, transform 0.2s ease;
            margin-top: 10px;
        }
        button:hover {
            background-color: #1a5bce;
            transform: translateY(-2px);
        }
        button:active {
            transform: translateY(0);
        }
        .error {
            color: #e74c3c;
            background-color: #fcecec;
            border: 1px solid #e74c3c;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 500;
        }
        .success {
            color: #28a745;
            background-color: #e6ffe6;
            border: 1px solid #28a745;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 500;
        }
        .login-link {
            text-align: center;
            margin-top: 25px;
        }
        .login-link a {
            color: #2575fc;
            text-decoration: none;
            display: block;
            margin: 10px 0;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        .login-link a:hover {
            text-decoration: underline;
            color: #1a5bce;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>إعادة تعيين كلمة المرور</h1>
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="password">كلمة المرور الجديدة</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">تأكيد كلمة المرور</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit">تغيير كلمة المرور</button>
        </form>
        <div class="login-link">
            <a href="login.php">العودة إلى تسجيل الدخول</a>
        </div>
    </div>
</body>
</html> 