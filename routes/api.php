<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;
use App\Http\Controllers\AuthController;


Route::prefix('auth')->middleware('api')->controller(AuthController::class)->group(function () {
    Route::post('login', 'login')->name('auth.login');
    Route::post('register', 'register')->name('auth.register');
    Route::post('refresh', 'refresh')->name('auth.refresh');

    Route::middleware('jwt.auth.token')->group(function () {
        Route::post('logout', 'logout')->name('auth.logout');
        Route::get('user-profile', 'userProfile')->name('auth.user.profile');
        Route::post('send-registration-invite', 'sendRegistrationInvite')->name('auth.sendRegistrationInvite');
    });
});


Route::get('test-route', function () {
    return response()->json(['message' => 'API is working!']);
});


Route::post('test-mail-sent', function (Request $request) {
    try {
        $mailData = [
            'title' => 'Email Title',
            'message' => 'This is a test e-mail directed to only students of Lutfi Musiqi High School.',
            'session_title' => $request->session_title
        ];

        Mail::to('florentplakolli2@gmail.com')->send(new TestMail($mailData));

        return response()->json(['status' => 'success']);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'type' => class_basename($e),
            ],
        ], 500);
    }
});

Route::get('zen-quote', function() {
    try {
        $response = Http::get("https://zenquotes.io/api/random");

        if($response->successful()) {
            $quote = $response->json()[0];

            return response()->json([
                'success' => true,
                'quote' => [
                    'text' => $quote['q'],
                    'author' => $quote['a'],
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch quote from external API',
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch quote from extrenal API',
            'error' => [
            'message' => 'Failed to fetch quote',
            'details' => $e->getMessage()
        ]
        ]);
    }
});
