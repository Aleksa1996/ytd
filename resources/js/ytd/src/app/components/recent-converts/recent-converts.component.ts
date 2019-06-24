import { Component, OnInit } from '@angular/core';
import { Convert } from 'src/app/shared/Convert';

@Component({
  selector: 'app-recent-converts',
  templateUrl: './recent-converts.component.html',
  styleUrls: ['./recent-converts.component.scss']
})
export class RecentConvertsComponent implements OnInit {

  public converts: Convert[] = [
    { id: 1, headline: 'Headline 1', image: 'https://image.com', published: '1 hour ago', views: '220 M' },
    { id: 2, headline: 'Headline 2', image: 'https://image.com', published: '1 hour ago', views: '220 M' },
    { id: 3, headline: 'Headline 3', image: 'https://image.com', published: '1 hour ago', views: '220 M' },
    { id: 4, headline: 'Headline 4', image: 'https://image.com', published: '1 hour ago', views: '220 M' }
  ];

  constructor() { }

  ngOnInit() {
  }

}
