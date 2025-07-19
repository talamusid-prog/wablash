<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\PhonebookController;
use App\Http\Controllers\Web\IntegrationController;
use App\Http\Controllers\Web\AuthController;

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Create default admin user route (for development)
Route::post('/create-admin', [AuthController::class, 'createDefaultAdmin'])->name('create-admin');

// Home route - redirect to dashboard if authenticated, otherwise to login
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
})->name('home');

// Protected routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/sessions', [DashboardController::class, 'sessions'])->name('sessions.index');
    Route::get('/sessions/create', [DashboardController::class, 'createSession'])->name('sessions.create');
    Route::get('/campaigns', [DashboardController::class, 'campaigns'])->name('campaigns');
    Route::get('/campaigns/create', [DashboardController::class, 'createCampaign'])->name('campaigns.create');
    Route::get('/messages', [DashboardController::class, 'messages'])->name('messages');
    Route::get('/test-send', [DashboardController::class, 'testSend'])->name('test-send');

    // Phonebook routes - custom routes harus di atas resource route
    Route::get('/phonebook/template', [PhonebookController::class, 'template'])->name('phonebook.template');
    Route::post('/phonebook/import', [PhonebookController::class, 'import'])->name('phonebook.import');
    Route::get('/phonebook/export', [PhonebookController::class, 'export'])->name('phonebook.export');
    Route::get('/phonebook/grabber', [PhonebookController::class, 'grabber'])->name('phonebook.grabber');
    Route::post('/phonebook/grab-contacts', [PhonebookController::class, 'grabContacts'])->name('phonebook.grab-contacts');
    Route::get('/phonebook/get-grabbed-contacts', [PhonebookController::class, 'getGrabbedContacts'])->name('phonebook.get-grabbed-contacts');
    Route::post('/phonebook/import-grabbed-contacts', [PhonebookController::class, 'importGrabbedContacts'])->name('phonebook.import-grabbed-contacts');
    Route::get('/phonebook/group/{groupId}/participants', [PhonebookController::class, 'groupParticipants'])->name('phonebook.group-participants');
    Route::get('/phonebook/individual-contacts', [PhonebookController::class, 'individualContacts'])->name('phonebook.individual-contacts');
    Route::delete('/phonebook/group/{groupId}/delete', [PhonebookController::class, 'deleteGroup'])->name('phonebook.delete-group');
    Route::get('/phonebook/manual-group/{groupName}/participants', [PhonebookController::class, 'manualGroupParticipants'])->name('phonebook.manual-group-participants');
    Route::delete('/phonebook/manual-group/{groupName}/contact/{contactId}/delete', [PhonebookController::class, 'deleteManualGroupContact'])->name('phonebook.delete-manual-group-contact');

    // Grup management routes
    Route::get('/phonebook/create-group', [PhonebookController::class, 'createGroup'])->name('phonebook.create-group');
    Route::post('/phonebook/store-group', [PhonebookController::class, 'storeGroup'])->name('phonebook.store-group');
    Route::get('/phonebook/groups', [PhonebookController::class, 'getGroups'])->name('phonebook.get-groups');
    Route::put('/phonebook/group/{groupName}/update', [PhonebookController::class, 'updateGroup'])->name('phonebook.update-group');
    Route::delete('/phonebook/manual-group/{groupName}/delete', [PhonebookController::class, 'deletePhonebookGroup'])->name('phonebook.delete-phonebook-group');
    Route::resource('phonebook', PhonebookController::class);

    // Test route untuk debugging
    Route::get('/test-template', function() {
        return response()->json(['message' => 'Test route berfungsi']);
    });
    


    // Integration routes
    Route::prefix('integration')->name('integration.')->group(function () {
        Route::get('/', [IntegrationController::class, 'index'])->name('index');
        Route::get('/documentation', [IntegrationController::class, 'documentation'])->name('documentation');
        Route::get('/sdk', [IntegrationController::class, 'sdk'])->name('sdk');
        Route::get('/testing', [IntegrationController::class, 'testing'])->name('testing');
        Route::get('/webhook', [IntegrationController::class, 'webhook'])->name('webhook');
        Route::get('/support', [IntegrationController::class, 'support'])->name('support');
        Route::get('/download-sdk/{language}', [IntegrationController::class, 'downloadSdk'])->name('download-sdk');
        
        // API routes for integration dashboard
        Route::get('/api-stats', [IntegrationController::class, 'getApiStats'])->name('api-stats');
        Route::get('/recent-calls', [IntegrationController::class, 'getRecentApiCalls'])->name('recent-calls');
        Route::post('/test-endpoint', [IntegrationController::class, 'testApiEndpoint'])->name('test-endpoint');
    });

    // API Key management routes
    Route::prefix('integration/keys')->name('integration.keys.')->group(function () {
        Route::get('/', [App\Http\Controllers\Web\ApiKeyController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\Web\ApiKeyController::class, 'store'])->name('store');
        Route::put('/{apiKey}', [App\Http\Controllers\Web\ApiKeyController::class, 'update'])->name('update');
        Route::delete('/{apiKey}', [App\Http\Controllers\Web\ApiKeyController::class, 'destroy'])->name('destroy');
        Route::get('/usage-stats', [App\Http\Controllers\Web\ApiKeyController::class, 'getUsageStats'])->name('usage-stats');
    });


});
