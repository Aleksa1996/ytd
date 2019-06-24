import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { RecentConvertsComponent } from './recent-converts.component';

describe('RecentConvertsComponent', () => {
  let component: RecentConvertsComponent;
  let fixture: ComponentFixture<RecentConvertsComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ RecentConvertsComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(RecentConvertsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
