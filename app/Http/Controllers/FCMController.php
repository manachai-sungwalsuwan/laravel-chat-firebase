<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Room;
use App\Chat;

use Auth;

class FCMController extends Controller
{
    public function index(Request $req) {
        $fcm_token = $req->fcm_token;
        $user_id = $req->user_id;

        $user = User::findOrFail($user_id);
        $user->fcm_token = $fcm_token;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'user token updated successfully'
        ]);
    }
    
    public function getRooms() {
        $response = [
            'success' => true,
            'data' => Room::all(),
        ];
        return response()->json($response);
    }

    public function getRoom(Request $req) {
        $response = [
            'success' => true,
            'data' => Room::find($req->roomId),
        ];
        return response()->json($response);
    }

    public function saveRoom(Request $req) {
        $room = new Room();
        $room->name = $req->roomId;
        $room->save();

        // $chat = new Chat();
        // $chat->room_id = $room->id;
        // $chat->sender_id = $req->senderId;
        // $chat->recipient_id = $req->recipientId;
        // $chat->save();
        $chats = [
            ['room_id' => $room->id, 'sender_id' => $req->senderId, 'recipient_id' => $req->recipientId],
            ['room_id' => $room->id, 'sender_id' => $req->recipientId, 'recipient_id' => $req->senderId],
        ];
        Chat::insert($chats);

        $response = [
            'success' => true,
            'message' => 'create room success',
        ];
        return response()->json($response);
    }
}
