import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';

import { environment } from '../../environments/environment';

import * as io from 'socket.io-client';

@Injectable({
    providedIn: 'root'
})
export class SocketService {
    private socket: SocketIOClient.Socket;
    private fd: number;

    constructor() {
        this.socket = io(environment.apiUrl, { path: '/api', transports: ['websocket'], reconnectionAttempts: 5 });
        this.on('CONNECT_SOCKET').subscribe((message) => {
            if (!message.fd) {
                throw new Error('Error did not recived fd on connect!');
            }
            this.fd = message.fd;
        });
    }

    public emit(event: string, data: any) {
        this.socket.emit(event, data);
    }

    public on(event: string) {
        return Observable.create(observer => {
            this.socket.on(event, data => {
                observer.next(data);
            });
        })
    }

    public getFd() {
        return this.fd;
    }
}
