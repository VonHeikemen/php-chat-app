# PHP Chat example
simple chat made with phpsocket.io and socket.io client

# phpsocket.io
A server side alternative implementation of [socket.io](https://github.com/socketio/socket.io) in PHP based on [Workerman](https://github.com/walkor/Workerman).<br>

# Run chat example
cd examples/chat

## Start php web server
```php -S localhost:3000 -t public```

## Start Websocket server
```php socket_server.php start``` for debug mode

```php socket_server.php start -d ``` for daemon mode

## Stop Websocket server
```php socket_server.php stop```

## Status
```php socket_server.php status```

# License
MIT