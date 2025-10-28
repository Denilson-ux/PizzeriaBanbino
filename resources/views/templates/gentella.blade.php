<!DOCTYPE html>
<html lang="en">
  <head>
    @yield('css')
  </head>
  <body class="nav-md">
    <div class="container body">
      <div class="main_container">
        <div class="col-md-3 left_col">
          <div class="left_col scroll-view">
            <!-- sidebar menu -->
            <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
              <div class="menu_section">
                <h3>General</h3>
                <ul class="nav side-menu">
                  <li>
                    <a>
                      <i class="fas fa-utensils"></i> Items Menu <span class="fa fa-chevron-down"></span>
                    </a>
                    <ul class="nav child_menu">
                      <li><a href="{{ url('/admin/tipo-menu') }}">Tipo item menú</a></li>
                      <li><a href="{{ url('/admin/item-menu') }}">Item menú</a></li>
                      <li><a href="{{ url('/admin/menu') }}">Catálogo menú</a></li>
                    </ul>
                  </li>

                  <li>
                    <a>
                      <i class="fas fa-warehouse"></i> <strong>Gestión de Almacenes Físicos</strong> <span class="fa fa-chevron-down"></span>
                    </a>
                    <ul class="nav child_menu">
                      <li><a href="{{ route('almacenes-fisicos.index') }}"><i class="fas fa-warehouse"></i> Almacenes Físicos</a></li>
                      <li><a href="{{ route('almacenes-fisicos.create') }}"><i class="fas fa-plus"></i> Nuevo Almacén</a></li>
                    </ul>
                  </li>

                  <li>
                    <a>
                      <i class="fa fa-archive"></i> Inventario y Ingredientes <span class="fa fa-chevron-down"></span>
                    </a>
                    <ul class="nav child_menu">
                      <li><a href="{{ url('/admin/ingredientes') }}">Ingredientes</a></li>
                      <li><a href="{{ url('/admin/ingredientes/stock-bajo') }}">Stock bajo</a></li>
                      <li><a href="{{ url('/admin/ingredientes/reporte-inventario') }}">Reporte de inventario</a></li>
                    </ul>
                  </li>

                  <li>
                    <a>
                      <i class="fa fa-shopping-cart"></i> Compras y Proveedores <span class="fa fa-chevron-down"></span>
                    </a>
                    <ul class="nav child_menu">
                      <li><a href="{{ url('/admin/compras') }}">Compras</a></li>
                      <li><a href="{{ url('/admin/proveedores') }}">Proveedores</a></li>
                    </ul>
                  </li>
                  
                  <!-- Sistema Anterior -->
                  <li>
                    <a>
                      <i class="fa fa-box text-muted"></i> <span class="text-muted">Sistema Anterior</span> <span class="fa fa-chevron-down"></span>
                    </a>
                    <ul class="nav child_menu">
                      <li><a href="{{ url('/admin/almacen') }}"><span class="text-muted">Almacén (Antiguo)</span></a></li>
                    </ul>
                  </li>
                </ul>
              </div>
            </div>
            <!-- /sidebar menu -->
          </div>
        </div>

        <div class="right_col" role="main">
            <div class="container">
                @yield('container')
            </div>
        </div>

      </div>
    </div>

    @yield('js')
  </body>
</html>