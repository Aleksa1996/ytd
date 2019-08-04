import { TestBed } from '@angular/core/testing';

import { YoutubevideoService } from './youtubevideo.service';

describe('YoutubevideoService', () => {
  beforeEach(() => TestBed.configureTestingModule({}));

  it('should be created', () => {
    const service: YoutubevideoService = TestBed.get(YoutubevideoService);
    expect(service).toBeTruthy();
  });
});
