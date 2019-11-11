<?php

header('Content-type: application/json');

$ini = parse_ini_file('../../../app.ini', true);

$link = mysqli_connect($ini[database][host], $ini[database][user], $ini[database][password], $ini[database][name]) or die('Ошибка');
mysqli_set_charset($link, 'utf8');

$content = [];

$playground_id = htmlspecialchars($_GET['id']);

if ($playground_id != '') {
    $result = mysqli_query($link, "SELECT * FROM `playgrounds` WHERE `id` = '$playground_id'");
    $row = mysqli_fetch_assoc($result);
    $address = $row['address'];
    $latitude = $row['latitude'];
    $longitude = $row['longitude'];
    $size = $row['size'];
    $is_illuminated = $row['is_illuminated'];
    $is_fenced = $row['is_fenced'];
    $district_id = $row['district_id'];

    if (isset($address)) {
        array_push($content, [
            'id' => $playground_id,
            'address' => $address,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'size' => $size,
            'is_illuminated' => $is_illuminated,
            'is_fenced' => $is_fenced,
            'district_id' => $district_id,
        ]);
    } else {
        array_push($content, [
            'id' => $playground_id,
            'error' => 'Не найден'
        ]);
    }
} else {
    $result = mysqli_query($link, "SELECT * FROM `playgrounds`");

    while ($row = mysqli_fetch_assoc($result)) {
        $playground_id = $row['id'];
        $address = $row['address'];
        $latitude = $row['latitude'];
        $longitude = $row['longitude'];
        $size = $row['size'];
        $is_illuminated = $row['is_illuminated'];
        $is_fenced = $row['is_fenced'];
        $district_id = $row['district_id'];

        array_push($content, [
            'id' => $playground_id,
            'address' => $address,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'size' => $size,
            'is_illuminated' => $is_illuminated,
            'is_fenced' => $is_fenced,
            'district_id' => $district_id,
        ]);
    }
}

if ($content) {
    $json_str = json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
    echo $json_str;
}