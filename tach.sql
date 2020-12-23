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