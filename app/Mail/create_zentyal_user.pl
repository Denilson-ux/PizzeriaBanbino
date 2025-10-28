#!/usr/bin/perl
use strict;
use warnings;
use EBox;
use EBox::Samba::User;


# Argumentos proporcionados en la línea de comandos
my $username = "jose";
my $givenname = "Jose Luis";
my $surname = "Tagua";
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
    print $user;
    print "Usuario '$username' creado exitosamente.\n";
} else {
    print "Error al crear el usuario.\n";
}
