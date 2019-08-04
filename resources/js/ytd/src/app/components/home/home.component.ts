import { Component, OnInit } from '@angular/core';
import { faSyncAlt } from '@fortawesome/free-solid-svg-icons';
import { Validators, FormBuilder } from '@angular/forms';
import { YoutubevideoService } from 'src/app/services/youtubevideo.service';
import { YoutubeVideo } from 'src/app/shared/YoutubeVideo';

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.scss']
})
export class HomeComponent implements OnInit {

  faSyncAlt = faSyncAlt;

  public youtubeVideo: YoutubeVideo;

  constructor(private fb: FormBuilder, private youtubevideoService: YoutubevideoService) { }

  ngOnInit() {
  }

  public convertForm = this.fb.group({
    link: [
      '',
      [
        Validators.required,
        Validators.minLength(3),
        Validators.pattern('^https:\/\/(www\.youtube\.com\/watch\\?v=(.)+|youtu.be\/(.)+)$')
      ]
    ]
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
    const { link } = this.convertForm.value;

    this.youtubevideoService.submit(link).subscribe(
      (r) => {
        this.youtubeVideo = r.body;
        console.log(r);
      },
      ({ error, status }) => {

      }
    );
  }

}
