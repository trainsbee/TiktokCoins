<?php
$url = "https://www.tiktok.com/@dgmmind";

$headers = [
    "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) " .
    "AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36"
];

// Obtener el HTML
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$html = curl_exec($ch);
curl_close($ch);

// Buscar el bloque JSON dentro del script id="__UNIVERSAL_DATA_FOR_REHYDRATION__"
if (preg_match('/<script id="__UNIVERSAL_DATA_FOR_REHYDRATION__" type="application\/json">(.*?)<\/script>/s', $html, $matches)) {
    $json = $matches[1];
    $data = json_decode($json, true);

    // Verificar si contiene la clave con la info del usuario
    if (isset($data["__DEFAULT_SCOPE__"]["webapp.user-detail"]["userInfo"]["user"])) {
        $user = $data["__DEFAULT_SCOPE__"]["webapp.user-detail"]["userInfo"]["user"];


        echo "<pre>";
        echo "Nombre: " . $user["nickname"] . PHP_EOL;
        echo "Usuario: @" . $user["uniqueId"] . PHP_EOL;
        echo "Bio: " . $user["signature"] . PHP_EOL;
        echo "Avatar: " . $user["avatarMedium"] . PHP_EOL;
        echo "Verified: " . ($user["verified"] ? "Sí" : "No") . PHP_EOL;
        echo "Followers: " . ($user["followerCount"] ?? "No disponible") . PHP_EOL;
        echo "Siguiendo: " . ($user["followingCount"] ?? "No disponible") . PHP_EOL;
        echo "Videos: " . ($user["videoCount"] ?? "No disponible") . PHP_EOL;
        echo "</pre>";
    } else {
        echo "No se encontró el campo 'userInfo' en el JSON.";
    }
} else {
    echo "No se encontró el bloque '__UNIVERSAL_DATA_FOR_REHYDRATION__'.";
}
?>
