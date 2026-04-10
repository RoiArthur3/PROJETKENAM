<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use App\Models\ProductKeyword;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductCatalogController extends Controller
{
    public function index(Request $request)
    {
        $categories = ProductCategory::query()
            ->with(['keywords' => function ($q) {
                $q->with('countries')->orderBy('keyword');
            }])
            ->orderBy('name')
            ->get();

        $countries = Country::where('is_active', true)->orderBy('name')->get();
        $allKeywords = ProductKeyword::with('category')->orderBy('keyword')->get();

        return view('admin.groupeur.produits-interdits', [
            'categories' => $categories,
            'countries' => $countries,
            'allKeywords' => $allKeywords,
        ]);
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
        ]);

        ProductCategory::updateOrCreate(
            ['name' => trim($validated['name'])],
            ['name' => trim($validated['name'])]
        );

        return redirect()->route('admin.groupeur.product-catalog.index')
            ->with('success', 'Catégorie enregistrée.');
    }

    public function storeKeyword(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'nullable|exists:product_categories,id',
            'keyword' => 'required|string|max:500',
            'status' => 'required|in:allowed,forbidden',
        ]);

        $rawKeywords = $validated['keyword'];
        $keywords = array_map('trim', explode(',', $rawKeywords));
        $keywords = array_filter($keywords, 'strlen');

        $count = 0;
        foreach ($keywords as $kw) {
            $keyword = mb_strtolower(trim($kw));
            if ($keyword) {
                ProductKeyword::updateOrCreate(
                    [
                        'category_id' => $validated['category_id'] ?? null,
                        'keyword' => $keyword,
                    ],
                    [
                        'status' => $validated['status'],
                        'is_active' => true,
                    ]
                );
                $count++;
            }
        }

        return redirect()->route('admin.groupeur.product-catalog.index')
            ->with('success', "{$count} mot-clé(s) enregistré(s).");
    }

    public function toggleKeyword(Request $request, ProductKeyword $keyword)
    {
        $keyword->update([
            'is_active' => !$keyword->is_active,
        ]);

        return redirect()->route('admin.groupeur.product-catalog.index')
            ->with('success', 'Mot-clé mis à jour.');
    }

    public function storeDestinationRestriction(Request $request)
    {
        $validated = $request->validate([
            'country_id' => 'required|exists:countries,id',
            'product_keyword_id' => 'required|exists:product_keywords,id',
            'restriction_type' => 'required|in:allowed,forbidden',
            'notes' => 'nullable|string|max:500',
        ]);

        // Vérifier si la restriction existe déjà
        $existing = DB::table('country_product_keyword')
            ->where('country_id', $validated['country_id'])
            ->where('product_keyword_id', $validated['product_keyword_id'])
            ->first();

        if ($existing) {
            return redirect()->route('admin.groupeur.product-catalog.index')
                ->with('error', 'Cette restriction existe déjà.');
        }

        DB::table('country_product_keyword')->insert([
            'country_id' => $validated['country_id'],
            'product_keyword_id' => $validated['product_keyword_id'],
            'restriction_type' => $validated['restriction_type'],
            'notes' => $validated['notes'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.groupeur.product-catalog.index')
            ->with('success', 'Restriction par pays de destination enregistrée.');
    }

    public function destroyDestinationRestriction(Request $request, $restrictionId)
    {
        DB::table('country_product_keyword')
            ->where('id', $restrictionId)
            ->delete();

        return redirect()->route('admin.groupeur.product-catalog.index')
            ->with('success', 'Restriction supprimée.');
    }

    public function keywordsJson(Request $request)
    {
        $destinationCountryCode = $request->get('destination_country_code');

        $keywords = ProductKeyword::query()
            ->where('is_active', true)
            ->with('countries')
            ->get(['keyword', 'status']);

        $known = collect();
        $forbidden = collect();

        foreach ($keywords as $keyword) {
            $known->push($keyword->keyword);

            // Vérifier si le produit est interdit dans le pays de destination spécifié
            if ($destinationCountryCode && $keyword->isForbiddenInDestinationCountry($destinationCountryCode)) {
                $forbidden->push($keyword->keyword);
            } elseif ($keyword->status === 'forbidden') {
                // Si pas de restriction spécifique mais statut général interdit
                $forbidden->push($keyword->keyword);
            }
        }

        return response()->json([
            'known' => $known->unique()->values(),
            'forbidden' => $forbidden->unique()->values(),
        ]);
    }
}
