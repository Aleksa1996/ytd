import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';

import { ContactMessage } from '../shared/ContactMessage';

@Injectable({
  providedIn: 'root'
})
export class ContactService {

  constructor(private http: HttpClient) { }

  public submit(message: ContactMessage) {
    return this.http.post('/api/v1/contact', message).subscribe((response) => {
      console.log(response);
    });
  }
}
