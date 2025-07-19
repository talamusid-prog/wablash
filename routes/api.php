<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WhatsAppController;
use App\Http\Controllers\Api\BlastController;
use App\Http\Controllers\Api\PhonebookController;
use App\Http\Controllers\Api\IntegrationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// WhatsApp Session Routes
Route::prefix('whatsapp')->group(function () {
    Route::get('/sessions', [WhatsAppController::class, 'index']);
    Route::get('/sessions/active', [WhatsAppController::class, 'getActiveSessions']);
    Route::post('/sessions', [WhatsAppController::class, 'store']);
    Route::get('/sessions/{whatsAppSession}', [WhatsAppController::class, 'show']);
    Route::put('/sessions/{whatsAppSession}', [WhatsAppController::class, 'update']);
    Route::delete('/sessions/{whatsAppSession}', [WhatsAppController::class, 'destroy']);
    Route::get('/sessions/{whatsAppSession}/qr', [WhatsAppController::class, 'getQrCode']);
    Route::post('/sessions/{whatsAppSession}/send', [WhatsAppController::class, 'sendMessage']);
});

// Blast Campaign Routes
Route::prefix('blast')->group(function () {
    Route::get('/campaigns', [BlastController::class, 'index']);
    Route::post('/campaigns', [BlastController::class, 'store']);
    Route::get('/campaigns/{blastCampaign}', [BlastController::class, 'show']);
    Route::put('/campaigns/{blastCampaign}', [BlastController::class, 'update']);
    Route::delete('/campaigns/{blastCampaign}', [BlastController::class, 'destroy']);
    Route::post('/campaigns/{blastCampaign}/start', [BlastController::class, 'start']);
    Route::get('/campaigns/{blastCampaign}/statistics', [BlastController::class, 'statistics']);
    Route::get('/campaigns/{blastCampaign}/messages', [BlastController::class, 'messages']);
});

// Phonebook Routes
Route::prefix('phonebook')->group(function () {
    Route::get('/', [PhonebookController::class, 'index']);
    Route::post('/', [PhonebookController::class, 'store']);
    Route::get('/{phonebook}', [PhonebookController::class, 'show']);
    Route::put('/{phonebook}', [PhonebookController::class, 'update']);
    Route::delete('/{phonebook}', [PhonebookController::class, 'destroy']);
    Route::get('/groups', [PhonebookController::class, 'groups']);
    Route::get('/search', [PhonebookController::class, 'search']);
});

// Frontend API Routes (without prefix)
Route::get('/sessions', [WhatsAppController::class, 'index']);
Route::get('/sessions/active', [WhatsAppController::class, 'getActiveSessions']);
Route::post('/sessions', [WhatsAppController::class, 'store']);
Route::delete('/sessions/{session_id}', [WhatsAppController::class, 'destroy']);
Route::post('/sessions/{session_id}/connect', [WhatsAppController::class, 'connect']);
Route::post('/sessions/{session_id}/reconnect', [WhatsAppController::class, 'reconnect']);
Route::get('/sessions/{session_id}/qr', [WhatsAppController::class, 'getQrCode']);
// Ubah route agar pakai {session_id} (UUID) bukan model binding
Route::get('/sessions/{session_id}/status', [WhatsAppController::class, 'getStatus']);
Route::post('/sessions/{session_id}/test-send', [WhatsAppController::class, 'testSendMessage']);
Route::get('/sessions/{session_id}/grab-groups', [WhatsAppController::class, 'grabGroupContacts']);
Route::get('/sessions/{session_id}/grab-contacts', [WhatsAppController::class, 'grabIndividualContacts']);
Route::get('/sessions/{session_id}/grab-all', [WhatsAppController::class, 'grabAllContacts']);
Route::get('/sessions/{session_id}/contacts', [WhatsAppController::class, 'getSavedContacts']);
Route::delete('/sessions/{session_id}/contacts', [WhatsAppController::class, 'deleteSavedContacts']);
Route::get('/engine-status', [WhatsAppController::class, 'engineStatus']);

Route::get('/campaigns', [BlastController::class, 'index']);
Route::post('/campaigns', [BlastController::class, 'store']);
Route::post('/campaigns/phone-numbers', [BlastController::class, 'getPhoneNumbers']);
Route::get('/campaigns/check-contacts', [BlastController::class, 'checkContacts']);
Route::delete('/campaigns/{blastCampaign}', [BlastController::class, 'destroy']);
Route::post('/campaigns/{blastCampaign}/start', [BlastController::class, 'start']);
Route::post('/campaigns/{blastCampaign}/pause', [BlastController::class, 'pause']);
Route::post('/campaigns/{blastCampaign}/update-status', [BlastController::class, 'updateCampaignStatus']);

Route::get('/messages', [WhatsAppController::class, 'messages']);
Route::get('/messages/{id}', [WhatsAppController::class, 'showMessage']);
Route::delete('/messages/{id}', [WhatsAppController::class, 'deleteMessage']);
Route::post('/messages/{id}/retry', [WhatsAppController::class, 'retryMessage']);

// Phonebook API Routes (without prefix)
Route::get('/phonebook', [PhonebookController::class, 'index']);
Route::post('/phonebook', [PhonebookController::class, 'store']);
Route::get('/phonebook/{phonebook}', [PhonebookController::class, 'show']);
Route::put('/phonebook/{phonebook}', [PhonebookController::class, 'update']);
Route::delete('/phonebook/{phonebook}', [PhonebookController::class, 'destroy']);
Route::get('/phonebook-groups', [PhonebookController::class, 'groups']);
Route::get('/phonebook-search', [PhonebookController::class, 'search']); 

// Grup management API routes
Route::post('/phonebook/groups', [PhonebookController::class, 'storeGroup']);
Route::get('/phonebook/groups', [PhonebookController::class, 'getGroups']);
Route::put('/phonebook/groups/{groupName}', [PhonebookController::class, 'updateGroup']);
Route::delete('/phonebook/groups/{groupName}', [PhonebookController::class, 'deletePhonebookGroup']);

// Integration Routes
Route::prefix('v1/integration')->group(function () {
    Route::get('/system-status', [IntegrationController::class, 'systemStatus']);
    Route::post('/bulk-send', [IntegrationController::class, 'bulkSend']);
    Route::post('/send-template', [IntegrationController::class, 'sendTemplateMessage']);
    Route::post('/import-contacts', [IntegrationController::class, 'importContacts']);
    Route::get('/export-contacts', [IntegrationController::class, 'exportContacts']);
    Route::get('/webhook-config', [IntegrationController::class, 'getWebhookConfig']);
    Route::post('/webhook-config', [IntegrationController::class, 'setWebhookConfig']);
    Route::post('/test-webhook', [IntegrationController::class, 'testWebhook']);
});

