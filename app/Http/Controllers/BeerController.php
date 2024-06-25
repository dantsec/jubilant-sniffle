<?php

namespace App\Http\Controllers;

use App\Exports\BeerExport;
use App\Http\Requests\BeerRequest;
use App\Mail\ExportEmail;
use App\Models\Export;
use Illuminate\Support\Facades\Mail;
use App\Services\PunkapiService;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;

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

        $filename = 'founded-beers-' . now()->format('Y-m-d-H_i') . '.xlsx';

        /**
         * Save excel file into our S3.
         */
        Excel::store(
            new BeerExport($filteredBeers),
            $filename,
            's3'
        );

        /**
         * Send report to user.
         */
        Mail::to('test@test.com')
            ->send(new ExportEmail($filename));

        Export::create([
            'file_name' => $filename,
            'user_id' => Auth::user()->id
        ]);

        return '[INFO] Report Created Succesfully!';
    }
}
