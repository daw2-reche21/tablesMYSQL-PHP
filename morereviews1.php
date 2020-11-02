<?php
$db = mysqli_connect(gethostname(), 'root', 'root') or 
    die ('Unable to connect. Check your connection parameters.');
mysqli_select_db( $db,'moviesite') or die(mysqli_error($db));


$query = <<<ENDSQL
INSERT INTO reviews
    (review_movie_id, review_date, reviewer_name, review_comment,
        review_rating)
VALUES 
    (4, "2020-10-23", "Joe Stiwe", "The best film I'v never watched, Pocahontas 4 ever.", 5.3),
    (5, "2019-06-23", "Casimira Murcianica", "Me pensaba que era una del Mario bro ese, pero resultó ser otro tipo de fontanero jeje. Le pongo un dos porque no era lo que esperaba.", 2.6),
    (6, "1995-11-22", "Eddy Murphy", "I don't know how many time I've whatched thisi fils, the leadeactor is brilliant, funny and sexy.", 1.5)
ENDSQL;
mysqli_query( $db,$query) or die(mysqli_error($db));

echo 'Datos de reviewers actualizados';
?>