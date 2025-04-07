<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class ChatController extends Controller
{
    public function chatList(){
        $users = User::All();

        return view('components.chat-widget', compact('users'));
    }
}
