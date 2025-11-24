import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';

// import 'rxjs/add/operator/map';
import swal from 'sweetalert2';
import { environment } from '../../environments/environment';
// import { map } from 'rxjs/operators';
import { Usuario } from '../modelos/Usuario';
import { Router } from '@angular/router';
import { BehaviorSubject, Subject } from 'rxjs';
import { sha256 } from 'js-sha256';

@Injectable({ providedIn: 'root' })
export class LoginService {
  private urlogin = environment.urlogin + 'api/users/';
  private urlapp = environment.url + 'login/';
  public isLogin = new BehaviorSubject<boolean>(false);
  public usuario = new Usuario();

  constructor(public http: HttpClient, private router: Router) {
    this.usuario.reset();
    this.valDataUser();
  }
  valDataUser() {
    const data = localStorage.getItem('RUCdatosUser');
    if (data != null && data.length > 0) {
      const datos = JSON.parse(data);
      if (datos.token !== undefined && datos.usuario && datos.token) {
        this.isLogin.next(true);
        this.usuario.usuario = datos.usuario;
        this.usuario.token = datos.token;
        this.usuario.apptoken = sha256(datos.token);
        this.usuario.name = datos.name;
        this.usuario.company = datos.company;
        this.usuario.department = datos.department;
        this.usuario.displayname = datos.displayname;
        this.usuario.mail = datos.mail;
        this.usuario.tiempo = datos.tiempo;
        this.usuario.isLogin = true;
        this.usuario.roles = datos.roles;
      }
    } else {
      this.usuario.reset();
    }
  }
  ingresar(values: any, m: string) {
    values.app = environment.app;
    if (m) swal.fire('Ingrese sus datos', m, 'warning');
    else this.http.post(this.urlogin + 'login', values).subscribe((data: any) => {
      if (data.data.token !== undefined && data.success) {
        this.http.post(this.urlapp + 'ingresar', data.data).subscribe((res: any) => {
          if (res.success !== undefined && res.success) {
            localStorage.setItem('RUCdatosUser', JSON.stringify(data.data));
            this.valDataUser();
            // this.router.navigate(['main'], { queryParams: { page: 1 } });
            this.router.navigate(['main']);
          } else console.log(res);
        }, err => { swal.fire('Error', 'code:' + err.status); });
      } else swal.fire(data.data, '', 'warning');
    }, err => { swal.fire('Error', 'code:' + err.status); }
    );
  }
  userlogout() {
    this.http.post(this.urlapp + 'logout', { usuario: this.usuario.usuario }).subscribe();
    this.usuario.reset();
    localStorage.removeItem('RUCdatosUser');
    this.isLogin.next(false);
    this.router.navigate(['login']);
  }
  logueado() {
    return this.usuario.isLogin !== undefined && this.usuario.isLogin;
  }

  validar(force: boolean = false) {
    if (this.logueado()) {
      const d1 = new Date(this.usuario.tiempo * 1000);
      const d2 = new Date();
      d2.setHours(d2.getHours() + 1);
      if (force || d1 <= d2) {
        this.http.post(this.urlogin + 'verificar', { usuario: this.usuario.usuario, token: this.usuario.token })
          .subscribe((dt: any) => {
            if (!(dt.success !== undefined && dt.success === '1' && dt.data === 'ok')) {
              this.userlogout();
            }
          });
      }
    } else this.userlogout();
  }
  getRoles() {
    return this.usuario.roles;
  }
  getmenu() {
    const data = localStorage.getItem('RUCdatosmenu');
    return (data != null && data.length > 0) ? JSON.parse(data) : false;
  }
  getmenubtn() {
    const data = localStorage.getItem('RUCdatosmenubtn');
    return (data != null && data.length > 0) ? JSON.parse(data) : false;
  }
  listmenu() {
    this.http.post(this.urlapp + 'menu', { roles: this.getRoles() }).subscribe((data: any) => {
      if (data[0] !== undefined) {
        localStorage.removeItem('RUCdatosmenu');
        localStorage.removeItem('RUCdatosmenubtn');
        data.map((v: any) => {
          if (v.masbtn) {
            localStorage.setItem('RUCdatosmenubtn', JSON.stringify(v.masbtn));
            delete v.masbtn;
          }
        }
        );
        localStorage.setItem('RUCdatosmenu', JSON.stringify(data));
      }
    });
  }
  parsestring(msj: any, separador = '<br>') {
    // console.log(msj.constructor === Array);
    if (msj instanceof Array || msj.constructor === Array) {
      msj = msj.join(separador);
    } else if (msj instanceof Object || msj.constructor === Object) {
      msj = Object.entries(msj).map(entry => entry.join(':')).join(separador);
    }
    return msj;
  }
  mensaje(msj: any, tipo = 'success', t = '') {
    swal.fire({
      width: 'auto',
      position: 'top-end',
      html: msj,
      title: t,
      buttonsStyling: false,
      customClass: {
        popup: 'bg-' + tipo,
        confirmButton: 'btn btn-success btn-sm',
      }
      // tipo = warning, success, danger, info
    });
  }
}
