<?php
use Illuminate\Support\Facades\Broadcast;
use App\Models\User;


//Rutas para las notificaciones privadas a usuarios

Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
