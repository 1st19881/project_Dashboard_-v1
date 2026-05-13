# 📌 คำสั่งที่ใช้บ่อย — Project STD (Laravel)

---

## 🔄 Sync ไฟล์ (Local <-> Server)

```powershell
# ต้องรันที่ Local เท่านั้น (D:\dev_laravel\project_std)

# แสดงเมนูให้เลือก
.\sync.ps1

# ดึงจาก Server มา Local
.\sync.ps1 pull

# ส่งจาก Local ไป Server
.\sync.ps1 push

# ดูก่อนว่าจะ copy อะไร (ไม่ทำจริง)
.\sync.ps1 pull -dry
.\sync.ps1 push -dry
```

> ⚠️ ถ้ารัน script ไม่ได้ (Execution Policy) ให้รันครั้งเดียว:
> ```powershell
> Set-ExecutionPolicy RemoteSigned -Scope CurrentUser
> ```

---

## 🚀 Laravel — คำสั่งพื้นฐาน

```powershell
# รัน dev server (local development)
php artisan serve

# ล้าง cache ทั้งหมด
php artisan optimize:clear

# ล้าง cache แยกตัว
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# สร้าง cache สำหรับ production (เร็วขึ้น)
php artisan optimize

# ดู route ทั้งหมด
php artisan route:list

# ดู route เฉพาะที่ต้องการ
php artisan route:list --path=tv-dashboard
php artisan route:list --path=admin
```

---

## 🛠️ Artisan — สร้างไฟล์

```powershell
# สร้าง Controller
php artisan make:controller Admin/NewController
php artisan make:controller Employee/NewController

# สร้าง Middleware
php artisan make:middleware NewMiddleware

# สร้าง Model
php artisan make:model NewModel

# สร้าง Request (Form Validation)
php artisan make:request NewRequest
```

---

## 📦 Composer & NPM

```powershell
# ติดตั้ง PHP dependencies
composer install

# อัพเดต PHP dependencies
composer update

# ติดตั้ง JS dependencies
npm install

# Build assets (CSS/JS) สำหรับ production
npm run build

# รัน Vite dev server (hot reload)
npm run dev
```

---

## 🗄️ ทดสอบ Database

```powershell
# เข้า Tinker (PHP REPL)
php artisan tinker

# ทดสอบ Oracle connection ใน Tinker:
# DB::connection('oracle')->select("SELECT SYSDATE FROM DUAL");
# DB::connection('oracle_intra')->select("SELECT SYSDATE FROM DUAL");
```

---

## 📂 Path สำคัญ

| รายการ          | Path                                                              |
| :-------------- | :---------------------------------------------------------------- |
| **Local**       | `D:\dev_laravel\project_std`                                      |
| **Server**      | `\\192.1.1.49\intra_dev\Intranet\PHP85\dev\project_std`          |
| **URL (Web)**   | `http://192.1.1.49/Intranet/PHP85/dev/project_std/public`        |
| **TV Dashboard**| `http://192.1.1.49/Intranet/PHP85/dev/project_std/public/tv-dashboard` |

---

## 🌐 URL ที่ใช้บ่อย

| หน้า              | URL                                          |
| :---------------- | :------------------------------------------- |
| Login Admin       | `.../public/login`                           |
| Login Employee    | `.../public/employee/login`                  |
| Admin Dashboard   | `.../public/admin`                           |
| Employee Dashboard| `.../public/employee`                        |
| TV Dashboard      | `.../public/tv-dashboard`                    |
| TV Dashboard API  | `.../public/tv-dashboard/data`               |

---

## 🔧 IIS — แก้ปัญหาบ่อย

```powershell
# รีสตาร์ท IIS (ต้องรันบน Server เป็น Admin)
iisreset

# ล้าง Laravel cache หลัง deploy
php artisan optimize:clear
```

---

## 📝 Git

```powershell
# ดูสถานะไฟล์ที่เปลี่ยน
git status

# ดู diff
git diff

# Commit
git add .
git commit -m "ข้อความ commit"

# ดู log ล่าสุด 5 รายการ
git log -n 5 --oneline
```
