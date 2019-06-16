<?php

namespace App\Console\Commands;

use App\Offer;
use App\Statistic;
use Illuminate\Console\Command;

class ParseOffers extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'osm:parse-offer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse offers';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle() {
        $offers = Offer::whereState( Offer::STATE_NEW )->get();

        $this->output->progressStart( $offers->count() );
        foreach ( $offers as $offer ) {
            $stats = $offer->poi_stats;
            Statistic::where( [ 'statable_id' => $offer->id, 'statable_type' => 'offer' ] )->delete();
            foreach ( $stats as $key => $value ) {
                Statistic::create( [
                    'statable_id'   => $offer->id,
                    'statable_type' => 'offer',
                    'key'           => $key,
                    'value'         => $value,
                ] );
            }
            $offer->state = Offer::STATE_READY;
            $offer->save();
            $this->output->progressAdvance();
        }
        $this->output->progressFinish();
    }
}
