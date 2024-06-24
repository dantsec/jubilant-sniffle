<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PunkapiService {
    public function getBeers(
        ?string $beer_name = null,
        ?string $food = null,
        ?string $malt = null,
        ?int $ibu_gt = null
    ) {
        // Pega os parametros da funcao que foram DEFINIDOS, onde e retornado um array com chave de mesmo nome do parametro.
        $params = get_defined_vars();

        return Http::punkapi()
            ->get('/beers', $params)
            ->throw("Error Processing Request", 1)
            ->json();
    }
}
