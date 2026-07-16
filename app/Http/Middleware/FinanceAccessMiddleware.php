<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FinanceAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403, 'Unauthorized access.');
        }

        // Admins and Bendahara Umum have global access
        if ($user->isAdmin() || $user->isBendaharaUmum()) {
            return $next($request);
        }

        // Bendahara Bidang is scoped to their own bidang_id
        if ($user->isBendaharaBidang()) {
            $bidangId = $user->bidang_id;

            // 1. Check if the request is trying to specify a different bidang_id
            if ($request->has('bidang_id') && (int)$request->input('bidang_id') !== (int)$bidangId) {
                abort(403, 'Anda tidak memiliki akses ke bidang ini.');
            }

            // 2. Check route parameters (e.g. {periode})
            $periode = $request->route('periode');
            if ($periode) {
                // If it is an ID, find it. If it is a model (route model binding), check directly.
                $periodeModel = is_numeric($periode) ? \App\Models\PeriodeLaporan::find($periode) : $periode;
                if ($periodeModel && (int)$periodeModel->bidang_id !== (int)$bidangId) {
                    abort(403, 'Anda tidak memiliki akses ke periode laporan ini.');
                }
            }

            // 3. Check route parameters (e.g. {transaksi})
            $transaksi = $request->route('transaksi');
            if ($transaksi) {
                $transaksiModel = is_numeric($transaksi) ? \App\Models\Transaksi::find($transaksi) : $transaksi;
                if ($transaksiModel && (int)$transaksiModel->bidang_id !== (int)$bidangId) {
                    abort(403, 'Anda tidak memiliki akses ke transaksi ini.');
                }
            }

            return $next($request);
        }

        abort(403, 'Role Anda tidak memiliki akses ke sistem keuangan.');
    }
}
