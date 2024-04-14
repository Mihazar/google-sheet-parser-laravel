<?php

namespace App\Services\Api;

use App\Interfaces\Api\Api;
use Google\Client as GoogleClient;
use Google\Service\Sheets as GoogleSheets;
use function PHPUnit\Framework\throwException;

class GoogleSheet implements Api {

    private $api;

    public function auth() : object
    {
        $client = new GoogleClient();

        $client->setApplicationName(env('GOOGLE_SHEET_APPLICATION_NAME'));
        $client->setDeveloperKey(env('GOOGLE_SHEET_DEVELOPER_KEY'));

        $this->api = new GoogleSheets($client);

        return $this;
    }

    public function getData(array $params) : array
    {
        $sheet_values = $this->api->spreadsheets_values->get($params['spreadsheet_id'], $params['range'])->values;
        return [
            'spreadsheet_info' => [
                'spreadsheet_id' => $params['spreadsheet_id'],
                'range' => $params['range']
            ],
            'spreadsheet_values' => $sheet_values
        ];
    }

    public function setData()
    {

    }
}
