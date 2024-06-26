<?php

namespace App\Http\Controllers;

use App\Http\Requests\BeerRequest;
use App\Jobs\ExportJob;
use App\Jobs\SendExportEmailJob;
use App\Jobs\StoreExportDataJob;
use App\Services\PunkapiService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\Meal;

class BeerController extends Controller
{
    /**
     * Aqui o laravel tenta fazer o match de PunkapiService automaticamente.
     * Nao precisamo fazer: `$service = new PunkapiService();`
     */
    public function index(BeerRequest $request, PunkapiService $service) {
        $filters = $request->validated();
        $beers = $service->getBeers(...$filters);
        $meals = Meal::all();
    
        return Inertia::render('Beers', [
            'beers' => $beers,
            'meals' => $meals,
            'filters' => $filters
        ]);
    }

    public function export(BeerRequest $request, PunkapiService $service) {
        $filename = 'founded-beers-' . now()->format('Y-m-d-H_i') . '.xlsx';

        ExportJob::withChain([
            new SendExportEmailJob($filename),
            new StoreExportDataJob(Auth::id(), $filename)
        ])->dispatch($request->validated(), $filename);

        return redirect()->back()
            ->with('success', 'Seu arquivo esta em processamento e em breve chegara por email!');
    }
}
