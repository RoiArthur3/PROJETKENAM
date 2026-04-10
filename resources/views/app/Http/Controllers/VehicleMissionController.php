<?php

namespace App\Http\Controllers;

use App\Models\VehicleMission;
use Illuminate\Http\Request;

class VehicleMissionController extends Controller
{
    public function index(Request $request)
    {
        $q = VehicleMission::with(['vehicle','user'])
            ->when($request->status, fn($qr) => $qr->where('status', $request->status))
            ->when($request->vehicle_id, fn($qr) => $qr->where('vehicle_id', $request->vehicle_id))
            ->when($request->from, fn($qr) => $qr->whereDate('start_at', '>=', $request->from))
            ->when($request->to, fn($qr) => $qr->whereDate('start_at', '<=', $request->to))
            ->orderByDesc('start_at');

        $missions = $q->paginate(10)->withQueryString();
        $statuses = ['planned','ongoing','done','canceled'];

        return view('parc.missions', compact('missions','statuses'));
    }

    public function export(Request $request)
    {
        $q = VehicleMission::with(['vehicle','user'])
            ->when($request->status, fn($qr) => $qr->where('status', $request->status))
            ->when($request->vehicle_id, fn($qr) => $qr->where('vehicle_id', $request->vehicle_id))
            ->when($request->from, fn($qr) => $qr->whereDate('start_at', '>=', $request->from))
            ->when($request->to, fn($qr) => $qr->whereDate('start_at', '<=', $request->to))
            ->orderByDesc('start_at');

        $missions = $q->limit(1000)->get();

        $filename = 'parc_missions_'.now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($missions) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM UTF-8

            fputcsv($out, ['reference','vehicule','conducteur','destination','debut','fin','statut']);

            foreach ($missions as $m) {
                fputcsv($out, [
                    $m->reference,
                    optional($m->vehicle)->immatriculation,
                    optional($m->user)->name,
                    $m->destination,
                    optional($m->start_at)->format('Y-m-d H:i'),
                    optional($m->end_at)->format('Y-m-d H:i'),
                    $m->status,
                ]);
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
