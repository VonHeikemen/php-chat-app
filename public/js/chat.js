
var socket = io(socket_host);
vex.defaultOptions.className = 'vex-theme-default';

function joinRoom() {
    vex.dialog.open({
        message: 'Join chat room',
        input: [
            '<label>Display name</label>',
            '<input type="text" name="name" autofocus/>',
            '<label>Room name</label>',
            '<input type="text" name="room"/>'
        ].join(''),
        buttons: [
            Object.assign({}, vex.dialog.buttons.YES, { text: 'Join' }),
        ],
        callback: function(data) {
            if( !data ){
                return appMessage('You must join a room', joinRoom);
            }

            socket.emit('join', data, roomResponse);
        }
    });
}

function welcome() {
    var btnProps = {
        text: 'Join a chat room',
        className: 'vex-center-button'
    };

    vex.dialog.open({
        message: 'Welcome!',
        buttons: [
            Object.assign({}, vex.dialog.buttons.YES, btnProps),
        ],
        callback: joinRoom
    });
}

function appMessage(message, callback) {
    var opts = {
        message: message,
        buttons: [vex.dialog.buttons.YES]
    };

    if(callback){
        opts.callback = callback;
    }

    vex.dialog.open(opts);
}

function roomResponse(data) {
    if( data.error )
        return appMessage(data.error, joinRoom);

    document.getElementById('msg-body').focus();
    console.log(data.success);
}

function sendMessage (e){
    e.preventDefault();
    var text = document.getElementById('msg-body');

    var message = {
        text: text.value,
    };

    socket.emit('createMessage', message, function (resp) {
        if(resp.noUser)
            return appMessage(resp.error, joinRoom);
        
        console.log(resp);
        text.value = '';
        document.getElementById('msg-body').focus();
    });
}

function renderMessage(response) {
    var messageData = {
        from: response.from,
        text: response.text,
        createdAt: messageTimestamp(response.createdAt),
        opts: response.opts
    };

    var template = document.getElementById('message-template').innerHTML;
    var message = Mustache.render(template, messageData);

    var list = document.getElementById('messages');
    list.innerHTML += message;

    scrollToBottom(list);
}

function messageTimestamp(date) {
    var options = {
        hour: 'numeric', minute: 'numeric',
        hour12: true
    };
      
    return new Intl.DateTimeFormat(['en-US'], options)
        .format(date)
        .toLocaleLowerCase();
}

function sendLocation(e) {
    if(!navigator.geolocation)
        return appMessage('Browser not supported');

    var btnShareLocation = this;

    btnShareLocation.innerText = 'Sending...';
    btnShareLocation.setAttribute('disabled', 'disabled')
    
    navigator.geolocation.getCurrentPosition(
        function (position) {
            var coords = {
                latitude: position.coords.latitude,
                longitude: position.coords.longitude
            };

            socket.emit('shareLocation', coords, function(resp) {
                if(resp.noUser)
                    return appMessage(resp.error, joinRoom);
                
                document.getElementById('msg-body').focus();
            });

            btnShareLocation.innerText = 'Send Location';
            btnShareLocation.removeAttribute('disabled');
        },
        function () {
            btnShareLocation.innerText = 'Send Location';
            btnShareLocation.removeAttribute('disabled');
            appMessage('Unable to fetch location');
        }
    );
}

function scrollToBottom(list) {
    var listTotalHeight = list.scrollHeight;
    var listVisiblePortion = list.clientHeight;

    if(listTotalHeight <= listVisiblePortion)
        return;

    var addedItem = list.lastElementChild;
    var lastItem = addedItem.previousElementSibling;

    var pixelsFromTop = list.scrollTop;
    var addedItemHeight = addedItem.clientHeight;
    var lastItemHeight = lastItem.clientHeight;

    var total =  listVisiblePortion + pixelsFromTop + addedItemHeight + lastItemHeight;

    if( total >= listTotalHeight )
        list.scrollTop = listTotalHeight;
}

function updateUserList(users) {
    var list = document.createElement('ol');

    users.forEach(function(user) {
        var li = document.createElement('li');
        li.innerText = user;
        list.appendChild(li);
    });

    var userList = document.getElementById('users');
    userList.innerHTML = '';
    userList.appendChild(list);
}

document.getElementById('message-form').addEventListener('submit', sendMessage);
document.getElementById('send-location').addEventListener('click', sendLocation);

welcome();

socket.on('connect', function() {
    console.log('Connected to server');
});

socket.on('newMessage', renderMessage);
socket.on('welcome', renderMessage);
socket.on('updateUserList', updateUserList);
socket.on('newLocationUrl', renderMessage);

socket.on('disconnect', function() {
    console.log('Disconnected from server');
});