<?php
session_start();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LifeGuard - الصفحة الرئيسية</title>
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
            color: var(--text-color);
            padding-top: 0;
        }

        .navbar {
            background: rgba(255, 255, 255, 0.95);
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            backdrop-filter: blur(10px);
            height: auto;
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 1rem;
            text-decoration: none;
            color: var(--primary-color);
            font-size: 1.5em;
            font-weight: 700;
        }

        .logo i {
            font-size: 1.2em;
        }

        .nav-links {
            display: flex;
            gap: 1.5rem;
            align-items: center;
            margin-top: 0;
            margin-bottom: 0;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--text-color);
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .nav-links a:hover {
            background: rgba(37, 117, 252, 0.1);
            color: var(--primary-color);
        }

        .nav-links .btn {
            background: var(--primary-color);
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            transition: all 0.3s ease;
        }

        .nav-links .btn:hover {
            background: #1a5bce;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(37, 117, 252, 0.3);
        }

        .welcome-message {
            background: rgba(255, 255, 255, 0.95);
            padding: 0.8rem 1.5rem;
            border-radius: 10px;
            margin: 0 0.5rem;
            text-align: center;
            animation: slideIn 0.5s ease-out;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        @keyframes slideIn {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .welcome-message h2 {
            color: var(--primary-color);
            margin: 0;
            font-size: 1.1em;
        }

        .welcome-message p {
            color: #666;
            margin: 0;
            font-size: 0.9em;
        }

        .hero {
            padding: 4rem 2rem;
            margin-top: 150px;
            text-align: center;
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .hero h1 {
            font-size: 3em;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            color: white;
            font-weight: 700;
        }

        .hero p {
            font-size: 1.2em;
            margin-bottom: 2rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            color: white;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
            font-weight: 500;
        }

        .features {
            padding: 4rem 2rem;
            background: rgba(255, 255, 255, 0.95);
        }

        .features-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            text-align: center;
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .feature-card i {
            font-size: 2.5em;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .feature-card h3 {
            color: var(--text-color);
            margin-bottom: 1rem;
        }

        .feature-card p {
            color: #666;
            line-height: 1.6;
        }

        .main-footer {
            background: linear-gradient(90deg, #f8faff 60%, #e9f0fb 100%);
            margin-top: 2.5rem;
            padding: 2.2rem 0 1.2rem 0;
            border-radius: 22px 22px 0 0;
            box-shadow: 0 -2px 18px rgba(37,117,252,0.08);
            font-size: 1.08em;
            color: #2c3e50;
            border-top: 1.5px solid #e3e8f0;
        }
        .footer-content {
            max-width: 1200px;
            margin: auto;
            line-height: 2.1;
            text-align: center;
        }
        .footer-hardware {
            display: flex;
            flex-wrap: nowrap;
            justify-content: center;
            gap: 1.5rem;
            margin-top: 1.2em;
            overflow-x: unset;
        }
        .hardware-card {
            background: #fff;
            border-radius: 14px;
            padding: 1.1em 1.3em;
            min-width: 260px;
            max-width: 320px;
            box-shadow: 0 1px 8px #2575fc13;
            text-align: right;
            border: 1px solid #e3e8f0;
            transition: box-shadow 0.3s, transform 0.3s;
        }
        .hardware-card:hover {
            box-shadow: 0 4px 18px #2575fc22;
            transform: translateY(-4px) scale(1.03);
        }
        .hardware-title {
            font-weight: bold;
            color: #2575fc;
            font-size: 1.08em;
        }
        .hardware-title i {
            margin-left: 0.4em;
        }
        @media (max-width: 1400px) {
            .footer-content {
                max-width: 98vw;
            }
            .hardware-card {
                min-width: 200px;
                max-width: 98vw;
            }
        }
        @media (max-width: 900px) {
            .footer-hardware {
                flex-direction: column;
                align-items: center;
                flex-wrap: wrap;
            }
            .hardware-card {
                min-width: 180px;
                max-width: 98vw;
            }
        }
        @media (max-width: 768px) {
            .navbar {
                padding: 1rem;
            }

            .nav-container {
                flex-direction: column;
                gap: 1rem;
            }

            .nav-links {
                flex-direction: column;
                width: 100%;
            }

            .nav-links a {
                width: 100%;
                text-align: center;
            }

            .hero {
                padding: 6rem 1rem 3rem;
                margin-top: 0;
            }

            .hero h1 {
                font-size: 2em;
            }

            .features {
                padding: 3rem 1rem;
            }
        }

        .copyright-bar {
            background: #e9f0fb;
            color: #2575fc;
            text-align: center;
            padding: 0.7em 0 0.5em 0;
            font-size: 1em;
            letter-spacing: 0.5px;
            border-radius: 0 0 18px 18px;
            box-shadow: 0 2px 8px #2575fc11 inset;
            margin-top: -0.5em;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">
                <i class="fas fa-heartbeat"></i>
                LifeGuard
            </a>
            <div class="nav-links">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="welcome-message">
                        <h2>مرحباً <span><?php echo htmlspecialchars($_SESSION['username']); ?></span></h2>
                    </div>
                    <a href="dashboard.php">لوحة التحكم</a>
                    <a href="logout.php" class="btn">تسجيل الخروج</a>
                <?php else: ?>
                    <a href="login.php">تسجيل الدخول</a>
                    <a href="register.php" class="btn">إنشاء حساب</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <section class="hero">
        <h1>مراقبة صحتك أصبحت أسهل</h1>
        <p>نظام متكامل لمراقبة معدل ضربات القلب، مستوى الأكسجين، ودرجة الحرارة</p>
    </section>

    <section class="features">
        <div class="features-container">
            <div class="feature-card">
                <i class="fas fa-heartbeat"></i>
                <h3>مراقبة معدل ضربات القلب</h3>
                <p>تتبع معدل ضربات قلبك في الوقت الفعلي مع تنبيهات فورية</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-lungs"></i>
                <h3>قياس مستوى الأكسجين</h3>
                <p>مراقبة مستويات الأكسجين في الدم للحفاظ على صحتك</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-thermometer-half"></i>
                <h3>تتبع درجة الحرارة</h3>
                <p>قياس درجة حرارة جسمك بدقة عالية</p>
            </div>
        </div>
    </section>
    <footer class="main-footer">
        <div class="footer-content">
            <b>نستخدم أحدث المكونات وأجهزة الاستشعار لضمان الدقة والجودة وطمأنينة المريض:</b>
            <div class="footer-hardware">
                <div class="hardware-card">
                    <span class="hardware-title"><i class="fab fa-arduino"></i> Arduino Uno R3 Board</span><br>
                    لوحة تحكم دقيقة وموثوقة تُستخدم لقراءة البيانات من الحساسات والتحكم في النظام بالكامل.
                </div>
                <div class="hardware-card">
                    <span class="hardware-title"><i class="fas fa-heartbeat"></i> MAX30102 Sensor</span><br>
                    حساس متطور لقياس معدل ضربات القلب ونسبة الأكسجين في الدم بدقة عالية.
                </div>
                <div class="hardware-card">
                    <span class="hardware-title"><i class="fas fa-thermometer-half"></i> LM35 Sensor</span><br>
                    حساس حراري لقياس درجة حرارة الجسم بسرعة واستجابة فورية.
                </div>
                <div class="hardware-card">
                    <span class="hardware-title"><i class="fas fa-sim-card"></i> GSM 808 Module</span><br>
                    وحدة اتصال لاسلكية لإرسال البيانات الطبية إلى النظام عن بعد عبر شبكة الجوال.
                </div>
            </div>
        </div>
    </footer>
    <div class="copyright-bar">
        <span>جميع الحقوق محفوظة &copy; <?php echo date('Y'); ?> LifeGuard</span>
    </div>
</body>
</html>