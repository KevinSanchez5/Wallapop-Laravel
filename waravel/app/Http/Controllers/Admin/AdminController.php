<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Realiza un respaldo de la base de datos y lo almacena en el servidor.
     */
    public function backupDatabase()
    {
        // Generar un archivo SQL con el respaldo de la base de datos
        // Guardarlo en el almacenamiento de Laravel (storage/app/backups)
    }

    /**
     * Muestra la lista de todos los clientes registrados en el sistema.
     */
    public function listClients()
    {
        // Obtener todos los usuarios con rol de cliente
        // Retornar una vista con la lista de clientes
    }

    /**
     * Muestra la lista de todos los administradores registrados.
     */
    public function listAdmins()
    {
        // Obtener todos los usuarios con rol de administrador
        // Retornar una vista con la lista de administradores
    }

    /**
     * Añadir un nuevo cliente al sistema manualmente.
     */
    public function addClient(Request $request)
    {
        // Validar los datos del formulario
        // Crear un nuevo usuario con rol de cliente
        // Enviar un correo de bienvenida
    }

    /**
     * Añadir un nuevo administrador al sistema.
     */
    public function addAdmin(Request $request)
    {
        // Validar los datos ingresados
        // Crear un nuevo usuario con rol de administrador
    }

    /**
     * Ver la lista de todos los productos en la plataforma vendidos, desactivados baneados....
     */
    public function listProducts()
    {
        // Obtener todos los productos de la base de datos
        // Retornar una vista con la lista de productos
    }

    /**
     * Banear un producto específico.
     */
    public function banProduct($id)
    {
        // Buscar el producto por ID
        // Cambiar su estado a "baneado"
    }

    /**
     * Eliminar un producto de forma permanente.
     */
    public function deleteProduct($id)
    {
        // Buscar y eliminar el producto de la base de datos
        // Eliminar imágenes asociadas si existen
    }

    /**
     * Eliminar un cliente de forma permanente.
     */
    public function deleteClient($id)
    {
        // Buscar al cliente
        // Eliminar su cuenta
    }

    /**
     * Restaurar un producto baneado (volverlo a activar).
     */
    public function restoreProduct($id)
    {
        // Buscar el producto baneado
        // Cambiar su estado a "activo"
    }

}
