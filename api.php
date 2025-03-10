<?php
header('Content-Type: application/json; charset=utf-8');
include 'config.php';

$action = $_GET['action'] ?? null;
$table = $_GET['table'] ?? null;

if (!$action || !$table) {
    die(json_encode(["error" => "پارامترهای لازم ارسال نشده‌اند."]));
}

switch ($action) {
    case "select":
        $stmt = $conn->query("SELECT * FROM `$table`");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case "insert":
        $data = $_POST;  // دریافت داده‌ها از فرم
        if (empty($data)) die(json_encode(["error" => "داده‌ای ارسال نشده است."]));

        $columns = implode(", ", array_keys($data));
        $values = ":" . implode(", :", array_keys($data));
        $stmt = $conn->prepare("INSERT INTO `$table` ($columns) VALUES ($values)");
        $stmt->execute($data);
        echo json_encode(["success" => "داده اضافه شد"]);
        break;

    case "update":
        parse_str(file_get_contents("php://input"), $data); // دریافت داده‌ها در PUT
        $id = $data['id'] ?? null;
        if (!$id) die(json_encode(["error" => "آیدی مشخص نشده است."]));

        unset($data['id']);
        if (empty($data)) die(json_encode(["error" => "داده‌ای برای بروزرسانی ارسال نشده است."]));

        $fields = implode(", ", array_map(fn($key) => "$key = :$key", array_keys($data)));
        $stmt = $conn->prepare("UPDATE `$table` SET $fields WHERE id = :id");
        $stmt->execute(array_merge($data, ["id" => $id]));
        echo json_encode(["success" => "داده ویرایش شد"]);
        break;

    case "delete":
        $id = $_GET['id'] ?? null;
        if (!$id) die(json_encode(["error" => "آیدی مشخص نشده است."]));

        $stmt = $conn->prepare("DELETE FROM `$table` WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(["success" => "داده حذف شد"]);
        break;
}
?>
