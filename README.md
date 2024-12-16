# API REST de Usuarios

Esta API permite realizar operaciones CRUD (Crear, Leer, Actualizar y Eliminar) sobre usuarios en un sistema. La base de datos está alojada en AWS RDS y utiliza MySQL.

---

## Base de Datos

**Detalles de conexión:**

- **Host:** bdapirest.c3oaumayk5ln.us-east-1.rds.amazonaws.com
- **Base de datos:** apirestuser
- **Usuario:** admin
- **Contraseña:** bdapirest_prueba1
- **Puerto:** 3306

---

## Comando para iniciar

Para iniciar el servidor local, utiliza el siguiente comando:

```bash
php -S localhost:8000 -t public
```
---

## Token Bearer
```bash
Bearer 0922bc21f1f75019a9a4bc899844f2436ca0c05e55420a83abd7c80c8c6fe8f8
```
---

## Endpoints

### **[GET /users](http://localhost:8000/users)**

Obtiene la lista de todos los usuarios.

**Respuesta:**

```json
[
    {
        "id": 1,
        "name": "Juan Perez",
        "email": "juan.perez@example.com",
        "phone": "1234567890",
        "address": "Calle 123",
        "cc": "123456789"
    },
    {
        "id": 2,
        "name": "Ana Lopez",
        "email": "ana.lopez@example.com",
        "phone": "9876543210",
        "address": "Avenida 456",
        "cc": "987654321"
    }
]
