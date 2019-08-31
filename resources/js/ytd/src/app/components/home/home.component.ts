import { Component, OnInit, OnDestroy, HostListener } from '@angular/core';
import { faSyncAlt } from '@fortawesome/free-solid-svg-icons';
import { Validators, FormBuilder } from '@angular/forms';
import { YoutubevideoService } from 'src/app/services/youtubevideo.service';
import { YoutubeVideo } from 'src/app/shared/YoutubeVideo';

import { interval, Subject, pipe } from 'rxjs';
import { takeUntil, delay } from 'rxjs/operators';

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.scss']
})
export class HomeComponent implements OnInit, OnDestroy {
  private unsubscribe = new Subject<void>();
  public faSyncAlt = faSyncAlt;
  public youtubeVideo: YoutubeVideo;
  public progress = {
    show: false,
    percentage: 0,
    type: null
  };

  // @HostListener('window:beforeunload', ['event'])
  // warnUser(event) {
  //   $event.returnValue = true;
  // }

  /** TODO
   *  1. Prikaz popularnih convertova
   *  2. Handleovanje kada korisnik izadje sa strane u toku convert-a
   * */

  constructor(private fb: FormBuilder, private youtubevideoService: YoutubevideoService) { }

  ngOnInit() {
    this.onVideoProcessingProgress();
  }

  ngOnDestroy() {
    this.unsubscribe.next();
    this.unsubscribe.complete();
  }

  public onSubmit() {
    this.updateProgress({ show: true, type: 'preparation' });

    // send request to convert and download video
    this.youtubevideoService
      .convertVideoFromUrl(this.convertForm.value.link)
      // .pipe(delay(1000))
      .subscribe(r => {
        this.youtubeVideo = r.body;
      });
  }

  public onVideoProcessingProgress() {
    this.youtubevideoService
      .onVideoProcessingProgress()
      .pipe(takeUntil(this.unsubscribe))
      .subscribe(({ progress_type, progress, link, file, for_fd }) => {
        // update progress
        this.updateProgress({ show: true, type: progress_type, percentage: progress });

        // if video processing is finished, download it
        if (progress_type == 'video_finished') {
          this.download(link, file);
        }
      });
  }

  public download(link, fileName) {
    this.youtubevideoService
      .downloadMp3(link)
      .subscribe(blob => {
        this.youtubevideoService.forceBrowserToDownload(blob, fileName);
        this.resetForm();
      });
  }

  public convertForm = this.fb.group({
    link: ['', [
      Validators.required,
      Validators.minLength(3),
      Validators.pattern('^https://(www.youtube.com/watch\\?v=(.)+|youtu.be/(.)+)$')
    ]]
  });

  public isFieldInvalid(fieldName: string) {
    const field = this.convertForm.get(fieldName);
    return !field.valid && field.touched;
  }

  public getFieldErrors(fieldName: string) {
    const field = this.convertForm.get(fieldName);
    return field.errors || {};
  }

  public resetForm() {
    this.convertForm.reset();
    this.updateProgress();
    this.youtubeVideo = null;
  }

  public updateProgress({ show = false, percentage = 0, type = null } = {}) {
    this.progress = { show, percentage, type };
  }
}
