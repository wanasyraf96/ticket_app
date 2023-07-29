<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
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

Route::post('/signin', [AuthController::class, 'signin'])->name('sign_in');


Route::get('/get_all_ticket', [TicketController::class, 'index'])->name('get_all_tickets');
Route::get('/ticket/{ticket}', [TicketController::class, 'show'])->name('get_ticket');
Route::get('/ticket/{ticket}/comments', [CommentController::class, 'index'])->name('get_ticket_comment');

Route::group(['middleware' => ['auth:sanctum',]], function () {
    Route::post('/signout', [AuthController::class, 'signout'])->name('sign_out');

    // Ticket : user action
    Route::get('/tickets', [TicketController::class, 'index'])->name('get_user_ticket');
    Route::post('/ticket', [TicketController::class, 'store'])->name('create_ticket');
    Route::match(['put', 'patch'], '/ticket/{ticket}', [TicketController::class, 'update'])->name('update_ticket');
    Route::delete('/ticket/{ticket}', [TicketController::class, 'destroy'])->name('delete_ticket');

    // Comments
    Route::post('/ticket/{ticket}/comments', [CommentController::class, 'store'])->name('create_ticket_comment');
    Route::delete('/ticket/{ticket}/comments', [CommentController::class, 'destroy'])->name('delete_ticket_comment');
});

Route::group(['middleware' => ['auth:sanctum', 'staff.api']], function () {
    Route::match(['put', 'patch'], '/ticket/{ticket}/{user}', [TicketController::class, 'assignTicket'])->name('assign_ticket');
    Route::post('/ticket/{ticket}', [TicketController::class, 'updateStatus'])->name('update_ticket_status');
});
