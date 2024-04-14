<?php

namespace App\Services\Parsers;

use App\Interfaces\Parsers\Parsers;
use App\Interfaces\Api\Api;
use App\Models\google_spreadsheet;
use App\Models\google_sheet;
use App\Models\sheet_categories;
use App\Models\sheet_products;
use App\Models\sheet_total_for_month;
use App\Models\sheet_total_for_year;

class GoogleSheet implements Parsers
{

    private const OBJECT_TYPE_PRODUCT = 'P';
    private const OBJECT_TYPE_CATEGORY = 'C';
    private object $api;
    private array $parsing_data;

    public function __construct(Api $api)
    {

        $this->api = $api->auth();
        $this->google_spreadsheet = new google_spreadsheet();
        $this->google_sheet = new google_sheet();
        $this->sheet_categories = new sheet_categories();
        $this->sheet_products = new sheet_products();
        $this->sheet_total_for_month = new sheet_total_for_month();
        $this->sheet_total_for_year = new sheet_total_for_year();

    }

    public function getData($params): object
    {
        $this->parsing_data = $this->api->getData($params);

        return $this;
    }

    public function toArray(){
        return $this->parsing_data;
    }

    public function setData() : bool
    {
        if(!empty($this->parsing_data)){

            $spreadsheet_id = $this->google_spreadsheet->firstOrCreate([
                'spreadsheet_google_id' => $this->parsing_data['spreadsheet_info']['spreadsheet_id'],
                'year' => date('Y', time())
            ])->id;

            $sheet_id = $this->google_sheet->firstOrCreate([
                'spreadsheet_id' => $spreadsheet_id,
                'range' => $this->parsing_data['spreadsheet_info']['range'],
                'title' => $this->parsing_data['spreadsheet_values'][0][0]
            ])->id;

            unset($this->parsing_data['spreadsheet_values'][0][0]);
            unset($this->parsing_data['spreadsheet_values'][0][1]);

            $prepared_data = $this->prepareData();
            $this->insertData($prepared_data, $spreadsheet_id, $sheet_id);

            return true;

        } else{

            throw new Exception('Data not found');

        }
    }

    private function insertData($prepared_data, $spreadsheet_id, $sheet_id){
        $categories = [];
        $products = [];

        $months = $this->getMonths();
        $total_key = 13;

        foreach ($prepared_data as $category => $row_data){
            $saved_category = $this->sheet_categories->getCategorie($category, $spreadsheet_id, $sheet_id);
            if(!$saved_category){
                $categories[$category] = $this->sheet_categories->create([
                    'spreadsheet_id' => $spreadsheet_id,
                    'sheet_id' => $sheet_id,
                    'name' => $category
                ])->id;
            } else{
                $categories[$category] = $saved_category['id'];
            }

            foreach ($row_data as $product_data){
                $saved_product = $this->sheet_products->getProduct($product_data[0], $categories[$category], $spreadsheet_id, $sheet_id);

                if(!$saved_product){
                    $products[$category] = $this->sheet_products->create([
                        'spreadsheet_id' => $spreadsheet_id,
                        'sheet_id' => $sheet_id,
                        'category_id' => $categories[$category],
                        'name' => $product_data[0],
                        'extra_info' => $this->getExtraInfo($product_data)
                    ])->id;
                } else{
                    $products[$category] = $saved_product['id'];
                }

                foreach ($months as $month_key => $month) {
                    $total = !empty($product_data[$month_key]) ? (double)str_replace(['$', ','], '', $product_data[$month_key]) : 0;
                    if($product_data[0] != 'Total'){
                        $total_for_month = [
                            'month' => $month,
                            'object_id' => $products[$category],
                            'object_type' => self::OBJECT_TYPE_PRODUCT,
                            'spreadsheet_id' => $spreadsheet_id,
                            'sheet_id' => $sheet_id,
                            'total' => $total
                        ];
                    } else{
                        $total_for_month = [
                            'object_id' => $categories[$category],
                            'object_type' => self::OBJECT_TYPE_CATEGORY,
                            'month' => $month,
                            'spreadsheet_id' => $spreadsheet_id,
                            'sheet_id' => $sheet_id,
                            'total' => $total
                        ];
                    }
                    $saved_total_for_month = $this->sheet_total_for_month->getTotal($total_for_month);
                    if($saved_total_for_month){
                        if($saved_total_for_month['total'] != $total_for_month['total']){
                            $this->sheet_total_for_month->updateTotal($saved_total_for_month['id'], $total_for_month['total']);
                        }
                    } else{
                        $this->sheet_total_for_month->create($total_for_month);
                    }
                }

                $total = !empty($product_data[$total_key]) ? (double)str_replace(['$', ','], '', $product_data[$total_key]) : 0;

                if($product_data[0] != 'Total'){
                    $total_for_year = [
                        'object_id' => $products[$category],
                        'object_type' => self::OBJECT_TYPE_PRODUCT,
                        'spreadsheet_id' => $spreadsheet_id,
                        'sheet_id' => $sheet_id,
                        'total' => $total
                    ];
                } else{
                    $total_for_year = [
                        'object_id' => $categories[$category],
                        'object_type' => self::OBJECT_TYPE_CATEGORY,
                        'spreadsheet_id' => $spreadsheet_id,
                        'sheet_id' => $sheet_id,
                        'total' => $total
                    ];
                }
                $saved_total = $this->sheet_total_for_year->getTotal($total_for_year);
                if($saved_total){
                    if($saved_total['total'] != $total_for_year['total']){
                        $this->sheet_total_for_year->updateTotal($saved_total['id'], $total_for_year['total']);
                    }
                } else{
                    $this->sheet_total_for_year->create($total_for_year);
                }
            }
        }
    }

    private function getExtraInfo($product_data){
        if(isset($product_data[15])){
            return json_encode(explode('|', $product_data[15]));
        } else{
            return json_encode([]);
        }
    }

    private function getMonths(){
        return [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December'
        ];
    }

    private function prepareData(){
        $values = $this->parsing_data['spreadsheet_values'];
        $products = [];
        $last_category = '';

        foreach ($values as $key => $value){

            if(empty($value)){
                continue;
            }

            if($value[0] == 'CO-OP'){
                break;
            }

            if(count($value) == 1){
                $last_category = $value[0];
                unset($value[0]);
            }

            if(!empty($value[0])){
                $products[$last_category][] = $value;
            }
        }

        return $products;
    }

}
