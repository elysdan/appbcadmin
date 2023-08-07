
<!doctype html>
<html>
<head>
    <!-- REQUIRED META TAGS -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Procash | Inicio</title>
    <meta name="description" content="Procash | Inicio">
    <link rel="shortcut icon" href="images/general/icon/logo.png" type="image/x-icon">

    <!-- BOOTSTRAP CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-wEmeIV1mKuiNpC+IOBjI7aAzPcEZeedi5yW5f2yOq55WWLwNGmvvx4Um1vskeMj0" crossorigin="anonymous">
    
    <!-- CUSTOM CSS -->
    <link rel="stylesheet" href="css/general.css">
    <link rel="stylesheet" href="css/index.css">
</head>
<body>

     <!-- START AND NAV MOBILE/DESKTOP -->
    <div class="section-start-nav">

         <!-- NAV MOBILE -->
        <nav id="nav-mobile" class="navbar navbar-expand-lg navbar-light backg-light-gray-3">
            <div class="container-fluid">
                <div>
                    <img src="{{asset(activeTemplate(true))}}/new_userimages/general/navbar/logo.png" class="mob-nav-logo" alt="logo" loading="lazy">
                </div>
                <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" href="index.html"><strong>Inicio</strong></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="about.html">Sobre nosotros</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="products.html">Productos Financieros</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="contact.html">Contáctanos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-mob-login backg-light-blue rounded-pill textf-white fontw-700" href="#">Iniciar sesión</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

         <!-- NAV DESKTOP -->
        <nav id="nav-desktop" class="navbar navbar-expand-lg navbar-light bg-transparent">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" href="index.html"><strong>Inicio</strong></a>
                        </li>
                        <span class="textf-white nav-desk-separator">|</span>
                        <li class="nav-item">
                            <a class="nav-link" href="about.html">Sobre nosotros</a>
                        </li>
                        <span class="textf-white nav-desk-separator">|</span>
                        <li class="nav-item">
                            <a class="nav-link" href="products.html">Productos Financieros</a>
                        </li>
                        <span class="textf-white nav-desk-separator">|</span>
                        <li class="nav-item">
                            <a class="nav-link" href="contact.html">Contáctanos</a>
                        </li>
                        <span class="textf-white nav-desk-separator">|</span>
                        <li class="nav-item">
                            <a class="nav-link nav-desk-login backg-light-blue rounded-pill fontw-700" href="#">Iniciar sesión</a>
                        </li>
                        <a href="#">
                            <li class="nav-item country-container">
                                <img class="country-image" src="https://www.countryflags.io/us/flat/64.png" alt="US" loading="lazy">
                                <span class="country-desc textf-white">US</span>
                            </li>
                        </a>
                        <span class="textf-white nav-desk-separator">|</span>
                        <a href="#">
                            <li class="nav-item country-container">
                                <img class="country-image" src="https://www.countryflags.io/es/flat/64.png" alt="ES" loading="lazy">
                                <span class="country-desc textf-white">ES</span>
                            </li>
                        </a>
                        <span class="textf-white nav-desk-separator">|</span>
                        <a href="#">
                            <li class="nav-item country-container">
                                <img class="country-image" src="https://www.countryflags.io/fr/flat/64.png" alt="FR" loading="lazy">
                                <span class="country-desc textf-white">FR</span>
                            </li>
                        </a>
                        <span class="textf-white nav-desk-separator">|</span>
                        <a href="#">
                            <li class="nav-item country-container">
                                <img class="country-image" src="https://www.countryflags.io/cn/flat/64.png" alt="CN" loading="lazy">
                                <span class="country-desc textf-white">CN</span>
                            </li>
                        </a>
                        <span class="textf-white nav-desk-separator">|</span>
                        <a href="#">
                            <li class="nav-item country-container">
                                <img class="country-image" src="https://www.countryflags.io/pt/flat/64.png" alt="PT" loading="lazy">
                                <span class="country-desc textf-white">PT</span>
                            </li>
                        </a>
                    </ul>
                </div>
            </div>
        </nav>

        <img class="section-start-nav-image" src="{{asset(activeTemplate(true))}}/new_userimages/index/welcome.jpg" alt="welcome" loading="lazy">

    </div>

@yield('content')





     
