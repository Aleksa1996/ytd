<?php


use Illuminate\Http\Request;
use SwooleTW\Http\Websocket\Facades\Websocket;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| Websocket Routes
|--------------------------------------------------------------------------
|
| Here is where you can register websocket events for your application.
|
*/

Websocket::on('connect', function ($websocket, Request $request) {
    // called while socket on connect
    $websocket->emit('CONNECT_SOCKET', ['fd' => $websocket->getSender()]);
});

Websocket::on('disconnect', function ($websocket) {
    // called while socket on disconnect
});

Websocket::on('VIDEO_PROCESSING_PROGRESS_B', function ($websocket, $data) {
    // Log::debug('socket connection from laravel queue worker, Payload: ' . json_encode($data) . '  fd > ' . $websocket->getSender());
    Websocket::to($data['for_fd'])->emit('VIDEO_PROCESSING_PROGRESS_F', $data);
});
