<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTOs\LoginData;
use App\DTOs\RegisterData;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * AuthController constructor.
     *
     * @param AuthService $authService The service handling core authentication business logic.
     */
    public function __construct(private readonly AuthService $authService) {}

    /**
     * Register a new user in the system.
     *
     * Validates incoming request credentials, provisions a new user record, 
     * and issues a long-lived Sanctum API token for subsequent requests.
     *
     * @param RegisterRequest $request Validated HTTP registration payload.
     * @return JsonResponse Standardized DTO payload containing access token and user metadata.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->authService->register(RegisterData::fromArray($request->validated()));

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->success([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ], 'User registered successfully', 201);
    }

    /**
     * Authenticate an existing user.
     *
     * Given an email and password, validates the credentials against the datastore.
     * If successful, revokes previously expired credentials (handled internally if needed) 
     * and provisions a fresh access token. Protected against brute-force via route throttling.
     *
     * @param LoginRequest $request Validated HTTP login payload.
     * @return JsonResponse JSON structure including authentication token or generic 401 error.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $token = $this->authService->login(LoginData::fromArray($request->only('email', 'password')));
        if (! $token) {
            return $this->error('Invalid credentials', 401);
        }

        return $this->success([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => auth()->user(),
        ], 'Logged in successfully', 200);
    }

    /**
     * Terminate the current user's active session.
     *
     * Invalidates and deletes the current Personal Access Token resolving 
     * out of the immediate secure channel to prevent replay attacks or session hijacking.
     *
     * @param Request $request Authenticated HTTP request carrying the Bearer token.
     * @return JsonResponse Confirms successful termination of the session state.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success(null, 'Logged out successfully', 200);
    }

    /**
     * Retrieve the authenticated user's contextual data.
     *
     * A lightweight identity hydration endpoint primarily used 
     * by thick clients (SPA/Mobile) to continuously verify session health.
     *
     * @param Request $request
     * @return JsonResponse User resource representation.
     */
    public function me(Request $request): JsonResponse
    {
        return $this->success($request->user(), 'User fetched successfully');
    }
}
