import { Component, OnInit } from '@angular/core';
import { Usuario } from '../../modelos/Usuario';
import { environment } from '../../../environments/environment';
import { LoginService } from '../../servicios/servicios.index';
declare function init_plugins();
@Component({
  selector: 'app-sidebar',
  templateUrl: './sidebar.component.html'
})
export class SidebarComponent implements OnInit {
  user = new Usuario();
  anio = new Date().getFullYear();
  rutas: any;
  version = environment.version;
  constructor(private _login: LoginService) { }
  ngOnInit() {
    this.user = this._login.usuario;
    this.rutas = this._login.getmenu();
    if (this.rutas) {
      init_plugins();
    }
  }
  salir() {
    this._login.userlogout();
  }
}
