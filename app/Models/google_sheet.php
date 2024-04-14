<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class google_sheet extends Model
{
    protected $guarded = [];

    public $timestamps = false;
    public $table = 'google_sheet';

    use HasFactory;
}
