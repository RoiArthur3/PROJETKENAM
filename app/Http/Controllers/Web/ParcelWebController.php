<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Parcel;
use Illuminate\Http\Request;

class ParcelWebController extends Controller
{
    public function index(Request $request)
    {
        $parcels = Parcel::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $parcelsCount = Parcel::where('user_id', $request->user()->id)->count();

        return view('parcels.index', [
            'parcels' => $parcels,
            'parcelsCount' => $parcelsCount
        ]);
    }

    public function dashboard(Request $request)
    {
        $parcels = Parcel::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $parcelsCount = Parcel::where('user_id', $request->user()->id)->count();

        return view('parcels.index', [
            'parcels' => $parcels,
            'parcelsCount' => $parcelsCount
        ]);
    }

    public function show($id)
    {
        $parcel = Parcel::with(['photos', 'statusHistory', 'invoice'])
            ->findOrFail($id);

        return view('parcels.show', [
            'parcel' => $parcel
        ]);
    }

    public function create()
    {
        return view('parcels.create');
    }

    public function showUploadForm($id)
    {
        $parcel = Parcel::findOrFail($id);

        return view('parcels.upload', [
            'parcel' => $parcel
        ]);
    }

    public function upload(Request $request, $id)
    {
        $parcel = Parcel::findOrFail($id);

        $request->validate([
            'photos' => 'required|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:5120',
            'type' => 'required|in:package,content'
        ]);

        // Le traitement réel de l'upload est géré par l'API (ParcelController)
        // On fait simplement une redirection vers la page de détails

        return redirect()->route('parcels.show', $parcel->id)
            ->with('success', 'Photos téléchargées avec succès');
    }
}
