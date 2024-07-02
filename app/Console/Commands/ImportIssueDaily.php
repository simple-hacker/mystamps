<?php

namespace App\Console\Commands;

use App\Year;
use App\Issue;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\ScraperController;

class ImportIssueDaily extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily cron job to check if there are any new issues and import them.';

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
     * Get current year.
     *
     * @var integer
     */
    protected $year;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->year = Year::firstOrCreate(['year' => date('Y')]);
        $this->scraper = new ScraperController();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->issues = Issue::withTrashed()
                            ->where('year', $this->year->year)
                            ->whereNotNull('cgbs_issue')
                            ->pluck('cgbs_issue')
                            ->toArray();

        $this->info('Checking for any new issues that we have not imported...');
        Log::info('Daily issue import is running.');

        $cgbs_issues = array_diff($this->scraper->cgbsIssuesByYear($this->year->year), $this->issues);

        foreach($cgbs_issues as $cgbs_issue) {
            $this->scraper->issue($cgbs_issue);
            $this->info('Imported new cgbs_issue ' . $cgbs_issue);
            Log::info('Imported new cgbs_issue ' . $cgbs_issue);
        }

        $this->info('Finished cron job');
        Log::info('Daily issue import has ended.');

        return 0;

    }
}
