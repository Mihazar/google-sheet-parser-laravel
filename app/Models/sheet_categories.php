<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class sheet_categories extends Model
{
    protected $guarded = [];

    public $timestamps = false;
    public $table = 'sheet_categories';

    use HasFactory;

    public function getCategorie($category, $spreadsheet_id, $sheet_id){
        return $this->where('name', $category)->where('spreadsheet_id', $spreadsheet_id)->where('sheet_id', $sheet_id)->first();
    }
}
