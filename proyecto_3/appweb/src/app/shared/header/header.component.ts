import { Component, OnInit } from '@angular/core';
import { Usuario } from '../../modelos/Usuario';
import { LoginService } from 'src/app/servicios/servicios.index';
@Component({
  selector: 'app-header',
  templateUrl: './header.component.html',
  styles: []
})
export class HeaderComponent implements OnInit {
  user = new Usuario();
  constructor(private _login: LoginService) { }
  ngOnInit() {
    this.user = this._login.usuario;
  }
  salir() {
    this._login.userlogout();
  }
}
