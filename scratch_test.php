<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, __DIR__ . '/cookie.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__ . '/cookie.txt');
$html = curl_exec($ch);

preg_match('/name="_token" value="([^"]+)"/', $html, $matches);
$token = $matches[1] ?? '';

curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    '_token' => $token,
    'email' => 'admin@greengsm.com',
    'password' => 'Password@123'
]));
$loginRes = curl_exec($ch);

curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/trips');
curl_setopt($ch, CURLOPT_POST, false);
$tripsHtml = curl_exec($ch);

echo "Trips Page Title / Header Check:\n";
if (strpos($tripsHtml, 'Driver and Trip Performance Monitoring') !== false) {
    echo "SUCCESS: Authenticated and loaded /trips page successfully!\n";
} else {
    echo "FAILED: Still redirected or not authenticated.\n";
}

if (strpos($tripsHtml, 'liveGpsMapModal') !== false) {
    echo "SUCCESS: liveGpsMapModal element found in rendered Blade view!\n";
}

if (strpos($tripsHtml, 'btnSimulateControl') !== false) {
    echo "SUCCESS: btnSimulateControl found in Blade view!\n";
}
