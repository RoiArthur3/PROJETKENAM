<?php

namespace App\Services;

use App\Models\VehicleMission;
use App\Models\Vehicule;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class VehicleMissionCreationService
{
    public function create(array $attributes): VehicleMission
    {
        return DB::transaction(function () use ($attributes) {
            return VehicleMission::create($this->preparePayload($attributes));
        });
    }

    public function update(VehicleMission $mission, array $attributes): VehicleMission
    {
        return DB::transaction(function () use ($mission, $attributes) {
            $mission->update($this->preparePayload($attributes, $mission));

            return $mission->fresh();
        });
    }

    public function preparePayload(array $attributes, ?VehicleMission $existingMission = null): array
    {
        $startAt = Carbon::parse($attributes['start_at']);
        $endAt = Carbon::parse($attributes['end_at']);
        $durationDays = (int) ($attributes['duration_days'] ?? max(1, $startAt->diffInDays($endAt) + 1));
        $dailySupplierPrice = (float) ($attributes['daily_supplier_price'] ?? 0);
        $dailyClientPrice = (float) ($attributes['daily_client_price'] ?? 0);

        $payload = $attributes;
        $payload['reference'] = $existingMission?->reference ?: ($attributes['reference'] ?? $this->generateReference($startAt));
        $payload['duration_days'] = $durationDays;
        $payload['daily_supplier_price'] = $dailySupplierPrice;
        $payload['daily_client_price'] = $dailyClientPrice;
        $payload['total_supplier_cost'] = $durationDays * $dailySupplierPrice;
        $payload['total_client_amount'] = $durationDays * $dailyClientPrice;
        $payload['gross_margin'] = $payload['total_client_amount'] - $payload['total_supplier_cost'];
        $payload['source_type'] = $payload['source_type'] ?? null;
        $payload['source_id'] = $payload['source_id'] ?? null;
        $payload['source_reference'] = $payload['source_reference'] ?? null;

        [$resolvedSubmodule, $resolvedBillingMode] = $this->resolvePointageProfile($payload);
        $payload['pointage_submodule'] = $resolvedSubmodule;
        $payload['billing_mode'] = $resolvedBillingMode;

        return $payload;
    }

    private function resolvePointageProfile(array $payload): array
    {
        $submodule = $payload['pointage_submodule'] ?? null;
        $billingMode = $payload['billing_mode'] ?? null;

        $vehicle = !empty($payload['vehicle_id']) ? Vehicule::find($payload['vehicle_id']) : null;
        $vehicleType = strtolower((string) ($vehicle->type_materiel ?? ''));
        $vehicleSuggestsPlateau = str_contains($vehicleType, 'camion');

        if (!in_array($submodule, ['engin', 'camion_plateau'], true)) {
            $submodule = $vehicleSuggestsPlateau ? 'camion_plateau' : 'engin';
        }

        if ($submodule === 'engin') {
            $billingMode = 'standard';
        } else {
            if (!in_array($billingMode, ['monthly', 'trip'], true)) {
                $billingMode = 'trip';
            }

            if (($payload['source_type'] ?? null) === 'project' && !empty($payload['source_id'])) {
                $project = Project::find($payload['source_id']);
                if ($project && strtolower((string) $project->type) === 'location') {
                    $billingMode = 'monthly';
                }
            }
        }

        return [$submodule, $billingMode];
    }

    public function generateReference(Carbon $missionDate): string
    {
        $prefix = 'MSN-' . $missionDate->format('Ymd') . '-';
        $sequence = VehicleMission::where('reference', 'like', $prefix . '%')->count() + 1;

        do {
            $reference = $prefix . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
            $sequence++;
        } while (VehicleMission::where('reference', $reference)->exists());

        return $reference;
    }
}