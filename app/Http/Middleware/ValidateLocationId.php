<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidateLocationId
{
    public function handle(Request $request, Closure $next)
    {
        $locationId = $request->input('id_lokasi');
        
        if ($locationId && !in_array($locationId, [1, 2])) {
            return redirect()->back()->with('error', 'ID Lokasi tidak valid. Hanya bisa menggunakan GKN 1 atau GKN 2.');
        }

        return $next($request);
    }
}