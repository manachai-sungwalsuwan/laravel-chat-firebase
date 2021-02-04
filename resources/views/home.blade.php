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
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif

                    {{ __('You are logged in!') }}

                    <a href="javascript:void(0);" onclick="createUser();">create user in firebase</a>
                </div>
            </div>
            <br>
            <div class="card">
                <div class="card-header">Users</div>
                <div class="card-body">
                    @foreach ($users as $user)
                    <div class="alert alert-success" role="alert">
                        <a href="javascript:void(0);" onclick="createChat({{$user->id}});">
                            {{ $user->name }} (เริ่มสนทนา)
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-6">
                    <div class="card">
                        <div class="card-header">Chats</div>
                        <div class="card-body">
                            @forelse ($chats as $chat)
                            <div class="alert alert-success" role="alert">
                                <a href="javascript:void(0);" onclick="startChat({{$chat->room_id}});">
                                    {{ $chat->user->name }} (สนทนา) ({{ $chat->room->name}})
                                </a>
                            </div>
                            @empty
                                <p class="text-center">ยังไม่มีผู้สทนา</p>
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card">
                        <div class="card-header" id="room_text">Room</div>
                        <div class="card-body">
                            <div class="chat-container">
                                {{-- <p class="chat chat-left">This is chat left.</p>
                                <p class="chat chat-right">This is chat right.</p>
                                <p class="chat chat-left">This is chat left.</p>
                                <p class="chat chat-right">This is chat right.</p> --}}
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-10">
                                    <input type="text" id="message" class="form-control" placeholder="Enter message">
                                </div>
                                <div class="col-md-2">
                                    <input type="hidden" id="room_id" value="">
                                    <button type="button" class="btn btn-primary" onclick="sendMessage();">Send</button>
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
    // const messaging = firebase.messaging();
    // // Add the public key generated from the console here.
    // messaging.getToken({vapidKey: "BDgeD4Ib5ERyIvr3tOQwwBMsrjXOrZRJy_QqHF29_dw5oytjRZMoL4koBCz8CpDrfyCVYs1shlAbntrwaA_F3LM"});

    // function sendTokenToServer(fcm_token) {
    //     const user_id = '{{Auth::user()->id}}';
    //     axios.post('/api/save-token', {
    //         fcm_token, user_id
    //     })
    //     .then(function (res) {
    //         console.log(res);
    //     })
    //     .catch(function (error) {
    //         console.log(error);
    //     });   
    // }

    // messaging.getToken().then((currentToken) => {
    //     if (currentToken) {
    //         sendTokenToServer(currentToken)
    //     } else {
    //         console.log('Yoy should alllow notification!');
    //     }
    // }).catch((err) => {
    //     console.log('An error occurred while retrieving token. ', err);
    // });

    const database = firebase.firestore();

    const usersCollection = database.collection('users');

    const chatsCollection = database.collection('chats');

    function createUser() {
        const user_id = '{{Auth::user()->id}}';

        // usersCollection
        // .doc(user_id)
        // .set({
        //     id: user_id,
        //     first_name: '{{Auth::user()->name}}'
        // })
        // .then(() => {console.log('Insert data success');})
        // .catch(error => {console.log(error);});
        
        usersCollection
        .add({
            id: user_id,
            first_name: '{{Auth::user()->name}}'
        })
        .then(() => {console.log('Insert data success');})
        .catch(error => {console.log(error);});
    }

    function createChat(userId) {
        chatsCollection
        .add({})
        .then(function(docRef) {
            console.log("Document written with ID: ", docRef.id);
            $("#room_text").html("Room " + docRef.id);
            $("#room_id").val(docRef.id);
            createRoom(docRef.id, userId);
        })
        .catch(function(error) {
            console.error("Error adding document: ", error);
        }); 
    }

    function createRoom(roomId, userId) {
        let senderId = '{{Auth::user()->id}}';
        let recipientId = userId;
        axios.post('/api/save-room', {
            roomId, senderId, recipientId
        })
        .then(function (res) {
            console.log(res);
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
            let docRef = res.data.data.name;
            $("#room_text").html("Room " + docRef);
            $("#room_id").val(docRef);

            getAllMessage(docRef);
        })
        .catch(function (error) {
            console.log(error);
        });
    }

    function sendMessage() {
        let message = $("#message").val();
        let doc = $("#room_id").val();
        let userId = '{{Auth::user()->id}}';
        let userName = '{{Auth::user()->name}}';
        
        chatsCollection
        .doc(doc)
        .collection("message")
        .add({
            createdAt: firebase.firestore.FieldValue.serverTimestamp(),
            userId: userId,
            userName: userName,
            message: message,
        })
        .then(function(docRef) {
            // let ref = docRef.path.split("/");
            // console.log('collection : ' + ref[0]);
            // console.log('room : ' + ref[1]);
            // console.log('message : ' + ref[2]);
            // console.log('collection : ' + ref[3]);
            // console.log("Document successfully written!", docRef.id);
            $("#message").val("");
        })
        .catch(function(error) {
            console.error("Error writing document: ", error);
        });
    }

    // get real time
    // chatsCollection
    // .doc("zeBfyJoepPpazl7wArAK")
    // .collection("message")
    // .orderBy('createdAt', 'desc')
    // .onSnapshot(function(querySnapshot) {
    //     $(".chat-container").empty();
    //     const allMessages = [];
    //     querySnapshot.forEach(doc => {
    //         // console.log(doc.id, " => ", doc.data());
    //         // let id = doc.data().userId;
    //         // let username = doc.data().userName;
    //         // let message = doc.data().message;
    //         // let time = doc.data().createdAt.toDate().toLocaleTimeString('en-US')
    //         // let isMe = id == '{{Auth::user()->id}}' ? 'chat-right' : 'chat-left';
    //         // $(".chat-container").append('<p class="chat '+isMe+'">' + username + ' : ' + message + '<br>' + time + '</p>');
    //         if (doc) allMessages.push(doc.data())
    //     });
        
    //     let reverse = Array.prototype.reverse.call(allMessages);
        
    //     if (reverse.length > 0) {
    //         $.each(reverse, function (index, value) {
    //             console.log(value)
    //             let id = value.userId;
    //             let username = value.userName;
    //             let message = value.message;
    //             let time = value.createdAt.toDate().toLocaleTimeString('en-US');
    //             let isMe = id == '{{Auth::user()->id}}' ? 'chat-right' : 'chat-left';
    //             $(".chat-container").append('<p class="chat '+isMe+'">' + username + ' : ' + message + '<br>' + time + '</p>');
    //         });
    //     } else {
    //         $(".chat-container").append('<p class="text-center">กรุณาส่งข้อความเริ่มการสนทนา</p');
    //     }

    // }, function(error) {
    //     console.log(error);
    // });

    // get data
    // chatsCollection
    // .doc("3aD5dfeHKQHdMHuRd30l")
    // .collection("message")
    // .get()
    // .then(querySnapshot => {
    //     querySnapshot.forEach(doc => {
    //         console.log(doc.id, " => ", doc.data());
    //     });
    // });

    function getAllMessage(roomId) {
        chatsCollection
        .doc(roomId)
        .collection("message")
        .orderBy('createdAt', 'desc')
        .onSnapshot(function(querySnapshot) {
            $(".chat-container").empty();
            const allMessages = [];
            querySnapshot.forEach(doc => {
                if (doc) {
                    allMessages.push(doc.data())
                    // allMessages.push({
                    //     'userId': doc.data().userId,
                    //     'userName': doc.data().userName,
                    //     'message': doc.data().message,
                    //     'createdAt': doc.data().createdAt.toDate().toLocaleTimeString('en-US')
                    // });
                }
            });
            
            let reverse = Array.prototype.reverse.call(allMessages);
            
            if (reverse.length > 0) {
                $.each(reverse, function (index, value) {
                    // console.log(value)
                    let id = value.userId;
                    let username = value.userName;
                    let message = value.message;
                    let time = value.createdAt.toDate().toLocaleTimeString('en-US');
                    let isMe = id == '{{Auth::user()->id}}' ? 'chat-right' : 'chat-left';
                    $(".chat-container").append('<p class="chat '+isMe+'">' + username + ' : ' + message + '<br>' + time + '</p>');
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