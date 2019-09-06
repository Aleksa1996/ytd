import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

//components
import { HomeComponent } from './components/home/home.component';
import { ContactComponent } from './components/contact/contact.component';
import { NotFoundComponent } from './components/not-found/not-found.component';

// resolvers
import { HomeResolverService } from './services/home-resolver.service';

// guards
import { CanDeactivateHomeGuard } from './can-deactivate-home.guard';

const routes: Routes = [
  { path: '', component: HomeComponent, resolve: { youtubeVideos: HomeResolverService }, canDeactivate: [CanDeactivateHomeGuard] },
  { path: 'contact', component: ContactComponent },
  { path: '**', component: NotFoundComponent }
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
