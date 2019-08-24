import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

//components
import { HomeComponent } from './components/home/home.component';
import { ContactComponent } from './components/contact/contact.component';
import { NotFoundComponent } from './components/not-found/not-found.component';


const routes: Routes = [
  { path: '', component: HomeComponent },
  { path: 'contact', component: ContactComponent },

  // { path: 'user/:id', component: UserComponent },
  // { path: 'post/:id', component: PostDetailComponent },
  // { path: 'login', component: LoginComponent, canActivate: [NotAuthGuardService] },
  // { path: 'signup', component: SignupComponent, canActivate: [NotAuthGuardService] },
  // { path: 'new', component: PostNewFormComponent, canActivate: [AuthGuardService] },
  // {
  //   path: 'profile',
  //   component: ProfileComponent,
  //   canActivate: [AuthGuardService]
  // },
  // { path: 'profile/settings', component: SettingsComponent, canActivate: [AuthGuardService] },
  // { path: 'admin', component: AdminPanelComponent, canActivate: [AuthGuardService] },
  // { path: 'admin/section/new', component: SectionEditFormComponent, canActivate: [AuthGuardService] },
  // { path: 'admin/section/:id', component: SectionEditFormComponent, canActivate: [AuthGuardService] },
  // { path: 'admin/user/new', component: UserEditFormComponent, canActivate: [AuthGuardService] },
  // { path: 'admin/user/:id', component: UserEditFormComponent, canActivate: [AuthGuardService] },
  { path: '**', component: NotFoundComponent }
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
