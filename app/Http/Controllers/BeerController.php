<?php

namespace App\Http\Controllers;

use App\Exports\BeerExport;
use App\Http\Requests\BeerRequest;
use App\Services\PunkapiService;
use Maatwebsite\Excel\Facades\Excel;

class BeerController extends Controller
{
    // Aqui o laravel tenta fazer o match de PunkapiService automaticamente.
    public function index(BeerRequest $request, PunkapiService $service) {
        // $service = new PunkapiService();
        return $service->getBeers(...$request->validated());
    }

    public function export(BeerRequest $request, PunkapiService $service) {
        $beers = $service->getBeers(...$request->validated());

        $filteredBeers = collect($beers)->map(function($value, $key) {
            return collect($value)
                ->only(['name', 'tagline', 'first_brewed', 'description'])
                ->toArray();
        })->toArray();

        Excel::store(
            new BeerExport($filteredBeers),
            'olw-report.xlsx',
            's3'
        );

        return '[INFO] Report Created Succesfully!';
    }
}
