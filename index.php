<?php
include 'config.php';

// دریافت لیست جداول برای نمایش
$tables = [];
$query = $conn->query("SHOW TABLES");
while ($row = $query->fetch(PDO::FETCH_NUM)) {
    $tables[] = $row[0];
}

// پردازش فرم ایجاد جدول
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_table'])) {
    $table_name = trim($_POST['table_name']);
    $fields = $_POST['fields'] ?? [];

    if (empty($table_name)) {
        die("<p class='alert alert-danger'>نام جدول نمی‌تواند خالی باشد!</p>");
    }

    if (empty($fields)) {
        die("<p class='alert alert-danger'>حداقل یک فیلد برای ایجاد جدول لازم است!</p>");
    }

    $query = "CREATE TABLE `$table_name` (";
    $primary_key_set = false;

    foreach ($fields as $field) {
        $name = trim($field['name']);
        $type = trim($field['type']);
        $auto_increment = isset($field['auto_increment']) ? true : false;

        if (empty($name) || empty($type)) {
            die("<p class='alert alert-danger'>نام و نوع فیلد نمی‌توانند خالی باشند!</p>");
        }

        if ($auto_increment) {
            if ($type != "INT") {
                die("<p class='alert alert-danger'>فقط فیلدهای از نوع INT می‌توانند AUTO_INCREMENT داشته باشند!</p>");
            }
            if ($primary_key_set) {
                die("<p class='alert alert-danger'>فقط یک فیلد می‌تواند AUTO_INCREMENT باشد!</p>");
            }
            $query .= "`$name` $type AUTO_INCREMENT PRIMARY KEY, ";
            $primary_key_set = true;
        } else {
            $query .= "`$name` $type, ";
        }
    }

    $query = rtrim($query, ", ") . ");";

    try {
        $conn->exec($query);
        echo "<p class='alert alert-success'>جدول `$table_name` با موفقیت ایجاد شد.</p>";
    } catch (PDOException $e) {
        echo "<p class='alert alert-danger'>خطا: " . $e->getMessage() . "</p>";
    }
}

// حذف جدول
if (isset($_GET['delete_table'])) {
    $table_name = $_GET['delete_table'];
    $conn->exec("DROP TABLE `$table_name`");
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>مدیریت جداول دیتابیس</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            background: #0f172a;
            color: white;
            font-family: 'Vazir', sans-serif;
            text-align: center;
        }
        .container {
            max-width: 600px;
            background: #1e293b;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.3);
            margin-top: 20px;
        }
        input, select {
            background: #334155 !important;
            color: white !important;
            border: none;
        }
        .btn-custom {
            background: linear-gradient(45deg, #06b6d4, #3b82f6);
            color: white;
            border: none;
            transition: 0.3s;
        }
        .btn-custom:hover {
            background: linear-gradient(45deg, #3b82f6, #06b6d4);
        }
        .table-list {
            list-style: none;
            padding: 0;
        }
        .table-list li {
            background: #334155;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
        }
        .delete-btn {
            background: #ef4444;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            text-decoration: none;
            transition: 0.3s;
        }
        .delete-btn:hover {
            background: #dc2626;
        }
    </style>
    <script>
        let fieldCount = 0;

        function addField() {
            fieldCount++;
            const container = document.getElementById('fields_container');
            const fieldHTML = `
            <div class="mb-2 d-flex justify-content-between align-items-center">
                <input type="text" class="form-control w-50" placeholder="نام فیلد" name="fields[${fieldCount}][name]" required>
                <select class="form-select w-25" name="fields[${fieldCount}][type]">
                    <option value="INT">INT</option>
                    <option value="VARCHAR(255)">VARCHAR(255)</option>
                    <option value="TEXT">TEXT</option>
                </select>
                <div class="btn-group" role="group" aria-label="Auto Increaments">
                <input class="btn-check" type="checkbox" id="btncheck1" name="fields[${fieldCount}][auto_increment]" onclick="checkAutoIncrement(this)">
                  <label class="btn btn-outline-primary" for="btncheck1">Auto_Increament</label>
                </div>
            </div>
            `;
            container.insertAdjacentHTML('beforeend', fieldHTML);
        }

        function checkAutoIncrement(checkbox) {
            const allCheckboxes = document.querySelectorAll('input[type="checkbox"][name$="[auto_increment]"]');
            if (checkbox.checked) {
                allCheckboxes.forEach(cb => {
                    if (cb !== checkbox) cb.checked = false;
                });
            }
        }
    </script>
</head>
<body>

<div class="container">
    <h2 class="mb-4">ایجاد جدول جدید</h2>
    <form method="POST">
        <input type="text" class="form-control mb-3" name="table_name" placeholder="نام جدول" required>
        <div id="fields_container"></div>
        <button type="button" class="btn btn-custom w-100 mt-2" onclick="addField()">➕ افزودن فیلد</button>
        <button type="submit" name="create_table" class="btn btn-success w-100 mt-2">✅ ایجاد جدول</button>
    </form>
</div>

<div class="container mt-3">
    <h2 class="mb-3">جداول موجود</h2>
    <ul class="table-list">
        <?php foreach ($tables as $table) : ?>
            <li><?= htmlspecialchars($table) ?>
                <a href="index.php?delete_table=<?= urlencode($table) ?>" class="delete-btn">🗑 حذف</a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

</body>
</html>
