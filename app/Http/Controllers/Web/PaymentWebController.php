<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentWebController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|in:cash,bank_transfer,card,mobile_money,stripe,paypal,other'
        ]);

        $invoice = Invoice::findOrFail($request->invoice_id);

        if (Auth::user() && Auth::user()->role === 'client' && $invoice->user_id !== Auth::id()) {
            abort(403);
        }

        // En production, ici on intégrerait avec un service de paiement comme Stripe
        // Pour le moment, on crée juste un enregistrement de paiement

        $payment = Payment::create([
            'payment_reference' => 'PAY-' . now()->format('YmdHis') . '-' . random_int(1000, 9999),
            'invoice_id' => $request->invoice_id,
            'user_id' => Auth::id(),
            'amount' => $request->amount,
            'currency' => $invoice->currency ?? 'EUR',
            'payment_method' => $request->method,
            'status' => 'completed',
            'paid_at' => now(),
        ]);

        return redirect()->route('payments.receipt', $payment->id);
    }

    public function receipt(Request $request, string $id)
    {
        $payment = Payment::with(['invoice.parcel', 'user'])->findOrFail($id);

        if (Auth::user() && Auth::user()->role === 'client' && $payment->user_id !== Auth::id()) {
            abort(403);
        }

        return view('payments.receipt', [
            'payment' => $payment,
        ]);
    }
}
