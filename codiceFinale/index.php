<?php
session_start();

// --- 1. CONFIGURAZIONE E DATI INIZIALI ---

// Se il menu non esiste nella sessione, lo inizializziamo una volta sola.
// Le modifiche successive avverranno su $_SESSION['menu'] e resteranno salvate finché non chiudi il browser.
if (!isset($_SESSION['menu'])) {
    $_SESSION['menu'] = [
        'p' => [ 
            [
                'id' => 1, 'nome' => 'Pizza Margherita', 'prezzo' => 6.00, 
                'descrizione' => 'La regina delle pizze.', 
                'ingredienti' => ['Pomodoro', 'Mozzarella', 'Basilico', 'Olio EVO'], 
                'disponibile' => true, 
                'img' => 'https://images.unsplash.com/photo-1574071318508-1cdbab80d002?auto=format&fit=crop&w=500&q=60'
            ],
            [
                'id' => 2, 'nome' => 'Pizza Diavola', 'prezzo' => 8.00, 
                'descrizione' => 'Piccante e gustosa.', 
                'ingredienti' => ['Pomodoro', 'Mozzarella', 'Salame piccante'], 
                'disponibile' => true, 
                'img' => 'https://images.unsplash.com/photo-1628840042765-356cda07504e?auto=format&fit=crop&w=500&q=60'
            ],
            [
                'id' => 3, 'nome' => 'Pizza Capricciosa', 'prezzo' => 7.50, 
                'descrizione' => 'Ottima per chi ha fame.', 
                'ingredienti' => ['Pomodoro', 'Mozzarella', 'Prosciutto','Carciofini','Funghi'], 
                'disponibile' => true, 
                'img' => 'cap.png'
            ],
            [
                'id' => 4, 'nome' => 'Pizza Salsiccia e Patate', 'prezzo' => 9.00, 
                'descrizione' => 'Molto saporita.', 
                'ingredienti' => ['Salsiccie', 'Mozzarella', 'Patate'], 
                'disponibile' => true, 
                'img' => 'salPa.png'
            ]

        ],
        'f' => [ 
            [
                'id' => 5, 'nome' => 'Supplì', 'prezzo' => 3.00, 
                'descrizione' => 'Classico romano.', 
                'ingredienti' => ['Riso', 'Pomodoro', 'Carne', 'Mozzarella'], 
                'disponibile' => true, 
                'img' => 'sup.png'
            ],
            [
                'id' => 6, 'nome' => 'Patate fritte', 'prezzo' => 3.00, 
                'descrizione' => '100g', 
                'ingredienti' => ['Patate'], 
                'disponibile' => true, 
                'img' => 'pat.png'
            ]
        ],
        'b' => [ 
            [
                'id' => 7, 'nome' => 'Coca Cola', 'prezzo' => 2.50, 
                'descrizione' => 'Lattina 33cl', 
                'ingredienti' => [], 
                'disponibile' => true, 
                'img' => 'https://images.unsplash.com/photo-1622483767028-3f66f32aef97?auto=format&fit=crop&w=500&q=60'
            ],
            [
                'id' => 8, 'nome' => 'Acqua', 'prezzo' => 1.50, 
                'descrizione' => '0,5 litri', 
                'ingredienti' => [], 
                'disponibile' => true, 
                'img' => 'aq.png'
            ]
        ]
    ];
}

$tutti_ingredienti = ['Pomodoro', 'Mozzarella', 'Mozzarella di bufala', 'Olio EVO', 'Basilico', 'Salame piccante', 'Riso', 'Salsiccie', 'Patate', 'Prosciutto', 'Funghi', 'Carciofini','Wurstel'];

// --- 2. LOGICA BACKEND ---

// Login
if (isset($_POST['action']) && $_POST['action'] === 'login') {
    if ($_POST['username'] === 'admin' && $_POST['password'] === 'admin') {
        $_SESSION['is_logged_in'] = true;
    }
    header("Location: index.php");
    exit;
}

// Logout (CORRETTO: Non cancella più il menu modificato!)
if (isset($_GET['logout'])) {
    unset($_SESSION['is_logged_in']); // Rimuove solo i privilegi admin, mantiene i dati menu
    header("Location: index.php");
    exit;
}

// Modifica (Update)
if (isset($_POST['action']) && $_POST['action'] === 'update' && isset($_SESSION['is_logged_in'])) {
    $cat = $_POST['categoria'];
    $id_target = $_POST['id'];
    
    if (isset($_SESSION['menu'][$cat])) {
        foreach ($_SESSION['menu'][$cat] as &$prod) {
            if ($prod['id'] == $id_target) {
                $prod['nome'] = $_POST['nome'];
                $prod['prezzo'] = floatval($_POST['prezzo']);
                $prod['ingredienti'] = $_POST['ingredienti_selezionati'] ?? [];
                // Se il checkbox non è spuntato, 'disponibile' diventa false
                $prod['disponibile'] = isset($_POST['disponibile']); 
                break;
            }
        }
    }
    header("Location: index.php");
    exit;
}

// Aggiungi Nuovo (Create)
if (isset($_POST['action']) && $_POST['action'] === 'create' && isset($_SESSION['is_logged_in'])) {
    $cat = $_POST['categoria'];
    
    // Creiamo il nuovo prodotto
    $nuovo_prodotto = [
        'id' => time(), // ID univoco temporale
        'nome' => $_POST['nome'],
        'prezzo' => floatval($_POST['prezzo']),
        'descrizione' => $_POST['descrizione'],
        'ingredienti' => $_POST['ingredienti_selezionati'] ?? [],
        'disponibile' => true, // Di default visibile
        'img' => !empty($_POST['img']) ? $_POST['img'] : 'https://images.unsplash.com/photo-1513104890138-7c749659a591?auto=format&fit=crop&w=500&q=60'
    ];

    // Lo aggiungiamo all'array di sessione, così sarà visibile in home
    $_SESSION['menu'][$cat][] = $nuovo_prodotto;
    
    header("Location: index.php");
    exit;
}

$is_admin = isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'];
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pizzeria Tradizionale</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        :root { --primary-red: #B93736; --bg-cream: #FCEEDF; }
        body { background-color: var(--bg-cream); font-family: 'Segoe UI', sans-serif; }
        
        /* Immagini Uniformi */
        .product-img-container {
            height: 200px;
            width: 100%;
            overflow: hidden;
        }
        .product-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .navbar { background-color: var(--primary-red); }
        .hero {
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('https://images.unsplash.com/photo-1513104890138-7c749659a591?auto=format&fit=crop&w=1920&q=80');
            background-size: cover; height: 350px; display: flex; align-items: center; justify-content: center; color: white;
            position: relative;
        }
        
        .nav-pills .nav-link { color: var(--primary-red); border: 2px solid var(--primary-red); border-radius: 20px; margin: 0 5px; font-weight: bold; }
        .nav-pills .nav-link.active { background-color: var(--primary-red); color: white; }

        .login-btn { position: absolute; top: 20px; right: 20px; z-index: 1000; }
        
        /* Barra Ricerca */
        .search-container { max-width: 600px; margin: -30px auto 30px auto; position: relative; z-index: 10; }
        .search-input { border-radius: 30px; padding: 15px 25px; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

    <div class="login-btn">
        <?php if ($is_admin): ?>
            <div class="d-flex gap-2">
                <button class="btn btn-success rounded-pill shadow" data-bs-toggle="modal" data-bs-target="#createModal">
                    <i class="fas fa-plus"></i> AGGIUNGI PIATTO
                </button>
                <a href="?logout=1" class="btn btn-light btn-sm rounded-pill shadow d-flex align-items-center">Esci (Admin)</a>
            </div>
        <?php else: ?>
            <button type="button" class="btn btn-outline-light rounded-pill" data-bs-toggle="modal" data-bs-target="#loginModal">
                Area Riservata <i class="fas fa-user ms-2"></i>
            </button>
        <?php endif; ?>
    </div>

    <nav class="navbar navbar-dark">
        <div class="container justify-content-center">
            <span class="navbar-brand fw-bold fs-3"><i class="fas fa-pizza-slice me-2"></i>PIZZERIA</span>
        </div>
    </nav>

    <div class="hero">
        <h1 class="display-4 fw-bold">Il Gusto della Tradizione</h1>
    </div>

    <div class="container">
        <div class="search-container">
            <input type="text" id="searchInput" class="form-control search-input" placeholder="Cerca una pizza o un ingrediente..." onkeyup="filterMenu()">
        </div>

        <div class="text-center mb-4">
            <h2 class="fw-bold" style="color: var(--primary-red);">IL NOSTRO MENU</h2>
        </div>

        <ul class="nav nav-pills justify-content-center mb-4" id="pills-tab" role="tablist">
            <li class="nav-item"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#pills-pizza">PIZZA</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#pills-fritti">FRITTI</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#pills-bibite">BEVANDE</button></li>
        </ul>

        <div class="tab-content">
            <?php 
            $categories = [
                'p' => ['id' => 'pills-pizza', 'active' => 'active show'],
                'f' => ['id' => 'pills-fritti', 'active' => ''],
                'b' => ['id' => 'pills-bibite', 'active' => '']
            ];
            foreach ($categories as $key => $meta): 
            ?>
                <div class="tab-pane fade <?= $meta['active'] ?>" id="<?= $meta['id'] ?>">
                    <div class="row">
                        <?php 
                        if (!empty($_SESSION['menu'][$key])):
                            foreach ($_SESSION['menu'][$key] as $item): 
                                // LOGICA DI VISUALIZZAZIONE:
                                // Se l'utente NON è admin E il prodotto NON è disponibile -> NASCONDI
                                if (!$is_admin && $item['disponibile'] == false) {
                                    continue; // Salta questo giro del ciclo, non stampare nulla
                                }
                                
                                // Se è admin, mostra ma con opacità se nascosto
                                $opacity_class = (!$item['disponibile']) ? 'opacity-50 border-warning' : '';
                        ?>
                        <div class="col-lg-6 mb-4 product-card-wrapper">
                            <div class="card border-0 shadow-sm overflow-hidden h-100 <?= $opacity_class ?>">
                                <div class="row g-0 h-100">
                                    <div class="col-4 product-img-container">
                                        <img src="<?= $item['img'] ?>" class="product-img">
                                    </div>
                                    <div class="col-8">
                                        <div class="card-body d-flex flex-column h-100 justify-content-center">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <h5 class="fw-bold product-name mb-1"><?= $item['nome'] ?></h5>
                                                <span class="fs-5 fw-bold text-dark"><?= number_format($item['prezzo'], 2) ?>€</span>
                                            </div>
                                            <p class="text-muted small mb-1"><?= $item['descrizione'] ?></p>
                                            <p class="small mb-2 text-danger">
                                                <?= implode(', ', $item['ingredienti']) ?>
                                            </p>
                                            
                                            <?php if ($is_admin): ?>
                                                <div class="mt-auto pt-2 border-top">
                                                    <button class="btn btn-sm btn-outline-danger w-100 fw-bold" 
                                                            onclick='openEditModal(<?= json_encode($item) ?>, "<?= $key ?>")'>
                                                        <i class="fas fa-cog"></i> MODIFICA
                                                    </button>
                                                    <?php if(!$item['disponibile']): ?>
                                                        <div class="badge bg-warning text-dark w-100 mt-1">NASCOSTO AGLI UTENTI</div>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php 
                            endforeach; 
                        endif;
                        ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="modal fade" id="loginModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background-color: var(--primary-red); color: white;">
                <div class="modal-body p-4 text-center">
                    <h3 class="fw-bold mb-3">ACCESSO PROPRIETARIO</h3>
                    <form method="POST">
                        <input type="hidden" name="action" value="login">
                        <input type="text" name="username" class="form-control mb-2" placeholder="Username" required>
                        <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
                        <button class="btn btn-light text-danger w-100 fw-bold">ENTRA</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold">Aggiungi Nuovo Prodotto</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="create">
                        
                        <div class="mb-3">
                            <label class="fw-bold">Categoria</label>
                            <select name="categoria" class="form-select">
                                <option value="p">Pizza</option>
                                <option value="f">Antipasto/Fritto</option>
                                <option value="b">Bevanda</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="fw-bold">Nome</label>
                            <input type="text" name="nome" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold">Descrizione</label>
                            <input type="text" name="descrizione" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold">URL Immagine</label>
                            <input type="text" name="img" class="form-control" placeholder="http://...">
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold">Prezzo (€)</label>
                            <input type="number" step="0.50" name="prezzo" class="form-control" required>
                        </div>

                        <h6 class="fw-bold mt-3">Ingredienti</h6>
                        <div class="row g-2">
                            <?php foreach($tutti_ingredienti as $ing): ?>
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="ingredienti_selezionati[]" value="<?= $ing ?>">
                                    <label class="form-check-label small"><?= $ing ?></label>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <button type="submit" class="btn btn-success w-100 mt-4 fw-bold">AGGIUNGI AL MENU</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold">Modifica Prodotto</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="categoria" id="editCat">
                        <input type="hidden" name="id" id="editId">
                        
                        <div class="mb-3">
                            <label class="fw-bold">Nome</label>
                            <input type="text" name="nome" id="editNome" class="form-control">
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="fw-bold">Prezzo (€)</label>
                                <input type="number" step="0.50" name="prezzo" id="editPrezzo" class="form-control">
                            </div>
                            <div class="col-6 mb-3 pt-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="disponibile" id="editDisponibile">
                                    <label class="form-check-label fw-bold">Visibile ai clienti</label>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        <h6 class="fw-bold">Ingredienti</h6>
                        <div class="row g-2">
                            <?php foreach($tutti_ingredienti as $ing): ?>
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input ing-checkbox" type="checkbox" name="ingredienti_selezionati[]" value="<?= $ing ?>">
                                    <label class="form-check-label small"><?= $ing ?></label>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <button type="submit" class="btn btn-danger w-100 mt-4 fw-bold">SALVA MODIFICHE</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openEditModal(item, category) {
            document.getElementById('editCat').value = category;
            document.getElementById('editId').value = item.id;
            document.getElementById('editNome').value = item.nome;
            document.getElementById('editPrezzo').value = item.prezzo;
            document.getElementById('editDisponibile').checked = item.disponibile;
            
            // Reset ingredienti
            document.querySelectorAll('.ing-checkbox').forEach(cb => cb.checked = false);
            // Spunta ingredienti esistenti
            if(item.ingredienti) {
                item.ingredienti.forEach(ing => {
                    let cb = document.querySelector(`input[value="${ing}"]`);
                    if(cb) cb.checked = true;
                });
            }
            new bootstrap.Modal(document.getElementById('editModal')).show();
        }

        function filterMenu() {
            let input = document.getElementById('searchInput').value.toLowerCase();
            let cards = document.getElementsByClassName('product-card-wrapper');

            for (let i = 0; i < cards.length; i++) {
                let name = cards[i].querySelector('.product-name').innerText.toLowerCase();
                if (name.includes(input)) {
                    cards[i].style.display = "";
                } else {
                    cards[i].style.display = "none";
                }
            }
        }
    </script>
</body>
</html>