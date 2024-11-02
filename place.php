<?php
// Подключение к базе данных
$pdo = new PDO('mysql:host=localhost;dbname=flightpool1;charset=utf8', 'root', null, [
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

//Токен, приходит
$token = getallheaders()['Authorization'];

// TODO: по токену получить documtnt_number пользователя

// Получаем document_number пользователя по токену
$stmt = $pdo->prepare("SELECT document_number FROM users WHERE api_token = ?");
$stmt->execute([$token]);
$user = $stmt->fetch();

// Устанавливаем заголовок Content-Type
header('Content-Type: application/json; charset=utf-8');


//по номеру документа из таблицы passengers получить place_from, place_back
if ($user) {
    // Получаем места из таблицы passengers
    $stmt = $pdo->prepare("SELECT place_from, place_back FROM passengers WHERE document_number = ?");
    $stmt->execute([$user['document_number']]);
    $places = $stmt->fetch();

    echo json_encode([
        'status' => 'success',
        'data' => [
            'document_number' => $user['document_number'],
            'place_from' => $places['place_from'] ?? null,
            'place_back' => $places['place_back'] ?? null
        ]
    ], JSON_UNESCAPED_UNICODE);
} else {
    http_response_code(401);
    echo json_encode([
        'status' => 'error',
        'message' => 'Неверный токен'
    ], JSON_UNESCAPED_UNICODE);
}



?>