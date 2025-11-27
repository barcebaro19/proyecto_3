import { Component } from '@angular/core';
import { LoginService } from '../../servicios/servicios.index';

@Component({
  selector: 'app-menu',
  templateUrl: './menu.component.html'
})
export class MenuComponent {
  constructor(private _login: LoginService){}
  menulist = [
    {
      nombre: 'Home',
      url: 'home',
      icono: 'fa fa-list'
    },
    {
      nombre: 'Contratantes',
      url: 'contratantes',
      icono: 'fa fa-list'
    },
    {
      nombre: 'Consultas',
      url: 'consultas',
      icono: 'fa fa-list'
    },
    {
      nombre: 'Ejemplo',
      url: 'ejemplo',
      icono: 'fa fa-list'
    },
    {
      nombre: 'Main',
      url: 'main',
      icono: 'fa fa-list'
    }
  ];
  cerrarventana() {
    window.open('', '_parent', '');
    window.close();
  }
  salir(){
    this._login.userlogout();
  }
}
