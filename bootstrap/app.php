<?php

use App\Http\Middleware\UsuarioHabilitado;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Los proxies/túneles (Cloudflare, ngrok, Nginx, etc.) terminan el HTTPS y
        // avisan del esquema real con X-Forwarded-Proto. Sin confiar en ellos,
        // Laravel cree que la petición es http y genera los `action` de los
        // formularios con http:// dentro de una página https://, lo que hace que
        // Chrome muestre "La información que estás a punto de enviar no es segura"
        // y bloquee el envío (p. ej. al crear un usuario).
        // Si los proxies tienen IPs fijas, reemplazar '*' por el array de IPs.
        // (No se puede usar config()/env() aquí: en este punto la configuración
        // todavía no está cargada, y con la config cacheada devolvería null).
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'habilitado' => UsuarioHabilitado::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
