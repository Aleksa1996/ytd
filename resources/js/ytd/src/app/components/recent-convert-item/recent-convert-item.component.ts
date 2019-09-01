import { Component, OnInit, Input } from '@angular/core';
import { YoutubeVideo } from 'src/app/shared/YoutubeVideo';

@Component({
  selector: 'app-recent-convert-item',
  templateUrl: './recent-convert-item.component.html',
  styleUrls: ['./recent-convert-item.component.scss']
})
export class RecentConvertItemComponent implements OnInit {

  @Input() youtubeVideo: YoutubeVideo;
  @Input() index: number;

  constructor() { }

  ngOnInit() {
    console.log(this.youtubeVideo);
  }

}
