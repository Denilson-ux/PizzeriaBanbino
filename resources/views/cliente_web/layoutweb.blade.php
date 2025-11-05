<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="keywords" content="" />
  <meta name="description" content="" />
  <meta name="author" content="" />
  <link rel="shortcut icon" href="{{asset('/feane/images/favicon.png')}}" type="">
  <link rel="stylesheet" href="{{asset('/bootstrap/css/alertify.min.css')}}"/>
  <link rel="stylesheet" href="{{asset('/pizzeria/css/style_web.css')}}"/>

  {{-- personalizado --}}
  <link rel="stylesheet" href="{{asset('/pizzeria/css/style.css')}}"/>

  <title> Mi pizzeria </title>

  <link rel="stylesheet" type="text/css" href="{{asset('/feane/css/bootstrap.css')}}" />
  <link rel="stylesheet" type="text/css" href="{{asset('/feane/css/carousel.min.css')}}" />
  <!-- QUITADO INTEGRITY DE SELECT -->
  <link rel="stylesheet" href="{{asset('/feane/css/select.min.css')}}" />
  <link href="{{asset('/feane/css/font-awesome.min.css')}}" rel="stylesheet" />
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
  <link href="{{asset('/feane/css/style.css')}}" rel="stylesheet" />
  <link href="{{asset('/feane/css/responsive.css')}}" rel="stylesheet" />

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
   <!-- jQery -->
   <script src="{{asset('/feane/js/jquery-3.4.1.min.js')}}"></script>

    @yield('clienteweb-css')

    <!-- Custom styles for profile modal -->
    <style>
        /* Perfil Modal - Mejora de visibilidad del texto */
        .profile-modal .modal-content {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }
        
        .profile-modal .modal-header {
            background: linear-gradient(135deg, #ff6b35, #f7931e);
            color: white;
            border-radius: 15px 15px 0 0;
            border-bottom: none;
        }
        
        .profile-modal .modal-header .close {
            color: white;
            opacity: 1;
            font-size: 1.5rem;
        }
        
        .profile-modal .modal-header .close:hover {
            color: rgba(255, 255, 255, 0.8);
        }
        
        .profile-modal .modal-body {
            background: white;
            color: #333;
            padding: 2rem;
        }
        
        .profile-modal .form-control {
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid #e9ecef;
            color: #333;
            font-weight: 500;
        }
        
        .profile-modal .form-control:focus {
            background: white;
            border-color: #ff6b35;
            box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.25);
            color: #333;
        }
        
        .profile-modal .form-control::placeholder {
            color: #6c757d;
            opacity: 1;
        }
        
        .profile-modal .btn-warning {
            background: linear-gradient(135deg, #ff6b35, #f7931e);
            border: none;
            color: white;
            font-weight: 600;
            padding: 10px 30px;
            border-radius: 25px;
            transition: all 0.3s ease;
        }
        
        .profile-modal .btn-warning:hover {
            background: linear-gradient(135deg, #e55a2e, #e8851a);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 107, 53, 0.4);
        }
        
        .profile-modal .modal-backdrop {
            background-color: rgba(0, 0, 0, 0.6);
        }
        
        /* Asegurar que las etiquetas sean legibles */
        .profile-modal label {
            color: #333 !important;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        /* Mejorar el input de archivo */
        .profile-modal input[type="file"] {
            background: white;
            border: 2px dashed #ff6b35;
            padding: 15px;
            border-radius: 10px;
            color: #333;
        }
        
        .profile-modal input[type="file"]:hover {
            border-color: #e55a2e;
            background: #fff5f3;
        }
    </style>

</head>

<body>

  <div id="loader-container"></div>

  <!-- header section strats -->
  <header class="header_section header-section">
    <div class="container">
      <nav class="navbar navbar-expand-lg custom_nav-container ">
        <a class="navbar-brand" href="index.html">
          <span>
            pizzeria
          </span>
        </a>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class=""> </span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav  mx-auto ">
            <li class="nav-item">
              <a class="nav-link" href="{{asset('/')}}">Home</span></a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#section-items">Menú del día</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#about">Sobre nosotros</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="{{asset('/form')}}" id="registrarme-nav">Registrarme</a>
            </li>
          </ul>
          <div class="dropdown" id="dropdwn-user">
            <button class="btn btn-sm btn-dark dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="text-white"><i class="fa fa-user" aria-hidden="true"></i></span>
                <span id="nombre-usuario"></span>
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                <button class="dropdown-item" type="button" id="perfil">
                  <i class="fas fa-user"></i> Perfil
                </button>
                
                <button class="dropdown-item" type="button" id="mis-pedidos">
                    <i class="fas fa-shopping-cart"></i> Mis pedidos
                </button>
                
                <button class="dropdown-item" type="button" id="cerrar-sesion">
                    <i class="fas fa-sign-out-alt"></i> Salir
                </button>            
            </div>
          </div>
          <div class="user_option" id="nav-carrito-search">
            <a class="btn btn-sm user_link" href="alg" data-toggle="modal" data-target="#carritoModal">
              <i class="fa fa-shopping-cart" aria-hidden="true"></i>
            </a>
            {{-- <form class="form-inline">
              <button class="btn my-2 my-sm-0 nav_search-btn" type="submit">
                <i class="fa fa-search" aria-hidden="true"></i>
              </button>
            </form> --}}
            {{-- <a href="" class="order_online text-center">
              Ordene en línea
            </a> --}}
          </div>
        </div>
      </nav>
    </div>
  </header>
  <!-- end header section -->

    <section class="contenido-web">
        @yield('contentweb')
    </section>


  <div id="section-footer">
      <!-- about section -->
     <section class="about_section layout_padding" id="about">
      <div class="container  ">
  
       <div class="d-flex justify-content-center">
          <div class="col-md-6">
              <h2>Envíanos un mensaje</h2>
              <input type="text" class="form-control my-2" id="asunto"  placeholder="Asunto">
              <textarea name="" class="form-control" id="descripcion-mensaje" cols="30" rows="5" placeholder="Escribte tu mensaje"></textarea>
          </div>
          <div class="col-md-6">
            <div class="detail-box">
                <div class="heading_container">
                    <h2>
                      Bienvenido a Mi pizzeria
                    </h2>
                    <p>
                      "En Mi Pizzería, nos enorgullece ofrecer una experiencia pizzera única y acogedora. Nuestro restaurante está diseñado para transportarte a un lugar donde la buena comida y la hospitalidad excepcional se encuentran. Utilizamos ingredientes frescos y de la más alta calidad para crear pizzas deliciosas que satisfacen los paladares más exigentes.
                    </p>
                    <p>
                        Nuestra misión es crear recuerdos a través de la comida, brindando momentos especiales y
                        auténticos sabores en cada bocado. Ya sea que vengas a disfrutar de una comida en nuestro
                        acogedor comedor o a ordenar para llevar, estamos comprometidos a hacer que tu experiencia
                        sea inolvidable.
                    </p>
                    <p>
                        ¡Gracias por elegir Mi pizzeria! Esperamos tener el placer de servirte pronto.
                    </p>
                </div>
            </div>
        </div>
      </div>
    </section>
    <!-- end about section -->

  <!-- comentarios client section -->
  <section class="client_section layout_padding-bottom">
    <div class="container">
      <div class="heading_container heading_center psudo_white_primary mb_45">
        <h2>
          Lo que dicen nuestros clientes
        </h2>
      </div>
      <div class="carousel-wrap row ">
        <div class="owl-carousel client_owl-carousel">
          <div class="item">
            <div class="box">
              <div class="detail-box">
                <p>
                  Disfruté de momentos increíbles en Mi pizzeria. La comida es exquisita, y el ambiente es perfecto para relajarse. Sin duda, un lugar único.
                </p>
                <h6>
                  Moana Michell
                </h6>
                <p>
                  magna aliqua
                </p>
              </div>
              <div class="img-box">
                {{-- <img src="images/client1.jpg" alt="" class="box-img"> --}}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- end comentarios client section -->

  <!-- footer section -->
  <footer class="footer_section">
    <div class="container">
      <div class="row">
        <div class="col-md-4 footer-col">
          <div class="footer_contact">
            <h4>
              Contáctanos
            </h4>
            <div class="contact_link_box">
              <a href="#">
                <i class="fa fa-map-marker" aria-hidden="true"></i>
                <span>
                  Ubicación
                </span>
              </a>
              <a href="#">
                <i class="fa fa-phone" aria-hidden="true"></i>
                <span>
                  +591 XXXXXXXX
                </span>
              </a>
              <a href="mailto:pizzeria@gmail.com">
                <i class="fa fa-envelope" aria-hidden="true"></i>
                <span>
                  pizzeria@gmail.com
                </span>
              </a>
            </div>
          </div>
        </div>
        <div class="col-md-4 footer-col">
          <div class="footer_detail">
            <a href="#" class="footer-logo">
              Mi pizzeria
            </a>
            <p>
              Descubre una experiencia gastronómica única en Mi pizzeria. Utilizamos ingredientes frescos para ofrecer sabores auténticos que te transportarán a nuevos lugares.
            </p>
            <div class="footer_social">
              <a href="#">
                <i class="fa fa-facebook" aria-hidden="true"></i>
              </a>
              <a href="#">
                <i class="fa fa-twitter" aria-hidden="true"></i>
              </a>
              <a href="#">
                <i class="fa fa-instagram" aria-hidden="true"></i>
              </a>
            </div>
          </div>
        </div>
        <div class="col-md-4 footer-col">
          <h4>
            Horario de Atención
          </h4>
          <p>
            Todos los días
          </p>
          <p>
            10:00 a.m. - 10:00 p.m.
          </p>
        </div>
      </div>
      <div class="footer-info">
        <p>
          &copy; <span id="displayYear"></span> Todos los derechos reservados por<br><br>
          &copy; <span id="displayYear"></span> Distribuido por
        </p>
      </div>
    </div>
    
    {{-- Contador de visitas --}}
    @include('components.page-visit-counter')
    
  </footer>
  <!-- footer section -->
</div>

<div id="id-footer">

</div>

 

  <!-- popper js - QUITADO INTEGRITY -->
  <script src="{{asset('/feane/js/popper.min.js')}}"></script>
  <!-- bootstrap js -->
  <script src="{{asset('/feane/js/bootstrap.js')}}"></script>
  <!-- owl slider -->
  <script src="{{asset('/feane/js/carousel.min.js')}}"></script>
  <!-- isotope js -->
  <script src="{{asset('/feane/js/isotope-layout@3.0.4_dist_isotope.pkgd.min.js')}}"></script>
  <!-- nice select -->
  <script src="{{asset('/feane/js/ajax_libs_jquery-nice-select_1.1.0_js_jquery.nice-select.min.js')}}"></script>
  <!-- custom js -->
  <script src="{{asset('/feane/js/custom.js')}}"></script>

  {{-- alert --}}
  <script src="{{asset('/bootstrap/js/alertify.min.js')}}"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script src="{{asset('/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

  <script src="{{asset('/bootstrap/js/spin.min.js')}}"></script>

  {{-- parametros --}}
  <script src="{{asset('/pizzeria/js/parametros.js')}}"></script>
  <script src="{{asset('/pizzeria/js/cliente_web/nav.js')}}"></script>

  @yield('clienteweb-js')
</body>

</html>