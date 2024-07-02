<?php

namespace App\Console\Commands;

use App\Issue;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\ScraperController;

class ImportStampsOnReleaseDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:releasedate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import the issue\'s stamps on it\'s release date.';

    /**
     * Laravel Collection of issues.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $issues;

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
        // Get all issues with release date of today
        $this->issues = Issue::where('release_date', Carbon::today())->get();

        Log::info($this->issues->count() . ' issues are released today.');

        foreach($this->issues as $issue) {
            // If issue does not have any stamps then import
            if ($issue->stamps()->count() == 0) {
                $this->scraper->issue($issue->cgbs_issue);
                $this->info('Imported stamps for ' . $issue->title);
                Log::info('Imported stamps for ' . $issue->title);
            }
        }

        return 0;
    }
}
