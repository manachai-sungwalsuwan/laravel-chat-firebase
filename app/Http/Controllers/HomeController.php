<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Room;
use App\Chat;

use Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $users = User::where('id', '<>', Auth::user()->id)->get();
        $chats = Chat::where('sender_id', Auth::user()->id)->get();

        $data = [
            'users' => $users,
            'chats' => $chats,
        ];
        
        return view('home', $data);
    }
}
