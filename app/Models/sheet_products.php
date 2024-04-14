<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class sheet_products extends Model
{
    protected $guarded = [];

    public $timestamps = false;
    public $table = 'sheet_products';

    use HasFactory;

    public function getProduct($product_name, $category_id, $spreadsheet_id, $sheet_id){
        return $this->where('name', $product_name)->where('category_id', $category_id)->where('spreadsheet_id', $spreadsheet_id)->where('sheet_id', $sheet_id)->first();
    }
}
