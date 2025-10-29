<?php
header("Content-Type: application/json; charset=utf-8");

$username = $_GET['user'] ?? '';
if (!$username) {
    echo json_encode(["error" => "No se indicó usuario"]);
    exit;
}

$url = "https://www.tiktok.com/@{$username}";
$headers = [
    "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) " .
    "AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36"
];

// Obtener HTML
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_HTTPHEADER => $headers,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_TIMEOUT => 15,
    CURLOPT_SSL_VERIFYPEER => false
]);
$html = curl_exec($ch);
curl_close($ch);

if (!$html) {
    echo json_encode(["error" => "No se pudo obtener la página"]);
    exit;
}

// Extraer JSON
if (preg_match('/<script id="__UNIVERSAL_DATA_FOR_REHYDRATION__" type="application\/json">(.*?)<\/script>/s', $html, $matches)) {
    $json = html_entity_decode($matches[1]);
    $data = json_decode($json, true);

    $scope = $data["__DEFAULT_SCOPE__"]["webapp.user-detail"] ?? null;
    if ($scope && isset($scope["userInfo"]["user"])) {
        $user = $scope["userInfo"]["user"];
        $stats = $scope["userInfo"]["statsV2"] ?? $scope["userInfo"]["stats"] ?? [];

        $result = [
            "username" => $user["uniqueId"] ?? '',
            "bio" => $user["signature"] ?? '',
            "avatar" => $user["avatarMedium"] ?? '',
            "followers" => $stats["followerCount"] ?? 0,
            "verified" => $user["verified"] ?? false
        ];

        echo json_encode($result);
        exit;
    }
}

echo json_encode(["error" => "Usuario no encontrado"]);
?>
