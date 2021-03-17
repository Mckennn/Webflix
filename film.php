<?php
ob_start();

// On va d'abord récupèrer l'id dans l'URL
$id = $_GET['id'] ?? 0;

// On inclus la connexion à la base de données avant
// pour pouvoir afficher le titre du film dans la balise
// title (require_once pour être sûr de ne faire qu'une
// seule connexion)
require_once 'config/database.php';

/**
 * Si on veut récupérer le film et ses acteurs en 1 seule requête
 * (Attention, on doit changer le code)
 * SELECT * FROM movie
 * INNER JOIN movie_has_actor ON movie.id = movie_has_actor.movie_id
 * INNER JOIN actor ON actor.id = movie_has_actor.actor_id
 * WHERE movie.id = 4;
 */

global $db;
$query = $db->prepare('SELECT * FROM movie WHERE id = :id');
$query->bindValue(':id', $id);
$query->execute(); // Nécessaire si on prépare la requête
$movie = $query->fetch(); // On a une seule ligne de résultat

// J'affiche le titre du film dans la balise title du head
$title = $movie['title'];
require __DIR__ . '/partials/header.php';

$username = $_POST['username'] ?? '';
$message = $_POST['message'] ?? '';
$note = $_POST['note'] ?? '';

$errors = [];

if (!empty($_POST) && isset($_POST['comment'])) {
    if (strlen($username) < 2) {
        $errors['username'] = 'Le pseudo est trop court';
    }

    if (iconv_strlen($message) < 15) {
        $errors['message'] = 'Le message est trop court';
    }

    if ($note === "void") {
        $errors['note'] = 'Veuiller choisir une note';
    }

    if (empty($errors)) {
        $query = $db->prepare(
            'INSERT INTO comment (pseudo, message, note, movie_id) VALUES (:username, :message, :note, :movie_id)'
        );
        $query->bindValue(':username', $username);
        $query->bindValue(':message', $message);
        $query->bindValue(':note', $note);
        $query->bindValue(':movie_id', $movie['id']);

        $query->execute();

        header('Location: film.php?id=' . $movie['id']);
    }
}

$query = $db->prepare('SELECT * FROM comment WHERE movie_id = :movie_id');
$query->bindValue(':movie_id', $movie['id']);
$query->execute();
$commentaires = $query->fetchAll();



// Si le film n'existe pas
// if (!$movie) {
//     require 'partials/404.php';
// }
?>

<div class="container my-4">
    <div class="row">
        <div class="col-lg-5">
            <img class="img-fluid" src="uploads/movies/<?= $movie['cover']; ?>" />
        </div>
        <div class="col-lg-7">
            <div class="card shadow">
                <div class="card-body">
                    <h1><?= $movie['title']; ?></h1>
                    <p>Durée: <?= convertToHours($movie['duration']); ?></p>
                    <p>Sorti le <?= formatDate($movie['released_at']); ?></p>

                    <div>
                        <?= $movie['description']; ?>
                    </div>

                    <div class="mt-5">
                        <?php
                        $query = $db->prepare(
                            'SELECT * FROM actor
                            INNER JOIN movie_has_actor ON actor.id = movie_has_actor.actor_id
                            WHERE movie_has_actor.movie_id = :id'
                        );
                        $query->execute([':id' => $id]);
                        $actors = $query->fetchAll();
                        ?>
                        <h5>Avec :</h5>
                        <ul class="list-unstyled">
                            <?php foreach ($actors as $actor) { ?>
                                <li><a href="./acteur.php?id=<?= $actor['id']; ?>">
                                        <?= $actor['firstname'] . ' ' . $actor['name']; ?></a></li>
                            <?php } ?>
                        </ul>
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


            <div class="card shadow">
                <div class="card-body">
                    <?php foreach ($commentaires as $commentaire) { ?>
                        <h5><?= $commentaire['pseudo']; ?> : </h5>
                        <p><?= $commentaire['message']; ?></p>
                        <p><strong><?= $commentaire['note']; ?>/5</strong></p>
                        <p><?= $commentaire['created_at']; ?></p>

                        <div name="trait" style="border-bottom : 1px solid black"></div>
                    <?php } ?>

                    <form action="" method="POST">
                        <div class="row">
                            <div class="d-grid">
                                <div class="form-group">
                                    <label for="username">Pseudo</label>
                                    <input type="text" name="username" class="form-control <?= isset($errors['username']) ? 'is-invalid' : ''; ?>" value="<?= $username; ?>">

                                    <?php if (isset($errors['username'])) {
                                        echo '<span class="text-danger">' . $errors['username'] . '</span>';
                                    } ?>
                                </div>
                            </div>
                            <div class="d-grid">
                                <div class="form-group">
                                    <label for="message">Message</label>
                                    <textarea name="message" id="message" class="form-control <?= isset($errors['message']) ? 'is-invalid' : ''; ?>"><?= $message; ?></textarea>

                                    <?php if (isset($errors['message'])) {
                                        echo '<span class="text-danger">' . $errors['message'] . '</span>';
                                    } ?>
                                </div>
                            </div>
                            <div class="d-grid">
                                <div class="form-group">
                                    <label for="note">Note</label>
                                    <select class="form-select<?= isset($errors['note']) ? 'is-invalid' : ''; ?>" aria-label="Default select example" name="note" id="note" value="<?= $note; ?>">
                                        <option value="void" selected>Choisir une note</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                    </select>


                                    <?php if (isset($errors['note'])) {
                                        echo '<span class="text-danger">' . $errors['note'] . '</span>';
                                    } ?>
                                </div>
                            </div>
                            <div class="d-grid">
                                <button class="btn btn-danger" name="comment">Envoyer</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>

            <?php

            if (!empty($_POST) && isset($_POST['panier'])) {
                $_SESSION = [
                    'cart' => [
                        ['title' => $movie['title'], 'format' => $_POST['format'], 'cover' => $movie['cover'], 'quantity' => 1]
                    ]
                ];

                header('Location: cart.php');
            }
            ?>

            <div class="card shadow">
                <div class="card-body">
                    <form action="" method="POST">
                        <div class="d-grid">
                            <button class="btn btn-success" name="panier">Ajouter au panier</button>
                        </div>
                        <div class="form-group">
                            <label for="format">Format du film</label>
                            <select class="form-select" aria-label="Default select example" name="format" id="format" value="<?= $format; ?>">
                                <option value="void" selected>Choix du format</option>
                                <option value="1080p">1080p</option>
                                <option value="4k">4k</option>
                                <option value="VOD">VOD</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<?php require 'partials/footer.php'; ?>