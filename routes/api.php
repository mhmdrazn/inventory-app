<?php

use App\Http\Controllers\Api\V1;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

Route::prefix('v1')->name('api.v1.')->group(function () {

    /**
     * Issue a Sanctum token for API consumers.
     */
    Route::post('/login', function (Request $request) {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string'],
        ]);

        $user = User::with('role')->where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Kredensial tidak valid.'],
            ]);
        }

        $token = $user->createToken($credentials['device_name'] ?? 'api-token')->plainTextToken;

        return response()->json([
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role?->name,
                ],
            ],
            'message' => 'Login successful.',
            'status' => 200,
        ]);
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', function (Request $request) {
            $request->user()->currentAccessToken()?->delete();

            return response()->json([
                'data' => null,
                'message' => 'Logged out successfully.',
                'status' => 200,
            ]);
        });

        Route::get('/user', function (Request $request) {
            $user = $request->user()->load('role');

            return response()->json([
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role?->name,
                ],
                'message' => 'Authenticated user retrieved.',
                'status' => 200,
            ]);
        });

        // Products
        Route::apiResource('products', V1\ProductController::class);

        // Borrowings
        Route::apiResource('borrowings', V1\BorrowingController::class);
        Route::patch('borrowings/{borrowing}/return', [V1\BorrowingController::class, 'returnItems']);

        // Categories
        Route::apiResource('categories', V1\CategoryController::class)->only(['index', 'show']);

        // Dashboard stats
        Route::get('dashboard/stats', [V1\DashboardController::class, 'stats']);
    });
});
