<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Expense;
use App\Models\BankAccount;
use App\Models\Fournisseur;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class AccountingController extends Controller
{
    public function dashboard()
    {
        $ca_total = (float) (Invoice::query()->sum('net_amount'));
        $depenses_total = (float) (Expense::query()->sum('montant'));
        $tresorerie = (float) (BankAccount::query()->sum('solde_actuel'));

        $factures_payees = (int) Invoice::where('status', 'payee')->count();
        $factures_attente = (int) Invoice::whereIn('status', ['en_attente','pending','a_facturer'])->count();
        $factures_retard = (int) Invoice::where('status', 'en_retard')->count();

        $top_fournisseurs = Expense::selectRaw('COALESCE(fournisseur, "—") as fournisseur, SUM(montant) as total')
            ->groupBy('fournisseur')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $recent_invoices = Invoice::orderByDesc('issue_date')->orderByDesc('id')->limit(6)->get();

        return view('comptabilite.dashboard', compact(
            'ca_total','depenses_total','tresorerie','factures_payees','factures_attente','factures_retard','top_fournisseurs','recent_invoices'
        ));
    }

    public function balance(Request $request)
    {
        $total_invoices = (float) Invoice::sum('net_amount');
        $total_invoices_paid = (float) Invoice::whereIn('status', ['payee','paid'])->sum('net_amount');
        $total_invoices_unpaid = $total_invoices - $total_invoices_paid;

        $total_expenses = (float) Expense::sum('montant');
        $tresorerie = (float) BankAccount::sum('solde_actuel');

        return view('comptabilite.balance', compact(
            'total_invoices','total_invoices_paid','total_invoices_unpaid','total_expenses','tresorerie'
        ));
    }

    public function journal(Request $request)
    {
        $invoiceEntries = Invoice::select(
                'id',
                'issue_date as date'
            )
            ->selectRaw("invoice_number as reference")
            ->selectRaw("net_amount as montant")
            ->selectRaw("'Facture' as libelle")
            ->selectRaw("'invoice' as type")
            ->get();

        $driver = DB::connection()->getDriverName();
        $concatExpr = $driver === 'sqlite' ? "('DEP-' || id)" : "CONCAT('DEP-', id)";

        $expenseEntries = Expense::select(
                'id',
                'date_depense as date'
            )
            ->selectRaw("COALESCE(reference, $concatExpr) as reference")
            ->selectRaw("montant as montant")
            ->selectRaw("categorie as libelle")
            ->selectRaw("'expense' as type")
            ->get();

        $entries = $invoiceEntries->concat($expenseEntries)->sortBy('date')->values();

        // Paginate the combined collection for the view
        $perPage = (int) ($request->integer('per_page') ?: 15);
        $page = (int) ($request->integer('page') ?: 1);
        $total = $entries->count();
        $items = $entries->slice(($page - 1) * $perPage, $perPage)->values();
        $entries = new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('comptabilite.journal', compact('entries'));
    }
}
