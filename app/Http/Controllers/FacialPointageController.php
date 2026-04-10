<?php

namespace App\Http\Controllers;

use App\Models\FacialDevice;
use App\Models\FacialEvent;
use App\Models\Personnel;
use App\Models\Pointage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class FacialPointageController extends Controller
{
    public function index(Request $request)
    {
        $query = FacialEvent::with(['device', 'pointage.personnel']);

        if ($request->filled('device_id')) {
            $query->where('facial_device_id', $request->device_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('event_time', $request->date);
        } else {
            $query->whereDate('event_time', today());
        }

        if ($request->filled('employee_code')) {
            $query->where('employee_code', 'like', '%' . $request->employee_code . '%');
        }

        $events = $query->orderByDesc('event_time')->paginate(50);
        $devices = FacialDevice::where('is_active', true)->orderBy('name')->get();

        return view('rh.facial-pointage.index', compact('events', 'devices'));
    }

    public function dashboard()
    {
        $today = today();
        $facialDevicesTable = (new FacialDevice())->getTable();
        $facialEventsTable = (new FacialEvent())->getTable();
        $hasFacialDevicesTable = Schema::hasTable($facialDevicesTable);
        $hasFacialEventsTable = Schema::hasTable($facialEventsTable);

        // Stats du jour
        $todayEvents = 0;
        $todayPointages = Pointage::whereDate('date_pointage', $today)->count();
        $activeDevices = 0;
        $onlineDevices = 0;

        $recentEvents = collect();

        if ($hasFacialEventsTable) {
            $todayEvents = FacialEvent::whereDate('event_time', $today)->count();

            // Événements récents (dernière heure)
            $recentEvents = FacialEvent::with(['device', 'pointage.personnel'])
                ->where('event_time', '>=', now()->subHour())
                ->orderByDesc('event_time')
                ->limit(20)
                ->get();
        }

            if ($hasFacialDevicesTable) {
                $activeDevices = FacialDevice::where('is_active', true)->count();
                $onlineDevices = FacialDevice::where('last_seen_at', '>=', now()->subMinutes(5))->count();
            }

        // Pointages du jour par personnel
        $todayPointagesByPersonnel = Pointage::with('personnel')
            ->whereDate('date_pointage', $today)
            ->orderByDesc('date_pointage')
            ->orderByDesc('heure_arrivee')
            ->limit(20)
            ->get();

        // Terminaux actifs avec leur dernier événement
        $devicesWithLastEvent = collect();

        if ($hasFacialDevicesTable) {
            $devicesWithLastEventQuery = FacialDevice::where('is_active', true)->orderBy('name');
            if ($hasFacialEventsTable) {
                $devicesWithLastEventQuery->with(['events' => fn($q) => $q->orderByDesc('event_time')->limit(1)]);
            }
            $devicesWithLastEvent = $devicesWithLastEventQuery->get();
        }

        if ($hasFacialDevicesTable && !$hasFacialEventsTable) {
            $devicesWithLastEvent->each(function (FacialDevice $device): void {
                $device->setRelation('events', collect());
            });
        }

        return view('rh.facial-pointage.dashboard', compact(
            'todayEvents',
            'todayPointages',
            'activeDevices',
            'onlineDevices',
            'recentEvents',
            'todayPointagesByPersonnel',
            'devicesWithLastEvent',
            'hasFacialDevicesTable',
            'hasFacialEventsTable'
        ));
    }

    public function showEvent(FacialEvent $event)
    {
        $event->load(['device', 'pointage.personnel']);
        return view('rh.facial-pointage.show-event', compact('event'));
    }

    public function showPointage(Pointage $pointage)
    {
        $pointage->load(['personnel', 'user']);
        $relatedEvents = FacialEvent::where('pointage_id', $pointage->id)
            ->with('device')
            ->orderByDesc('event_time')
            ->get();

        return view('rh.facial-pointage.show-pointage', compact('pointage', 'relatedEvents'));
    }

    /**
     * Voir le flux en direct
     */
    public function live()
    {
        $device = FacialDevice::where('is_active', true)->first();
        
        // Derniers événements pour le panneau latéral
        $recentEvents = collect();
        if (Schema::hasTable((new FacialEvent())->getTable())) {
            $recentEvents = FacialEvent::with(['device', 'pointage.personnel'])
                ->orderByDesc('event_time')
                ->limit(10)
                ->get();
        }

        return view('rh.facial-pointage.live', compact('device', 'recentEvents'));
    }
}
