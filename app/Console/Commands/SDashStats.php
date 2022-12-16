<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SDashStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sdash:stats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Print statistics about the application, such as the number of groups, SmartFigures, & users.';

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
     * @return int
     */
    public function handle()
    {
        $tablesOfInterest = [
            'groups',
            'panels',
            'users',
        ];
        $result = array();
        foreach($tablesOfInterest as $table)
        {
            $result[$table] = $this->get_stats($table);
        }
        print(json_encode($result, JSON_PRETTY_PRINT));
        return 0;
    }

    protected function get_stats($table)
    {
        return array(
            'total' => DB::table($table)->count(),
            'byCreationDate' => DB::table($table)->select(DB::raw('date(created_at) as creation_date, count(*) as total'))->groupBy('creation_date')->get(),
        );
    }
}
