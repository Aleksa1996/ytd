import { Injectable } from '@angular/core';
import { HttpClient, HttpResponse, HttpParams } from '@angular/common/http';

import { Observable } from 'rxjs';
import { YoutubeVideo } from '../shared/YoutubeVideo';



@Injectable({
  providedIn: 'root'
})
export class YoutubevideoService {

  constructor(private http: HttpClient) { }

  public submit(link: string): Observable<HttpResponse<YoutubeVideo>> {
    const youtubeVideoId = YoutubeVideo.getVideoIdByLink(link);
    const params = new HttpParams().set('video_id', youtubeVideoId);
    return this.http.get<YoutubeVideo>('/api/v1/convert', { observe: 'response', params });
  }

}
