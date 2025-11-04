/* global $, alertify */

(function () {
  'use strict';

  // Sobrescribir para mostrar botones con el mismo estilo (Editar/Eliminar)
  window.accionesFormatter = function (value, row) {
    const gestionar = `<a href="/admin/item-menu/${row.id_item_menu}/receta" class="btn btn-sm btn-success mr-1" title="Gestionar receta" data-toggle="tooltip"><i class="fas fa-utensils"></i></a>`;
    const editar = `<button class="btn btn-sm btn-warning mr-1 edit" data-edit="${row.id_item_menu}" title="Editar" data-toggle="tooltip"><i class="fas fa-edit"></i></button>`;
    const eliminar = `<button class="btn btn-sm btn-danger delete" data-delete="${row.id_item_menu}" title="Eliminar" data-toggle="tooltip"><i class="fas fa-trash"></i></button>`;
    return gestionar + editar + eliminar;
  };
  
  // Definir funciones globales para compatibilidad
  window.editarItem = function(id) {
    // Simular click en el botón con la clase .edit
    $(`.edit[data-edit="${id}"]`).trigger('click');
  };
  
  window.eliminarItem = function(id) {
    // Simular click en el botón con la clase .delete
    $(`.delete[data-delete="${id}"]`).trigger('click');
  };
})();