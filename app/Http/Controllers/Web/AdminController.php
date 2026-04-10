<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Parcel;
use App\Models\ParcelPhoto;
use App\Models\ParcelStatusHistory;
use App\Models\Shipment;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        // Statistiques de base
        $parcelsCount = Parcel::count();
        $shipmentsCount = Shipment::count();
        $warehousesCount = Warehouse::count();
        $usersCount = User::count();

        // Statistiques avancées
        $totalRevenue = DB::table('invoices')->sum('total_amount') ?? 0;
        $totalWeight = Parcel::sum('actual_weight') ?? 0;
        $averageDeliveryTime = $this->calculateAverageDeliveryTime();

        // Statistiques par statut
        $parcelStatuses = Parcel::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Statistiques par transport
        $transportModes = Parcel::select('transport_mode', DB::raw('count(*) as count'))
            ->groupBy('transport_mode')
            ->pluck('count', 'transport_mode')
            ->toArray();

        // Données pour graphiques (30 derniers jours)
        $dailyStats = $this->getDailyStats(30);
        $monthlyRevenue = $this->getMonthlyRevenue(12);

        // Top clients
        $topClients = User::withCount('parcels')
            ->orderBy('parcels_count', 'desc')
            ->limit(5)
            ->get();

        // Derniers colis reçus
        $recentParcels = Parcel::with(['user', 'statusHistory'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Colis en alerte (en attente depuis plus de 7 jours)
        $alertParcels = Parcel::where('status', 'declared')
            ->where('created_at', '<', now()->subDays(7))
            ->count();

        // Entrepôts les plus actifs
        $activeWarehouses = Warehouse::withCount('parcels')
            ->orderBy('parcels_count', 'desc')
            ->limit(3)
            ->get();

        return view('admin.dashboard', [
            'parcelsCount' => $parcelsCount,
            'shipmentsCount' => $shipmentsCount,
            'warehousesCount' => $warehousesCount,
            'usersCount' => $usersCount,
            'totalRevenue' => $totalRevenue,
            'totalWeight' => $totalWeight,
            'averageDeliveryTime' => $averageDeliveryTime,
            'parcelStatuses' => $parcelStatuses,
            'transportModes' => $transportModes,
            'dailyStats' => $dailyStats,
            'monthlyRevenue' => $monthlyRevenue,
            'topClients' => $topClients,
            'recentParcels' => $recentParcels,
            'alertParcels' => $alertParcels,
            'activeWarehouses' => $activeWarehouses
        ]);
    }

    private function calculateAverageDeliveryTime()
    {
        $deliveredParcels = Parcel::where('status', 'delivered')
            ->whereNotNull('received_at_warehouse_date')
            ->whereNotNull('delivered_date')
            ->get();

        if ($deliveredParcels->isEmpty()) {
            return 0;
        }

        $totalDays = $deliveredParcels->sum(function ($parcel) {
            return $parcel->received_at_warehouse_date
                ->diffInDays($parcel->delivered_date ?? now());
        });

        return round($totalDays / $deliveredParcels->count(), 1);
    }

    private function getDailyStats($days)
    {
        return Parcel::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'count' => $item->count
                ];
            });
    }

    private function getMonthlyRevenue($months)
    {
        return DB::table('invoices')
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->where('created_at', '>=', now()->subMonths($months))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => $item->month,
                    'revenue' => (float) $item->revenue
                ];
            });
    }

    public function scanParcel(Request $request)
    {
        return view('admin.scan');
    }

    public function processScan(Request $request)
    {
        // Logique de traitement du scan
    }

    public function receptionsIndex(Request $request)
    {
        $query = Parcel::query()->with(['user', 'photos']);

        if ($request->filled('tracking_number')) {
            $query->where('tracking_number', 'like', '%' . $request->string('tracking_number') . '%');
        }

        if ($request->filled('client')) {
            $client = $request->string('client');
            $query->whereHas('user', function ($q) use ($client) {
                $q->where('name', 'like', '%' . $client . '%')
                    ->orWhere('email', 'like', '%' . $client . '%');
            });
        }

        if ($request->filled('transport_mode')) {
            $query->where('transport_mode', $request->string('transport_mode'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('arrival_date')) {
            $query->whereDate('created_at', $request->string('arrival_date'));
        }

        $parcels = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('admin.receptions.index', [
            'parcels' => $parcels,
            'filters' => $request->only(['tracking_number', 'client', 'arrival_date', 'transport_mode', 'status']),
        ]);
    }

    public function receptionsShow(Request $request, string $id)
    {
        $parcel = Parcel::with(['user', 'photos', 'statusHistory.changedBy'])->findOrFail($id);

        return view('admin.receptions.show', [
            'parcel' => $parcel,
        ]);
    }

    public function receptionsReceipt(Request $request, string $id)
    {
        $parcel = Parcel::with(['user'])->findOrFail($id);

        return view('admin.receptions.receipt', [
            'parcel' => $parcel,
        ]);
    }

    public function receptionsUpdate(Request $request, string $id)
    {
        if (!Auth::user() || !in_array(Auth::user()->role, ['admin', 'warehouse', 'agent'], true)) {
            abort(403);
        }

        $parcel = Parcel::with(['user'])->findOrFail($id);

        $validated = $request->validate([
            'actual_weight' => 'required|numeric|min:0',
            'length' => 'required|numeric|min:0',
            'width' => 'required|numeric|min:0',
            'height' => 'required|numeric|min:0',
            'conformity' => 'required|in:conforme,non_conforme,refuse',
            'notes' => 'nullable|string|max:2000',
            'rejection_reason' => 'nullable|string|max:2000',
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $oldStatus = $parcel->status;

        $newStatus = match ($validated['conformity']) {
            'refuse' => 'rejected',
            default => 'received',
        };

        $parcel->update([
            'actual_weight' => $validated['actual_weight'],
            'length' => $validated['length'],
            'width' => $validated['width'],
            'height' => $validated['height'],
            'status' => $newStatus,
            'received_at_warehouse_date' => now(),
            'inspection_date' => now(),
            'notes' => $validated['notes'] ?? null,
            'rejection_reason' => $validated['conformity'] === 'refuse' ? ($validated['rejection_reason'] ?? 'Colis refusé') : null,
        ]);

        ParcelStatusHistory::create([
            'parcel_id' => $parcel->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'changed_by' => Auth::id(),
            'comment' => ($validated['notes'] ?? null) ?: ($validated['rejection_reason'] ?? null),
            'ip_address' => $request->ip(),
        ]);

        if ($request->hasFile('photos')) {
            $order = (int) (ParcelPhoto::where('parcel_id', $parcel->id)->max('order') ?? 0);
            foreach ($request->file('photos') as $photo) {
                $order++;
                $path = $photo->store('parcels/' . $parcel->id, 'public');
                ParcelPhoto::create([
                    'parcel_id' => $parcel->id,
                    'type' => 'warehouse',
                    'file_path' => $path,
                    'file_name' => $photo->getClientOriginalName(),
                    'file_size' => $photo->getSize(),
                    'mime_type' => $photo->getMimeType(),
                    'order' => $order,
                    'caption' => 'Réception entrepôt',
                ]);
            }
        }

        return redirect()->route('admin.receptions.show', $parcel->id)
            ->with('success', 'Réception enregistrée et statut mis à jour.');
    }

    public function clientsIndex(Request $request)
    {
        $query = User::query()->withCount('parcels');

        if ($request->filled('q')) {
            $q = $request->string('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', '%' . $q . '%')
                    ->orWhere('email', 'like', '%' . $q . '%');
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->string('role'));
        } else {
            $query->where('role', 'client');
        }

        if ($request->filled('active')) {
            $active = $request->string('active');
            if ($active === '1') {
                $query->where('is_active', true)->where('account_status', 'active');
            }
            if ($active === '0') {
                $query->where(function ($q) {
                    $q->where('is_active', false)
                        ->orWhereIn('account_status', ['blocked', 'suspended']);
                });
            }
        }

        $clients = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('admin.clients.index', [
            'clients' => $clients,
            'filters' => $request->only(['q', 'active', 'role']),
        ]);
    }

    public function clientsShow(Request $request, string $id)
    {
        $client = User::with(['parcels' => function ($q) {
            $q->orderBy('created_at', 'desc');
        }])->findOrFail($id);

        $totalInvoiced = (float) DB::table('invoices')->where('user_id', $client->id)->sum('total_amount');
        $totalPaid = (float) DB::table('payments')->where('user_id', $client->id)->where('status', 'completed')->sum('amount');
        $totalDue = max(0, $totalInvoiced - $totalPaid);

        return view('admin.clients.show', [
            'client' => $client,
            'stats' => [
                'total_invoiced' => $totalInvoiced,
                'total_paid' => $totalPaid,
                'total_due' => $totalDue,
            ],
        ]);
    }

    public function clientsUpdate(Request $request, string $id)
    {
        $client = User::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'company_name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:2000',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'client_type' => 'required|in:individual,business',
            'notification_preference' => 'required|in:email,sms',
            'auto_billing' => 'nullable|boolean',
        ]);

        $client->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'company_name' => $validated['company_name'] ?? null,
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
            'country' => $validated['country'] ?? null,
            'client_type' => $validated['client_type'],
            'notification_preference' => $validated['notification_preference'],
            'auto_billing' => (bool) ($request->boolean('auto_billing')),
        ]);

        return redirect()->route('admin.clients.show', $client->id)
            ->with('success', 'Informations client mises à jour.');
    }

    public function clientsSetStatus(Request $request, string $id)
    {
        $client = User::findOrFail($id);

        $validated = $request->validate([
            'account_status' => 'required|in:active,blocked,suspended',
        ]);

        $isActive = $validated['account_status'] === 'active';

        $client->update([
            'account_status' => $validated['account_status'],
            'is_active' => $isActive,
        ]);

        return redirect()->route('admin.clients.show', $client->id)
            ->with('success', 'Statut du client mis à jour.');
    }
}
