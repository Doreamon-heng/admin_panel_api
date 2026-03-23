<?php

namespace App\Http\Controllers\middleware;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiKeyMiddleware extends Controller
{
    public function handle(Request $request, \Closure $next)
    {
       if ($request->header('X-API-KEY') !== env('API_SECRET_KEY')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }      
}
