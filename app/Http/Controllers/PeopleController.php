<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\Request;
use App\Http\Requests\PeopleRequest;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PeopleController extends Controller
{
    /**
     * Display a listing of people with optional filters
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Person::query();

            // Filtro por tipo
            if ($request->has('type') && !empty($request->type)) {
                $query->byType($request->type);
            }

            // Filtro por nome
            if ($request->has('name') && !empty($request->name)) {
                $query->byName($request->name);
            }

            // Filtro por CPF
            if ($request->has('cpf') && !empty($request->cpf)) {
                $query->byCpf($request->cpf);
            }

            // Ordenação
            $orderBy = $request->get('order_by', 'created_at');
            $orderDirection = $request->get('order_direction', 'desc');

            $allowedOrderBy = ['name', 'cpf', 'type', 'created_at', 'updated_at'];
            if (in_array($orderBy, $allowedOrderBy)) {
                $query->orderBy($orderBy, $orderDirection);
            }

            // Paginação
            $perPage = $request->get('per_page', 15);
            $perPage = min($perPage, 100); // Máximo de 100 itens por página

            if ($request->has('paginate') && $request->paginate === 'true') {
                $people = $query->paginate($perPage);
                return response()->json([
                    'status' => 'success',
                    'data' => $people->items(),
                    'pagination' => [
                        'current_page' => $people->currentPage(),
                        'last_page' => $people->lastPage(),
                        'per_page' => $people->perPage(),
                        'total' => $people->total(),
                    ],
                    'message' => 'People retrieved successfully'
                ]);
            } else {
                $people = $query->get();
                return response()->json([
                    'status' => 'success',
                    'data' => $people,
                    'message' => 'People retrieved successfully'
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error retrieving people: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created person
     */
    public function store(PeopleRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();

            // Remove caracteres especiais do CPF
            $validatedData['cpf'] = preg_replace('/\D/', '', $validatedData['cpf']);

            $person = Person::create($validatedData);

            return response()->json([
                'status' => 'success',
                'data' => $person,
                'message' => 'Pessoa cadastrada com sucesso'
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao criar pessoa: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified person
     */
    public function show($id): JsonResponse
    {
        try {
            $person = Person::findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => $person,
                'message' => 'Person retrieved successfully'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pessoa não encontrada'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao buscar pessoa: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified person
     */
    public function update(PeopleRequest $request, $id): JsonResponse
    {
        try {
            $person = Person::findOrFail($id);
            $validatedData = $request->validated();

            // Remove caracteres especiais do CPF
            $validatedData['cpf'] = preg_replace('/\D/', '', $validatedData['cpf']);

            $person->update($validatedData);

            return response()->json([
                'status' => 'success',
                'data' => $person->fresh(),
                'message' => 'Pessoa atualizada com sucesso'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pessoa não encontrada'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao atualizar pessoa: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified person
     */
    public function destroy($id): JsonResponse
    {
        try {
            $person = Person::findOrFail($id);
            $person->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Pessoa deletada com sucesso'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pessoa não encontrada'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao deletar pessoa: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search people by multiple criteria
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $searchTerm = $request->get('q', '');

            if (empty($searchTerm)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Termo de busca é obrigatório'
                ], 400);
            }

            $people = Person::where('name', 'like', '%' . $searchTerm . '%')
                ->orWhere('cpf', 'like', '%' . $searchTerm . '%')
                ->orWhere('email', 'like', '%' . $searchTerm . '%')
                ->orderBy('name')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $people,
                'message' => 'Search completed successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro na busca: ' . $e->getMessage()
            ], 500);
        }
    }
}
