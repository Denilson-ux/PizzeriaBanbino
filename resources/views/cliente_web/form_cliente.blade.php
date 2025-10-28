@extends('cliente_web.layoutweb')


@section('contentweb')

<section class="book_section layout_padding mt-3">
    <div class="container">
      <div class="heading_container">
        <h4>
          <i class="fas fa-user-plus"></i> Crea una cuenta para hacer tu pedido
        </h4>
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="form_container">
            
              <div>
                <input id="usuario" type="text" class="form-control" placeholder="Nombre de usuario" />
              </div>
              <div>
                <input id="nombre" type="text" class="form-control" placeholder="Nombre" />
              </div>
              <div>
                <input id="paterno" type="text" class="form-control" placeholder="Apellido" />
              </div>
              <div>
                <input id="telefono" type="text" class="form-control" placeholder="Telefono" />
              </div>
              {{-- <div>
                <input type="date" class="form-control">
              </div> --}}
              <div class="btn_box">
                <button id="btn-registrar">
                    Registrarme
                </button>
              </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form_container">
              <div>
                <input id="correo" type="email" class="form-control" placeholder="Tu Correo" />
                <p id="correo-advertencia" class="form-label text-danger d-none">Correo no disponible</p>
                
              </div>
              <div>
                <input id="password" type="password" class="form-control" placeholder="Contraseña" />
                <p class="form-label text-danger d-none password-advertencia">Las contraseñas no coinciden</p>
              </div>
              <div>
                <input id="password-repite" type="password" class="form-control" placeholder="Repite la Contraseña" />
                <p class="form-label text-danger d-none password-advertencia">Las contraseñas no coinciden</p>

              </div>
              {{-- <div>
                <input type="date" class="form-control">
              </div> --}}
          </div>
        </div>
      </div>
    </div>
</section>


@endsection

@section('clienteweb-css')
  <style>
    body {
        background-image: url({{asset('/images/fondo_cliente3.jpg')}}); /* Reemplaza '/path/to/your/image.jpg' con la ruta real de tu imagen */
        background-size: cover;
        background-position: center center;
        background-attachment: fixed; /* Para mantener la imagen fija mientras se desplaza */
        color: white; /* Color de texto blanco u otro color que desees */
    }
  </style>
@endsection

  @section('clienteweb-js')

      <script src="{{asset('/pizzeria/js/cliente_web/form_cliente.js')}}"></script>

  @endsection