import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, FormControl, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { LoginService } from '../servicios/servicios.index';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss']
})
export class LoginComponent implements OnInit {
  miFormulario: FormGroup;
  hide = true;

  constructor(public fb: FormBuilder, private _login: LoginService, router: Router) {
    if (_login.logueado()) {
      router.navigate(['main']);
    }
  }

  ngOnInit() {
    this.miFormulario = this.fb.group({
      password: new FormControl('', Validators.required),
      username: new FormControl('', Validators.required)
    });
  }
  ingresar() {
    const v = this.miFormulario.value;
    let m = '';
    if (this.miFormulario.invalid) {
      if (!v.username) m = '<p>Nombre de usuario es requerido</p>';
      if (!v.password) m += '<p>Contraseña requerida</p>';
    }
    this._login.ingresar(v, m);
  }
  ver(pw: any, e: any) {
    if (pw.type === 'password') {
      pw.type = 'text';
      e.className = 'fa fa-eye-slash fa-fw  text-danger';
    } else {
      pw.type = 'password';
      e.className = 'fa fa-eye fa-fw';
    }
  }
}
