#!/usr/bin/perl

use strict;
use warnings;
use EBox;
use EBox::Samba::User;

# Argumentos proporcionados en la línea de comandos
my $username = "ezequiel1";
my $givenname = "Ezequiel1";
my $surname = "Apellido1";
my $password = "123";

# Inicializar Zentyal
EBox::init();

# Obtener el contenedor predeterminado de usuarios Samba
my $parent = EBox::Samba::User->defaultContainer();

# Crear el usuario en Zentyal
my $user = EBox::Samba::User->create(
    samAccountName => $username,
    parent         => $parent,
    givenName      => $givenname,
    sn             => $surname,
    password       => $password
);

# Verificar si la creación del usuario fue exitosa
if ($user) {
    print "Usuario creado exitosamente";
} else {
    print "Error al crear el usuario";
}
