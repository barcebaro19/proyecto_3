import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClientModule } from '@angular/common/http';
import {
  LoginService,
  LoaderService,
  HomeService,
} from 'src/app/servicios/servicios.index';
@NgModule({
  imports: [
    CommonModule,
    HttpClientModule,
  ],
  providers: [
    LoginService,
    LoaderService,
    HomeService,
  ],
  declarations: []
})
export class ServiciosModule { }
