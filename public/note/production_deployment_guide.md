# 📋 Production Deployment Guide

> Project STD — Laravel 13 + Oracle OCI8 on IIS (Windows Server)
> อัพเดตล่าสุด: 13 พ.ค. 2569

---

## 📌 สารบัญ

1. [ข้อมูลเซิร์ฟเวอร์](#1-ข้อมูลเซิร์ฟเวอร์)
2. [Prerequisites](#2-prerequisites)
3. [ตั้งค่า .env สำหรับ Production](#3-ตั้งค่า-env-สำหรับ-production)
4. [IIS Web.config](#4-iis-webconfig)
5. [ขั้นตอนการ Deploy](#5-ขั้นตอนการ-deploy)
6. [Cache & Optimize](#6-cache--optimize)
7. [Sync Tool (sync.ps1)](#7-sync-tool)
8. [Security Checklist](#8-security-checklist)
9. [Troubleshooting](#9-troubleshooting)
10. [Rollback](#10-rollback)

---

## 1. ข้อมูลเซิร์ฟเวอร์

| รายการ           | ค่า                                                         |
| :--------------- | :---------------------------------------------------------- |
| **Server IP**    | `192.1.1.49`                                                |
| **Web Server**   | IIS (Internet Information Services)                         |
| **PHP Version**  | PHP 8.5 (`C:\PHP\PHP85\php-cgi.exe`)                       |
| **Framework**    | Laravel 13 (via FastCGI)                                    |
| **Oracle Client**| OCI8 Extension (`yajra/laravel-oci8 ^13.0`)                |
| **Server Path**  | `\\192.1.1.49\intra_dev\Intranet\PHP85\dev\project_std`    |
| **Public URL**   | `http://192.1.1.49/Intranet/PHP85/dev/project_std/public`  |

---

## 2. Prerequisites

### บน Server (192.1.1.49)

- [x] PHP 8.5 ติดตั้งและลงทะเบียน FastCGI ใน IIS
- [x] PHP Extensions ที่จำเป็น:
  - `oci8` — เชื่อมต่อ Oracle Database
  - `mbstring` — รองรับ UTF-8 / Thai
  - `openssl` — Laravel encryption
  - `pdo` — Database abstraction
  - `fileinfo` — File upload validation
  - `tokenizer`, `xml`, `ctype`, `json`
- [x] Oracle Instant Client ติดตั้งและอยู่ใน PATH
- [x] IIS URL Rewrite Module ติดตั้งแล้ว
- [x] Composer ติดตั้งแล้ว (สำหรับ `composer install`)

### บน Local (D:\dev_laravel\project_std)

- [x] Git repository initialized
- [x] `composer install` สำเร็จ
- [x] `sync.ps1` พร้อมใช้งาน

---

## 3. ตั้งค่า .env สำหรับ Production

> ⚠️ **สำคัญ:** ไฟล์ `.env` **ไม่ถูก sync** ไปยัง Server (อยู่ใน skip list ของ sync.ps1)
> ต้องสร้าง/แก้ไขบน Server โดยตรง

```env
# ===== Application =====
APP_NAME="Project STD"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://192.1.1.49/Intranet/PHP85/dev/project_std/public

# ===== Logging =====
LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=error

# ===== Session =====
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_PATH=/

# ===== Oracle Database — HRMS (192.1.1.240) =====
DB_CONNECTION=oracle
DB_HOST=192.1.1.240
DB_PORT=1521
DB_DATABASE=
DB_SERVICE_NAME=HRMS
DB_USERNAME=HRMSIT
DB_PASSWORD=ITHRMS

# ===== Oracle Database — INTRA (192.1.1.241) =====
DB_INTRA_HOST=192.1.1.241
DB_INTRA_PORT=1521
DB_INTRA_SERVICE=SAGDB
DB_INTRA_USERNAME=WEB
DB_INTRA_PASSWORD=web123
```

### ค่าที่ต้องเปลี่ยนจาก Development

| ค่า              | Development       | Production          |
| :--------------- | :---------------- | :------------------ |
| `APP_ENV`        | `local`           | `production`        |
| `APP_DEBUG`      | `true`            | **`false`**         |
| `APP_URL`        | `http://localhost` | URL เต็มของ Server |
| `LOG_LEVEL`      | `debug`           | `error`             |

---

## 4. IIS Web.config

โปรเจคนี้ใช้ **2 ไฟล์** web.config:

### 4.1 Root web.config (`/project_std/web.config`)

ทำหน้าที่ Rewrite URL ทั้งหมดไปที่ `public/index.php` (เพื่อให้ Laravel จัดการ routing)

```xml
<?xml version="1.0" encoding="UTF-8"?>
<configuration>
    <system.webServer>
        <rewrite>
            <rules>
                <rule name="Laravel Force public" stopProcessing="true">
                    <match url="^(.*)$" ignoreCase="false" />
                    <conditions logicalGrouping="MatchAll">
                        <add input="{REQUEST_FILENAME}" matchType="IsFile" negate="true" />
                        <add input="{REQUEST_FILENAME}" matchType="IsDirectory" negate="true" />
                    </conditions>
                    <action type="Rewrite" url="public/index.php" />
                </rule>
            </rules>
        </rewrite>
    </system.webServer>
</configuration>
```

### 4.2 Public web.config (`/project_std/public/web.config`)

ลงทะเบียน PHP FastCGI handler และ rewrite ภายใน public folder

```xml
<?xml version="1.0" encoding="UTF-8"?>
<configuration>
    <system.webServer>
        <handlers>
            <remove name="PHP85_via_FastCGI" />
            <add name="PHP85_via_FastCGI" path="*.php" verb="GET,HEAD,POST"
                 modules="FastCgiModule"
                 scriptProcessor="C:\PHP\PHP85\php-cgi.exe"
                 resourceType="Either" />
        </handlers>
        <rewrite>
            <rules>
                <rule name="Laravel Routing" stopProcessing="true">
                    <match url="^(.*)$" ignoreCase="false" />
                    <conditions logicalGrouping="MatchAll">
                        <add input="{REQUEST_FILENAME}" matchType="IsFile" negate="true" />
                    </conditions>
                    <action type="Rewrite" url="index.php" />
                </rule>
            </rules>
        </rewrite>
    </system.webServer>
</configuration>
```

### Static Assets (IIS Workaround)

เนื่องจาก IIS อาจ route ไฟล์ static ผิด, โปรเจคมี route สำรองใน `routes/web.php`:

```php
// CSS files
Route::get('css/{file}', function ($file) { ... })->where('file', '.*');

// JS files
Route::get('js/{file}', function ($file) { ... })->where('file', '.*');

// Favicon
Route::get('favicon.ico', function () { ... });
```

---

## 5. ขั้นตอนการ Deploy

### 5.1 Deploy ครั้งแรก (First-time Setup)

```powershell
# 1. Copy โปรเจคไปยัง Server
powershell -ExecutionPolicy Bypass -File .\sync.ps1 push

# 2. เข้าไปที่ Server path (RDP หรือ UNC)
#    \\192.1.1.49\intra_dev\Intranet\PHP85\dev\project_std

# 3. ติดตั้ง dependencies บน Server
composer install --no-dev --optimize-autoloader

# 4. สร้างไฟล์ .env บน Server (copy จากตัวอย่างด้านบน)

# 5. สร้าง APP_KEY (ถ้ายังไม่มี)
php artisan key:generate

# 6. สร้าง cache
php artisan optimize

# 7. ตรวจสอบ storage permissions
#    ให้แน่ใจว่า IIS user (IIS_IUSRS) มีสิทธิ์ write ใน:
#    - storage/
#    - bootstrap/cache/
```

### 5.2 Deploy อัพเดตปกติ (Routine Update)

```powershell
# 1. ดูก่อนว่าจะ sync อะไร
powershell -ExecutionPolicy Bypass -File .\sync.ps1 push -dry

# 2. ถ้าพร้อม — push ขึ้น Server
powershell -ExecutionPolicy Bypass -File .\sync.ps1 push

# 3. ล้าง cache บน Server (รันบน Server หรือผ่าน UNC)
php artisan optimize:clear
php artisan optimize
```

### 5.3 ดึงไฟล์จาก Server มา Local

```powershell
# Preview ก่อน
powershell -ExecutionPolicy Bypass -File .\sync.ps1 pull -dry

# Pull จริง
powershell -ExecutionPolicy Bypass -File .\sync.ps1 pull
```

---

## 6. Cache & Optimize

### สร้าง Cache (Production)

```powershell
# รวมทุกอย่างในคำสั่งเดียว
php artisan optimize

# หรือแยกทำทีละตัว:
php artisan config:cache     # cache config จาก .env
php artisan route:cache      # cache route definitions
php artisan view:cache       # cache compiled Blade templates
```

### ล้าง Cache

```powershell
# ล้างทั้งหมด
php artisan optimize:clear

# หรือแยกทำ:
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

> 💡 **ต้องล้าง cache ทุกครั้ง** หลังจากแก้ไข `.env`, `config/*.php`, หรือ `routes/web.php`

---

## 7. Sync Tool

### ไฟล์ที่ไม่ถูก Sync (Excluded)

| ประเภท      | รายการ                                              |
| :---------- | :-------------------------------------------------- |
| **Folders** | `.git`, `vendor`, `node_modules`, `storage`, `bkup`, `.gemini` |
| **Files**   | `.env`, `.env_cilen`                                |

### วิธีรัน

```powershell
# ถ้า Execution Policy ยังไม่ได้ตั้ง:
powershell -ExecutionPolicy Bypass -File .\sync.ps1

# หรือตั้ง policy ถาวร (ครั้งเดียว):
Set-ExecutionPolicy RemoteSigned -Scope CurrentUser
```

### Sync Mode

| คำสั่ง                   | การทำงาน                           |
| :---------------------- | :--------------------------------- |
| `.\sync.ps1 pull`      | Server → Local (ดึงมา)            |
| `.\sync.ps1 push`      | Local → Server (ส่งขึ้น)          |
| `.\sync.ps1 pull -dry` | Preview pull (ไม่ทำจริง)          |
| `.\sync.ps1 push -dry` | Preview push (ไม่ทำจริง)          |

> ⚠️ sync.ps1 ใช้ Robocopy `/MIR` — ไฟล์ที่ไม่มีในต้นทางจะถูก **ลบ** ที่ปลายทาง (ยกเว้น excluded items)

---

## 8. Security Checklist

### ก่อน Deploy ขึ้น Production

- [ ] `APP_DEBUG=false` ใน `.env`
- [ ] `APP_ENV=production` ใน `.env`
- [ ] `LOG_LEVEL=error` (ไม่ใช่ `debug`)
- [ ] Comment out หรือลบ test routes (`/test/db`, `/test/db/tables`)
- [ ] ตรวจสอบว่าไม่มี debug route หลุดขึ้น production
- [ ] `.env` ไม่ถูก expose ผ่าน web (IIS rewrite ป้องกันอยู่)
- [ ] `storage/logs/` ไม่สามารถเข้าถึงจาก web
- [ ] `APP_KEY` ถูก generate แล้ว

### Route ที่ต้องระวัง

```php
// ❌ ต้อง comment out หรือลบก่อนขึ้น Production:
// Route::prefix('test')->group(function () { ... });

// ✅ Route ปกติที่ใช้งาน:
// /login              — Admin Login
// /admin              — Admin Dashboard (protected)
// /employee/login     — Employee Login
// /employee           — Employee Dashboard (protected)
// /tv-dashboard       — TV Dashboard (public)
// /tv-dashboard/data  — TV Dashboard API (public)
```

---

## 9. Troubleshooting

### ปัญหาที่พบบ่อย

#### 9.1 หน้าขาว / 500 Error

```powershell
# 1. ตรวจสอบ log
# ดูไฟล์ที่: storage/logs/laravel.log

# 2. ล้าง cache
php artisan optimize:clear

# 3. ตรวจสอบ .env มีค่าครบหรือไม่
# 4. ตรวจสอบ storage/ มีสิทธิ์ write หรือไม่
```

#### 9.2 CSS/JS โหลดไม่ได้

- ตรวจสอบ `web.config` ทั้ง 2 ไฟล์ถูกต้อง
- ตรวจสอบ IIS URL Rewrite Module ติดตั้งแล้ว
- Route สำรองใน `web.php` จะจัดการ static assets

#### 9.3 Oracle Connection Error

```powershell
# ทดสอบใน Tinker
php artisan tinker
# >>> DB::connection('oracle')->select("SELECT SYSDATE FROM DUAL");
# >>> DB::connection('oracle_intra')->select("SELECT SYSDATE FROM DUAL");
```

- ตรวจสอบ Oracle Instant Client อยู่ใน PATH
- ตรวจสอบ `php.ini` enable `extension=oci8`
- ตรวจสอบ firewall เปิด port 1521

#### 9.4 ภาษาไทยแสดงผลผิด

- Oracle HRMS ใช้ charset `TH8TISASCII`
- Oracle INTRA ใช้ charset `WE8DEC`
- ตรวจสอบ `NLS_LANG` environment variable บน Server

#### 9.5 Sync script รันไม่ได้ (Execution Policy)

```powershell
# วิธีที่ 1: Bypass ครั้งเดียว
powershell -ExecutionPolicy Bypass -File .\sync.ps1

# วิธีที่ 2: ตั้งค่าถาวร
Set-ExecutionPolicy RemoteSigned -Scope CurrentUser
```

#### 9.6 IIS ไม่ตอบสนองหลัง Deploy

```powershell
# รีสตาร์ท IIS (ต้องเป็น Admin บน Server)
iisreset

# หรือรีสตาร์ทเฉพาะ Application Pool ผ่าน IIS Manager
```

---

## 10. Rollback

### วิธี Rollback กลับไป version ก่อนหน้า

```powershell
# 1. บน Local — กลับไป commit ก่อนหน้า
git log -n 5 --oneline
git checkout <commit-hash>

# 2. Push version เก่าขึ้น Server
powershell -ExecutionPolicy Bypass -File .\sync.ps1 push

# 3. ล้าง cache บน Server
php artisan optimize:clear
php artisan optimize

# 4. กลับมา branch ปัจจุบัน (บน Local)
git checkout main
```

---

## 📂 โครงสร้างโปรเจค (สรุป)

```
project_std/
├── app/
│   └── Http/
│       ├── Controllers/
│       │   ├── Admin/            # Admin controllers
│       │   ├── Auth/             # Login controllers
│       │   └── Employee/         # Employee + TV Dashboard controllers
│       └── Middleware/
│           ├── AdminMiddleware.php
│           └── EmployeeMiddleware.php
├── config/
│   └── database.php              # Oracle connections (oracle, oracle_intra)
├── public/
│   ├── css/                      # Static CSS
│   ├── note/                     # Documentation (this file)
│   ├── index.php                 # Entry point
│   ├── web.config                # IIS rewrite (public level)
│   └── .htaccess                 # Apache fallback
├── resources/views/
│   ├── admin/                    # Admin Blade templates
│   └── employee/                 # Employee + TV Dashboard templates
├── routes/
│   └── web.php                   # All route definitions
├── storage/                      # Logs, cache, sessions
├── sync.ps1                      # Sync tool (Local <-> Server)
├── web.config                    # IIS rewrite (root level)
└── .env                          # Environment config (NOT synced!)
```
