<?php

use App\Http\Controllers\TicketController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::get('/get_all_ticket', [TicketController::class, 'index'])->name('get_all_tickets');
Route::get('/ticket/{ticket}', [TicketController::class, 'show'])->name('get_ticket');

Route::group(['middleware' => ['auth:sanctum',]], function () {
    // Ticket : user action
    Route::get('/ticket', [TicketController::class, 'index'])->name('get_user_ticket');
    Route::post('/ticket', [TicketController::class, 'store'])->name('create_ticket');
    Route::match(['put', 'patch'], '/ticket/{ticket}', [TicketController::class, 'update'])->name('update_ticket');

    // Comments
});

Route::group(['middleware' => ['auth:sanctum', 'staff.api']], function () {
    Route::match(['put', 'patch'], '/ticket/{ticket}/{user}', [TicketController::class, 'assignTicket'])->name('assign_ticket');
});
