<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidateSyncToken
{
    public function handle(Request $request, Closure $next)
    {
        $expected = env('NEXUS_SYNC_TOKEN');
        $header   = $request->header('Authorization', '');

        if (!$expected || !str_starts_with($header, 'Bearer ')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $provided = substr($header, 7); // strip "Bearer "

        if (!hash_equals($expected, $provided)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
