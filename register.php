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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $errors = [];

    if (empty($name)) {
        $errors[] = "الرجاء إدخال الاسم";
    }

    if (empty($email)) {
        $errors[] = "الرجاء إدخال البريد الإلكتروني";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "البريد الإلكتروني غير صالح";
    }

    if (empty($password)) {
        $errors[] = "الرجاء إدخال كلمة المرور";
    } elseif (strlen($password) < 6) {
        $errors[] = "كلمة المرور يجب أن تكون 6 أحرف على الأقل";
    }

    if ($password !== $confirm_password) {
        $errors[] = "كلمات المرور غير متطابقة";
    }

    if (empty($errors)) {
        try {
            $pdo = new PDO("sqlite:health_monitoring.db");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // التحقق من وجود البريد الإلكتروني
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errors[] = "البريد الإلكتروني مستخدم بالفعل";
            } else {
                // إنشاء المستخدم الجديد
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
                $stmt->execute([$name, $email, $hashed_password]);

                $_SESSION['user_id'] = $pdo->lastInsertId();
                $_SESSION['username'] = $name;
                header("Location: index.php");
                exit();
            }
        } catch(PDOException $e) {
            $errors[] = "حدث خطأ أثناء إنشاء الحساب";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LifeGuard - إنشاء حساب جديد</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2575fc;
            --secondary-color: #6a11cb;
            --success-color: #2ecc71;
            --warning-color: #f1c40f;
            --danger-color: #e74c3c;
            --text-color: #2c3e50;
            --bg-color: #f8f9fa;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--primary-color) 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            color: var(--text-color);
            animation: bgMove 10s linear infinite alternate;
        }
        @keyframes bgMove {
            0% { background-position: 0% 50%; }
            100% { background-position: 100% 50%; }
        }
        .container {
            background: rgba(255, 255, 255, 0.97);
            padding: 2.8rem 2.2rem 2.2rem 2.2rem;
            border-radius: 24px;
            box-shadow: 0 12px 40px 0 rgba(37, 117, 252, 0.13), 0 2px 8px rgba(0,0,0,0.07);
            width: 100%;
            max-width: 430px;
            backdrop-filter: blur(12px);
            animation: fadeIn 0.7s cubic-bezier(.39,.575,.56,1.000);
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .logo {
            text-align: center;
            margin-bottom: 2.2rem;
        }
        .logo i {
            font-size: 3.2em;
            color: var(--primary-color);
            margin-bottom: 1rem;
            filter: drop-shadow(0 2px 8px #2575fc33);
        }
        h1 {
            color: var(--primary-color);
            text-align: center;
            margin-bottom: 2rem;
            font-size: 2.1em;
            font-weight: 800;
            letter-spacing: 1px;
        }
        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }
        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #444;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        input {
            width: 100%;
            padding: 12px 18px 12px 15px;
            border: 2px solid #e3e8f0;
            border-radius: 10px;
            font-size: 1.05em;
            transition: all 0.3s cubic-bezier(.39,.575,.56,1.000);
            background: #f8faff;
            color: #222;
            font-weight: 500;
            box-shadow: 0 1px 2px rgba(37,117,252,0.03);
        }
        input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 117, 252, 0.13);
            outline: none;
            background: #fff;
        }
        button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.15em;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(.39,.575,.56,1.000);
            margin-top: 1rem;
            box-shadow: 0 2px 8px rgba(37, 117, 252, 0.10);
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.7em;
        }
        button:hover {
            background: linear-gradient(90deg, var(--secondary-color), var(--primary-color));
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 5px 18px rgba(37, 117, 252, 0.18);
        }
        button:active {
            transform: translateY(0);
        }
        .error {
            background: #fcecec;
            color: var(--danger-color);
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            text-align: center;
            font-weight: 500;
            border: 1px solid var(--danger-color);
            animation: shake 0.5s ease-in-out;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        .links {
            text-align: center;
            margin-top: 2rem;
        }
        .links a {
            color: var(--primary-color);
            text-decoration: none;
            display: inline-block;
            margin: 0.5rem;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(.39,.575,.56,1.000);
            padding: 0.5rem 1.2rem;
            border-radius: 6px;
            background: #f8faff;
            box-shadow: 0 1px 2px rgba(37,117,252,0.04);
        }
        .links a:hover {
            background: rgba(37, 117, 252, 0.09);
            transform: translateY(-2px) scale(1.03);
        }
        .links a i {
            margin-left: 0.5rem;
        }
        .password-requirements {
            font-size: 0.95em;
            color: #666;
            margin-top: 0.5rem;
            padding: 0.5rem;
            background: rgba(37, 117, 252, 0.08);
            border-radius: 5px;
        }
        @media (max-width: 480px) {
            .container {
                padding: 1.2rem;
            }
            h1 {
                font-size: 1.5em;
            }
            input {
                padding: 10px 18px 10px 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <i class="fas fa-heartbeat"></i>
        </div>
        <h1>إنشاء حساب جديد</h1>
        <?php if (!empty($errors)): ?>
            <div class="error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo implode('<br>', $errors); ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="name">الاسم</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">البريد الإلكتروني</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">كلمة المرور</label>
                <input type="password" id="password" name="password" required>
                <div class="password-requirements">
                    يجب أن تكون كلمة المرور 6 أحرف على الأقل
                </div>
            </div>
            <div class="form-group">
                <label for="confirm_password">تأكيد كلمة المرور</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit">
                <i class="fas fa-user-plus"></i>
                إنشاء حساب
            </button>
        </form>
        <div class="links">
            <a href="login.php">
                <i class="fas fa-sign-in-alt"></i>
                تسجيل الدخول
            </a>
        </div>
    </div>
</body>
</html>