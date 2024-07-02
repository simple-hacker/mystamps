<?php

namespace App\Console\Commands;

use App\Issue;
use Goutte\Client;
use Illuminate\Console\Command;
use Symfony\Component\DomCrawler\Crawler;

class FixStampDescriptions extends Command
{
    protected $client;
    protected $baseURI = 'https://www.collectgbstamps.co.uk';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stamps:descriptions {issue?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports and overwrites only the stamp descriptions from collectgbstamps';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->client = new Client();
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
            $this->line('Importing stamp descriptions for ' . $issues->title);
            $this->line('');
            $this->importStampDescriptions($issues);
        } else {

            $bar = $this->output->createProgressBar($count);
            $bar->setFormat("%current%/%max% [%bar%] %percent:3s%%\n%message%");

            $bar->start();

            $issues->each(function ($issue) use ($bar) {
                $bar->setMessage('Importing stamp descriptions for ' . $issue->title);
                $this->importStampDescriptions($issue);
                $bar->advance();
            });

            $this->info('');
            $bar->finish();
        }

        return 0;
    }

    private function importStampDescriptions(&$issue)
    {
        $url = $this->baseURI . '/explore/issues/?issue=' . $issue->cgbs_issue;

        $stamps = $issue->stamps;

        $stamp_titles = [];

        $this->client
            ->request('GET', $url)
            ->filter('.stamp_entry')
            ->each(function (Crawler $stamp, $i) use (&$stamps, &$stamp_titles) {

                    // Get title and put it in to stamp_titles ready for multiple stamps
                    $title = $stamp->filter('h3')->text();
                    $stamp_titles[$title][] = $title;

                    $description = $stamp->text();

                    // Remove title from the start of the text including any left over whitespace
                    if (substr($description, 0, strlen($title)) == $title) {
                        $description = trim(substr($description, strlen($title)));
                    }

                    $denomination = null;

                    // Match stamp denomination (1st 2nd) from the start of the description
                    $regex = "/^(£\d+\.\d{2,}|£\d+|\d+p|1st Large|2nd Large|1st|2nd|\d+s\d+d|\d+d|\d+½?d|\d*½d|\d+s\d+½?d|\d+s|\d+½?p)/i";
                    preg_match($regex, $description, $denominationMatch);

                    if (isset($denominationMatch[0])) {
                        $denomination = $denominationMatch[0];

                        // Remove denomination from the start of the text including any left over whitespace
                        if (substr($description, 0, strlen($denomination)) == $denomination) {
                            $description = trim(substr($description, strlen($denomination)));
                        }
                    }

                    // Find the stamp in issue
                    $editStamp = $stamps->filter(function($stamp) use ($title, &$stamp_titles) {

                        // Change the title to match what was saved in the database before
                        // If stamps have duplicate titles in the issue, we append the number after
                        if (count($stamp_titles[$title]) > 1) {
                            $count = count($stamp_titles[$title]);
                            $title = "{$title} ({$count})";
                        }

                        return $stamp->title === $title;
                    });

                    // If we only have one stamp after filtering then proceed with updating
                    if (count($editStamp) === 1) {
                        // Proceed with editing the stamp
                        // dump($editStamp->first()->title);
                        // dump($editStamp->first()->description, $description);
                        $editStamp->first()->update(['description' => $description]);
                    }
                    // Else ignore
            });
    }
}
