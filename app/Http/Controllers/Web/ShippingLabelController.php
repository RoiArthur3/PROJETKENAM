<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ShippingLabel;
use App\Models\ReceivingAddress;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ShippingLabelController extends Controller
{
    public function index()
    {
        $labels = auth()->user()->shippingLabels()
            ->with('receivingAddress')
            ->latest()
            ->get();

        return view('client.shipping-labels.index', compact('labels'));
    }

    public function create()
    {
        $defaultAddress = ReceivingAddress::default()->first();
        $addresses = ReceivingAddress::active()->get();

        return view('client.shipping-labels.create', compact('defaultAddress', 'addresses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sender_name' => 'required|string|max:255',
            'sender_address' => 'required|string',
            'sender_city' => 'required|string|max:255',
            'sender_postal_code' => 'required|string|max:20',
            'sender_country' => 'required|string|max:255',
            'sender_phone' => 'required|string|max:20',
            'sender_email' => 'required|email|max:255',
            'parcel_contents' => 'required|string',
            'declared_value' => 'nullable|numeric|min:0',
            'weight' => 'nullable|numeric|min:0',
            'purchase_store' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'receiving_address_id' => 'nullable|exists:receiving_addresses,id',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['label_number'] = $this->generateLabelNumber();
        $validated['status'] = 'draft';

        $label = ShippingLabel::create($validated);

        return redirect()->route('client.shipping-labels.index')
            ->with('success', 'Étiquette créée avec succès');
    }

    public function edit(ShippingLabel $shippingLabel)
    {
        if ($shippingLabel->user_id !== auth()->id()) {
            abort(403);
        }

        $addresses = ReceivingAddress::active()->get();

        return view('client.shipping-labels.edit', compact('shippingLabel', 'addresses'));
    }

    public function update(Request $request, ShippingLabel $shippingLabel)
    {
        if ($shippingLabel->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'sender_name' => 'required|string|max:255',
            'sender_address' => 'required|string',
            'sender_city' => 'required|string|max:255',
            'sender_postal_code' => 'required|string|max:20',
            'sender_country' => 'required|string|max:255',
            'sender_phone' => 'required|string|max:20',
            'sender_email' => 'required|email|max:255',
            'parcel_contents' => 'required|string',
            'declared_value' => 'nullable|numeric|min:0',
            'weight' => 'nullable|numeric|min:0',
            'purchase_store' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'receiving_address_id' => 'nullable|exists:receiving_addresses,id',
        ]);

        $shippingLabel->update($validated);

        return redirect()->route('client.shipping-labels.index')
            ->with('success', 'Étiquette mise à jour avec succès');
    }

    public function generatePdf(ShippingLabel $shippingLabel)
    {
        if ($shippingLabel->user_id !== auth()->id()) {
            abort(403);
        }

        $shippingLabel->update([
            'status' => 'generated',
            'generated_at' => now(),
        ]);

        $pdf = Pdf::loadView('client.shipping-labels.pdf', compact('shippingLabel'));

        $filename = 'etiquette-' . $shippingLabel->label_number . '.pdf';

        return $pdf->download($filename);
    }

    public function markAsPrinted(ShippingLabel $shippingLabel)
    {
        if ($shippingLabel->user_id !== auth()->id()) {
            abort(403);
        }

        $shippingLabel->update([
            'status' => 'printed',
            'printed_at' => now(),
        ]);

        return back()->with('success', 'Étiquette marquée comme imprimée');
    }

    public function destroy(ShippingLabel $shippingLabel)
    {
        if ($shippingLabel->user_id !== auth()->id()) {
            abort(403);
        }

        // Supprimer le fichier PDF s'il existe
        if ($shippingLabel->pdf_path && Storage::exists($shippingLabel->pdf_path)) {
            Storage::delete($shippingLabel->pdf_path);
        }

        $shippingLabel->delete();

        return redirect()->route('client.shipping-labels.index')
            ->with('success', 'Étiquette supprimée avec succès');
    }

    private function generateLabelNumber(): string
    {
        do {
            $number = 'LBL' . date('Y') . strtoupper(Str::random(6)) . rand(100, 999);
        } while (ShippingLabel::where('label_number', $number)->exists());

        return $number;
    }
}
