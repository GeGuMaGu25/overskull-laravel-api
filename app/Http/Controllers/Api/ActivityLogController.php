<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ActivityLogResource;
use App\Services\ActivityLogService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ActivityLogController extends Controller
{
    public function __construct(private readonly ActivityLogService $activityLogService) {}

    #[OA\Get(
        path: '/api/activity-logs',
        operationId: 'listActivityLogs',
        summary: 'Listar el log de auditoría',
        description: 'Devuelve las últimas acciones (create / update / delete) registradas sobre productos y categorías.',
        tags: ['Auditoría'],
        parameters: [
            new OA\Parameter(name: 'log_name', in: 'query', required: false, schema: new OA\Schema(type: 'string', enum: ['product', 'category'])),
            new OA\Parameter(name: 'event', in: 'query', required: false, schema: new OA\Schema(type: 'string', enum: ['created', 'updated', 'deleted'])),
            new OA\Parameter(name: 'limit', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 50, maximum: 200)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Registros de auditoría',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Registros de auditoría obtenidos correctamente'),
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/ActivityLog')),
                    ]
                )
            ),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $activities = $this->activityLogService->latest($request->only(['log_name', 'event', 'limit']));

        return ApiResponse::success(
            ActivityLogResource::collection($activities),
            'Registros de auditoría obtenidos correctamente',
        );
    }
}
