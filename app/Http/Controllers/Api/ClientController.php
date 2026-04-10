<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientAddress;
use App\Models\ClientContact;
use App\Models\ClientDocument;
use App\Http\Requests\Client\StoreClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Http\Requests\Client\StoreClientAddressRequest;
use App\Http\Requests\Client\StoreClientContactRequest;
use App\Http\Requests\Client\StoreClientDocumentRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ClientController extends Controller
{
    /**
     * Display a listing of clients.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Client::with(['user', 'assignedAgent', 'primaryAddress', 'primaryContact']);

        // Filters
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('client_code', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->has('client_type')) {
            $query->where('client_type', $request->client_type);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->has('is_vip')) {
            $query->where('is_vip', $request->boolean('is_vip'));
        }

        if ($request->has('payment_terms')) {
            $query->where('payment_terms', $request->payment_terms);
        }

        if ($request->has('assigned_agent_id')) {
            $query->where('assigned_agent_id', $request->assigned_agent_id);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $clients = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $clients,
            'message' => 'Clients retrieved successfully'
        ]);
    }

    /**
     * Store a newly created client.
     */
    public function store(StoreClientRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $clientData = $request->validated();
            $clientData['client_code'] = Client::generateClientCode();
            $clientData['registration_date'] = now();

            $client = Client::create($clientData);

            // Create primary address if provided
            if ($request->has('address') && !empty($request->address)) {
                $addressData = $request->address;
                $addressData['client_id'] = $client->id;
                $addressData['address_type'] = 'shipping';
                $addressData['is_primary'] = true;
                ClientAddress::create($addressData);
            }

            // Create primary contact if provided
            if ($request->has('contact') && !empty($request->contact)) {
                $contactData = $request->contact;
                $contactData['client_id'] = $client->id;
                $contactData['contact_type'] = 'primary';
                $contactData['is_primary'] = true;
                ClientContact::create($contactData);
            }

            DB::commit();

            $client->load(['user', 'assignedAgent', 'primaryAddress', 'primaryContact']);

            return response()->json([
                'success' => true,
                'data' => $client,
                'message' => 'Client created successfully'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create client',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified client.
     */
    public function show(Client $client): JsonResponse
    {
        $client->load([
            'user',
            'assignedAgent',
            'addresses',
            'contacts',
            'documents' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'parcels' => function ($query) {
                $query->orderBy('created_at', 'desc')->limit(10);
            },
            'invoices' => function ($query) {
                $query->orderBy('created_at', 'desc')->limit(10);
            }
        ]);

        return response()->json([
            'success' => true,
            'data' => $client,
            'message' => 'Client retrieved successfully'
        ]);
    }

    /**
     * Update the specified client.
     */
    public function update(UpdateClientRequest $request, Client $client): JsonResponse
    {
        try {
            DB::beginTransaction();

            $client->update($request->validated());

            // Update primary address if provided
            if ($request->has('address') && !empty($request->address)) {
                $addressData = $request->address;
                $primaryAddress = $client->primaryAddress();
                
                if ($primaryAddress) {
                    $primaryAddress->update($addressData);
                } else {
                    $addressData['client_id'] = $client->id;
                    $addressData['address_type'] = 'shipping';
                    $addressData['is_primary'] = true;
                    ClientAddress::create($addressData);
                }
            }

            DB::commit();

            $client->load(['user', 'assignedAgent', 'primaryAddress', 'primaryContact']);

            return response()->json([
                'success' => true,
                'data' => $client,
                'message' => 'Client updated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update client',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified client.
     */
    public function destroy(Client $client): JsonResponse
    {
        try {
            // Check if client has active parcels or invoices
            $hasActiveParcels = $client->parcels()->whereIn('status', ['announced', 'received', 'inspected', 'grouped', 'in_transit'])->exists();
            $hasUnpaidInvoices = $client->invoices()->where('status', '!=', 'paid')->exists();

            if ($hasActiveParcels || $hasUnpaidInvoices) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete client with active parcels or unpaid invoices'
                ], 422);
            }

            $client->delete();

            return response()->json([
                'success' => true,
                'message' => 'Client deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete client',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get client statistics.
     */
    public function statistics(Client $client): JsonResponse
    {
        $stats = [
            'total_parcels' => $client->parcels()->count(),
            'active_parcels' => $client->parcels()->whereIn('status', ['announced', 'received', 'inspected', 'grouped', 'in_transit'])->count(),
            'completed_parcels' => $client->parcels()->where('status', 'delivered')->count(),
            'total_invoices' => $client->invoices()->count(),
            'paid_invoices' => $client->invoices()->where('status', 'paid')->count(),
            'unpaid_invoices' => $client->invoices()->where('status', '!=', 'paid')->count(),
            'total_spent' => $client->invoices()->where('status', 'paid')->sum('total_amount'),
            'average_parcel_value' => $client->parcels()->avg('declared_value') ?? 0,
            'most_used_transport_mode' => $client->parcels()
                ->selectRaw('transport_mode, COUNT(*) as count')
                ->groupBy('transport_mode')
                ->orderByDesc('count')
                ->first()?->transport_mode,
            'last_activity' => $client->parcels()->max('updated_at'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
            'message' => 'Client statistics retrieved successfully'
        ]);
    }

    /**
     * Toggle client status (active/inactive).
     */
    public function toggleStatus(Client $client): JsonResponse
    {
        $client->is_active = !$client->is_active;
        $client->save();

        return response()->json([
            'success' => true,
            'data' => $client,
            'message' => "Client " . ($client->is_active ? 'activated' : 'deactivated') . " successfully"
        ]);
    }

    /**
     * Add address to client.
     */
    public function addAddress(StoreClientAddressRequest $request, Client $client): JsonResponse
    {
        $addressData = $request->validated();
        $addressData['client_id'] = $client->id;

        $address = ClientAddress::create($addressData);

        return response()->json([
            'success' => true,
            'data' => $address,
            'message' => 'Address added successfully'
        ], 201);
    }

    /**
     * Add contact to client.
     */
    public function addContact(StoreClientContactRequest $request, Client $client): JsonResponse
    {
        $contactData = $request->validated();
        $contactData['client_id'] = $client->id;

        $contact = ClientContact::create($contactData);

        return response()->json([
            'success' => true,
            'data' => $contact,
            'message' => 'Contact added successfully'
        ], 201);
    }

    /**
     * Upload document for client.
     */
    public function uploadDocument(StoreClientDocumentRequest $request, Client $client): JsonResponse
    {
        try {
            $file = $request->file('document');
            $documentData = $request->validated();
            
            // Store file
            $filePath = $file->store('client_documents', 'public');
            
            $documentData['client_id'] = $client->id;
            $documentData['file_path'] = $filePath;
            $documentData['file_size'] = $file->getSize();
            $documentData['mime_type'] = $file->getMimeType();
            $documentData['uploaded_by'] = auth()->id();

            $document = ClientDocument::create($documentData);

            return response()->json([
                'success' => true,
                'data' => $document,
                'message' => 'Document uploaded successfully'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload document',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update client statistics.
     */
    public function updateStatistics(Client $client): JsonResponse
    {
        $client->updateStatistics();

        return response()->json([
            'success' => true,
            'data' => $client,
            'message' => 'Client statistics updated successfully'
        ]);
    }

    /**
     * Get clients by agent.
     */
    public function getByAgent(Request $request, int $agentId): JsonResponse
    {
        $clients = Client::where('assigned_agent_id', $agentId)
            ->with(['user', 'assignedAgent', 'primaryAddress'])
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $clients,
            'message' => 'Agent clients retrieved successfully'
        ]);
    }

    /**
     * Search clients by multiple criteria.
     */
    public function advancedSearch(Request $request): JsonResponse
    {
        $query = Client::with(['user', 'assignedAgent', 'primaryAddress']);

        // Advanced filters
        if ($request->has('total_orders_min')) {
            $query->where('total_orders', '>=', $request->total_orders_min);
        }

        if ($request->has('total_orders_max')) {
            $query->where('total_orders', '<=', $request->total_orders_max);
        }

        if ($request->has('total_revenue_min')) {
            $query->where('total_revenue', '>=', $request->total_revenue_min);
        }

        if ($request->has('total_revenue_max')) {
            $query->where('total_revenue', '<=', $request->total_revenue_max);
        }

        if ($request->has('registration_date_from')) {
            $query->whereDate('registration_date', '>=', $request->registration_date_from);
        }

        if ($request->has('registration_date_to')) {
            $query->whereDate('registration_date', '<=', $request->registration_date_to);
        }

        if ($request->has('last_order_date_from')) {
            $query->whereDate('last_order_date', '>=', $request->last_order_date_from);
        }

        if ($request->has('last_order_date_to')) {
            $query->whereDate('last_order_date', '<=', $request->last_order_date_to);
        }

        $clients = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $clients,
            'message' => 'Advanced search completed successfully'
        ]);
    }
}
