<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion | Kenam Services</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@500;600;700&family=Manrope:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink: #121618;
            --bone: #f5f3ef;
            --accent: #ffb703;
            --accent-strong: #f59e0b;
            --glass: rgba(255, 255, 255, 0.82);
            --shadow: 0 30px 80px rgba(18, 22, 24, 0.25);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Manrope', sans-serif;
            color: var(--ink);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: url("<?php echo e(asset('images/login-bg.jpg')); ?>") center/cover no-repeat fixed;
            position: relative;
        }

        body::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(120deg, rgba(18, 22, 24, 0.65), rgba(18, 22, 24, 0.2));
            z-index: 0;
        }

        .login-shell {
            position: relative;
            z-index: 1;
            width: min(920px, 92vw);
            display: grid;
            grid-template-columns: minmax(0, 1.1fr) minmax(0, 1fr);
            gap: 24px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 28px;
            padding: 30px;
            backdrop-filter: blur(10px);
            align-items: center;
        }

        .login-brand {
            padding: 30px;
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            text-align: center;
            align-items: center;
        }

        .brand-top {
            display: grid;
            gap: 14px;
            justify-items: center;
        }

        .brand-logo {
            width: 96px;
            height: 96px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px;
            box-shadow: 0 18px 45px rgba(0, 0, 0, 0.25);
        }

        .brand-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .login-brand h1 {
            font-family: 'Syne', sans-serif;
            font-size: clamp(28px, 3vw, 40px);
            margin: 0 0 12px;
        }

        .login-brand p {
            margin: 0;
            font-size: 15px;
            line-height: 1.6;
            opacity: 0.9;
        }

        .login-card {
            background: var(--glass);
            border-radius: 22px;
            padding: 32px;
            box-shadow: var(--shadow);
            text-align: center;
        }

        .login-card h2 {
            font-family: 'Syne', sans-serif;
            font-size: 24px;
            margin: 0 0 18px;
        }

        .field {
            display: grid;
            gap: 8px;
            margin-bottom: 16px;
            text-align: left;
        }

        .label {
            font-weight: 600;
            font-size: 14px;
        }

        .input-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #fff;
            border-radius: 14px;
            padding: 12px 14px;
            border: 1px solid #e5e7eb;
        }

        .input-wrap span {
            font-weight: 600;
            color: #6b7280;
        }

        input {
            border: none;
            outline: none;
            width: 100%;
            font-size: 16px;
            font-family: inherit;
            background: transparent;
        }

        .helper {
            font-size: 12px;
            color: #6b7280;
        }

        .actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-top: 10px;
        }

        .btn-primary {
            background: var(--accent);
            color: #111;
            border: none;
            font-weight: 700;
            padding: 12px 22px;
            border-radius: 999px;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 30px rgba(245, 158, 11, 0.35);
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
        }

        .invalid-feedback {
            color: #b91c1c;
            font-size: 12px;
        }

        @media (max-width: 860px) {
            .login-shell {
                grid-template-columns: 1fr;
            }

            .login-brand {
                display: none;
            }
        }
    </style>
</head>
<body>
    <main class="login-shell">
        <section class="login-brand">
            <div class="brand-top">
                <div class="brand-logo">
                    <img src="<?php echo e(asset('images/logo-kenam1.png')); ?>" alt="Kenam Services">
                </div>
                <div>
                    <h1>Kenam Services</h1>
                    <p>Bienvenue. Connectez-vous avec votre numéro de téléphone pour accéder à votre espace de travail.</p>
                </div>
            </div>
            <p>Besoin d'aide ? Contactez l'équipe support.</p>
        </section>

        <section class="login-card">
            <h2>Connexion par téléphone</h2>

            <?php if(session('status')): ?>
                <div class="helper"><?php echo e(session('status')); ?></div>
            <?php endif; ?>

            <?php if($errors->any()): ?>
                <div class="helper" style="color: #b91c1c; margin-bottom: 16px;">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div><?php echo e($error); ?></div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('login.submit')); ?>" id="loginForm">
                <?php echo csrf_field(); ?>

                <div class="field">
                    <span class="label">Numéro de téléphone</span>
                    <div class="input-wrap">
                        <span>+225</span>
                        <input id="phone" type="tel"
                               name="telephone"
                               value="<?php echo e(old('telephone')); ?>"
                               required
                               autofocus
                               pattern="0[0-9]{9}"
                               maxlength="10"
                               inputmode="numeric"
                               placeholder="0701234567">
                    </div>
                    <span class="helper">Saisir exactement 10 chiffres commençant par 0 (ex: 0701234567).</span>
                </div>

                <div class="field">
                    <span class="label">Mot de passe</span>
                    <div class="input-wrap">
                        <input id="password" type="password"
                               name="password"
                               required
                               autocomplete="current-password"
                               placeholder="Votre mot de passe">
                    </div>
                </div>

                <div class="actions">
                    <label class="remember">
                        <input type="checkbox" name="remember" <?php echo e(old('remember') ? 'checked' : ''); ?>>
                        Se souvenir de moi
                    </label>
                    <button type="submit" class="btn-primary">Se connecter</button>
                </div>
            </form>
        </section>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const phoneInput = document.getElementById('phone');
            const loginForm = document.getElementById('loginForm');

            loginForm.addEventListener('submit', function() {
                let phone = phoneInput.value.replace(/\D/g, '');
                // Si le numéro commence par 0, le garder tel quel (déjà 10 chiffres)
                // Sinon, ajouter 0 devant (format 9 chiffres + 0)
                if (phone.length === 9 && !phone.startsWith('0')) {
                    phone = '0' + phone;
                }
                // Si déjà 10 chiffres et commence par 0, le garder
                phoneInput.value = phone;
            });

            phoneInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                // Limiter à 10 chiffres maximum
                if (value.length > 10) {
                    value = value.slice(0, 10);
                }
                e.target.value = value;
            });
        });
    </script>
</body>
</html>
<?php /**PATH C:\laragon\www\kenam\resources\views/auth/phone-login-new.blade.php ENDPATH**/ ?>