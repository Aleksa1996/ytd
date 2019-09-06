import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { ReactiveFormsModule } from '@angular/forms';
import { HttpClientModule } from '@angular/common/http';
import { TransferHttpCacheModule } from '@nguniversal/common';

import { FontAwesomeModule } from '@fortawesome/angular-fontawesome';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { HomeComponent } from './components/home/home.component';
import { RecentConvertsComponent } from './components/recent-converts/recent-converts.component';
import { RecentConvertItemComponent } from './components/recent-convert-item/recent-convert-item.component';
import { ContactComponent } from './components/contact/contact.component';
import { NotFoundComponent } from './components/not-found/not-found.component';

import { SocketService } from './services/socket.service';
import { YoutubevideoService } from './services/youtubevideo.service';


@NgModule({
  declarations: [
    AppComponent,
    HomeComponent,
    RecentConvertsComponent,
    RecentConvertItemComponent,
    ContactComponent,
    NotFoundComponent
  ],
  imports: [
    BrowserModule.withServerTransition({ appId: 'serverApp' }),
    TransferHttpCacheModule,
    HttpClientModule,
    AppRoutingModule,
    FontAwesomeModule,
    ReactiveFormsModule
  ],
  providers: [
    SocketService,
    YoutubevideoService
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }
