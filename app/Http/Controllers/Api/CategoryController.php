<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\CategoryService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class CategoryController extends Controller
{
    public function __construct(private readonly CategoryService $categoryService) {}

    #[OA\Get(
        path: '/api/categories',
        operationId: 'listCategories',
        summary: 'Listar categorías',
        tags: ['Categorías'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Listado de categorías',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Categorías obtenidas correctamente'),
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Category')),
                    ]
                )
            ),
        ]
    )]
    public function index(): JsonResponse
    {
        $categories = $this->categoryService->list();

        return ApiResponse::success(
            CategoryResource::collection($categories),
            'Categorías obtenidas correctamente',
        );
    }

    #[OA\Post(
        path: '/api/categories',
        operationId: 'createCategory',
        summary: 'Crear categoría',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/CategoryInput')),
        tags: ['Categorías'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Categoría creada',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Categoría creada correctamente'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Category'),
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Error de validación', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function store(CategoryRequest $request): JsonResponse
    {
        $category = $this->categoryService->create($request->validated());

        return ApiResponse::created(new CategoryResource($category), 'Categoría creada correctamente');
    }

    #[OA\Get(
        path: '/api/categories/{id}',
        operationId: 'showCategory',
        summary: 'Ver detalle de una categoría',
        tags: ['Categorías'],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'), example: 1)],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Detalle de la categoría',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Categoría obtenida correctamente'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Category'),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Categoría no encontrada', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundError')),
        ]
    )]
    public function show(Category $category): JsonResponse
    {
        return ApiResponse::success(
            new CategoryResource($this->categoryService->find($category)),
            'Categoría obtenida correctamente',
        );
    }

    #[OA\Put(
        path: '/api/categories/{id}',
        operationId: 'updateCategory',
        summary: 'Actualizar categoría',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/CategoryInput')),
        tags: ['Categorías'],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'), example: 1)],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Categoría actualizada',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Categoría actualizada correctamente'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Category'),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Categoría no encontrada', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundError')),
            new OA\Response(response: 422, description: 'Error de validación', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function update(CategoryRequest $request, Category $category): JsonResponse
    {
        $category = $this->categoryService->update($category, $request->validated());

        return ApiResponse::success(new CategoryResource($category), 'Categoría actualizada correctamente');
    }

    #[OA\Delete(
        path: '/api/categories/{id}',
        operationId: 'deleteCategory',
        summary: 'Eliminar categoría',
        tags: ['Categorías'],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'), example: 1)],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Categoría eliminada',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Categoría eliminada correctamente'),
                        new OA\Property(property: 'data', type: 'object', nullable: true, example: null),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Categoría no encontrada', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundError')),
            new OA\Response(response: 409, description: 'La categoría tiene productos asociados', content: new OA\JsonContent(ref: '#/components/schemas/ConflictError')),
        ]
    )]
    public function destroy(Category $category): JsonResponse
    {
        $this->categoryService->delete($category);

        return ApiResponse::success(null, 'Categoría eliminada correctamente');
    }
}
