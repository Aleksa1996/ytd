import { Component, OnInit } from '@angular/core';
import { faSyncAlt } from '@fortawesome/free-solid-svg-icons';
import { Validators, FormBuilder } from '@angular/forms';

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.scss']
})
export class HomeComponent implements OnInit {

  faSyncAlt = faSyncAlt;

  constructor(private fb: FormBuilder) { }

  ngOnInit() {
  }

  public convertForm = this.fb.group({
    link: ['', [Validators.required, Validators.minLength(3)]]
  });

  public isFieldInvalid(fieldName: string) {
    const field = this.convertForm.get(fieldName);
    return !field.valid && field.touched;
  }

  public getFieldErrors(fieldName: string) {
    const field = this.convertForm.get(fieldName);
    return field.errors || {};
  }

  public onSubmit() {
    // get data and store it in object
    // const { firstName, lastName, email, message } = this.convertForm.value;
    console.log(this.convertForm.value);
  }

}
