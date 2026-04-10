<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Parcel;
use App\Models\ParcelStatusHistory;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Payment::with(['invoice', 'user']);

        if ($request->user()->isClient()) {
            $query->where('user_id', $request->user()->id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $payments,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank_transfer,card,mobile_money,stripe,paypal,other',
            'status' => 'nullable|in:pending,completed,failed,refunded',
            'transaction_id' => 'nullable|string',
            'payment_details' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $invoice = Invoice::with('parcel')->findOrFail($request->invoice_id);

        if ($request->user()->isClient() && $invoice->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé',
            ], 403);
        }

        $payment = Payment::create([
            'payment_reference' => $this->generatePaymentReference(),
            'invoice_id' => $invoice->id,
            'user_id' => $request->user()->id,
            'amount' => $request->amount,
            'currency' => $invoice->currency,
            'payment_method' => $request->payment_method,
            'status' => $request->status ?? 'completed',
            'transaction_id' => $request->transaction_id,
            'payment_details' => $request->payment_details,
            'paid_at' => ($request->status ?? 'completed') === 'completed' ? now() : null,
            'notes' => $request->notes,
        ]);

        if ($payment->status === 'completed') {
            $invoice->update([
                'status' => 'paid',
                'paid_date' => now()->toDateString(),
            ]);

            if ($invoice->parcel) {
                $parcel = $invoice->parcel;
                $oldStatus = $parcel->status;
                $parcel->update(['status' => 'paid']);

                ParcelStatusHistory::create([
                    'parcel_id' => $parcel->id,
                    'old_status' => $oldStatus,
                    'new_status' => 'paid',
                    'changed_by' => $request->user()->id,
                    'comment' => 'Paiement enregistré',
                    'ip_address' => $request->ip(),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Paiement enregistré',
            'data' => $payment->load(['invoice', 'user']),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $payment = Payment::with(['invoice', 'user'])->findOrFail($id);

        $request = request();
        if ($request->user() && $request->user()->isClient() && $payment->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $payment,
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

        $payment = Payment::with('invoice.parcel')->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,completed,failed,refunded',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $payment->update([
            'status' => $request->status,
            'notes' => $request->notes,
            'paid_at' => $request->status === 'completed' ? now() : $payment->paid_at,
        ]);

        if ($request->status === 'completed' && $payment->invoice) {
            $payment->invoice->update([
                'status' => 'paid',
                'paid_date' => now()->toDateString(),
            ]);

            if ($payment->invoice->parcel) {
                $parcel = $payment->invoice->parcel;
                $oldStatus = $parcel->status;
                $parcel->update(['status' => 'paid']);

                ParcelStatusHistory::create([
                    'parcel_id' => $parcel->id,
                    'old_status' => $oldStatus,
                    'new_status' => 'paid',
                    'changed_by' => $request->user()->id,
                    'comment' => 'Paiement confirmé',
                    'ip_address' => $request->ip(),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Paiement mis à jour',
            'data' => $payment->load(['invoice', 'user']),
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

        $payment = Payment::findOrFail($id);
        $payment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Paiement supprimé',
        ]);
    }

    private function generatePaymentReference(): string
    {
        return 'PAY-' . now()->format('Ymd') . '-' . strtoupper(Str::random(8));
    }
}
