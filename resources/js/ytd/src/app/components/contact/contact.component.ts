import { Component, OnInit } from '@angular/core';
import { faPaperPlane } from '@fortawesome/free-solid-svg-icons';
import { FormBuilder, Validators } from '@angular/forms';
import { ContactService } from 'src/app/services/contact.service';
import { ContactMessage } from 'src/app/shared/ContactMessage';

@Component({
  selector: 'app-contact',
  templateUrl: './contact.component.html',
  styleUrls: ['./contact.component.scss']
})
export class ContactComponent implements OnInit {

  faPaperPlane = faPaperPlane;

  public contactForm = this.fb.group({
    firstName: ['', [Validators.required, Validators.minLength(3), Validators.maxLength(10)]],
    lastName: ['', [Validators.required, Validators.minLength(3), Validators.maxLength(10)]],
    email: ['', [Validators.required, Validators.email]],
    message: ['', [Validators.required, Validators.minLength(10), Validators.maxLength(250)]],
  });

  constructor(private fb: FormBuilder, private contactService: ContactService) { }

  ngOnInit() {
  }

  public isFieldInvalid(fieldName: string) {
    const field = this.contactForm.get(fieldName);
    return !field.valid && field.touched;
  }

  public getFieldErrors(fieldName: string) {
    const field = this.contactForm.get(fieldName);
    return field.errors || {};
  }

  public onSubmit() {
    const { firstName, lastName, email, message } = this.contactForm.value;
    const contactMessage = new ContactMessage(firstName + ' ' + lastName, email, message);
    this.contactService.submit(contactMessage);
  }

}
