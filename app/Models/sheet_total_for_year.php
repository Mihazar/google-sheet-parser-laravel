<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class sheet_total_for_year extends Model
{
    protected $guarded = [];

    public $timestamps = false;
    public $table = 'sheet_total_for_year';

    use HasFactory;

    public function getTotal($total_for_year){
        return $this->where('object_id', $total_for_year['object_id'])->where('object_type', $total_for_year['object_type'])->where('spreadsheet_id', $total_for_year['spreadsheet_id'])->where('sheet_id', $total_for_year['sheet_id'])->first();
    }

    public function updateTotal($id, $total){
        return $this->where('id', $id)->update(['total' => $total]);
    }
}
