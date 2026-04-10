<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Parcel;
use App\Models\User;
use App\Services\InvoiceCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceWebController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with(['parcel', 'payments'])->orderBy('created_at', 'desc');

        if (Auth::user() && Auth::user()->role === 'client') {
            $query->where('user_id', Auth::id());
        }

        $invoices = $query->paginate(15);

        return view('invoices.index', [
            'invoices' => $invoices,
        ]);
    }

    public function create()
    {
        // Vérifier les autorisations (admin et agent peuvent créer des factures)
        if (!Auth::check() || !in_array(Auth::user()->role, ['admin', 'agent'])) {
            abort(403);
        }

        // Récupérer les colis sans facture
        $parcelsWithoutInvoice = Parcel::whereDoesntHave('invoice')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        // Récupérer tous les clients pour la sélection
        $clients = User::where('role', 'client')->orderBy('name')->get();

        return view('invoices.create', [
            'parcelsWithoutInvoice' => $parcelsWithoutInvoice,
            'clients' => $clients,
        ]);
    }

    public function store(Request $request)
    {
        // Vérifier les autorisations (admin et agent peuvent créer des factures)
        if (!Auth::check() || !in_array(Auth::user()->role, ['admin', 'agent'])) {
            abort(403);
        }

        $validated = $request->validate([
            'client_id' => 'nullable|exists:users,id',
            'parcel_id' => 'required|exists:parcels,id',
            'shipping_cost' => 'required|numeric|min:0',
            'cartons_cost' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'issue_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:issue_date',
        ]);

        // Vérifier que le colis n'a pas déjà de facture
        if (Invoice::where('parcel_id', $validated['parcel_id'])->exists()) {
            return redirect()->back()
                ->with('error', 'Ce colis a déjà une facture.')
                ->withInput();
        }

        $parcel = Parcel::findOrFail($validated['parcel_id']);

        // Utiliser le client sélectionné ou le client du colis
        $clientId = $validated['client_id'] ?? $parcel->user_id;

        // Calculer le total
        $totalAmount = $validated['shipping_cost'] + $validated['cartons_cost'];

        // Générer un numéro de facture unique
        $invoiceNumber = 'INV-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));

        $invoice = Invoice::create([
            'invoice_number' => $invoiceNumber,
            'parcel_id' => $validated['parcel_id'],
            'user_id' => $clientId,
            'shipping_cost' => $validated['shipping_cost'],
            'cartons_cost' => $validated['cartons_cost'],
            'subtotal' => $totalAmount,
            'tax_amount' => 0, // Pour l'instant, pas de TVA
            'total_amount' => $totalAmount,
            'currency' => 'XOF',
            'status' => 'draft',
            'issue_date' => $validated['issue_date'],
            'due_date' => $validated['due_date'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', 'Facture créée avec succès.');
    }

    public function show($id)
    {
        $invoice = Invoice::with(['parcel', 'payments'])->findOrFail($id);

        // Vérifier les autorisations
        if (Auth::user()->role === 'client' && $invoice->user_id !== Auth::id()) {
            abort(403);
        }

        // Mettre à jour les coûts automatiquement si nécessaire
        if ($invoice->parcel) {
            InvoiceCalculatorService::updateInvoiceFromParcel($invoice, $invoice->parcel);
            // Recharger l'invoice pour avoir les valeurs à jour
            $invoice->refresh();
        }

        return view('invoices.show', [
            'invoice' => $invoice,
        ]);
    }

    public function edit($id)
    {
        $invoice = Invoice::with(['parcel', 'payments'])->findOrFail($id);

        // Vérifier les autorisations (seul l'admin peut éditer)
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        return view('invoices.edit', [
            'invoice' => $invoice,
        ]);
    }

    public function update(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        // Vérifier les autorisations (seul l'admin peut éditer)
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $validated = $request->validate([
            'cartons_cost' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Calculer le nouveau total
        $currentTotal = $invoice->shipping_cost + $validated['cartons_cost'];

        $invoice->update([
            'cartons_cost' => $validated['cartons_cost'],
            'total_amount' => $currentTotal,
            'notes' => $validated['notes'] ?? $invoice->notes,
        ]);

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', 'Facture mise à jour avec succès.');
    }

    public function markAsPaid($id)
    {
        // Vérifier les autorisations
        if (!Auth::check() || !in_array(Auth::user()->role, ['admin', 'agent'])) {
            abort(403);
        }

        $invoice = Invoice::findOrFail($id);

        // Mettre à jour le statut
        $invoice->update([
            'status' => 'paid',
            'paid_date' => now(),
        ]);

        return redirect()->route('invoices.index')
            ->with('success', 'Facture #' . $invoice->invoice_number . ' marquée comme payée.');
    }

    public function markAsUnpaid($id)
    {
        // Vérifier les autorisations
        if (!Auth::check() || !in_array(Auth::user()->role, ['admin', 'agent'])) {
            abort(403);
        }

        $invoice = Invoice::findOrFail($id);

        // Mettre à jour le statut
        $invoice->update([
            'status' => 'pending',
            'paid_date' => null,
        ]);

        return redirect()->route('invoices.index')
            ->with('success', 'Facture #' . $invoice->invoice_number . ' marquée comme non payée.');
    }

    public function downloadPdf($id)
    {
        $invoice = Invoice::with(['parcel', 'payments', 'user'])->findOrFail($id);

        if (Auth::user() && Auth::user()->role === 'client' && $invoice->user_id !== Auth::id()) {
            abort(403);
        }

        $pdf = Pdf::loadView('invoices.pdf', [
            'invoice' => $invoice
        ]);

        return $pdf->download("facture-{$invoice->id}.pdf");
    }
}
