<?php

namespace App\Http\Controllers;

use App\Http\Requests\BeerRequest;
use App\Jobs\ExportJob;
use App\Jobs\SendExportEmailJob;
use App\Jobs\StoreExportDataJob;
use App\Services\PunkapiService;
use Illuminate\Support\Facades\Auth;

class BeerController extends Controller
{
    // Aqui o laravel tenta fazer o match de PunkapiService automaticamente.
    public function index(BeerRequest $request, PunkapiService $service) {
        // $service = new PunkapiService();
        return $service->getBeers(...$request->validated());
    }

    public function export(BeerRequest $request, PunkapiService $service) {
        $filename = 'founded-beers-' . now()->format('Y-m-d-H_i') . '.xlsx';

        ExportJob::withChain([
            new SendExportEmailJob($filename),
            new StoreExportDataJob(Auth::id(), $filename)
        ])->dispatch($request->validated(), $filename);

        return '[INFO] Report Created Succesfully!';
    }
}
