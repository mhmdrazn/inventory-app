<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use OpenApi\Attributes as OA;

/**
 * Base OpenAPI info + security scheme for the Warehaus API.
 *
 * Kept as an empty controller purely so the l5-swagger annotation scanner
 * has a well-known place to pick up the top-level metadata.
 */
#[OA\Info(
    title: 'Warehaus API',
    version: '1.0.0',
    description: 'REST API v1 untuk sistem manajemen inventaris Warehaus. Setiap endpoint mengembalikan envelope { success, message, data }.',
)]
#[OA\Server(
    url: L5_SWAGGER_CONST_HOST,
    description: 'Warehaus API server',
)]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Personal Access Token',
    description: 'Login via POST /api/v1/login to obtain a token.',
)]
#[OA\Schema(
    schema: 'ApiEnvelope',
    required: ['success', 'message'],
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(property: 'message', type: 'string', example: 'OK'),
        new OA\Property(property: 'data', type: 'object', nullable: true),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'ApiError',
    required: ['success', 'message'],
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: false),
        new OA\Property(property: 'message', type: 'string', example: 'Validasi gagal.'),
        new OA\Property(property: 'data', type: 'object', nullable: true),
    ],
    type: 'object',
)]
class ApiInfoController extends Controller {}

