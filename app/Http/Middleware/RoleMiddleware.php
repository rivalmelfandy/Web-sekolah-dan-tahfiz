<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Batasi akses berdasarkan peran pengguna yang sedang login.
     *
     * @param  Closure(Request): (Response)  $next
     * @param  string  ...$roles  daftar peran yang diizinkan, mis. "admin", "ustadz", "siswa"
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        $allowed = array_filter(
            array_map(fn (string $role) => UserRole::tryFrom($role), $roles)
        );

        if (! $user instanceof User || ! in_array($user->role, $allowed, true)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
