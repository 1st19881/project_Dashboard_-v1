# 🚗 ระบบจองรถ — KPI Dashboard

> ระบบแดชบอร์ดภายในองค์กรสำหรับแสดงข้อมูลสถิติการจองรถ พร้อมหน้าจอ TV Dashboard สำหรับแสดงผลบนจอทีวี

---

## 📋 ภาพรวมโปรเจกต์

โปรเจกต์นี้พัฒนาด้วย **Laravel 13** บน **PHP 8.5** ใช้ฐานข้อมูล **Oracle** (ผ่าน `yajra/laravel-oci8`) โดยรันอยู่บนเซิร์ฟเวอร์ **IIS** ภายในองค์กร

### ✨ ฟีเจอร์หลัก

- **ระบบ Login 2 ระดับ** — Admin และ Employee แยก Authentication อิสระ
- **Admin Dashboard** — แดชบอร์ดสำหรับผู้ดูแลระบบ
- **Employee Dashboard** — แดชบอร์ดสำหรับพนักงาน
- **TV Dashboard** — หน้าจอแสดงผลสถิติแบบเรียลไทม์สำหรับจอทีวี 65 นิ้ว (1080p) รองรับการเลือกวันที่ย้อนหลัง และ Auto-Refresh

---

## 🏗️ โครงสร้างโปรเจกต์

```
project_std/
├── app/
│   └── Http/
│       └── Controllers/
│           ├── Admin/
│           │   └── DashboardController.php        # แดชบอร์ดผู้ดูแลระบบ
│           ├── Auth/
│           │   └── LoginController.php            # ระบบ Login Admin
│           └── Employee/
│               ├── EmployeeDashboardController.php # แดชบอร์ดพนักงาน
│               ├── EmployeeLoginController.php     # ระบบ Login พนักงาน
│               └── TVDashboardController.php       # TV Dashboard + API ข้อมูล
├── resources/
│   └── views/
│       ├── admin/          # หน้าเว็บฝั่ง Admin
│       ├── auth/           # หน้า Login
│       ├── employee/       # หน้าเว็บฝั่ง Employee + TV Dashboard
│       └── layouts/        # Layout Templates
├── routes/
│   └── web.php             # เส้นทาง URL ทั้งหมด
├── public/                 # Static Assets (CSS, JS, Images)
├── config/                 # Config ทั้งหมด (database, app, etc.)
└── web.config              # ตั้งค่า IIS URL Rewrite
```

---

## 🔧 ความต้องการของระบบ

| รายการ             | เวอร์ชัน / รายละเอียด                |
| :----------------- | :------------------------------------ |
| PHP                | 8.5+                                  |
| Laravel            | 13.x                                  |
| ฐานข้อมูล          | Oracle (ผ่าน OCI8)                    |
| Web Server         | IIS พร้อม URL Rewrite Module          |
| Oracle Extension   | `yajra/laravel-oci8 ^13.0`            |
| Node.js            | สำหรับ Vite (build assets)            |

---

## 🗄️ การเชื่อมต่อฐานข้อมูล

โปรเจกต์เชื่อมต่อ Oracle 2 ตัว:

| Connection       | เซิร์ฟเวอร์      | Service Name | ใช้งาน                        | Charset       |
| :--------------- | :--------------- | :----------- | :---------------------------- | :------------ |
| `oracle`         | 192.1.1.240      | HRMS         | ข้อมูล HR / พนักงาน           | TH8TISASCII   |
| `oracle_intra`   | 192.1.1.241      | SAGDB        | ข้อมูลจองรถ / TV Dashboard    | WE8DEC        |

---

## 🌐 เส้นทาง URL (Routes)

### 🔑 ระบบ Authentication

| Method | URL                  | ชื่อ Route              | คำอธิบาย                |
| :----- | :------------------- | :---------------------- | :---------------------- |
| GET    | `/login`             | `login`                 | หน้า Login Admin        |
| POST   | `/login`             | `login.submit`          | ส่งข้อมูล Login Admin   |
| POST   | `/logout`            | `logout`                | Logout Admin            |
| GET    | `/employee/login`    | `employee.login`        | หน้า Login พนักงาน      |
| POST   | `/employee/login`    | `employee.login.submit` | ส่งข้อมูล Login พนักงาน |
| POST   | `/employee/logout`   | `employee.logout`       | Logout พนักงาน          |

### 🛡️ Admin Routes (ต้อง Login ก่อน)

| Method | URL         | ชื่อ Route        | คำอธิบาย             |
| :----- | :---------- | :---------------- | :------------------- |
| GET    | `/admin`    | `admin.dashboard` | แดชบอร์ดผู้ดูแลระบบ |

### 👤 Employee Routes (ต้อง Login ก่อน)

| Method | URL           | ชื่อ Route            | คำอธิบาย              |
| :----- | :------------ | :-------------------- | :-------------------- |
| GET    | `/employee`   | `employee.dashboard`  | แดชบอร์ดพนักงาน       |

### 📺 TV Dashboard (เปิดสาธารณะ — สำหรับจอแสดงผล)

| Method | URL                  | ชื่อ Route            | คำอธิบาย                       |
| :----- | :------------------- | :-------------------- | :----------------------------- |
| GET    | `/tv-dashboard`      | `tv.dashboard.index`  | หน้า TV Dashboard              |
| GET    | `/tv-dashboard/data` | `tv.dashboard.data`   | API ข้อมูลกราฟ (JSON)          |

---

## 📺 TV Dashboard

หน้าจอ TV Dashboard ออกแบบสำหรับแสดงผลบน **จอทีวี 65 นิ้ว (1920×1080)** โดยเฉพาะ:

- 🎨 **Dark Theme** — ธีมสีเข้ม อ่านง่ายจากระยะไกล
- 📊 **กราฟรายวัน / รายเดือน** — แสดงสถิติการจองรถแบบเรียลไทม์
- 📅 **เลือกวันที่ย้อนหลัง** — รองรับ Query Parameter `?date=YYYY-MM-DD`
- 🔄 **Auto-Refresh** — รีเฟรชข้อมูลอัตโนมัติ
- 📱 **Responsive** — รองรับ Tablet และ Mobile ด้วย

---

## 🚀 การติดตั้ง

### 1. ติดตั้ง Dependencies

```bash
composer install
npm install
```

### 2. ตั้งค่า Environment

```bash
cp .env.example .env
php artisan key:generate
```

แก้ไข `.env` ให้ตรงกับเซิร์ฟเวอร์:

```dotenv
APP_NAME="KPI Dashboard"
APP_URL=http://your-server/path/to/public

DB_HOST=192.1.1.240
DB_SERVICE_NAME=HRMS
DB_USERNAME=xxxxx
DB_PASSWORD=xxxxx

DB_INTRA_HOST=192.1.1.241
DB_INTRA_SERVICE=SAGDB
DB_INTRA_USERNAME=xxxxx
DB_INTRA_PASSWORD=xxxxx
```

### 3. Build Assets

```bash
npm run build
```

### 4. ตั้งค่า IIS

- ชี้ Document Root ไปที่โฟลเดอร์ `public/`
- ติดตั้ง **URL Rewrite Module** บน IIS
- ตรวจสอบไฟล์ `web.config` ในโฟลเดอร์ `public/`

---

## 📁 ไฟล์สำคัญ

| ไฟล์ | คำอธิบาย |
| :--- | :------- |
| `routes/web.php` | กำหนดเส้นทาง URL ทั้งหมดของระบบ |
| `app/Http/Controllers/Employee/TVDashboardController.php` | Controller หลักของ TV Dashboard พร้อม API ข้อมูลกราฟ |
| `resources/views/employee/tv_dashboard.blade.php` | หน้าเว็บ TV Dashboard (Blade Template) |
| `config/database.php` | ตั้งค่าการเชื่อมต่อ Oracle ทั้ง 2 ตัว |
| `web.config` | ตั้งค่า IIS URL Rewrite สำหรับ Laravel |
| `.env` | ตั้งค่า Environment (ฐานข้อมูล, App URL, ฯลฯ) |

---

## 🛠️ สำหรับนักพัฒนา

### เพิ่มโมดูลใหม่

1. สร้าง Controller ในโฟลเดอร์ `app/Http/Controllers/Admin/` หรือ `Employee/`
2. สร้าง View ในโฟลเดอร์ `resources/views/admin/` หรือ `employee/`
3. เพิ่ม Route ใน `routes/web.php` ภายใน group ที่เหมาะสม:

```php
// ภายใน Admin group
Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/new-module', [NewController::class, 'index'])->name('new-module.index');
});

// ภายใน Employee group
Route::middleware('employee')->prefix('employee')->name('employee.')->group(function () {
    Route::get('/new-module', [NewController::class, 'index'])->name('new-module.index');
});
```

### ทดสอบการเชื่อมต่อ DB

ใน `routes/web.php` มี Route ทดสอบการเชื่อมต่อ Oracle ที่ถูก Comment ไว้ สามารถเปิดใช้ได้ชั่วคราวเพื่อทดสอบ (อย่าลืมปิดก่อนขึ้น Production)

---

## 📝 หมายเหตุ

- โปรเจกต์นี้รันบน **IIS** จึงมี `web.config` สำหรับ URL Rewrite แทน `.htaccess`
- มี Static Asset Routes ใน `web.php` เป็น Workaround สำหรับ IIS ที่อาจไม่ส่ง CSS/JS ถูกต้อง
- TV Dashboard เป็น Route สาธารณะ (ไม่ต้อง Login) เพราะใช้แสดงผลบนจอทีวี
- การเชื่อมต่อ Oracle ใช้ Charset `TH8TISASCII` / `WE8DEC` เพื่อรองรับภาษาไทย
