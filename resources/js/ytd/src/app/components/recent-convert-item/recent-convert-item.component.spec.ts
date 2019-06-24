import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { RecentConvertItemComponent } from './recent-convert-item.component';

describe('RecentConvertItemComponent', () => {
  let component: RecentConvertItemComponent;
  let fixture: ComponentFixture<RecentConvertItemComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ RecentConvertItemComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(RecentConvertItemComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
