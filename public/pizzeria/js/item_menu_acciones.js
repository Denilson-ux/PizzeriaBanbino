/* global $, alertify */

(function () {
  'use strict';

  // Si ya existe, no redefinir
  if (window.accionesFormatter) return;

  window.accionesFormatter = function (value, row) {
    const gestionar = `<a href="/admin/item-menu/${row.id_item_menu}/receta" class="btn btn-sm btn-secondary mr-1">Gestionar receta</a>`;
    const editar = `<a class="btn btn-sm btn-warning mr-1" onclick="window.editarItem && window.editarItem(${row.id_item_menu})">Editar</a>`;
    const eliminar = `<a class="btn btn-sm btn-danger" onclick="window.eliminarItem && window.eliminarItem(${row.id_item_menu})">Eliminar</a>`;
    return gestionar + editar + eliminar;
  };

  // Si se inicializa la tabla vía JS aquí, asegúrate de usar el formatter.
  document.addEventListener('DOMContentLoaded', function () {
    const $tabla = $('#tabla-item-menu');
    if ($tabla.length) {
      // Si ya hay opciones programáticas, no sobreescribimos aquí.
      // Este archivo solo proporciona el formatter por defecto.
    }
  });
})();
