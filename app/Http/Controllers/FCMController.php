<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Room;
use App\Chat;

use Auth;

class FCMController extends Controller
{   
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

    public function checkRoom(Request $req) {
        $chat = Chat::where('sender_id', $req->senderId)->where('recipient_id', $req->userId)->first();
        $isChat = is_null($chat) ? 0 : 1;
        $roomId = is_null($chat) ? null : $chat->room_id;
        $response = [
            'success' => true,
            'isChat' => $isChat,
            'roomId' => $roomId,
        ];
        return response()->json($response);
    }

    public function saveRoom(Request $req) {
        $room = new Room();
        $room->room_name = $req->roomId;
        $room->save();

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
