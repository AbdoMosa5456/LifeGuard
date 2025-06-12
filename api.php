<?php
// api.php: يستقبل البيانات من الأردوينو ويحفظها في ملف نصي بسيط
if (isset($_GET['heart_rate']) && isset($_GET['spo2']) && isset($_GET['temp'])) {
    $data = [
        'heart_rate' => $_GET['heart_rate'],
        'spo2' => $_GET['spo2'],
        'temp' => $_GET['temp'],
        'timestamp' => date('Y-m-d H:i:s')
    ];
    file_put_contents('latest_data.json', json_encode($data));
    echo 'OK';
} else {
    echo 'Missing parameters';
}
?>
