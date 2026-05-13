<?php

use App\Providers\AppServiceProvider;
use Yajra\Oci8\Oci8ServiceProvider;

return [
    AppServiceProvider::class,
    Yajra\Oci8\Oci8ServiceProvider::class,      // เพิ่มบรรทัดนี้ถ้ายังไม่มี
];
