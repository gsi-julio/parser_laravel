<?php

namespace App\Console\Commands;

use App\Http\Controllers\ParserController;
use Illuminate\Console\Command;

class CheckNumberToday extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:checkNumber';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comprueba la salida de números de la Lotería de Florida';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $controller = new ParserController();
        $controller->sendNumber();
    }


}
