import { Injectable, Inject, Optional, PLATFORM_ID } from '@angular/core';
import { HttpInterceptor, HttpHandler, HttpRequest, HttpHeaders } from '@angular/common/http';
import { Request } from 'express';
import { REQUEST } from '@nguniversal/express-engine/tokens';
import { environment } from 'src/environments/environment';
import { isPlatformServer } from '@angular/common';

@Injectable()
export class UniversalInterceptor implements HttpInterceptor {

    public isServerSide;

    constructor(@Optional() @Inject(REQUEST) protected request: Request, @Inject(PLATFORM_ID) private platformId: Object) {
        this.isServerSide = isPlatformServer(this.platformId);
    }

    intercept(req: HttpRequest<any>, next: HttpHandler) {
        let serverReq: HttpRequest<any> = req;
        if (this.request && req.url.startsWith(environment.apiUrl) && this.isServerSide) {
            let newUrl = req.url.replace(environment.apiUrl, environment.backendUrl);
            serverReq = req.clone({ url: newUrl });
        }
        return next.handle(serverReq);
    }
}
