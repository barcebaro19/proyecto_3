import { Component, OnInit } from '@angular/core';
import { Router, Event, NavigationStart, NavigationEnd, NavigationCancel, NavigationError, ActivatedRoute } from '@angular/router';
import { LoaderService } from '../servicios/servicios.index';
import { environment } from '../../environments/environment';

@Component({
  selector: 'app-paginas',
  templateUrl: './paginas.component.html'
})
export class PaginasComponent implements OnInit {
  clases = '';
  anio = new Date().getFullYear();
  version = environment.version;
  constructor(private _router: Router, private route: ActivatedRoute, private _loader: LoaderService) {
    this._router.events.subscribe((event: Event) => {
      this.navigationInterceptor(event);
    });
  }
  ngOnInit() {
    this.route.queryParams.subscribe(v => {
      if (v.page !== undefined) {
        // this._login.listmenu();
      }
    });
    // setTimeout(() => {this._loader.show(); console.log('this._loader.show()'); }, 4000);
  }
  private navigationInterceptor(event: Event): void {
    // mostrar y ocultar loading de navegacion
    switch (true) {
      case event instanceof NavigationStart: {
        this._loader.show();
        break;
      }
      case event instanceof NavigationEnd:
      case event instanceof NavigationCancel:
      case event instanceof NavigationError: {
        this._loader.hide();
        break;
      }
      default: {
        break;
      }
    }
  }
}
