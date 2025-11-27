import { HttpEvent, HttpHandler, HttpInterceptor, HttpRequest, HttpResponse, HttpErrorResponse } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable, of } from 'rxjs';
// import { map } from 'rxjs/operator';
import { finalize, tap, catchError } from 'rxjs/operators';
import { LoginService, LoaderService } from '../servicios/servicios.index';
@Injectable()
export class AuthInterceptor implements HttpInterceptor {
  constructor(private _loader: LoaderService, private user: LoginService) { }
  intercept(req: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
    if (req.body && req.body.token === undefined && this.user.usuario.apptoken) {
      req = req.clone({ headers: req.headers.set('token', this.user.usuario.apptoken) });
    }
    this._loader.show();
    return next.handle(req).pipe(
      tap(event => {
        if (event instanceof HttpResponse) {
          if (event.body.salir) {
            this.user.userlogout();
          }
          if (event.body.error) {
            this.user.mensaje(this.user.parsestring(event.body.error));
          }
        }
      }),
      finalize(() => this._loader.hide()),
      catchError((err: any) => {
        if (err instanceof HttpErrorResponse) {
          try {
            console.log(err);
            this.user.mensaje(err.message, 'warning');
          } catch (e) {
            this.user.mensaje('Un error a ocurrido', 'warning');
          }
        }
        return of(err);
      }));
  }
}