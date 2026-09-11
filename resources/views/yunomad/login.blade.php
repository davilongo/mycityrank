<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YuNomad — Acceso equipo</title>
    <style>
        :root{
            --paper:#ECE7DC;
            --paper-raised:#F6F2E8;
            --ink:#23303D;
            --ink-soft:#5B6B78;
            --air-blue:#2C5697;
            --air-red:#B23A34;
            --line:#C7BCA3;
            --cta-bg:#9C332E;
            --cta-fg:#F7F1E4;
        }
        *{box-sizing:border-box;}
        body{
            margin:0;
            min-height:100vh;
            display:flex;align-items:center;justify-content:center;
            background:var(--paper);
            color:var(--ink);
            font-family:system-ui,-apple-system,'Segoe UI',sans-serif;
            padding:24px;
        }
        .card{
            width:100%;max-width:380px;
            background:var(--paper-raised);
            border:1.5px solid var(--line);
            border-radius:6px;
            padding:32px 28px;
            box-shadow:0 10px 26px -16px rgba(35,48,61,.25);
        }
        .wordmark{font-weight:700;font-size:1.5rem;letter-spacing:.02em;text-align:center;margin-bottom:6px;}
        .wordmark .wm-yu{color:var(--air-blue);}
        .wordmark .wm-nomad{color:var(--air-red);}
        .subtitle{text-align:center;font-size:.85rem;color:var(--ink-soft);margin:0 0 26px;}
        .field{margin-bottom:16px;}
        .field label{display:block;font-size:.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.08em;color:var(--ink-soft);margin-bottom:6px;}
        .field input{
            width:100%;font-size:1rem;padding:.7em .8em;
            border:1.5px solid var(--line);border-radius:4px;
            background:var(--paper);color:var(--ink);
        }
        .error{color:var(--air-red);font-size:.82rem;margin:-10px 0 14px;}
        .btn{
            width:100%;font-weight:600;letter-spacing:.03em;text-transform:uppercase;font-size:.9rem;
            background:var(--cta-bg);color:var(--cta-fg);
            border:none;border-radius:4px;padding:.9em 1.2em;
            cursor:pointer;
        }
        .back-link{display:block;text-align:center;margin-top:20px;font-size:.82rem;color:var(--ink-soft);text-decoration:none;border-bottom:1px solid transparent;}
        .back-link:hover{border-color:var(--ink-soft);}
    </style>
</head>
<body>
    <div class="card">
        <div class="wordmark"><span class="wm-yu">Yu</span><span class="wm-nomad">Nomad</span></div>
        <p class="subtitle">Acceso del equipo · escribir cartas</p>

        @if($errors->any())
            <p class="error">{{ $errors->first() }}</p>
        @endif

        <form method="POST" action="{{ route('yunomad.login.store') }}">
            @csrf
            <div class="field">
                <label for="email">Correo</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
            </div>
            <div class="field">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Entrar</button>
        </form>

        <a href="/yunomad/" class="back-link">&larr; Volver a la landing</a>
    </div>
</body>
</html>
