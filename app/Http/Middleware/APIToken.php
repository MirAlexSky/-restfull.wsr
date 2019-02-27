<?php

namespace App\Http\Middleware;

use Closure;
use App\User;

class APIToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $bearer = $request->header('Authorization');
        if ($bearer) 
        {
            $bearer = substr($bearer, stripos($bearer, ' '));
            $bearer = trim($bearer);

            $bearer = explode(':', $bearer);   
            $user = User::where('login', base64_decode($bearer[0]))->first();
            if ($user) {
                if ($user->api_token === $bearer[1]) {
                    return $next($request);
                }
            }
            return response()->json([
                'Message' => 'Unauthorized: user not exsists',
            ])->setStatusCode(401, 'Unauthorized');

        }

        return response()->json([
            'Message' => 'Unauthorized',
        ])->setStatusCode(401, 'Unauthorized');
    }
}
