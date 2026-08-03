<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bienvenido a XploreFree</title>
</head>
<body style="margin:0;padding:0;background:#f3f4f6;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    <div style="max-width:520px;margin:0 auto;padding:40px 16px;">
        <div style="background:#ffffff;border-radius:16px;padding:36px 32px;box-shadow:0 1px 3px rgba(0,0,0,0.08);">
            <h1 style="font-size:22px;font-weight:800;color:#111827;margin:0 0 16px;">✈️ ¡Bienvenido a XploreFree!</h1>

            <p style="font-size:15px;line-height:1.6;color:#374151;margin:0 0 16px;">
                Hola {{ $user->name }},
            </p>

            <p style="font-size:15px;line-height:1.6;color:#374151;margin:0 0 16px;">
                Tu cuenta de <strong>agencia</strong> en XploreFree ya está activa. Ya puedes iniciar sesión con tu email
                (<strong>{{ $user->email }}</strong>) y empezar a publicar tus viajes organizados para que los vean miles de viajeros.
            </p>

            <p style="text-align:center;margin:28px 0;">
                <a href="{{ route('login') }}"
                   style="display:inline-block;background:#0ea5e9;color:#ffffff;text-decoration:none;font-weight:700;font-size:14px;padding:12px 28px;border-radius:8px;">
                    Iniciar sesión
                </a>
            </p>

            <p style="font-size:13px;line-height:1.6;color:#6b7280;margin:0;">
                Si no recuerdas tu contraseña, usa la opción "¿Olvidaste tu contraseña?" en la pantalla de acceso para crear una nueva.
            </p>
        </div>

        <p style="text-align:center;font-size:12px;color:#9ca3af;margin-top:24px;">
            XploreFree — Descubre el mundo
        </p>
    </div>
</body>
</html>
