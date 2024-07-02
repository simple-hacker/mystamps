<?php

namespace App\Console\Commands;

use App\Stamp;
use Illuminate\Console\Command;

class RemoveRoyalMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stamps:royalmail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove Image Preview By Royal Mail from stamp descriptions';

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
        $stamps = Stamp::all();

        $bar = $this->output->createProgressBar(count($stamps));

        $bar->start();

        $stamps->each(function ($stamp) use ($bar) {
            $this->removeRoyalMail($stamp);
            $bar->advance();
        });

        $this->info('');
        $bar->finish();

        return 0;
    }

    private function removeRoyalMail(&$stamp)
    {
        $description = $stamp->description;

        $description = preg_replace('/Image preview by Royal Mail/i', '', $description);

        $stamp->description = trim($description);

        $stamp->save();
    }
}
