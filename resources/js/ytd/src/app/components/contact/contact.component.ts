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

  public formSubmitStatus: { message: String, valid: Boolean } = { message: '', valid: false };

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
    // get data and store it in object
    const { firstName, lastName, email, message } = this.contactForm.value;
    const contactMessage = new ContactMessage(firstName + ' ' + lastName, email, message);

    // restart error message
    this.formSubmitStatus = { message: '', valid: false };

    // call api (send contact form message), handle response
    this.contactService.submit(contactMessage).subscribe(
      (r) => {
        this.formSubmitStatus = { message: r.body.message, valid: true };
        this.contactForm.reset();
        setTimeout(() => this.formSubmitStatus = { message: '', valid: false }, 4000);
      },
      ({ error, status }) => {
        if (error.error_type == 'general_errors') {
          if (status == 429) {
            error.message = 'You already have sent 2 messages in 5 minutes!';
          }
          this.formSubmitStatus = { message: error.message, valid: false };
        } else if (error.error_type == 'form_errors') {
          error.errors.forEach(({ field, message }) => this.contactForm.get(field).setErrors({ 'api_validation': { message } }));
        }
      }
    );
  }

}
