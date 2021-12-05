<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Models\Ticket;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    $last_four_resolved = Ticket::where("state_id", 3)->orderByDesc("updated_at")->limit(4)->get();
    $counter = count(Ticket::where("state_id", 2)->get());
    return view('pages.index', ["tickets" => $last_four_resolved, "counter" => $counter]);
})->name("home");

Route::middleware("guest")->group(function () {
    Route::match(["get", "post"], "/register", [AuthController::class, "handleRegister"])->name("register");
    Route::match(["get", "post"], "/login", [AuthController::class, "handleLogin"])->name("register");
});

Route::middleware("auth")->group(function () {
    Route::get("/profile/tickets", [UserController::class, "getTickets"])->name("list_tickets");
    Route::match(["get", "post"], "/profile/tickets/new", [UserController::class, "newTicket"])->name("new_tickets");
    Route::get("/profile/tickets/{id}", [UserController::class, "getOneTicket"])->name("show_ticket");
    Route::any("/profile/tickets/{id}/delete", [UserController::class, "deleteTicket"])->name("delete_ticket");
    Route::post("/exit", [AuthController::class, "exit"]);

    Route::middleware("admin")->group(function () {
        Route::get("/superadmin", [AdminController::class, "adminPage"])->name("admin");
        Route::match(["get", "post"], "/superadmin/categories", [AdminController::class, "adminCategories"])->name("admin_categories");
        Route::match(["get", "post"], "/superadmin/tickets", [AdminController::class, "adminTickets"])->name("admin_tickets");
    });
});
