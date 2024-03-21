<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;

class StatusController extends Controller
{
    public function index()
    {
        return StatusEnum::values();
    }
}
