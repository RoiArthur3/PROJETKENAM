<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

abstract class ResourceController extends BaseController
{
    /**
     * Le modèle associé au contrôleur
     */
    protected $model;

    /**
     * Règles de validation pour la création
     */
    protected $storeRules = [];

    /**
     * Règles de validation pour la mise à jour
     */
    protected $updateRules = [];

    /**
     * Champs à inclure dans la réponse
     */
    protected $with = [];

    /**
     * Affiche une liste des ressources
     */
    public function index(Request $request): JsonResponse
    {
        $query = $this->model::query();

        // Charger les relations
        if (!empty($this->with)) {
            $query->with($this->with);
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $items = $query->paginate($perPage);

        return $this->success($items);
    }

    /**
     * Stocke une nouvelle ressource
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), $this->storeRules);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        try {
            $item = $this->model::create($request->all());
            return $this->success($item, 'Resource created successfully', 201);
        } catch (\Exception $e) {
            return $this->error('Failed to create resource', 500);
        }
    }

    /**
     * Affiche la ressource spécifiée
     */
    public function show($id): JsonResponse
    {
        try {
            $item = $this->model::with($this->with)->findOrFail($id);
            return $this->success($item);
        } catch (\Exception $e) {
            return $this->error('Resource not found', 404);
        }
    }

    /**
     * Met à jour la ressource spécifiée
     */
    public function update(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), $this->updateRules);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        try {
            $item = $this->model::findOrFail($id);
            $item->update($request->all());
            return $this->success($item, 'Resource updated successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to update resource', 500);
        }
    }

    /**
     * Supprime la ressource spécifiée
     */
    public function destroy($id): JsonResponse
    {
        try {
            $item = $this->model::findOrFail($id);
            $item->delete();
            return $this->success(null, 'Resource deleted successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to delete resource', 500);
        }
    }
}
