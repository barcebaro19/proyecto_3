import { Component } from '@angular/core';
import { LoaderService } from '../../servicios/servicios.index';
@Component({
  selector: 'app-loader',
  templateUrl: './loader.component.html'
})
export class LoaderComponent {
  loading = false;
  constructor(private _loader: LoaderService) {
    this._loader.isLoading.subscribe((v) => {
      this.loading = v;
    });
  }
}
