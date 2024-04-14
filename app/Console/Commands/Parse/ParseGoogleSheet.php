<?php

namespace App\Console\Commands\Parse;

use App\Interfaces\Api\Api;
use App\Services\Parsers\GoogleSheet as GoogleSheetParser;
use Illuminate\Console\Command;

class  ParseGoogleSheet extends Command
{
    protected $signature = 'parse:google-sheet {spreadsheet_id} {range}';

    protected $description = 'Run parsing from Google Sheet';

    public function __construct(protected Api $api){
        parent::__construct();
    }

    public function handle()
    {
        $GoogleSheetParser = new GoogleSheetParser($this->api);
        $GoogleSheetParser->getData([
            'spreadsheet_id' => $this->argument('spreadsheet_id'),
            'range' => $this->argument('range'),
        ])->setData();

        $this->info('Парсинг завершен');
    }
}
