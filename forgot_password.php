<?php
session_start();
require_once 'config.php';

// إذا كان المستخدم مسجل الدخول، قم بتوجيهه إلى لوحة التحكم
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    
    if (empty($email)) {
        $error = "الرجاء إدخال البريد الإلكتروني";
    } else {
        try {
            $pdo = new PDO("sqlite:health_monitoring.db");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // التحقق من وجود البريد الإلكتروني
            $stmt = $pdo->prepare("SELECT id, name FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                // إنشاء رمز إعادة تعيين فريد
                $reset_token = bin2hex(random_bytes(32));
                $reset_expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
                
                // تحديث قاعدة البيانات
                $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?");
                $stmt->execute([$reset_token, $reset_expires, $email]);
                
                // إرسال البريد الإلكتروني (باستخدام دالة mail() المدمجة في PHP)
                $reset_link = APP_URL . "/reset_password.php?token=" . $reset_token;
                $subject = 'إعادة تعيين كلمة المرور';
                $message = "
                    <div dir='rtl' style='font-family: Arial, sans-serif;'>
                        <h2>مرحباً {$user['name']}</h2>
                        <p>لقد تلقينا طلباً لإعادة تعيين كلمة المرور الخاصة بك. إذا لم تقم بهذا الطلب، يمكنك تجاهل هذا البريد الإلكتروني.</p>
                        <p>لإعادة تعيين كلمة المرور، يرجى النقر على الرابط التالي:</p>
                        <p><a href='{$reset_link}' style='background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>إعادة تعيين كلمة المرور</a></p>
                        <p>أو يمكنك نسخ الرابط التالي ولصقه في متصفحك:</p>
                        <p style='word-break: break-all;'>{$reset_link}</p>
                        <p>ينتهي هذا الرابط خلال ساعة واحدة.</p>
                        <p>مع تحيات،<br>فريق نظام مراقبة الصحة</p>
                    </div>
                ";
                $headers = 'MIME-Version: 1.0' . "
";
                $headers .= 'Content-type: text/html; charset=UTF-8' . "
";
                $headers .= 'From: نظام مراقبة الصحة <noreply@healthmonitoring.com>' . "
";

                // محاولة إرسال البريد الإلكتروني
                if (mail($email, $subject, $message, $headers)) {
                    $success = "تم إرسال رابط إعادة تعيين كلمة المرور إلى بريدك الإلكتروني (اختبار: {$reset_link})";
                } else {
                    error_log("خطأ في إرسال البريد الإلكتروني باستخدام دالة mail(): فشل إرسال البريد لـ {$email}");
                    $error = "عذراً، حدث خطأ أثناء إرسال البريد الإلكتروني. يرجى المحاولة مرة أخرى لاحقاً.";
                }
            } else {
                $error = "البريد الإلكتروني غير مسجل في النظام";
            }
        } catch(PDOException $e) {
            error_log("خطأ في قاعدة البيانات: " . $e->getMessage());
            $error = "عذراً، حدث خطأ في النظام. يرجى المحاولة مرة أخرى لاحقاً.";
        }
    }
}
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LifeGuard - نسيت كلمة المرور</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to right, #6a11cb, #2575fc); /* Gradient background */
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #333;
            direction: rtl; /* Right-to-left for Arabic */
            text-align: right; /* Align text to the right for Arabic */
        }
        .container {
            background-color: #ffffff;
            padding: 35px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2); /* Stronger shadow */
            width: 100%;
            max-width: 450px; /* Slightly wider container */
            box-sizing: border-box;
        }
        h2 {
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
        input[type="email"] {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 1em;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        input[type="email"]:focus {
            border-color: #2575fc;
            box-shadow: 0 0 0 3px rgba(37, 117, 252, 0.2);
            outline: none;
        }
        button {
            width: 100%;
            padding: 12px 20px;
            background-color: #2575fc; /* More vibrant blue */
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
            transform: translateY(-2px); /* Slight lift effect */
        }
        button:active {
            transform: translateY(0);
        }
        .error {
            color: #e74c3c; /* Red for errors */
            background-color: #fcecec;
            border: 1px solid #e74c3c;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 500;
        }
        .success {
            color: #28a745; /* Green for success */
            background-color: #e6ffe6;
            border: 1px solid #28a745;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 500;
        }
        .back-link {
            text-align: center;
            margin-top: 25px;
        }
        .back-link a {
            color: #2575fc; /* Matching link color */
            text-decoration: none;
            display: block;
            margin: 10px 0;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        .back-link a:hover {
            text-decoration: underline;
            color: #1a5bce;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>نسيت كلمة المرور</h2>
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="email">البريد الإلكتروني:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <button type="submit">إرسال رابط إعادة التعيين</button>
        </form>
        <div class="back-link">
            <a href="login.php">العودة إلى تسجيل الدخول</a>
        </div>
    </div>
</body>
</html> 