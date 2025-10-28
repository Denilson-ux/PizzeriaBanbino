@extends('cliente_web.layoutweb')


@section('contentweb')

<div class="hero_area">
  {{-- Imagen de carrusel --}}
  <div class="bg-box">
    <img src="{{asset('/feane/images/hero-bg.jpg')}}" alt="">
  </div>

  <!-- slider section -->
  <section class="slider_section ">
    <div id="customCarousel1" class="carousel slide" data-ride="carousel">
      <div class="carousel-inner">
        <div class="carousel-item active">
          <div class="container ">
            <div class="row">
              <div class="col-md-7 col-lg-6 ">
                <div class="detail-box">
                  <h1>
                    Bienvenido a Mi pizzeria
                  </h1>
                  <p>
                    "Sumérgete en una experiencia pizzera única en nuestro acogedor restaurante. Descubre la deliciosa fusión de sabores en cada pizza, cuidadosamente preparada para satisfacer tus antojos. Deleita tus sentidos con nosotros."
                  </p>
                  <div class="btn-box">
                    <a href="#section-items" class="btn1">
                      ¡Ordena Ahora!
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="carousel-item ">
          <div class="container ">
            <div class="row">
              <div class="col-md-7 col-lg-6 ">
                <div class="detail-box">
                  <h1>
                    Explora los Sabores en Mi pizzeria
                  </h1>
                  <p>
                    "Deleita tu paladar con nuestras exquisitas pizzas artesanales. Cada porción es una explosión de sabor y frescura. En Mi Pizzería, no solo ofrecemos comida rápida, ¡ofrecemos una experiencia pizzera inolvidable!"
                  </p>
                  <div class="btn-box">
                    <a href="#section-items" class="btn1">
                      ¡Haz tu Pedido Ahora!
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="carousel-item">
          <div class="container">
            <div class="row">
              <div class="col-md-7 col-lg-6 ">
                <div class="detail-box">
                  <h1>
                    Descubre Mi pizzeria
                  </h1>
                  <p>
                    "Crea momentos especiales con nosotros. En Mi Pizzería, no solo horneamos deliciosas pizzas, sino que también creamos recuerdos. Ven y disfruta de la atmósfera acogedora mientras te entregamos delicias irresistibles que te harán volver por más."
                  </p>
                  <div class="btn-box">
                    <a href="#section-items" class="btn1">
                      ¡Ordénalo Ya!
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="container">
        <ol class="carousel-indicators">
          <li data-target="#customCarousel1" data-slide-to="0" class="active"></li>
          <li data-target="#customCarousel1" data-slide-to="1"></li>
          <li data-target="#customCarousel1" data-slide-to="2"></li>
        </ol>
      </div>
    </div>
  </section>
  <!-- end slider section -->
</div>

<section class="food_section layout_padding-bottom mt-3 mb-3" id="section-items">
    <div class="container">
    <div class="heading_container heading_center">
      <h2>
        Menú del días
      </h2>
    </div>

    <ul class="filters_menu" id="ul-tipo-menu">
      <li data-filter=".fries">Fries</li>
    </ul>

    <div class="filters-content">
      <div class="row grid" id="contenido-item-menu">
        
      </div>
    </div>
    {{-- <div class="btn-box">
      <a href="">
        View More
      </a>
    </div> --}}
    </div>
</section>


{{-- modal carrito --}}
<!-- Modal -->
<div class="modal fade custom-modal" id="carritoModal" tabindex="-1" role="dialog" aria-labelledby="carritoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content custom-modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="carritoModalLabel"><i class="fas fa-receipt"></i> Detalle del carrito</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body overflow-auto">
        <p class="text-white fs-6" style="font-size: 25px">Monto total Bs.: <span id="monto" class="text-white" style="font-size: 25px"></span></p>
        <div class="row container d-flex justify-content-center align-items-center" id="content-modal-carrito">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-shopping-cart"></i> Seguir comprando</button>
        <button type="button" class="btn btn-info" id="proceder-pagar"><i class="fas fa-money-check"></i> Proceder a pedir</button>
      </div>
    </div>
  </div>
</div>
{{-- end modal carrito --}}


<!-- modal perfil-->
<div class="modal fade" id="modal-perfil" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        {{-- <h5 class="modal-title" id="staticBackdropLabel">Tu información</h5> --}}
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
          <div class="row">
              <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Perfil de Usuario</h3>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <!-- Columna de la Foto de Perfil -->
                                    <div class="col-md-4 text-center">
                                        <img src="https://via.placeholder.com/150" alt="Foto de Perfil" id="preview" class="img-fluid rounded-circle">
                                        <input type="file" class="form-control mt-2" accept="image/*" onchange="previewImage(event)" />
                                    </div>
            
                                    <!-- Columna de la Información del Usuario -->
                                    <div class="col-md-8">
                                        <form>
                                            <h5>Información Personal</h5>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="nombre" class="form-label">Nombre:</label>
                                                    <input type="text" class="form-control" id="nombre" value="John Doe">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="email" class="form-label">Email:</label>
                                                    <input type="email" class="form-control" id="email" value="john@example.com">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="telefono" class="form-label">Teléfono:</label>
                                                    <input type="tel" class="form-control" id="telefono" value="(123) 456-7890">
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-warning">
                                              <i class="fas fa-save"></i> 
                                              Guardar Cambios
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
              </div>
          </div>
      </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-dark" data-bs-dismiss="modal">
              <i class="fas fa-door-open"></i>    
              Close
          </button>
      </div>
    </div>
  </div>
</div>

{{-- modal inicio de sesión --}}
<div class="modal fade" id="modal-inicio-sesion" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog text-dark">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Iniciar Sesión</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
                  <div class="mb-3">
                      <label for="email-inicio" class="form-label">Correo Electrónico</label>
                      <input type="email-inicio" class="form-control" id="email-inicio" required>
                  </div>
                  <div class="mb-3">
                      <label for="password-inicio" class="form-label">Contraseña</label>
                      <input type="password" class="form-control" id="password-inicio" required>
                  </div>
                  <button type="button" class="btn btn-info" id="crear-cuenta"><i class="fas fa-user-plus"></i> Crear cuenta</button>
                  <button type="button" class="btn btn-warning" id="iniciar-sesion"><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</button>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          </div>
      </div>
  </div>
</div>

{{-- Botón flotante --}}
<div class="floating-cart-button" id="boton-flotante">
  <span id="total-item" class="text-white"></span>
  <a id="open-cart" href="alg" class="text-warning" data-toggle="modal" data-target="#carritoModal">
    <i class="fa fa-shopping-cart" aria-hidden="true"></i> Carrito
  </a>
</div>
{{-- end boton flotante --}}

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
    .custom-modal-content {
      background-image: url({{asset('/images/fondo_cliente3.jpg')}}); /* Ruta de tu imagen de fondo */
      background-size: cover;
      background-position: center;
      color: #fff; /* Color del texto */
    }
  </style>
@endsection

@section('clienteweb-js')
  <script src="{{asset('/pizzeria/js/cliente_web/index_cliente_web.js')}}"></script>
  <script src="{{asset('/pizzeria/js/cliente_web/form_cliente.js')}}"></script>
@endsection