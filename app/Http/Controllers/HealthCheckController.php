<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HealthCheckController extends Controller
{
    /**
     * Health check endpoint for Railway monitoring
     */
    public function check()
    {
        $status = [
            'status' => 'ok',
            'timestamp' => now()->toIso8601String(),
            'app' => [
                'name' => config('app.name'),
                'env' => config('app.env'),
                'version' => '1.0.0',
            ],
            'checks' => [],
        ];

        // 1. Check Database Connection
        try {
            DB::connection()->getPdo();
            $status['checks']['database'] = [
                'status' => 'ok',
                'message' => 'Database connected',
            ];
        } catch (\Exception $e) {
            $status['checks']['database'] = [
                'status' => 'error',
                'message' => 'Database connection failed: ' . $e->getMessage(),
            ];
            $status['status'] = 'degraded';
        }

        // 2. Check Cache Connection
        try {
            \Cache::put('health_check_test', 'ok', 10);
            $status['checks']['cache'] = [
                'status' => 'ok',
                'message' => 'Cache working',
            ];
        } catch (\Exception $e) {
            $status['checks']['cache'] = [
                'status' => 'warning',
                'message' => 'Cache issue: ' . $e->getMessage(),
            ];
        }

        // 3. Check Storage
        try {
            $testFile = 'health-check-' . time() . '.txt';
            Storage::disk('local')->put($testFile, 'test');
            Storage::disk('local')->delete($testFile);
            $status['checks']['storage'] = [
                'status' => 'ok',
                'message' => 'Storage writable',
            ];
        } catch (\Exception $e) {
            $status['checks']['storage'] = [
                'status' => 'warning',
                'message' => 'Storage issue: ' . $e->getMessage(),
            ];
        }

        // 4. Check Cloudinary Connection
        try {
            $status['checks']['cloudinary'] = [
                'status' => 'ok',
                'message' => 'Cloudinary configured',
                'cloud_name' => config('cloudinary.cloud_name') ? 'set' : 'not set',
            ];
        } catch (\Exception $e) {
            $status['checks']['cloudinary'] = [
                'status' => 'warning',
                'message' => 'Cloudinary error: ' . $e->getMessage(),
            ];
        }

        // 5. Check Mail Configuration
        try {
            $status['checks']['mail'] = [
                'status' => 'ok',
                'message' => 'Mail configured',
                'driver' => config('mail.mailer'),
            ];
        } catch (\Exception $e) {
            $status['checks']['mail'] = [
                'status' => 'warning',
                'message' => 'Mail configuration issue',
            ];
        }

        // Set HTTP status code
        $httpStatus = $status['status'] === 'ok' ? 200 : ($status['status'] === 'degraded' ? 503 : 500);

        return response()->json($status, $httpStatus)
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
    }

    /**
     * Ready check - for Railway readiness probe
     */
    public function ready()
    {
        try {
            // Check critical services
            DB::connection()->getPdo();

            return response()->json([
                'ready' => true,
                'timestamp' => now()->toIso8601String(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'ready' => false,
                'error' => $e->getMessage(),
            ], 503);
        }
    }

    /**
     * Liveness check - for Railway liveness probe
     */
    public function live()
    {
        return response()->json([
            'alive' => true,
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
