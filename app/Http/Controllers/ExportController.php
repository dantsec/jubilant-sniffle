<?php

namespace App\Http\Controllers;

use App\Models\Export;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $exports = Export::paginate(15);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Export $export)
    {
        Storage::delete($export->file_name);

        $export->delete();

        return '[INFO] All the informations are destroyed!';
    }
}
