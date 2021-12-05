<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\State;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function getTickets(Request $request)
    {
        $userTickets = Ticket::where("user_id", Auth::user()->id)->get();

        // Need for sorting
        $categories = Category::all();
        $states = State::all();

        return view("pages.user.tickets", ["tickets" => $userTickets, "categories" => $categories, "states" => $states]);
    }

    public function newTicket(Request $request)
    {
        if ($request->isMethod("post")) {

            $data = $request->validate([
                "name" => "required",
                "description" => "required",
                "category" => "array",
                // Validate if every category id exists
                "category.*" => "exists:categories,id",
                "ticket_img" => "required|max:2048|mime:jpg,jpeg,png,bmp"
            ]);

            $new_ticket = new Ticket();

            $path = $request->file("ticket_img")->store("tickets/" . (string) Auth::user()->id . "/proglem_imgs");

            $new_ticket->name = $data['name'];
            $new_ticket->user_id = Auth::user()->id;
            $new_ticket->description = $data["description"];
            $new_ticket->ticket_img = $path;
            $new_ticket->state = State::where("id", 1)->first();

            $new_ticket->save();

            return redirect("list_tickets");
        }

        // Need for select
        $categories = Category::all();

        return view("pages.user.new_ticket", ["categories" => $categories]);
    }

    public function getOneTicket(Request $request, $id)
    {
        $ticket = Ticket::where("id", $id)->first();

        return view("pages.profile.ticket", ["ticket" => $ticket]);
    }

    public function delete_ticket(Request $request, $id)
    {
        Ticket::where("id", $id)->delete();

        return redirect("list_tickets");
    }
}
