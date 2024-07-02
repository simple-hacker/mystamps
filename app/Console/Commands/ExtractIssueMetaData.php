<?php

namespace App\Console\Commands;

use App\Issue;
use App\IssueCategory;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;

class ExtractIssueMetaData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'issues:metadata {issue?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extract issue metadata from description';

    protected $categories;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->categories = IssueCategory::all();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $issueId = $this->argument('issue');

        $issues = ($issueId) ? Issue::findOrFail($issueId) : Issue::all();

        $count = ($issues instanceof Issue) ? 1 : $issues->count();

        if ($count === 1) {
            $this->extractMetaData($issues);
        } else {

            $bar = $this->output->createProgressBar($count);

            $bar->start();

            $issues->each(function ($issue) use ($bar) {
                $this->extractMetaData($issue);
                $bar->advance();
            });

            $this->info('');
            $bar->finish();
        }

        return 0;
    }

    private function extractMetaData(&$issue)
    {
        $category = "/(Commemorative|Definitive|Post & Go|Machin Definitive)/i";
        $designer = "/Designed by (.*?)(?:<br\s*\/>)(?:\\r)/i";
        $size = "/Size (.*?)(?:<br\s*\/>)(?:\\r)/i";
        $printer = "/Printed by (.*?)(?:<br\s*\/>)(?:\\r)/i";
        $print_process = "/Print Process (.*?)(?:<br\s*\/>)(?:\\r)/i";
        $perforations = "/Perforations (.*?)(?:<br\s*\/>)(?:\\r)/i";
        $gum = "/Gum (.*?)(?:<br\s*\/>)(?:\\r)/i";

        $issue->description = nl2br($issue->description);
        // Add neew line to end of string to be able to capture last item
        $issue->description .= "<br />\r";

        // Release Date
        $release_date = Carbon::create($issue->release_date);

        if ($release_date->greaterThan(Carbon::create('6 February 1952'))) {
            $issue->monarch_id = 6;
        } elseif ($release_date->betweenIncluded(Carbon::create('11 December 1936'), Carbon::create('5 February 1952'))) {
            $issue->monarch_id = 5;
        } elseif ($release_date->betweenIncluded(Carbon::create('20 January 1936'), Carbon::create('10 December 1936'))) {
            $issue->monarch_id = 4;
        } elseif ($release_date->betweenIncluded(Carbon::create('6 May 1910'), Carbon::create('19 January 1936'))) {
            $issue->monarch_id = 3;
        } elseif ($release_date->betweenIncluded(Carbon::create('22 January 1901'), Carbon::create('5 May 1910'))) {
            $issue->monarch_id = 2;
        } elseif ($release_date->betweenIncluded(Carbon::create('20 June 1837'), Carbon::create('21 January 1901'))) {
            $issue->monarch_id = 1;
        }

        preg_match($category, $issue->description, $categoryMatches);
        if ($categoryMatches) {
            $findCategory = trim($categoryMatches[1]);
            $foundCategory = $this->categories->first(function ($category) use ($findCategory) {
                return strtolower($findCategory) === strtolower($category->category);
            });
            $issue->category_id = $foundCategory->id ?? null;
            $issue->description = str_replace($categoryMatches[0], '', $issue->description);
        }

        preg_match($designer, $issue->description, $designerMatches);
        if ($designerMatches) {
            $issue->designer = trim($designerMatches[1]);
            $issue->description = str_replace($designerMatches[0], '', $issue->description);
        }

        preg_match($size, $issue->description, $sizeMatches);
        if ($sizeMatches) {
            $issue->size = trim($sizeMatches[1]);
            $issue->description = str_replace($sizeMatches[0], '', $issue->description);
        }

        preg_match($printer, $issue->description, $printerMatches);
        if ($printerMatches) {
            $issue->printer = trim($printerMatches[1]);
            $issue->description = str_replace($printerMatches[0], '', $issue->description);
        }

        preg_match($print_process, $issue->description, $print_processMatches);
        if ($print_processMatches) {
            $issue->print_process = trim($print_processMatches[1]);
            $issue->description = str_replace($print_processMatches[0], '', $issue->description);
        }

        preg_match($perforations, $issue->description, $perforationsMatches);
        if ($perforationsMatches) {
            $issue->perforations = trim($perforationsMatches[1]);
            $issue->description = str_replace($perforationsMatches[0], '', $issue->description);
        }

        preg_match($gum, $issue->description, $gumMatches);
        if ($gumMatches) {
            $issue->gum = trim($gumMatches[1]);
            $issue->description = str_replace($gumMatches[0], '', $issue->description);
        }

        // Final trims
        $issue->description = preg_replace("/(<br\s*\/>)+/", '', $issue->description);
        $issue->description = trim($issue->description);

        $issue->save();
    }
}
