# DATABASE TACH

drop database if exists tach; 
create database tach;
use tach;

create table user(
    id varchar(100) primary key not null,
    nombre_usuario varchar(20) not null,
    nombres varchar(50) not null, 
    clave varchar(200) not null,
    cedula varchar(10) not null,
    direccion varchar(100) not null,
    telefono varchar(15) not null,
    celular varchar(15) not null,
    fecha_nacimiento date not null,
    correo varchar(320) not null,
    fecha_contratacion date null,
    salario double null,
    estado int not null,
    estado_tabla int not null,
    usr_ing varchar(50) not null,
    fec_ing datetime not null,
    usr_mod varchar(50) null,
    fec_mod datetime null
);

create table rol(
    id varchar(100) primary key not null,
    descripcion varchar(50) not null,
    estado int not null,
    estado_tabla int not null,
    usr_ing varchar(50) not null,
    fec_ing datetime not null,
    usr_mod varchar(50) null,
    fec_mod datetime null
);

create table rol_user(
    user_id varchar(100) not null,
    rol_id varchar(100) not null,
    foreign key(user_id) references user(id),
    foreign key(rol_id) references rol(id)
);

create table modulo(
    id int primary key not null,
    descripcion varchar(50) not null
);

create table modulo_rol(
    rol_id varchar(100) not null,
    modulo_id int not null,
    foreign key(rol_id) references rol(id),
    foreign key(modulo_id) references modulo(id)
);

create table proveedor(
    id varchar(100) primary key not null,
    descripcion varchar(50) not null,
    convenio int not null,
    telefono varchar(15) null,
    direccion varchar(100) null,
    tipo_proveedor varchar(100) null,
    contacto varchar(50) null,
    telefono_contacto varchar(15) null,
    correo_contacto varchar(320) null,
    estado int not null,
    estado_tabla int not null,
    usr_ing varchar(50) not null,
    fec_ing datetime not null,
    usr_mod varchar(50) null,
    fec_mod datetime null
);

create table marca(
    id varchar(100) primary key not null,
    descripcion varchar(50) not null,
    estado int not null,
    estado_tabla int not null,
    usr_ing varchar(50) not null,
    fec_ing datetime not null,
    usr_mod varchar(50) null,
    fec_mod datetime null
);

create table categoria(
    id varchar(100) primary key not null,
    descripcion varchar(50) not null,
    estado int not null,
    estado_tabla int not null,
    usr_ing varchar(50) not null,
    fec_ing datetime not null,
    usr_mod varchar(50) null,
    fec_mod datetime null
);

create table repuesto(
    id varchar(100) primary key not null,
    codigo varchar(50) not null,
    marca_id varchar(100) not null,
    categoria_id varchar(100) not null,
    modelo varchar(50) not null,
    fecha varchar(20) null,
    stock int not null,
    precio double null,
    descripcion varchar(100) null,
    estado int not null,
    estado_tabla int not null,
    usr_ing varchar(50) not null,
    fec_ing datetime not null,
    usr_mod varchar(50) null,
    fec_mod datetime null,
    foreign key(marca_id) references marca(id),
    foreign key(categoria_id) references categoria(id)
);

create table compra(
    id varchar(100) primary key not null,
    descripcion varchar(100) not null,
    proveedor_id varchar(100) not null,
    tipo_documento varchar(50) null,
    num_documento varchar(50) null,
    fecha date null,
    total double not null,
    estado_tabla int not null,
    usr_ing varchar(50) not null,
    fec_ing datetime not null,
    usr_mod varchar(50) null,
    fec_mod datetime null,
    foreign key(proveedor_id) references proveedor(id)
);

create table compra_detalles(
    id varchar(100) primary key not null,
    repuesto_id varchar(100) not null,
    cantidad int not null,
    precio double not null,
    compra_id varchar(100) not null,
    foreign key(repuesto_id) references repuesto(id),
    foreign key(compra_id) references compra(id)
        on delete cascade
);

/* Inserts */
#clave 123
insert into user values('e2ec4818-4af1-4f4e-b2bc-7d1adb2ec341', 'luisv397', 'Luis Alberto Velastegui Pino', 
    'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3', '0941500720', '', '2190434', '0959633941', 
    '1997-09-03', 'luisvelastegui307@gmail.com', '2020-11-30', 500, 1, 1, 'luisv397', now(), 'luisv397', now());
insert into rol values('e2ec4818-4af1-4f4e-b2bc-7d1adb2ec341', 'Administrador', 1, 1, 'luisv397', now(), 'luisv397', now());
insert into rol_user values('e2ec4818-4af1-4f4e-b2bc-7d1adb2ec341', 'e2ec4818-4af1-4f4e-b2bc-7d1adb2ec341');
insert into modulo values(1, 'Categorias');
insert into modulo values(2, 'Compras');
insert into modulo values(3, 'Marcas');
insert into modulo values(4, 'Proveedores');
insert into modulo values(5, 'Repuestos');
insert into modulo values(6, 'Usuarios');
insert into modulo values(7, 'Roles');
insert into modulo values(8, 'Ventas');
insert into modulo_rol values('e2ec4818-4af1-4f4e-b2bc-7d1adb2ec341', 1);
insert into modulo_rol values('e2ec4818-4af1-4f4e-b2bc-7d1adb2ec341', 2);
insert into modulo_rol values('e2ec4818-4af1-4f4e-b2bc-7d1adb2ec341', 3);
insert into modulo_rol values('e2ec4818-4af1-4f4e-b2bc-7d1adb2ec341', 4);
insert into modulo_rol values('e2ec4818-4af1-4f4e-b2bc-7d1adb2ec341', 5);
insert into modulo_rol values('e2ec4818-4af1-4f4e-b2bc-7d1adb2ec341', 6);
insert into modulo_rol values('e2ec4818-4af1-4f4e-b2bc-7d1adb2ec341', 7);
insert into modulo_rol values('e2ec4818-4af1-4f4e-b2bc-7d1adb2ec341', 8);
/* End Inserts*/


delimiter //
create procedure AddUsuario(json JSON)
begin
    declare _count int default 0;
    declare _id varchar(100) default json_unquote(json_extract(json, '$.id'));
    declare _nombre_usuario varchar(10) default json_unquote(json_extract(json, '$.nombreUsuario'));
    declare _nombres varchar(50) default json_unquote(json_extract(json, '$.nombres'));
    declare _clave varchar(200) default json_unquote(json_extract(json, '$.clave'));
    declare _cedula varchar(10) default json_unquote(json_extract(json, '$.cedula'));
    declare _direccion varchar(100) default json_unquote(json_extract(json, '$.direccion'));
    declare _telefono varchar(15) default json_unquote(json_extract(json, '$.telefono'));
    declare _celular varchar(15) default json_unquote(json_extract(json, '$.celular'));
    declare _fecha_nac date default json_unquote(json_extract(json, '$.fechaNacimiento'));
    declare _correo varchar(320) default json_unquote(json_extract(json, '$.correo'));
    declare _fecha_cont date default json_unquote(json_extract(json, '$.fechaContratacion'));
    declare _salario double default json_unquote(json_extract(json, '$.salario'));
    declare _usr_ing varchar(50) default json_unquote(json_extract(json, '$.usrIngreso'));
    declare _usr_mod varchar(50) default json_unquote(json_extract(json, '$.usrModificacion'));
    declare _estado varchar(50) default json_unquote(json_extract(json, '$.estado'));
    declare _roles json default json_extract(json, '$.roles');
    declare _rol_id varchar(100);
    insert into user values(_id, _nombre_usuario, _nombres, _clave, _cedula, _direccion, _telefono, _celular, 
        _fecha_nac, _correo, _fecha_cont, _salario, _estado, 1, _usr_ing, now(), _usr_mod, now()) on duplicate key update 
        nombre_usuario = _nombre_usuario, nombres = _nombres, clave = _clave, cedula = _cedula, direccion = _direccion, 
        telefono = _telefono, celular = _celular, fecha_nacimiento = _fecha_nac, correo = _correo, fecha_contratacion = 
        _fecha_cont, salario = _salario, usr_mod = _usr_mod, fec_mod = now(), estado = _estado;
    delete from rol_user where user_id = _id;
    while _count != json_length(_roles) do
        set _rol_id = json_unquote(json_extract(_roles, concat('$[', _count, ']')));
        insert into rol_user values(_id, _rol_id);
        set _count = _count + 1;
    end while;
end //
delimiter ;

delimiter //
create procedure AddRol(json JSON)
begin
    declare _count int default 0;
    declare _id varchar(100) default json_unquote(json_extract(json, '$.id'));
    declare _descripcion varchar(50) default json_unquote(json_extract(json, '$.descripcion'));
    declare _usr_ing varchar(50) default json_unquote(json_extract(json, '$.usrIngreso'));
    declare _usr_mod varchar(50) default json_unquote(json_extract(json, '$.usrModificacion'));
    declare _modulos json default json_extract(json, '$.modulos');
    declare _modulo_id int;
    insert into rol values(_id, _descripcion, 1, 1, _usr_ing, now(), _usr_mod, now()) on duplicate key update 
        descripcion = _descripcion, usr_mod = _usr_mod, fec_mod = now();
    delete from modulo_rol where rol_id = _id;
    while _count != json_length(_modulos) do
        set _modulo_id = json_unquote(json_extract(_modulos, concat('$[', _count, '].id')));
        insert into modulo_rol values(_id, _modulo_id);
        set _count = _count + 1;
    end while;
end //
delimiter ;

delimiter //
create procedure AddProveedor(json JSON)
begin
    declare _id varchar(100) default json_unquote(json_extract(json, '$.id'));
    declare _descripcion varchar(50) default json_unquote(json_extract(json, '$.descripcion'));
    declare _convenio int default json_unquote(json_extract(json, '$.convenio'));
    declare _telefono varchar(15) default json_unquote(json_extract(json, '$.telefono'));
    declare _direccion varchar(100) default json_unquote(json_extract(json, '$.direccion'));
    declare _tipo_proveedor varchar(100) default json_unquote(json_extract(json, '$.tipoProveedor'));
    declare _contacto varchar(50) default json_unquote(json_extract(json, '$.contacto'));
    declare _telefono_contacto varchar(15) default json_unquote(json_extract(json, '$.telefonoContacto'));
    declare _correo_contacto varchar(320) default json_unquote(json_extract(json, '$.correoContacto'));
    declare _usr_ing varchar(50) default json_unquote(json_extract(json, '$.usrIngreso'));
    declare _usr_mod varchar(50) default json_unquote(json_extract(json, '$.usrModificacion'));
    insert into proveedor values(_id, _descripcion, _convenio, _telefono, _direccion, _tipo_proveedor, _contacto, 
        _telefono_contacto, _correo_contacto, 1, 1, _usr_ing, now(), _usr_mod, now()) on duplicate key update 
        descripcion = _descripcion, convenio = _convenio, telefono = _telefono, direccion =_direccion, 
        tipo_proveedor = _tipo_proveedor, contacto = _contacto, telefono_contacto = _telefono_contacto, 
        correo_contacto = _correo_contacto, usr_mod = _usr_mod, fec_mod = now();
end //
delimiter ;

delimiter //
create procedure AddMarca(json JSON)
begin
    declare _id varchar(100) default json_unquote(json_extract(json, '$.id'));
    declare _descripcion varchar(50) default json_unquote(json_extract(json, '$.descripcion'));
    declare _usr_ing varchar(50) default json_unquote(json_extract(json, '$.usrIngreso'));
    declare _usr_mod varchar(50) default json_unquote(json_extract(json, '$.usrModificacion'));
    insert into marca values(_id, _descripcion, 1, 1, _usr_ing, now(), _usr_mod, now()) on duplicate key update 
        descripcion = _descripcion, usr_mod = _usr_mod, fec_mod = now();
end //
delimiter ;

delimiter //
create procedure AddCategoria(json JSON)
begin
    declare _id varchar(100) default json_unquote(json_extract(json, '$.id'));
    declare _descripcion varchar(50) default json_unquote(json_extract(json, '$.descripcion'));
    declare _usr_ing varchar(50) default json_unquote(json_extract(json, '$.usrIngreso'));
    declare _usr_mod varchar(50) default json_unquote(json_extract(json, '$.usrModificacion'));
    insert into categoria values(_id, _descripcion, 1, 1, _usr_ing, now(), _usr_mod, now()) on duplicate key update 
        descripcion = _descripcion, usr_mod = _usr_mod, fec_mod = now();
end //
delimiter ;

delimiter //
create procedure AddRepuesto(json JSON)
begin
    declare _id varchar(100) default json_unquote(json_extract(json, '$.id'));
    declare _codigo varchar(50) default json_unquote(json_extract(json, '$.codigo'));
    declare _marca_id varchar(100) default json_unquote(json_extract(json, '$.marca.id'));
    declare _categoria_id varchar(100) default json_unquote(json_extract(json, '$.categoria.id'));
    declare _modelo varchar(50) default json_unquote(json_extract(json, '$.modelo'));
    declare _fecha varchar(20) default json_unquote(json_extract(json, '$.fecha'));
    declare _stock int default json_unquote(json_extract(json, '$.stock'));
    declare _precio double default json_unquote(json_extract(json, '$.precio'));
    declare _descripcion varchar(100) default json_unquote(json_extract(json, '$.descripcion'));
    declare _usr_ing varchar(50) default json_unquote(json_extract(json, '$.usrIngreso'));
    declare _usr_mod varchar(50) default json_unquote(json_extract(json, '$.usrModificacion'));
    insert into repuesto values(_id, _codigo, _marca_id, _categoria_id, _modelo, _fecha, _stock, _precio, _descripcion, 1, 1, 
        _usr_ing, now(), _usr_mod, now()) on duplicate key update codigo = _codigo, marca_id = _marca_id, categoria_id = 
        _categoria_id, modelo = _modelo, fecha = _fecha, stock = _stock, precio = _precio, descripcion = _descripcion, usr_mod = 
        _usr_mod, fec_mod = now();
end //
delimiter ;