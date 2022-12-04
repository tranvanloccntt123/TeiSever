<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Http\Controllers\API\v1\Response as APIResponse;
class UserIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $applicationId = $request->application_id;
        $user = $request->user();
        if($user->application_id != $applicationId) return APIResponse::FAIL(['user' => "Không tìm thấy thông tin của người dùng"]);
        return $next($request);
    }
}
