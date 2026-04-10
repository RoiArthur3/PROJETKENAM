<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ReceivingAddress;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class ReceivingAddressController extends Controller
{
    public function index()
    {
        $addresses = ReceivingAddress::active()->get();
        return view('admin.receiving-addresses.index', compact('addresses'));
    }

    public function create()
    {
        return view('admin.receiving-addresses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'contact_person' => 'nullable|string|max:255',
            'instructions' => 'nullable|string',
            'warehouse_code' => 'nullable|string|max:50',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // Si cette adresse est définie par défaut, retirer le statut par défaut des autres
        if ($request->boolean('is_default')) {
            ReceivingAddress::where('is_default', true)->update(['is_default' => false]);
        }

        ReceivingAddress::create($validated);

        return redirect()->route('admin.receiving-addresses.index')
            ->with('success', 'Adresse de réception créée avec succès');
    }

    public function edit(ReceivingAddress $receivingAddress)
    {
        return view('admin.receiving-addresses.edit', compact('receivingAddress'));
    }

    public function update(Request $request, ReceivingAddress $receivingAddress)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'contact_person' => 'nullable|string|max:255',
            'instructions' => 'nullable|string',
            'warehouse_code' => 'nullable|string|max:50',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // Si cette adresse est définie par défaut, retirer le statut par défaut des autres
        if ($request->boolean('is_default')) {
            ReceivingAddress::where('id', '!=', $receivingAddress->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        $receivingAddress->update($validated);

        return redirect()->route('admin.receiving-addresses.index')
            ->with('success', 'Adresse de réception mise à jour avec succès');
    }

    public function destroy(ReceivingAddress $receivingAddress)
    {
        $receivingAddress->delete();

        return redirect()->route('admin.receiving-addresses.index')
            ->with('success', 'Adresse de réception supprimée avec succès');
    }

    public function downloadPdf(ReceivingAddress $receivingAddress)
    {
        $pdf = Pdf::loadView('admin.receiving-addresses.pdf', compact('receivingAddress'));

        $filename = 'adresse-reception-' . Str::slug($receivingAddress->name) . '.pdf';

        return $pdf->download($filename);
    }

    public function toggleDefault(ReceivingAddress $receivingAddress)
    {
        // Retirer le statut par défaut de toutes les autres adresses
        ReceivingAddress::where('id', '!=', $receivingAddress->id)
            ->where('is_default', true)
            ->update(['is_default' => false]);

        // Définir cette adresse comme par défaut
        $receivingAddress->update(['is_default' => true]);

        return back()->with('success', 'Adresse par défaut mise à jour');
    }

    public function toggleActive(ReceivingAddress $receivingAddress)
    {
        $receivingAddress->update(['is_active' => !$receivingAddress->is_active]);

        return back()->with('success', 'Statut de l\'adresse mis à jour');
    }
}
