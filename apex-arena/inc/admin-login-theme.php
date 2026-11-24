<?php
/**
 * APEX ARENA — Özel Koyu Yönetim Paneli Giriş Ekranı (wp-login.php)
 * Modern koyu cam/obsidyen kart, ikonlu giriş alanları, Kayıt Ol ve Web Sitesine Dön akışı.
 * Standart WordPress kimlik doğrulaması (%100 güvenli, eklentisiz) üzerinde çalışır.
 *
 * @package ApexArena
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Logo URL'sini ana sayfaya yönlendir
 */
add_filter( 'login_headerurl', function() {
    return home_url( '/' );
} );

/**
 * Logo başlık metnini APEX ARENA yap
 */
add_filter( 'login_headertext', function() {
    return 'APEX ARENA';
} );

/**
 * Tarayıcı sekme başlığını Türkçeleştir
 */
add_filter( 'login_title', function() {
    return 'Yönetim Paneli Girişi — APEX ARENA';
} );

/**
 * Koyu modern giriş ekranı stilleri (Fotoğraf referansı estetiği + APEX ARENA kimliği)
 */
add_action( 'login_enqueue_scripts', function() {
    ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style id="apex-custom-login-css">
        :root {
            --apex-login-bg: #07080a;
            --apex-login-card: #0f1116;
            --apex-login-input: #171a21;
            --apex-login-border: rgba(255, 255, 255, 0.085);
            --apex-login-border-focus: rgba(227, 24, 55, 0.72);
            --apex-login-accent: #e31837;
            --apex-login-accent-hover: #f42848;
            --apex-login-text: #f5f5f7;
            --apex-login-muted: #989ea9;
        }

        html, body.login {
            height: 100%;
            margin: 0;
            padding: 0;
            background: radial-gradient(ellipse 60% 50% at 50% 44%, rgba(227, 24, 55, 0.08) 0%, rgba(24, 32, 52, 0.14) 36%, #07080a 72%), #07080a !important;
            font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Text', 'Plus Jakarta Sans', system-ui, sans-serif !important;
            color: var(--apex-login-text);
            -webkit-font-smoothing: antialiased;
        }

        body.login {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 24px 16px;
            box-sizing: border-box;
        }

        /* Dil seçici ve gereksiz varsayılan WP kalabalığını gizle */
        .language-switcher,
        #login > .privacy-policy-page-link {
            display: none !important;
        }

        #login {
            width: 100% !important;
            max-width: 416px !important;
            padding: 36px 32px 30px !important;
            margin: 0 auto !important;
            background: var(--apex-login-card);
            border: 1px solid var(--apex-login-border);
            border-radius: 16px;
            box-shadow:
                0 30px 90px rgba(0, 0, 0, 0.72),
                0 0 0 1px rgba(255, 255, 255, 0.03),
                inset 0 1px 0 rgba(255, 255, 255, 0.09);
            box-sizing: border-box;
        }

        /* Marka Başlığı + "Yönetim Paneli Girişi" Alt Başlığı */
        .login h1 {
            margin: 0 0 26px !important;
            text-align: center;
        }

        .login h1 a {
            background-image: none !important;
            text-indent: 0 !important;
            width: auto !important;
            height: auto !important;
            margin: 0 !important;
            padding: 0 !important;
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-size: 26px !important;
            font-weight: 800 !important;
            letter-spacing: 0.08em !important;
            text-transform: uppercase;
            color: #ffffff !important;
            text-decoration: none !important;
            line-height: 1.15 !important;
            box-shadow: none !important;
        }

        .login h1 a::before {
            content: '';
            display: inline-block;
            width: 22px;
            height: 22px;
            border-radius: 6px;
            background: var(--apex-login-accent) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23ffffff'%3E%3Cpath d='M12 4 21 19H3L12 4Z'/%3E%3C/svg%3E") center / 12px 12px no-repeat;
            box-shadow: 0 4px 14px rgba(227, 24, 55, 0.45);
            flex-shrink: 0;
        }

        .login h1::after {
            content: 'Yönetim Paneli Girişi';
            display: block;
            margin-top: 10px;
            font-size: 14px;
            font-weight: 500;
            letter-spacing: 0.01em;
            color: var(--apex-login-muted);
        }

        /* Hata / Bilgilendirme kutuları */
        .login .message,
        .login .notice,
        .login .success,
        .login #login_error {
            background: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid rgba(255, 255, 255, 0.12) !important;
            border-left: 3px solid var(--apex-login-accent) !important;
            border-radius: 10px !important;
            color: #e4e7ee !important;
            box-shadow: none !important;
            padding: 11px 14px !important;
            margin: 0 0 18px !important;
            font-size: 13px !important;
            line-height: 1.5;
        }

        .login #login_error a,
        .login .message a {
            color: #ff6b81 !important;
        }

        /* İç form sıfırlama (kartın içinde bütünleşik) */
        .login form {
            margin: 0 !important;
            padding: 0 !important;
            background: transparent !important;
            border: 0 !important;
            box-shadow: none !important;
            overflow: visible !important;
        }

        .login label {
            display: block !important;
            font-size: 12.5px !important;
            font-weight: 500 !important;
            color: #c7ccd6 !important;
            margin-bottom: 7px !important;
            letter-spacing: 0.01em;
        }

        .login form .input,
        .login input[type="text"],
        .login input[type="password"],
        .login input[type="email"] {
            width: 100% !important;
            height: 44px !important;
            margin: 0 0 16px !important;
            padding: 0 14px 0 42px !important;
            background-color: var(--apex-login-input) !important;
            border: 1px solid var(--apex-login-border) !important;
            border-radius: 9px !important;
            color: #ffffff !important;
            font-size: 14px !important;
            line-height: 44px !important;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.35) !important;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
            box-sizing: border-box !important;
        }

        .login input::placeholder {
            color: #676e7c !important;
        }

        /* Kullanıcı Adı & Şifre sol ikonları (Referans Fotoğraf 2 tarzı) */
        #user_login {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='%23e31837'%3E%3Cpath d='M12 12a4.5 4.5 0 1 0-4.5-4.5A4.5 4.5 0 0 0 12 12Zm0 2.25c-4.2 0-7.75 2.15-7.75 4.8V20h15.5v-.95c0-2.65-3.55-4.8-7.75-4.8Z'/%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important;
            background-position: 14px center !important;
            background-size: 16px 16px !important;
        }

        #user_pass {
            padding-right: 44px !important;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='%23e31837'%3E%3Cpath d='M17 10h-1V7.5a4 4 0 0 0-8 0V10H7a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-7a2 2 0 0 0-2-2Zm-7-2.5a2 2 0 0 1 4 0V10h-4V7.5Z'/%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important;
            background-position: 14px center !important;
            background-size: 16px 16px !important;
        }

        .login form .input:focus,
        .login input[type="text"]:focus,
        .login input[type="password"]:focus {
            border-color: var(--apex-login-border-focus) !important;
            box-shadow: 0 0 0 3px rgba(227, 24, 55, 0.18) !important;
            outline: none !important;
            background-color: #1c2029 !important;
        }

        /* Chrome autofill beyaz arka planını koyulaştır */
        .login input:-webkit-autofill,
        .login input:-webkit-autofill:hover,
        .login input:-webkit-autofill:focus {
            -webkit-text-fill-color: #ffffff !important;
            -webkit-box-shadow: 0 0 0 1000px #171a21 inset !important;
            transition: background-color 5000s ease-in-out 0s;
        }

        /* Şifre göster/gizle göz ikonu */
        .login .wp-pwd {
            position: relative;
            margin-bottom: 16px;
        }
        .login .wp-pwd input#user_pass {
            margin-bottom: 0 !important;
        }
        .login .button.wp-hide-pw {
            position: absolute !important;
            right: 4px !important;
            top: 2px !important;
            height: 40px !important;
            width: 38px !important;
            background: transparent !important;
            border: 0 !important;
            color: #8b93a1 !important;
            box-shadow: none !important;
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
        }
        .login .button.wp-hide-pw:hover {
            color: #ffffff !important;
        }

        /* Beni Hatırla & Kayıt Ol satırı */
        .apex-login-meta-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin: 2px 0 18px;
            font-size: 12.5px;
        }

        .login .forgetmenot {
            float: none !important;
            margin: 0 0 18px !important;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 8px;
        }

        .login .forgetmenot .wp-login-remember-tooltip,
        .login .forgetmenot .dashicons-editor-help {
            display: none !important;
        }

        .login .forgetmenot label {
            display: inline-flex !important;
            align-items: center;
            gap: 6px;
            margin: 0 !important;
            font-size: 12.5px !important;
            color: var(--apex-login-muted) !important;
            cursor: pointer;
        }

        .login input[type="checkbox"] {
            background: #171a21 !important;
            border: 1px solid rgba(255, 255, 255, 0.22) !important;
            border-radius: 4px !important;
            box-shadow: none !important;
            margin: 0 !important;
            flex-shrink: 0;
        }

        .login input[type="checkbox"]:checked::before {
            filter: brightness(3);
        }

        .apex-login-register-link {
            margin-left: auto;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #ff4d67 !important;
            font-size: 12.5px;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.18s ease;
        }
        .apex-login-register-link:hover {
            color: #ff8093 !important;
        }

        /* Tam Genişlik GİRİŞ YAP Butonu */
        .login p.submit {
            margin: 0 !important;
            padding: 0 !important;
        }

        .login .button-primary {
            float: none !important;
            width: 100% !important;
            height: 44px !important;
            line-height: 44px !important;
            padding: 0 20px !important;
            background: var(--apex-login-accent) !important;
            border: 0 !important;
            border-radius: 9px !important;
            color: #ffffff !important;
            font-size: 13.5px !important;
            font-weight: 700 !important;
            letter-spacing: 0.06em !important;
            text-transform: uppercase;
            text-shadow: none !important;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.22), 0 8px 24px rgba(227, 24, 55, 0.34) !important;
            transition: background 0.2s ease, transform 0.15s ease, box-shadow 0.2s ease !important;
            cursor: pointer;
        }

        .login .button-primary:hover {
            background: var(--apex-login-accent-hover) !important;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.28), 0 12px 30px rgba(227, 24, 55, 0.45) !important;
            transform: translateY(-1px);
        }

        .login .button-primary:active {
            transform: scale(0.99);
        }

        /* Kart Alt Bağlantısı: Sadece temiz "← Web Sitesine Dön" (Fotoğraf 2 sadeliği) */
        .login #nav {
            display: none !important;
        }

        .login #backtoblog {
            margin: 18px 0 0 !important;
            padding: 14px 0 0 !important;
            border-top: 1px solid rgba(255, 255, 255, 0.07);
            text-align: center !important;
            font-size: 13px !important;
        }

        .login #nav a,
        .login #backtoblog a {
            color: #ff5b73 !important;
            text-decoration: none !important;
            font-weight: 500;
            transition: color 0.18s ease;
        }

        .login #nav a:hover,
        .login #backtoblog a:hover {
            color: #ffffff !important;
        }

        @media (max-width: 480px) {
            #login {
                padding: 28px 22px 24px !important;
            }
        }
    </style>
    <?php
} );

/**
 * Giriş formu içine Türkçe placeholder, "Kayıt Ol" kısa yolu ve "← Web Sitesine Dön" metni ekle
 */
add_action( 'login_footer', function() {
    $register_url = esc_url( home_url( '/uyelik/#kayit' ) );
    $home_url     = esc_url( home_url( '/' ) );
    ?>
    <script>
    (function () {
        var userInput = document.getElementById('user_login');
        var passInput = document.getElementById('user_pass');
        var submitBtn = document.getElementById('wp-submit');
        var rememberP = document.querySelector('.login .forgetmenot');
        var backLink  = document.querySelector('#backtoblog a');

        if (userInput) {
            userInput.setAttribute('placeholder', 'Kullanıcı Adı');
            var userLabel = document.querySelector('label[for="user_login"]');
            if (userLabel) userLabel.textContent = 'Kullanıcı Adı';
        }
        if (passInput) {
            passInput.setAttribute('placeholder', 'Şifre');
            var passLabel = document.querySelector('label[for="user_pass"]');
            if (passLabel) passLabel.textContent = 'Şifre';
        }
        if (submitBtn && submitBtn.value) {
            submitBtn.value = 'GİRİŞ YAP';
        }
        if (rememberP && !document.querySelector('.apex-login-register-link')) {
            var reg = document.createElement('a');
            reg.href = '<?php echo $register_url; ?>';
            reg.className = 'apex-login-register-link';
            reg.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg><span>Kayıt Ol</span>';
            rememberP.appendChild(reg);
        }
        if (backLink) {
            backLink.href = '<?php echo $home_url; ?>';
            backLink.textContent = '← Web Sitesine Dön';
        }
    })();
    </script>
    <?php
} );
