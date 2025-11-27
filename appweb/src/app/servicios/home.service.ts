import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../environments/environment';
@Injectable({ providedIn: 'root' })
export class HomeService {
  private urlapp = environment.url + 'controlador/';
  constructor(public http: HttpClient) { }
  listaritems() {
    return this.http.post(this.urlapp + 'metodo', {});
  }
  unitem(id: number) {
    return this.http.post(this.urlapp + 'metodo', {id});
  }
  masitems() {
    return this.http.post(this.urlapp + 'metodo', {});
  }
}
