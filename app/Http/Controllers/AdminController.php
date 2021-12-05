<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\State;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function adminPage(Request $request)
    {
        return view("pages.admin.admin");
    }

    public function adminCategories(Request $request)
    {
        if ($request->isMethod("post")) {
            $data = $request->validate([
                "name" => "required"
            ]);

            $newCategory = new Category();
            $newCategory->name = $data["name"];
            $newCategory->save();

            return redirect("admin_categories");
        }

        $categories = Category::all();
        return view("pages.admin.categories", ["categories" => $categories]);
    }

    public function adminTickets(Request $request)
    {
        if ($request->isMethod("post")) {
            if (!isset($request->add_to_job)) {
                $data = $request->validate([
                    "comment" => "required",
                    "solution_img" => "required|max:2048|mime:jpg,jpeg,png,bmp"
                ]);

                $validated_user = User::where("ticket_id", $request->ticket_id)->first();

                $path = $request->file("solution_img")->store("tickets/" . (string) $validated_user->id . "/solution_imgs");

                $ticketToResolve = Ticket::where("id", $request->ticket_id);
                $ticketToResolve->solution_img = $path;
                $ticketToResolve->comment = $data['comment'];
                $ticketToResolve->state = 3;
                $ticketToResolve->save();
                return redirect("admin_tickets");
            } else {
                $ticket = Ticket::where("id", $request->ticket_id)->first;
                $ticket->state = 2;
                $ticket->save();
                return redirect("admin_tickets");
            }
        }

        $categories = Category::all();
        $states = State::all();
        $tickets = Ticket::all();
        return view("pages.admin.tickets", ["categories" => $categories, "tickets" => $tickets, "states" => $states]);
    }
}
