export const environment = {
  production: false,
  version: 'v1.0.1',
  url: 'http://localhost/api/apirest/',
  urlogin: 'http://localhost/ssologin/',
  app: 1,
  datatableoption: {
    destroy: true,
    responsive: {
      details: false
    },
    language: {
      paginate: {
        previous: 'Anterior',
        next: 'Siguiente',
      },
      info: 'Pagina _START_ de _END_ de _TOTAL_ registros',
      search: 'Buscar:',
      lengthMenu: '_MENU_ registros'
    }
  }
};
