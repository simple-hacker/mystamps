<?php

namespace App\Console\Commands;

use App\Issue;
use Illuminate\Console\Command;
use App\Http\Controllers\ScraperController;

class ImportIssue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:issue {cgbs_issue?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import all issue and stamp data for all issues, or optionally for a specific issue cgbs_issue.';

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
        $this->issues = Issue::all()->sortBy('cgbs_issue');

        if ($this->argument('cgbs_issue')) {
            $this->import($this->argument('cgbs_issue'));
        } else {
            if ($this->confirm('You are about to import all issue and stamp data.  This will take a LONG TIME.  Are you sure you want to continue?')) {

                $start = 0;

                if ($this->confirm('Do you want to start from a certain point?')) {
                    $start = $this->ask('Where do you want to start from?');
                }

                foreach ($this->issues as $issue) {
                    if ($issue->cgbs_issue) {
                        if ($issue->cgbs_issue >= $start) {
                            $this->import($issue->cgbs_issue);
                        }
                    } else {
                        $this->error('Skipping because the issue doesn\'t have a cgbs_issue.');
                    }
                }
            }
        }

        return 0;
    }

    /**
    * Import the issue and stamps.
    *
    * @param $cgbs_issue
    * @return void
    */
    protected function import($cgbs_issue)
    {
        $this->scraper->issue($cgbs_issue);
        $this->info('Imported issue and stamps for cgbs_issue:' . $cgbs_issue);
    }
}
