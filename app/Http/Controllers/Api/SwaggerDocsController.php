<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use OpenApi\Attributes as OA;

#[OA\OpenApi(
    info: new OA\Info(
        version: "1.0.0",
        title: "Silva Kit REST API Documentation",
        description: "Enterprise REST API Documentation for Silva Kit Starter Kit",
        contact: new OA\Contact(name: "Ferdy Rahmat", email: "ferdyyrahmat@gmail.com")
    ),
    servers: [
        new OA\Server(url: "http://localhost:8000", description: "Local Artisan Server (Port 8000)"),
        new OA\Server(url: "http://localhost:8080", description: "Development Docker Server (Port 8080)"),
        new OA\Server(url: "/", description: "Current Host Domain")
    ]
)]
#[OA\SecurityScheme(
    securityScheme: "sanctum",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT",
    description: "Enter your Sanctum Personal Access Token"
)]
class SwaggerDocsController extends Controller
{
    #[OA\Get(
        path: "/api/user",
        summary: "Get Authenticated User Profile",
        tags: ["User Profile"],
        security: [["sanctum" => []]],
        responses: [
            new OA\Response(response: 200, description: "User profile details returned successfully"),
            new OA\Response(response: 401, description: "Unauthenticated API request")
        ]
    )]
    public function userProfile()
    {
        return response()->json([
            'success' => true,
            'user'    => auth()->user()
        ]);
    }
}
