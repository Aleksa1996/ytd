import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';

import { FontAwesomeModule } from '@fortawesome/angular-fontawesome';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { HomeComponent } from './components/home/home.component';
import { RecentConvertsComponent } from './components/recent-converts/recent-converts.component';
import { RecentConvertItemComponent } from './components/recent-convert-item/recent-convert-item.component';



@NgModule({
  declarations: [
    AppComponent,
    HomeComponent,
    RecentConvertsComponent,
    RecentConvertItemComponent,
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    FontAwesomeModule
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
