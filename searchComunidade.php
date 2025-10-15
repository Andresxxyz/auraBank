<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Comunidades - AuraBank</title>
    <meta name="description" content="Explore ou crie comunidades no AuraBank.">
    <meta name="keywords" content="comunidades, AuraBank, grupos, criar comunidade, buscar comunidade">

    <link href="assets/img/favicon.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Inter:wght@100;200;300;400;500;600;700;800;800;900&display=swap"
        rel="stylesheet">

    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

    <link href="assets/css/main.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #333; /* Cor principal, ajustar conforme seu tema */
            --secondary-color: #555; /* Cor secundária */
            --background-dark: #1a1a1d; /* Fundo escuro */
            --card-background: #2c2f33; /* Fundo de card */
            --border-color: #4f545c; /* Cor da borda */
        }

        body {
            background-color: var(--background-dark);
            color: var(--contrast-color);
            font-family: 'Inter', sans-serif;
        }

        .section-title h2 {
            font-weight: 700;
            margin-bottom: 10px;
            color: var(--default-color);
        }

        .section-title p {
            font-size: 1.1rem;
        }

        /* Barra de Pesquisa e Botão Criar Comunidade */
        .community-actions {
            display: flex;
            flex-wrap: wrap; /* Permite que os itens quebrem linha em telas menores */
            gap: 15px;
            justify-content: center;
            align-items: center;
            margin-bottom: 40px;
        }

        .search-bar {
            flex-grow: 1; /* Faz a barra de pesquisa ocupar o máximo de espaço */
            max-width: 600px; /* Limita a largura em telas maiores */
            display: flex;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid var(--border-color);
        }

        .search-bar input {
            flex-grow: 1;
            padding: 12px 18px;
            border: none;
            background-color: var(--card-background);
            color: var(--contrast-color);
            font-size: 1rem;
            outline: none;
        }


        .search-bar button {
            background-color: var(--accent-color);
            border: none;
            padding: 12px 20px;
            color: var(--primary-color); /* Texto escuro no botão amarelo */
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
            opacity: 0.95;
            transition: 0.3s ease;
        }

        .search-bar button:hover {
            opacity: 1;
        }
        
        .btn-create-community {
            background-color: var(--primary-color); /* Cor de fundo do botão */
            color: var(--accent-color); /* Cor do texto (aura) */
            border: 2px solid var(--accent-color); /* Borda de destaque */
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-create-community:hover {
            background-color: var(--accent-color); /* Inverte as cores no hover */
            color: var(--primary-color);
            box-shadow: 0 4px 15px rgba(255, 193, 7, 0.2);
        }

        /* Grid de Cards das Comunidades */
        .community-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); /* Colunas responsivas */
            gap: 30px;
            padding-top: 30px;
        }

        /* Estilo Individual do Card da Comunidade */
        .community-card {
            background-color: var(--card-background);
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
        }

        .community-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.4);
            cursor: pointer
        }

        .community-card-header {
            position: relative;
            height: 120px;
            background-color: #4f545c; /* Cor de fundo padrão para o header */
            overflow: hidden;
        }

        .community-card-header img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: brightness(0.7); /* Escurece um pouco a imagem */
        }
        
        .community-card-body {
            padding: 55px 20px 20px 20px; /* Padding maior em cima para compensar o avatar */
            display: flex;
            flex-direction: column;
            flex-grow: 1; /* Faz o corpo crescer para ocupar o espaço disponível */
        }

        .community-card h3 {
            font-size: 1.5rem;
            color: var(--accent-color);
            margin-bottom: 5px;
            font-weight: 700;
        }

        .community-card .description {
            font-size: 0.95rem;
            color: var(--contrast-color);
            margin-bottom: 15px;
            flex-grow: 1; /* Permite que a descrição ocupe o espaço */
        }

        .community-card .members-count {
            font-size: 0.9rem;
            color: var(--accent-color);
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            margin-top: auto; /* Empurra para o final do card */
            padding-top: 10px;
            border-top: 1px solid rgba(var(--border-color), 0.3);
            
        }
        
        .community-card .members-count i {
            font-size: 1rem;
        }
    </style>
</head>

<body class="starter-page-page">

    <?php include 'assets/php/navbar.php' ?>

    <main class="main">

        <div class="page-title dark-background" data-aos="fade">
            <div class="container position-relative">
                <h1>Comunidades</h1>
                <p>Encontre ou crie sua própria comunidade e farme aura com seus amigos.</p>
            </div>
        </div><section id="comunidades" class="comunidades section">
            <div class="container" data-aos="fade-up">

                <div class="section-title text-center pb-3">
                    <h2>Gerenciar Comunidades</h2>
                    <p>Use a barra de pesquisa para encontrar comunidades existentes ou crie uma nova para sua jornada.</p>
                </div>

                <div class="community-actions">
                    <div class="search-bar">
                        <input type="text" placeholder="Buscar por nome da comunidade..." id="searchCommunityInput">
                        <button type="button" id="searchCommunityBtn"><i class="bi bi-search"></i></button>
                    </div>
                    <a href="criarComunidade.php" class="btn-create-community">
                        <i class="bi bi-plus-circle-fill"></i> Criar Comunidade
                    </a>
                </div>

                <div class="community-grid">

                    <div class="community-card">
                        <div class="community-card-header">
                            <img src="https://picsum.photos/id/1018/400/200" alt="Banner da Comunidade">
                        </div>
                        <div class="community-card-body">
                            <h3>Aventureiros da Aura</h3>
                            <p class="description">Um grupo dedicado a explorar os mistérios da aura e compartilhar conhecimentos sobre novas formas de farmar.</p>
                            <span class="members-count">
                                <i class="bi bi-people-fill"></i> 1,234 Membros
                            </span>
                        </div>
                    </div>

                    <div class="community-card">
                        <div class="community-card-header">
                            <img src="https://picsum.photos/id/1025/400/200" alt="Banner da Comunidade">
                        </div>
                        <div class="community-card-body">
                            <h3>Colecionadores de Artefatos</h3>
                            <p class="description">Comunidade focada em descobrir e catalogar artefatos antigos que amplificam o poder da aura.</p>
                            <span class="members-count">
                                <i class="bi bi-people-fill"></i> 789 Membros
                            </span>
                        </div>
                    </div>

                    <div class="community-card">
                        <div class="community-card-header">
                            <img src="https://picsum.photos/id/10/400/200" alt="Banner da Comunidade">
                        </div>
                        <div class="community-card-body">
                            <h3>Guardiões da Essência</h3>
                            <p class="description">Protegemos os pontos de concentração de aura e auxiliamos novos membros em suas jornadas.</p>
                            <span class="members-count">
                                <i class="bi bi-people-fill"></i> 2,567 Membros
                            </span>
                        </div>
                    </div>
                    <div class="community-card">
                        <div class="community-card-header">
                            <img src="https://picsum.photos/id/1018/400/200" alt="Banner da Comunidade">
                        </div>
                        <div class="community-card-body">
                            <h3>Aventureiros da Aura</h3>
                            <p class="description">Um grupo dedicado a explorar os mistérios da aura e compartilhar conhecimentos sobre novas formas de farmar.</p>
                            <span class="members-count">
                                <i class="bi bi-people-fill"></i> 1,234 Membros
                            </span>
                        </div>
                    </div>

                    <div class="community-card">
                        <div class="community-card-header">
                            <img src="https://picsum.photos/id/1025/400/200" alt="Banner da Comunidade">
                        </div>
                        <div class="community-card-body">
                            <h3>Colecionadores de Artefatos</h3>
                            <p class="description">Comunidade focada em descobrir e catalogar artefatos antigos que amplificam o poder da aura.</p>
                            <span class="members-count">
                                <i class="bi bi-people-fill"></i> 789 Membros
                            </span>
                        </div>
                    </div>

                    <div class="community-card">
                        <div class="community-card-header">
                            <img src="https://picsum.photos/id/10/400/200" alt="Banner da Comunidade">
                        </div>
                        <div class="community-card-body">
                            <h3>Guardiões da Essência</h3>
                            <p class="description">Protegemos os pontos de concentração de aura e auxiliamos novos membros em suas jornadas.</p>
                            <span class="members-count">
                                <i class="bi bi-people-fill"></i> 2,567 Membros
                            </span>
                        </div>
                    </div>
                    <div class="community-card">
                        <div class="community-card-header">
                            <img src="https://picsum.photos/id/1018/400/200" alt="Banner da Comunidade">
                        </div>
                        <div class="community-card-body">
                            <h3>Aventureiros da Aura</h3>
                            <p class="description">Um grupo dedicado a explorar os mistérios da aura e compartilhar conhecimentos sobre novas formas de farmar.</p>
                            <span class="members-count">
                                <i class="bi bi-people-fill"></i> 1,234 Membros
                            </span>
                        </div>
                    </div>

                    <div class="community-card">
                        <div class="community-card-header">
                            <img src="https://picsum.photos/id/1025/400/200" alt="Banner da Comunidade">
                        </div>
                        <div class="community-card-body">
                            <h3>Colecionadores de Artefatos</h3>
                            <p class="description">Comunidade focada em descobrir e catalogar artefatos antigos que amplificam o poder da aura.</p>
                            <span class="members-count">
                                <i class="bi bi-people-fill"></i> 789 Membros
                            </span>
                        </div>
                    </div>

                    <div class="community-card">
                        <div class="community-card-header">
                            <img src="https://picsum.photos/id/10/400/200" alt="Banner da Comunidade">
                        </div>
                        <div class="community-card-body">
                            <h3>Guardiões da Essência</h3>
                            <p class="description">Protegemos os pontos de concentração de aura e auxiliamos novos membros em suas jornadas.</p>
                            <span class="members-count">
                                <i class="bi bi-people-fill"></i> 2,567 Membros
                            </span>
                        </div>
                    </div>
                    <div class="community-card">
                        <div class="community-card-header">
                            <img src="https://picsum.photos/id/1018/400/200" alt="Banner da Comunidade">
                        </div>
                        <div class="community-card-body">
                            <h3>Aventureiros da Aura</h3>
                            <p class="description">Um grupo dedicado a explorar os mistérios da aura e compartilhar conhecimentos sobre novas formas de farmar.</p>
                            <span class="members-count">
                                <i class="bi bi-people-fill"></i> 1,234 Membros
                            </span>
                        </div>
                    </div>

                    <div class="community-card">
                        <div class="community-card-header">
                            <img src="https://picsum.photos/id/1025/400/200" alt="Banner da Comunidade">
                        </div>
                        <div class="community-card-body">
                            <h3>Colecionadores de Artefatos</h3>
                            <p class="description">Comunidade focada em descobrir e catalogar artefatos antigos que amplificam o poder da aura.</p>
                            <span class="members-count">
                                <i class="bi bi-people-fill"></i> 789 Membros
                            </span>
                        </div>
                    </div>

                    <div class="community-card">
                        <div class="community-card-header">
                            <img src="https://picsum.photos/id/10/400/200" alt="Banner da Comunidade">
                        </div>
                        <div class="community-card-body">
                            <h3>Guardiões da Essência</h3>
                            <p class="description">Protegemos os pontos de concentração de aura e auxiliamos novos membros em suas jornadas.</p>
                            <span class="members-count">
                                <i class="bi bi-people-fill"></i> 2,567 Membros
                            </span>
                        </div>
                    </div>
                    <div class="community-card">
                        <div class="community-card-header">
                            <img src="https://picsum.photos/id/1018/400/200" alt="Banner da Comunidade">
                        </div>
                        <div class="community-card-body">
                            <h3>Aventureiros da Aura</h3>
                            <p class="description">Um grupo dedicado a explorar os mistérios da aura e compartilhar conhecimentos sobre novas formas de farmar.</p>
                            <span class="members-count">
                                <i class="bi bi-people-fill"></i> 1,234 Membros
                            </span>
                        </div>
                    </div>

                    <div class="community-card">
                        <div class="community-card-header">
                            <img src="https://picsum.photos/id/1025/400/200" alt="Banner da Comunidade">
                        </div>
                        <div class="community-card-body">
                            <h3>Colecionadores de Artefatos</h3>
                            <p class="description">Comunidade focada em descobrir e catalogar artefatos antigos que amplificam o poder da aura.</p>
                            <span class="members-count">
                                <i class="bi bi-people-fill"></i> 789 Membros
                            </span>
                        </div>
                    </div>

                    <div class="community-card">
                        <div class="community-card-header">
                            <img src="https://picsum.photos/id/10/400/200" alt="Banner da Comunidade">
                        </div>
                        <div class="community-card-body">
                            <h3>Guardiões da Essência</h3>
                            <p class="description">Protegemos os pontos de concentração de aura e auxiliamos novos membros em suas jornadas.</p>
                            <span class="members-count">
                                <i class="bi bi-people-fill"></i> 2,567 Membros
                            </span>
                        </div>
                    </div>
                    <div class="community-card">
                        <div class="community-card-header">
                            <img src="https://picsum.photos/id/1018/400/200" alt="Banner da Comunidade">
                        </div>
                        <div class="community-card-body">
                            <h3>Aventureiros da Aura</h3>
                            <p class="description">Um grupo dedicado a explorar os mistérios da aura e compartilhar conhecimentos sobre novas formas de farmar.</p>
                            <span class="members-count">
                                <i class="bi bi-people-fill"></i> 1,234 Membros
                            </span>
                        </div>
                    </div>

                    <div class="community-card">
                        <div class="community-card-header">
                            <img src="https://picsum.photos/id/1025/400/200" alt="Banner da Comunidade">
                        </div>
                        <div class="community-card-body">
                            <h3>Colecionadores de Artefatos</h3>
                            <p class="description">Comunidade focada em descobrir e catalogar artefatos antigos que amplificam o poder da aura.</p>
                            <span class="members-count">
                                <i class="bi bi-people-fill"></i> 789 Membros
                            </span>
                        </div>
                    </div>

                    <div class="community-card">
                        <div class="community-card-header">
                            <img src="https://picsum.photos/id/10/400/200" alt="Banner da Comunidade">
                        </div>
                        <div class="community-card-body">
                            <h3>Guardiões da Essência</h3>
                            <p class="description">Protegemos os pontos de concentração de aura e auxiliamos novos membros em suas jornadas.</p>
                            <span class="members-count">
                                <i class="bi bi-people-fill"></i> 2,567 Membros
                            </span>
                        </div>
                    </div>

                    </div>

            </div>
        </section>

    </main>

    <?php require("assets/php/rodape.php") ?>

    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <div id="preloader"></div>

    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/php-email-form/validate.js"></script>
    <script src="assets/vendor/aos/aos.js"></script>
    <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
    <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
    <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
    <script src="assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
    <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>

    <script src="assets/js/main.js"></script>

</body>

</html>