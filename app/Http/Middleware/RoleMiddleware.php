<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Closure;

class RoleMiddleware
{
	public function handle(Request $request, Closure $next, ...$roles)
	{
		$user = Auth::user();

		if (!$user || !in_array($user->role, $roles)) {
			return response()->json([
				'success' => false,
				'message' => 'Access denied'
			], 403);
		}

		return $next($request);
	}
}
