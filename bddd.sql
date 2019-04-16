create DATABASE if not EXISTS curso_angular4;
use curso_angular4;

create table productos (
    id  int(255) auto_increment not null,
    nombre  varchar(255),
    descripcion text,
    precio  varchar(255),
    imagen  varchar(255),
    CONSTRAINT pk_productos primary key(id)
) engine=InnoDb;