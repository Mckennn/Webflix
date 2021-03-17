<?php
// On va inclure le header (le doctype et le menu) sur chaque page
// $title = 'Mon super site';
require 'partials/header.php';

global $db;
$cover = $db->query('SELECT * FROM movie ORDER BY RAND() LIMIT 9')->fetchAll();
$movies = $db->query('SELECT * FROM movie ORDER BY RAND() LIMIT 4')->fetchAll();

?>

<!-- Ici, entre les 2 require, on peut intégrer notre page HTML -->

    <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="d-flex flex-row">
                    <img src="uploads/movies/<?= $cover[0]['cover']; ?>" class="d-block w-30" alt="..." style="width: 33%; height: 400px; object-fit: cover;">
                    <img src="uploads/movies/<?= $cover[1]['cover']; ?>" class="d-block w-30" alt="..." style="width: 33%; height: 400px; object-fit: cover;">
                    <img src="uploads/movies/<?= $cover[2]['cover']; ?>" class="d-block w-30" alt="..." style="width: 33%; height: 400px; object-fit: cover;">
                </div>
            </div>
            <div class="carousel-item">
                <div class="d-flex flex-row">
                    <img src="uploads/movies/<?= $cover[3]['cover']; ?>" class="d-block w-30" alt="..." style="width: 33%; height: 400px; object-fit: cover;">
                    <img src="uploads/movies/<?= $cover[4]['cover']; ?>" class="d-block w-30" alt="..." style="width: 33%; height: 400px; object-fit: cover;">
                    <img src="uploads/movies/<?= $cover[5]['cover']; ?>" class="d-block w-30" alt="..." style="width: 33%; height: 400px; object-fit: cover;">
                </div>
            </div>
            <div class="carousel-item">
                <div class="d-flex flex-row">
                    <img src="uploads/movies/<?= $cover[6]['cover']; ?>" class="d-block w-30" alt="..." style="width: 33%; height: 400px; object-fit: cover;">
                    <img src="uploads/movies/<?= $cover[7]['cover']; ?>" class="d-block w-30" alt="..." style="width: 33%; height: 400px; object-fit: cover;">
                    <img src="uploads/movies/<?= $cover[8]['cover']; ?>" class="d-block w-30" alt="..." style="width: 33%; height: 400px; object-fit: cover;">
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <div class="container">
    <h2>Sélection de films aléatoire</h2>

    <div class="row">
        <?php foreach ($movies as $movie) { ?>
            <div class="col-3">
                <div class="card shadow mb-4">
                    <img class="card-img-top" src="uploads/movies/<?= $movie['cover']; ?>" />
                    <div class="card-body">
                        <h2 class="card-title"><?= $movie['title']; ?></h2>
                        <p class="card-text">
                            Sorti en <?= substr($movie['released_at'], 0, 4); ?>
                        </p>
                        <p class="card-text">
                            <?= $movie['description']; ?>
                        </p>

                        <div class="d-grid">
                            <a href="./film.php?id=<?= $movie['id']; ?>" class="btn btn-danger">Voir le film</a>
                        </div>
                    </div>

                    <div class="card-footer text-muted">
                        <?php
                        // Représente la note du film
                        $note = rand(0, 5);
                        for ($i = 0; $i < 5; $i++) {
                            if ($i < $note) {
                                echo '★';
                            } else {
                                echo '☆';
                            }
                        } ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>


    <?php require 'partials/footer.php'; ?>