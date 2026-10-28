<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

/**
 * Global OpenAPI definition: API info, servers and reusable schemas.
 * Endpoints are documented directly on each controller method.
 */
#[OA\Info(
    version: '1.0.0',
    title: 'Overskull Products API',
    description: 'API REST para la gestión de productos y categorías. Todas las respuestas siguen el formato estándar { success, message, data | errors }.',
    contact: new OA\Contact(name: 'Gustavo Olivares', email: 'gustavoolivares302@gmail.com'),
)]
#[OA\Server(url: L5_SWAGGER_CONST_HOST, description: 'Servidor principal')]
#[OA\Tag(name: 'Categorías', description: 'CRUD de categorías')]
#[OA\Tag(name: 'Productos', description: 'CRUD de productos')]
#[OA\Schema(
    schema: 'Category',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Polos'),
        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Polos y camisetas de la colección Overskull'),
        new OA\Property(property: 'products_count', type: 'integer', example: 3),
        new OA\Property(property: 'created_at', type: 'string', example: '2026-10-28 15:00:00'),
        new OA\Property(property: 'updated_at', type: 'string', example: '2026-10-28 15:00:00'),
    ]
)]
#[OA\Schema(
    schema: 'CategoryInput',
    required: ['name'],
    properties: [
        new OA\Property(property: 'name', type: 'string', maxLength: 100, example: 'Polos'),
        new OA\Property(property: 'description', type: 'string', maxLength: 500, nullable: true, example: 'Polos y camisetas de la colección Overskull'),
    ]
)]
#[OA\Schema(
    schema: 'Product',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Camiseta Overskull'),
        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Camiseta negra edición limitada'),
        new OA\Property(property: 'price', type: 'number', format: 'float', example: 89.90),
        new OA\Property(property: 'stock', type: 'integer', example: 50),
        new OA\Property(property: 'category_id', type: 'integer', example: 1),
        new OA\Property(property: 'category', ref: '#/components/schemas/Category'),
        new OA\Property(property: 'created_at', type: 'string', example: '2026-10-28 15:00:00'),
        new OA\Property(property: 'updated_at', type: 'string', example: '2026-10-28 15:00:00'),
    ]
)]
#[OA\Schema(
    schema: 'ProductInput',
    required: ['name', 'price', 'stock', 'category_id'],
    properties: [
        new OA\Property(property: 'name', type: 'string', maxLength: 150, example: 'Camiseta Overskull'),
        new OA\Property(property: 'description', type: 'string', maxLength: 1000, nullable: true, example: 'Camiseta negra edición limitada'),
        new OA\Property(property: 'price', type: 'number', format: 'float', minimum: 0.01, example: 89.90),
        new OA\Property(property: 'stock', type: 'integer', minimum: 0, example: 50),
        new OA\Property(property: 'category_id', type: 'integer', example: 1),
    ]
)]
#[OA\Schema(
    schema: 'ValidationError',
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: false),
        new OA\Property(property: 'message', type: 'string', example: 'El campo nombre es requerido'),
        new OA\Property(
            property: 'errors',
            type: 'object',
            example: ['name' => ['El campo nombre es requerido'], 'price' => ['El precio debe ser mayor que 0']],
        ),
    ]
)]
#[OA\Schema(
    schema: 'NotFoundError',
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: false),
        new OA\Property(property: 'message', type: 'string', example: 'Recurso no encontrado'),
        new OA\Property(property: 'errors', type: 'object', nullable: true, example: null),
    ]
)]
#[OA\Schema(
    schema: 'ConflictError',
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: false),
        new OA\Property(property: 'message', type: 'string', example: 'No se puede eliminar una categoría que tiene productos asociados'),
        new OA\Property(property: 'errors', type: 'object', nullable: true, example: null),
    ]
)]
class OpenApiSpec {}
