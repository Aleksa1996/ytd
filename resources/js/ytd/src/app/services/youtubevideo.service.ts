import { Injectable } from '@angular/core';
import { HttpClient, HttpResponse, HttpParams } from '@angular/common/http';

import { Observable } from 'rxjs';
import { YoutubeVideo } from '../shared/YoutubeVideo';
import { SocketService } from './socket.service';


@Injectable({
  providedIn: 'root'
})
export class YoutubevideoService {

  constructor(private http: HttpClient, private socketService: SocketService) { }

  public convertVideoFromUrl(link: string): Observable<HttpResponse<YoutubeVideo>> {
    const video_id = YoutubeVideo.getVideoIdByLink(link);
    return this.http.post<YoutubeVideo>('/api/v1/videos/convert', { video_id, fd: this.socketService.getFd() }, { observe: 'response' });
  }

  public getPopularConverts(): Observable<YoutubeVideo[]> {
    return this.http.get<YoutubeVideo[]>('/api/v1/videos');
  }

  public onVideoProcessingProgress() {
    return this.socketService.on('VIDEO_PROCESSING_PROGRESS_F');
  }

  public downloadMp3(url) {
    return this.http.get(url, { responseType: 'blob' });
  }


  public forceBrowserToDownload(blob, fileName) {
    // It is necessary to create a new blob object with mime-type explicitly set
    // otherwise only Chrome works like it should
    const newBlob = new Blob([blob], { type: 'audio/mpeg' });

    // IE doesn't allow using a blob object directly as link href
    // instead it is necessary to use msSaveOrOpenBlob
    if (window.navigator && window.navigator.msSaveOrOpenBlob) {
      window.navigator.msSaveOrOpenBlob(newBlob);
      return;
    }

    // For other browsers:
    // Create a link pointing to the ObjectURL containing the blob.
    const data = window.URL.createObjectURL(newBlob);

    const link = document.createElement('a');
    link.href = data;
    link.download = fileName;

    // this is necessary as link.click() does not work on the latest firefox
    link.dispatchEvent(new MouseEvent('click', { bubbles: true, cancelable: true, view: window }));

    // For Firefox it is necessary to delay revoking the ObjectURL
    setTimeout(function () {
      window.URL.revokeObjectURL(data);
      link.remove();
    }, 100);
  }

}
