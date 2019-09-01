import { Component, OnInit, Input } from '@angular/core';
import { YoutubeVideo } from 'src/app/shared/YoutubeVideo';

@Component({
  selector: 'app-recent-converts',
  templateUrl: './recent-converts.component.html',
  styleUrls: ['./recent-converts.component.scss']
})
export class RecentConvertsComponent implements OnInit {

  @Input() public youtubeVideos: YoutubeVideo[];

  constructor() { }

  ngOnInit() {
  }

}
