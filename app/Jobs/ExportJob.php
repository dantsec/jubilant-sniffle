<?php

namespace App\Jobs;

use App\Services\PunkapiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BeerExport;

class ExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected array $data,
        protected string $filename,
        protected PunkapiService $service = new PunkapiService(),
    ){}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $beers = $this->service->getBeers(...$this->data);

        $filteredBeers = array_map(function($value) {
            return collect($value)
                ->only(['name', 'tagline', 'first_brewed', 'description'])
                ->toArray();
        }, $beers);

        /**
         * Save excel file into our S3.
         */
        Excel::store(
            new BeerExport($filteredBeers),
            $this->filename,
            's3'
        );
    }
}
