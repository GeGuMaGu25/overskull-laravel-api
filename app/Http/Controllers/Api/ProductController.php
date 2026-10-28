<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ProductController extends Controller
{
    public function __construct(private readonly ProductService $productService) {}

    #[OA\Get(
        path: '/api/products',
        operationId: 'listProducts',
        summary: 'Listar productos',
        description: 'Permite filtrar por nombre (search) y por categoría (category_id).',
        tags: ['Productos'],
        parameters: [
            new OA\Parameter(name: 'search', in: 'query', required: false, description: 'Texto a buscar en el nombre', schema: new OA\Schema(type: 'string'), example: 'camiseta'),
            new OA\Parameter(name: 'category_id', in: 'query', required: false, description: 'ID de la categoría', schema: new OA\Schema(type: 'integer'), example: 1),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Listado de productos',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Productos obtenidos correctamente'),
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Product')),
                    ]
                )
            ),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $products = $this->productService->list($request->only(['search', 'category_id']));

        return ApiResponse::success(
            ProductResource::collection($products),
            'Productos obtenidos correctamente',
        );
    }

    #[OA\Post(
        path: '/api/products',
        operationId: 'createProduct',
        summary: 'Crear producto',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/ProductInput')),
        tags: ['Productos'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Producto creado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Producto creado correctamente'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Product'),
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Error de validación', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function store(ProductRequest $request): JsonResponse
    {
        $product = $this->productService->create($request->validated());

        return ApiResponse::created(new ProductResource($product), 'Producto creado correctamente');
    }

    #[OA\Get(
        path: '/api/products/{id}',
        operationId: 'showProduct',
        summary: 'Ver detalle de un producto',
        tags: ['Productos'],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'), example: 1)],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Detalle del producto',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Producto obtenido correctamente'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Product'),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Producto no encontrado', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundError')),
        ]
    )]
    public function show(Product $product): JsonResponse
    {
        return ApiResponse::success(
            new ProductResource($this->productService->find($product)),
            'Producto obtenido correctamente',
        );
    }

    #[OA\Put(
        path: '/api/products/{id}',
        operationId: 'updateProduct',
        summary: 'Actualizar producto',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/ProductInput')),
        tags: ['Productos'],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'), example: 1)],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Producto actualizado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Producto actualizado correctamente'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/Product'),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Producto no encontrado', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundError')),
            new OA\Response(response: 422, description: 'Error de validación', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function update(ProductRequest $request, Product $product): JsonResponse
    {
        $product = $this->productService->update($product, $request->validated());

        return ApiResponse::success(new ProductResource($product), 'Producto actualizado correctamente');
    }

    #[OA\Delete(
        path: '/api/products/{id}',
        operationId: 'deleteProduct',
        summary: 'Eliminar producto',
        tags: ['Productos'],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'), example: 1)],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Producto eliminado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Producto eliminado correctamente'),
                        new OA\Property(property: 'data', type: 'object', nullable: true, example: null),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Producto no encontrado', content: new OA\JsonContent(ref: '#/components/schemas/NotFoundError')),
        ]
    )]
    public function destroy(Product $product): JsonResponse
    {
        $this->productService->delete($product);

        return ApiResponse::success(null, 'Producto eliminado correctamente');
    }
}
