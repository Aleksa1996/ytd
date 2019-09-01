import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import {
  CanDeactivate,
  ActivatedRouteSnapshot,
  RouterStateSnapshot
} from '@angular/router';

import { HomeComponent } from './components/home/home.component';

@Injectable({
  providedIn: 'root',
})
export class CanDeactivateHomeGuard implements CanDeactivate<HomeComponent> {

  canDeactivate(
    component: HomeComponent,
    route: ActivatedRouteSnapshot,
    state: RouterStateSnapshot
  ): Observable<boolean> | boolean {

    // Allow synchronous navigation (`true`) if we dont convert
    if (!component.progress.show) {
      return true;
    }
    // Otherwise ask the user with the dialog service and return its
    // observable which resolves to true or false when the user decides
    return component.dialogService.confirm('If you leave video will be not processed!');
  }
}
