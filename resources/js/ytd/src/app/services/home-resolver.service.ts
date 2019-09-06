import { Injectable } from '@angular/core';
import {
  Router, Resolve,
  RouterStateSnapshot,
  ActivatedRouteSnapshot
} from '@angular/router';
import { Observable } from 'rxjs';

import { YoutubevideoService } from './youtubevideo.service';
import { YoutubeVideo } from '../shared/YoutubeVideo';

@Injectable({
  providedIn: 'root'
})
export class HomeResolverService implements Resolve<YoutubeVideo[]> {

  constructor(private youtubeService: YoutubevideoService, private router: Router) { }

  resolve(route: ActivatedRouteSnapshot, state: RouterStateSnapshot): Observable<YoutubeVideo[]> | Observable<never> {
    return this.youtubeService.getPopularConverts();
  }
}
