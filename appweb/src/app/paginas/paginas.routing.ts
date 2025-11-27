import { Routes, RouterModule } from '@angular/router';
import { PaginasComponent } from './paginas.component';
import { MainComponent } from './main/main.component';

const routes: Routes = [
  {
    path: '',
    component: PaginasComponent,
    children: [
      { path: 'main', component: MainComponent, data: { titulo: 'Main'}},
      { path: '', redirectTo: 'main'}
    ]
  }
];
export const PAGINA_ROUTES = RouterModule.forChild(routes);
