<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;

class OrganizationsController extends Controller
{
    public function index() {
        return Organization::first();
    }

    public function show($id) {
        return Organization::findOrFail($id);
    }
}
