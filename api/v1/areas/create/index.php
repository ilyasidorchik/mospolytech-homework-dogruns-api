<?php

header('Content-type: application/json');

$ini = parse_ini_file('../../../../app.ini', true);
$link = mysqli_connect($ini[database][host], $ini[database][user], $ini[database][password], $ini[database][name]);

$content = [];
$successMessage = false;
$resultField = 'error';
$resultMessage = '';

if (mysqli_connect_errno()) {
    $resultMessage = 'Соединение с базой данных не удалось';
    goto output;
}

mysqli_set_charset($link, 'utf8');

$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);
$name = $input[0]['name'];

// Validation
$name = str_replace("'", "", $name);

$apiKey = htmlspecialchars($_GET['API_KEY']);

if (isset($name)) {
    if ($apiKey == $ini[api][key]) {
        $result = mysqli_query($link, "SELECT `id` FROM `areas` WHERE `name` = '$name'");
        $row = mysqli_fetch_assoc($result);
        $area_id = $row['id'];

        if (empty($area_id)) {
            mysqli_query($link, "INSERT INTO `areas` (`id`, `name`) VALUES ('NULL', '$name')");

            $result = mysqli_query($link, "SELECT `id` FROM `areas` WHERE `name` = '$name'");
            $row = mysqli_fetch_assoc($result);
            $area_id = $row['id'];

            $successMessage = true;
            $resultField = 'result';
            $resultMessage = [
                    'id' => $area_id,
                    'name' => (String)$name
                ];
        } else {
            $resultMessage = "Повторное добавление $name";
        }
    } else {
        $resultMessage = ($apiKey != '') ? 'Неверный ключ' : 'Не хватает ключа';
        $resultMessage .= '. Обратитесь к администратору: ilya@sidorchik.ru';
    }
}

output:

array_push($content, [
    'success' => $successMessage,
    $resultField => $resultMessage
]);

if ($content) {
    $json_str = json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    echo $json_str;
}