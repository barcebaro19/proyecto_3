import { NgModule } from '@angular/core';
import { HeaderComponent } from './header/header.component';
import { SidebarComponent } from './sidebar/sidebar.component';
import { BreadcrumbsComponent } from './breadcrumbs/breadcrumbs.component';
import { RouterModule } from '@angular/router';
import { CommonModule } from '@angular/common';
import { LoaderComponent } from './loader/loader.component';

@NgModule({
  imports: [
      RouterModule,
      CommonModule
  ],
  declarations: [
      HeaderComponent,
      SidebarComponent,
      BreadcrumbsComponent,
      LoaderComponent
  ],
  exports: [
      HeaderComponent,
      SidebarComponent,
      BreadcrumbsComponent,
      LoaderComponent
  ]
})
export class SharedModule { }