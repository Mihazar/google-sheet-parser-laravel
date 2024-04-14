<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class sheet_total_for_month extends Model
{
    protected $guarded = [];

    public $timestamps = false;
    public $table = 'sheet_total_for_month';

    use HasFactory;

    public function getTotal($total_for_month){
        return $this->where('object_id', $total_for_month['object_id'])->where('month', $total_for_month['month'])->where('object_type', $total_for_month['object_type'])->where('spreadsheet_id', $total_for_month['spreadsheet_id'])->where('sheet_id', $total_for_month['sheet_id'])->first();
    }

    public function updateTotal($id, $total){
        return $this->where('id', $id)->update(['total' => $total]);
    }
}
