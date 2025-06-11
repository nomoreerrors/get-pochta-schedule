<?php

function cleanText($text): string {
    // Заменим специальные пробелы (неразрывный, тонкий и пр.) обычным пробелом
    $text = preg_replace('/[\x{00A0}\x{2009}\x{202F}\x{FEFF}]/u', ' ', $text);

    // Удалим лишние пробелы и переносы
    $text = preg_replace('/\s+/', ' ', $text);

    return trim($text);
}

function checkAccess(): void
{
    // Нужна здесь на случай echo case
    header('Content-Type: application/json');

    $api_key = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

    if ($api_key !== $_ENV['API_KEY']) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid API key']);
    exit;
    }
    return;
}