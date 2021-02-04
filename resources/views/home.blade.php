@extends('layouts.app')

<style>
    .chat-container {
        display: flex;
        flex-direction: column;
        height: 300px;
        overflow: auto;
    }

    .chat {
        border: 1px solid grey;
        border-radius: 3px;
        width: 50%;
        padding: 0.5em;
    }

    .chat-left {
        background-color: white;
        align-self: flex-start;
    }

    .chat-right {
        background-color: #adff2f7f;
        align-self: flex-end;
    }
</style>

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">ผู้ใช้งานทั้งหมด</div>
                <div class="card-body">
                    @forelse ($users as $user)
                    <div class="alert alert-success" role="alert">
                        <a href="javascript:void(0);" onclick="createChat({{$user->id}});">
                            {{ $user->name }} (เริ่มสนทนา)
                        </a>
                    </div>
                    @empty
                    <p class="text-center">ยังไม่มีผู้ใช้งาน</p>
                    @endforelse
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-6">
                    <div class="card">
                        <div class="card-header">ผู้สนทนา</div>
                        <div class="card-body">
                            @forelse ($chats as $chat)
                            <div class="alert alert-success" role="alert">
                                <a href="javascript:void(0);" onclick="startChat({{$chat->room_id}});">
                                    {{ $chat->user->name }} ({{ $chat->room->room_name}})
                                </a>
                            </div>
                            @empty
                                <p class="text-center">ยังไม่มีผู้สนทนา</p>
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card">
                        <div class="card-header" id="room_text">ห้องสนทนา</div>
                        <div class="card-body">
                            <div class="chat-container"></div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-1 text-center">
                                    <i class="fa fa-image fa-2x" id="open-file"></i>
                                    <input type="file" id="file" style="display: none;" onchange="uploadFiles(this);">
                                </div>
                                <div class="col-md-9">
                                    <input type="text" id="message" class="form-control" placeholder="Enter message">
                                </div>
                                <div class="col-md-2">
                                    <input type="hidden" id="room_id" value="">
                                    <input type="hidden" id="auth_id" value="{{Auth::user()->id}}">
                                    <input type="hidden" id="auth_name" value="{{Auth::user()->name}}">
                                    <button type="button" class="btn btn-primary" onclick="sendMessage('');">Send</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function(e) {   
    $("#open-file").click(function () {
        $("#file").trigger('click');
    });
});
</script>
<script>
    const database = firebase.firestore();
    const storage = firebase.storage();
    const chatsCollection = database.collection('chats');

    function createChat(userId) {
        let senderId = $("#auth_id").val();
        axios.post('/api/check-room', {
            senderId, userId
        })
        .then(function (res) {
            // exits chat
            if (res.data.isChat > 0) {
                startChat(res.data.roomId);
            } else {
                chatsCollection
                .add({})
                .then(function(docRef) {
                    console.log("Document written with ID: ", docRef.id);
                    createRoom(docRef.id, userId);
                })
                .catch(function(error) {
                    console.error("Error adding document: ", error);
                });
            }
        })
        .catch(function (error) {
            console.log(error);
        });
    }

    function createRoom(roomId, userId) {
        let senderId = $("#auth_id").val();
        let recipientId = userId;
        axios.post('/api/save-room', {
            roomId, senderId, recipientId
        })
        .then(function (res) {
            console.log(res);
            window.location.reload();
        })
        .catch(function (error) {
            console.log(error);
        });  
    }

    function startChat(roomId) {
        axios.post('/api/get-room', {
            roomId
        })
        .then(function (res) {
            let docRef = res.data.data.room_name;
            $("#room_text").html("ห้องสนทนา " + docRef);
            $("#room_id").val(docRef);

            getAllMessage(docRef);
        })
        .catch(function (error) {
            console.log(error);
        });
    }

    function sendMessage(image) {
        let message = $("#message").val();
        let doc = $("#room_id").val();
        let userId = $("#auth_id").val();
        let userName = $("#auth_name").val();
        
        chatsCollection
        .doc(doc)
        .collection("message")
        .add({
            createdAt: firebase.firestore.FieldValue.serverTimestamp(),
            userId: userId,
            userName: userName,
            message: message,
            image: image,
        })
        .then(function(docRef) {
            $("#message").val("");
        })
        .catch(function(error) {
            console.error("Error writing document: ", error);
        });
    }

    function uploadFiles(element) {
        const ref = storage.ref();
        const file = $(element)[0].files[0];
        const name = new Date() + '-' + file.name;
        const metadata = {
            contentType:file.type
        }

        const task = ref.child('chats/'+name).put(file, metadata);

        let userId = $("#auth_id").val();
        let userName = $("#auth_name").val();
        
        task
        .then(snapshot => snapshot.ref.getDownloadURL())
        .then(url => {
            console.log(url)
            sendMessage(url);
        });
    }

    function getAllMessage(roomId) {
        chatsCollection
        .doc(roomId)
        .collection("message")
        .orderBy('createdAt', 'desc')
        .onSnapshot(function(querySnapshot) {
            $(".chat-container").empty();
            const allMessages = [];
            querySnapshot.forEach(doc => {
                if (doc) allMessages.push(doc.data())
            });
            
            let reverse = Array.prototype.reverse.call(allMessages);
            
            if (reverse.length > 0) {
                $.each(reverse, function (index, value) {
                    // console.log(value)
                    let id = value.userId;
                    let username = value.userName;
                    let message = value.message;
                    let image = value.image;
                    let time = value.createdAt.toDate().toLocaleTimeString('en-US');
                    let isMe = id == '{{Auth::user()->id}}' ? 'chat-right' : 'chat-left';
                    let chat = '';
                    if (image == "") {
                        chat = '<p class="chat '+isMe+'">' + username + ' : ' + message + '<br>' + time + '</p>';
                    } else {
                        chat = '<p class="chat '+isMe+'"><img src="'+image+'" width="100%" height="150" /><br>' + time + '</p>';
                    }
                    $(".chat-container").append(chat);
                });
            } else {
                $(".chat-container").append('<p class="text-center">กรุณาส่งข้อความเริ่มการสนทนา</p');
            }

        }, function(error) {
            console.log(error);
        });
    }
    
</script>
@endsection