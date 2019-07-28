import { Injectable } from '@angular/core';
import { HttpClient, HttpResponse } from '@angular/common/http';

import { ContactMessage } from '../shared/ContactMessage';
import { Observable } from 'rxjs';
import { SuccessResponse } from '../shared/SuccessResponse';

@Injectable({
  providedIn: 'root'
})
export class ContactService {

  constructor(private http: HttpClient) { }

  public submit(message: ContactMessage): Observable<HttpResponse<SuccessResponse>> {
    return this.http.post<SuccessResponse>('/api/v1/contact', message, { observe: 'response' });
  }
}
