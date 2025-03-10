# 📌 مستندات API مدیریت جداول دیتابیس

این API برای ایجاد، خواندن، ویرایش و حذف داده‌ها در جداول دیتابیس طراحی شده است.

## ⚙️ تنظیمات اولیه
ابتدا اطلاعات دیتابیس را در فایل `config.php` تنظیم کنید:

```php
$host = "localhost";
$dbname = "test_db";
$username = "root";
$password = "";
```

---

## 🔗 نحوه استفاده از API

### 📌 دریافت اطلاعات یک جدول
**متد:** `GET`  
**مثال درخواست:**
```
http://yourdomain.com/api.php?action=select&table=users
```
📌 **پاسخ JSON:**
```json
[
  { "id": 1, "name": "Ali", "email": "ali@gmail.com" },
  { "id": 2, "name": "Reza", "email": "reza@gmail.com" }
]
```

---

### 📌 افزودن داده جدید
**متد:** `POST`  
**هدر:** `Content-Type: application/x-www-form-urlencoded`  
**مثال درخواست:**
```
POST http://yourdomain.com/api.php?action=insert&table=users
Content-Type: application/x-www-form-urlencoded

name=Ali&email=ali@gmail.com
```
📌 **پاسخ JSON:**
```json
{ "success": "داده اضافه شد" }
```

---

### 📌 ویرایش داده موجود
**متد:** `PUT`  
**هدر:** `Content-Type: application/x-www-form-urlencoded`  
**مثال درخواست:**
```
PUT http://yourdomain.com/api.php?action=update&table=users
Content-Type: application/x-www-form-urlencoded

id=1&name=Reza&email=reza@gmail.com
```
📌 **پاسخ JSON:**
```json
{ "success": "داده ویرایش شد" }
```

---

### 📌 حذف داده
**متد:** `DELETE`  
**مثال درخواست:**
```
DELETE http://yourdomain.com/api.php?action=delete&table=users&id=1
```
📌 **پاسخ JSON:**
```json
{ "success": "داده حذف شد" }
```

---

## 🖥 تست API
برای تست API می‌توانید از ابزارهایی مانند **Postman**  استفاده کنید.

## 🛠 تکنولوژی‌های استفاده شده
- **PHP** (با PDO برای امنیت بیشتر)
- **MySQL**
- **HTML, CSS, JavaScript** (برای رابط کاربری)

