<?php

namespace App\Console\Commands;

use App\Year;
use App\Http\Controllers\ScraperController;
use Illuminate\Console\Command;

class ImportIssues extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:issues {year?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import basic issue information for every year, or optionally a single given year.';

    /**
     * Laravel Collection of available years.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $years;

    /**
     * Instaniate the app's Scraper Controller
     *
     * @var \App\Http\Controller\ScraperController
     */
    protected $scraper;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->scraper = new ScraperController();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->years = Year::all();

        if ($this->argument('year')) {
            $this->import($this->argument('year'));
        } else {
            if ($this->confirm('You are about to import basic data for all issues.  This will take a few minutes.  Are you sure you want to continue?')) {
                foreach ($this->years as $year) {
                    $this->import($year->year);
                }
            }
        }

        return 0;
    }

    /**
    * Import the issues.
    *
    * @param $year
    * @return void
    */
    protected function import($year)
    {
        if ($year > 1800 && $year < 3000) {
            $this->scraper->issuesByYear($year);
            $this->info('Imported issues for year ' . $year);
        } else {
            $this->error('Please enter a valid year between 1800 and 3000');
        }
    }
}
