<?php

namespace App\Console\Commands;

use App\Denomination;
use App\Stamp;
use Illuminate\Console\Command;

class ConvertDenominationToFaceValue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stamps:value {issue?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert the denomination to a numerical face value';

    protected $values;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->values = Denomination::pluck('value', 'denomination');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $issueId = $this->argument('issue');

        $stamps = ($issueId) ? Stamp::whereIssueId($issueId)->get() : Stamp::all();

        $bar = $this->output->createProgressBar(count($stamps));

        $bar->start();

        $stamps->each(function ($stamp) use ($bar) {
            $this->convertDenominationToFaceValue($stamp);
            $bar->advance();
        });

        $this->info('');
        $bar->finish();

        return 0;
    }

    private function convertDenominationToFaceValue($stamp)
    {
        if (! $stamp->denomination) {
            return;
        }

        switch ($stamp->denomination) {
            case "1st":
                $stamp->face_value = $this->values['1st'];
                break;
            case "2nd":
                $stamp->face_value = $this->values['2nd'];
                break;
            case "1st Large":
                $stamp->face_value = $this->values['1st Large'];
                break;
            case "2nd Large":
                $stamp->face_value = $this->values['2nd Large'];
                break;
            case (preg_match("/^£(\d+?([.]\d{1,2})?)/", $stamp->denomination, $matches) ? true : false):
                $stamp->face_value = (float) $matches[1];
                break;
            case (preg_match("/^(\d*)(½)?p$/", $stamp->denomination, $matches) ? true : false):
                $stamp->face_value = (float) $matches[1] / 100;
                break;
            default:
        }

        $stamp->save();
    }
}
