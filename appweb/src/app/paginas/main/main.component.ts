import { Component, OnInit } from '@angular/core';
import { LoginService } from '../../servicios/servicios.index';

@Component({
  selector: 'app-main',
  templateUrl: './main.component.html'
})
export class MainComponent implements OnInit {
  constructor( private _login: LoginService) {
    _login.validar();
   }
  ngOnInit() {
  }
}
