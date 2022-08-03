<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormEntry extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    const FORM_INVESTOR_OPTIN = 'Investor Opt-in';
    const FORM_CONTACT = 'Contact';
}
