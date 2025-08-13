<?php
header('Content-Type: application/json');
if (isset($_GET['q'])) {
    $q = urlencode($_GET['q']);
    $url = "https://nominatim.openstreetmap.org/search?format=json&q={$q}&limit=5";
} elseif (isset($_GET['lat']) && isset($_GET['lon'])) {
    $lat = $_GET['lat'];
    $lon = $_GET['lon'];
    $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$lat}&lon={$lon}&addressdetails=1";
} else {
    echo json_encode([]);
    exit;
}
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'MyApp/1.0 (email@domain.com)');
$res = curl_exec($ch);
curl_close($ch);
echo $res;
