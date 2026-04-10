<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Parcel;
use App\Models\ParcelStatusHistory;
use App\Models\Tariff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Invoice::with(['parcel', 'user']);

        if ($request->user()->isClient()) {
            $query->where('user_id', $request->user()->id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $invoices = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $invoices,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!$request->user()->isAdmin() && !$request->user()->isWarehouse()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'parcel_id' => 'required|exists:parcels,id',
            'customs_fee' => 'nullable|numeric|min:0',
            'handling_fee' => 'nullable|numeric|min:0',
            'insurance_fee' => 'nullable|numeric|min:0',
            'other_fees' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $parcel = Parcel::with('invoice')->findOrFail($request->parcel_id);

        if ($parcel->invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Une facture existe déjà pour ce colis',
            ], 409);
        }

        $chargeableWeight = (float) ($parcel->chargeable_weight ?? 0);
        if ($chargeableWeight <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Le poids facturable est requis (poids et/ou dimensions)',
            ], 422);
        }

        $tariff = Tariff::findApplicableTariff(
            $parcel->transport_mode,
            $parcel->origin_country,
            $parcel->destination_country,
            $chargeableWeight
        );

        if (!$tariff) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun tarif applicable trouvé',
            ], 422);
        }

        $shippingCost = ($chargeableWeight * (float) $tariff->price_per_kg) + (float) $tariff->fixed_fee;

        $customsFee = (float) ($request->customs_fee ?? 0);
        $handlingFee = (float) ($request->handling_fee ?? 0);
        $insuranceFee = (float) ($request->insurance_fee ?? 0);
        $otherFees = (float) ($request->other_fees ?? 0);
        $taxAmount = (float) ($request->tax_amount ?? 0);

        $subtotal = $shippingCost + $customsFee + $handlingFee + $insuranceFee + $otherFees;
        $total = $subtotal + $taxAmount;

        $invoice = Invoice::create([
            'invoice_number' => $this->generateInvoiceNumber(),
            'parcel_id' => $parcel->id,
            'user_id' => $parcel->user_id,
            'shipping_cost' => $shippingCost,
            'customs_fee' => $customsFee,
            'handling_fee' => $handlingFee,
            'insurance_fee' => $insuranceFee,
            'other_fees' => $otherFees,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $total,
            'currency' => $tariff->currency,
            'status' => 'pending',
            'issue_date' => now()->toDateString(),
            'due_date' => $request->due_date,
            'notes' => $request->notes,
        ]);

        $oldStatus = $parcel->status;
        $parcel->update(['status' => 'fees_calculated']);

        ParcelStatusHistory::create([
            'parcel_id' => $parcel->id,
            'old_status' => $oldStatus,
            'new_status' => 'fees_calculated',
            'changed_by' => $request->user()->id,
            'comment' => 'Facture générée',
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Facture créée avec succès',
            'data' => $invoice->load(['parcel', 'user']),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $invoice = Invoice::with(['parcel', 'user', 'payments'])->findOrFail($id);

        $request = request();
        if ($request->user() && $request->user()->isClient() && $invoice->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $invoice,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (!$request->user()->isAdmin() && !$request->user()->isWarehouse()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé',
            ], 403);
        }

        $invoice = Invoice::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'status' => 'nullable|in:draft,pending,paid,cancelled',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $invoice->update($request->only(['status', 'due_date', 'notes']));

        return response()->json([
            'success' => true,
            'message' => 'Facture mise à jour',
            'data' => $invoice,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $request = request();

        if (!$request->user() || !$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé',
            ], 403);
        }

        $invoice = Invoice::findOrFail($id);
        $invoice->delete();

        return response()->json([
            'success' => true,
            'message' => 'Facture supprimée',
        ]);
    }

    private function generateInvoiceNumber(): string
    {
        return 'INV-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
    }
}
