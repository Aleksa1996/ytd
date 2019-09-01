import { Component, OnInit, Input } from '@angular/core';
import { YoutubeVideo } from 'src/app/shared/YoutubeVideo';
import { faSyncAlt } from '@fortawesome/free-solid-svg-icons';

@Component({
  selector: 'app-recent-convert-item',
  templateUrl: './recent-convert-item.component.html',
  styleUrls: ['./recent-convert-item.component.scss']
})
export class RecentConvertItemComponent implements OnInit {

  @Input() youtubeVideo: YoutubeVideo;

  public faSyncAlt = faSyncAlt;

  constructor() { }

  ngOnInit() {
  }

}
