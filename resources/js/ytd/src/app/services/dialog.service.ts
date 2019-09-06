import { Injectable } from '@angular/core';
import { Observable, of } from 'rxjs';

import { PLATFORM_ID, Inject } from '@angular/core';
import { isPlatformBrowser, isPlatformServer } from '@angular/common';

/**
 * Async modal dialog service
 * DialogService makes this app easier to test by faking this service.
 * TODO: better modal implementation that doesn't use window.confirm
 */
@Injectable({
  providedIn: 'root',
})
export class DialogService {

  constructor(@Inject(PLATFORM_ID) private platformId: Object) { }
  /**
   * Ask user to confirm an action. `message` explains the action and choices.
   * Returns observable resolving to `true`=confirm or `false`=cancel
   */
  confirm(message?: string): Observable<boolean> {
    if (isPlatformServer(this.platformId)) {
      return of(true);
    }

    const confirmation = window.confirm(message || 'Is it OK?');
    return of(confirmation);
  }
}
