import { Component, OnInit, Input } from '@angular/core';
import { Convert } from 'src/app/shared/Convert';

@Component({
  selector: 'app-recent-convert-item',
  templateUrl: './recent-convert-item.component.html',
  styleUrls: ['./recent-convert-item.component.scss']
})
export class RecentConvertItemComponent implements OnInit {

  @Input() convert: Convert;
  @Input() index: number;

  constructor() { }

  ngOnInit() {
  }

}
