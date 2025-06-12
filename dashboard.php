<?php
session_start();

// التحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// قراءة البيانات من ملف JSON
$data = [
    'heart_rate' => '--',
    'spo2' => '--',
    'temp' => '--'
];

if (file_exists('latest_data.json')) {
    $json = file_get_contents('latest_data.json');
    $data = json_decode($json, true);
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LifeGuard - لوحة التحكم</title>
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
            --card-bg: #ffffff;
            --shadow-light: 0 5px 15px rgba(0, 0, 0, 0.08);
            --shadow-medium: 0 10px 30px rgba(0, 0, 0, 0.15);
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
            flex-direction: column;
            align-items: center;
            color: var(--text-color);
            padding: 20px;
            position: relative; /* لإبقاء زر تسجيل الخروج ثابتاً في مكانه الصحيح */
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 2rem auto;
            background: rgba(255, 255, 255, 0.98);
            border-radius: 25px;
            padding: 3rem;
            box-shadow: var(--shadow-medium);
            backdrop-filter: blur(15px);
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 2px solid rgba(37, 117, 252, 0.1);
        }

        h1 {
            color: var(--primary-color);
            font-size: 2.8em;
            font-weight: 700;
            margin: 0;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1.2rem;
            font-size: 1.1em;
            font-weight: 600;
            color: var(--text-color);
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.5em;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2.5rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: var(--card-bg);
            padding: 2rem;
            border-radius: 20px;
            box-shadow: var(--shadow-light);
            text-align: center;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(37, 117, 252, 0.1);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 6px;
            background: linear-gradient(90deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .stat-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
        }

        .stat-card:hover::before {
            transform: scaleX(1);
        }

        .stat-icon {
            font-size: 3em;
            margin-bottom: 1.2rem;
            color: var(--primary-color);
            transition: transform 0.3s ease-out;
        }

        .stat-card:hover .stat-icon {
            transform: scale(1.1);
            color: var(--secondary-color);
        }

        .stat-label {
            font-size: 1.3em;
            color: #555;
            margin-bottom: 0.8rem;
            font-weight: 600;
        }

        .stat-value {
            font-size: 3em;
            font-weight: bold;
            color: var(--primary-color);
            margin: 0.5rem 0;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.15);
        }

        .stat-unit {
            font-size: 0.5em;
            color: #666;
            margin-right: 0.5rem;
            font-weight: 500;
        }

        .timestamp {
            text-align: center;
            color: #777;
            margin-top: 3rem;
            font-size: 1em;
            padding: 1.2rem 2rem;
            background: rgba(37, 117, 252, 0.1);
            border-radius: 15px;
            display: inline-block;
            margin-left: auto; /* Center the block */
            margin-right: auto; /* Center the block */
            display: block;
            width: fit-content;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .logout-btn {
            position: absolute;
            top: 30px;
            right: 30px;
            padding: 12px 25px;
            background: var(--danger-color);
            color: white;
            border: none;
            border-radius: 30px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 5px 15px rgba(231, 76, 60, 0.4);
            font-size: 1.1em;
        }

        .logout-btn:hover {
            background: #c0392b;
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 8px 25px rgba(231, 76, 60, 0.5);
        }

        .logout-btn i {
            font-size: 1.3em;
        }

        @media (max-width: 768px) {
            .container {
                width: 95%;
                padding: 1.5rem;
            }

            h1 {
                font-size: 2.2em;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .header {
                flex-direction: column;
                gap: 1.5rem;
                text-align: center;
            }

            .logout-btn {
                position: static;
                margin: 1.5rem auto 0;
                width: fit-content;
            }
            .stat-value {
                font-size: 2.2em;
            }
            .stat-label {
                font-size: 1.1em;
            }
        }

        /* تأثيرات القيم */
        .value-change {
            animation: valueUpdate 0.5s ease-out;
        }

        @keyframes valueUpdate {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); color: var(--secondary-color); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body>
    <a href="logout.php" class="logout-btn">
        <i class="fas fa-sign-out-alt"></i>
        تسجيل الخروج
    </a>
    <div class="container">
        <div class="header">
            <h1>لوحة مراقبة الصحة</h1>
            <div class="user-info">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)); ?>
                </div>
                <span><?php echo htmlspecialchars($_SESSION['username'] ?? 'مستخدم'); ?></span>
            </div>
        </div>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-heartbeat"></i>
                </div>
                <div class="stat-label">معدل ضربات القلب</div>
                <div class="stat-value" id="hr-value">
                    <?php echo $data['heart_rate']; ?>
                    <span class="stat-unit">نبضة/دقيقة</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-lungs"></i>
                </div>
                <div class="stat-label">مستوى الأكسجين</div>
                <div class="stat-value" id="spo2-value">
                    <?php echo $data['spo2']; ?>
                    <span class="stat-unit">%</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-thermometer-half"></i>
                </div>
                <div class="stat-label">درجة الحرارة</div>
                <div class="stat-value" id="temp-value">
                    <?php echo $data['temp']; ?>
                    <span class="stat-unit">°C</span>
                </div>
            </div>
        </div>
        <div class="timestamp" id="timestamp">
            آخر تحديث: <?php echo $data['timestamp'] ?? '--'; ?>
        </div>
    </div>

    <script>
        function updateValue(elementId, newValue) {
            const element = document.getElementById(elementId);
            const oldValue = element.textContent.trim().split(' ')[0]; // Extract numeric part for comparison
            
            if (oldValue !== String(newValue)) { // Compare as strings
                element.innerHTML = newValue + ' <span class="stat-unit">' + element.children[0].textContent + '</span>'; // Preserve unit
                element.classList.add('value-change');
                setTimeout(() => {
                    element.classList.remove('value-change');
                }, 500);
            }
        }

        setInterval(() => {
            fetch('latest_data.json?' + new Date().getTime())
                .then(response => response.json())
                .then(data => {
                    updateValue('hr-value', data.heart_rate || '--');
                    updateValue('spo2-value', data.spo2 || '--');
                    updateValue('temp-value', data.temp || '--');
                    document.getElementById('timestamp').textContent = 'آخر تحديث: ' + (data.timestamp || '--');
                })
                .catch(error => console.error('Error:', error));
        }, 3000);
    </script>
</body>
</html>
