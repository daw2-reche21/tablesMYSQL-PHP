<?php
// function to generate ratings
function generate_ratings($rating) {
    $movie_rating = '';
    $rating=floatval($rating);
    $resto=ceil($rating)-$rating;


    if($resto>0){
        for ($i = 0; $i < $rating-1; $i++) {
            $movie_rating .= '<img src="star1.png" alt="star" width="14">';
        }
        $resto=$resto*100;
        $movie_rating.='<img src="star2.png" alt="halfstar" width="7.5" clip-path: circle('.$resto.'% at '.$resto.'% '.$resto.'%)>';
    }else{
        for ($i = 0; $i < $rating; $i++) {
            $movie_rating .= '<img src="star1.png" alt="star" width="14">';
        }
        
    }
    return $movie_rating;
}
function generate_average($movie) {
    
    global $db;

     $query = 'SELECT 
            TRUNCATE(AVG(review_rating),2) AS average 
       FROM
           reviews,movie
       WHERE
           review_movie_id = ' . $movie;
    
    $result = mysqli_query($db,$query) or die(mysqli_error($db));

    $row = mysqli_fetch_assoc($result);
    extract($row);

    return '<p>'.$average.'</p>';
}

// take in the id of a director and return his/her full name
function get_director($director_id) {

    global $db;

    $query = 'SELECT 
            people_fullname 
       FROM
           people
       WHERE
           people_id = ' . $director_id;
    $result = mysqli_query($db,$query) or die(mysqli_error($db));

    $row = mysqli_fetch_assoc($result);
    extract($row);

    return $people_fullname;
}

// take in the id of a lead actor and return his/her full name
function get_leadactor($leadactor_id) {

    global $db;

    $query = 'SELECT
            people_fullname
        FROM
            people 
        WHERE
            people_id = ' . $leadactor_id;
    $result = mysqli_query( $db,$query) or die(mysqli_error($db));

    $row = mysqli_fetch_assoc($result);
    extract($row);

    return $people_fullname;
}

// take in the id of a movie type and return the meaningful textual
// description
function get_movietype($type_id) {

    global $db;

    $query = 'SELECT 
            movietype_label
       FROM
           movietype
       WHERE
           movietype_id = ' . $type_id;
    $result = mysqli_query($db,$query) or die(mysqli_error($db));

    $row = mysqli_fetch_assoc($result);
    extract($row);

    return $movietype_label;
}

// function to calculate if a movie made a profit, loss or just broke even
function calculate_differences($takings, $cost) {

    $difference = $takings - $cost;

    if ($difference < 0) {     
        $color = 'red';
        $difference = '$' . abs($difference) . ' million';
    } elseif ($difference > 0) {
        $color ='green';
        $difference = '$' . $difference . ' million';
    } else {
        $color = 'blue';
        $difference = 'broke even';
    }

    return '<span style="color:' . $color . ';">' . $difference . '</span>';
}

//connect to MySQL
$db = mysqli_connect(gethostname(), 'root', 'root') or 
    die ('Unable to connect. Check your connection parameters.');
mysqli_select_db($db,'moviesite') or die(mysqli_error($db));



// retrieve information
$query = 'SELECT
        movie_name, movie_year, movie_director, movie_leadactor,
        movie_type, movie_running_time, movie_cost, movie_takings,movie_id
    FROM
        movie
    WHERE
        movie_id = ' . $_GET['movie_id'];
$result = mysqli_query($db,$query) or die(mysqli_error($db));

$row = mysqli_fetch_assoc($result);
$movie_name         = $row['movie_name'];
$movie_director     = get_director($row['movie_director']);
$movie_leadactor    = get_leadactor($row['movie_leadactor']);
$movie_year         = $row['movie_year'];
$movie_running_time = $row['movie_running_time'] .' mins';
$movie_takings      = $row['movie_takings'] . ' million';
$movie_cost         = $row['movie_cost'] . ' million';
$movie_health       = calculate_differences($row['movie_takings'],$row['movie_cost']);
$averagerating = generate_average($row['movie_id']);

// display the information
echo <<<ENDHTML
<html>
 <head>
  <title>Details and Reviews for: $movie_name</title>
  <style>
    .tabrev th:nth-child(odd){
    background: #000000;
    color: #FFFFFF;
    width:20px;

}
.tabrev th:nth-child(even){
    background: #FFFFFF;
    color: #000000;
    width:20px;
    
}
.tabrev td:nth-child(odd){
    background: #000000;
    color: #FFFFFF;
    width:20px;
}
.tabrev td:nth-child(even){
    background: #FFFFFF;
    color: #000000;
    width:20px;
}
.tabrev td{
 padding: 5px 10px;
text-align: center;

}

.tabrev{
    border: none;
    width: 100%;
}
  </style>
 </head>
 <body>
  <div style="text-align: center;">
   <h2>$movie_name</h2>
   <h3><em>Details</em></h3>
   <table cellpadding="2" cellspacing="2"
    style="width: 70%; margin-left: auto; margin-right: auto;">
    <tr>
     <td><strong>Title</strong></strong></td>
     <td>$movie_name</td>
     <td><strong>Release Year</strong></strong></td>
     <td>$movie_year</td>
    </tr><tr>
     <td><strong>Movie Director</strong></td>
     <td>$movie_director</td>
     <td><strong>Cost</strong></td>
     <td>$$movie_cost<td/>
    </tr><tr>
     <td><strong>Lead Actor</strong></td>
     <td>$movie_leadactor</td>
     <td><strong>Takings</strong></td>
     <td>$$movie_takings<td/>
    </tr><tr>
     <td><strong>Running Time</strong></td>
     <td>$movie_running_time</td>
     <td><strong>Health</strong></td>
     <td>$movie_health<td/>
    </tr>
    <tr>
     <td><strong>Average Rating</strong></td>
     <td>$averagerating</td>
    </tr>
   </table>
ENDHTML;
$ordenData=isset($_GET['orden']) ? $_GET['orden']:'review_date';
$descAsc='DESC';
$ordenData2=isset($_GET['orden2']) ? $_GET['orden2']: 'ASC';

if($ordenData2=='DESC'){
    $descAsc='ASC';
}else{
    $descAsc='DESC';
}
// retrieve reviews for this movie
$query = 'SELECT
        review_movie_id, review_date, reviewer_name, review_comment,
        review_rating
    FROM
        reviews
    WHERE
        review_movie_id = ' . $_GET['movie_id'] . '
    ORDER BY '
        .$ordenData.' '.$descAsc;

$pelicula=$_GET['movie_id'];
$result = mysqli_query($db,$query) or die(mysqli_error($db));

// display the reviews
echo <<< ENDHTML
   <h3><em>Reviews</em></h3>
   <table class="tabrev" cellpadding="2" cellspacing="2"
    style="width: 90%; margin-left: auto; margin-right: auto;">
    <tr>
     <th style="width: 7em;"><a href="N3P308details.php?movie_id=$pelicula&orden=review_date&orden2=$descAsc">Date</a></th>
     <th style="width: 10em;"><a href="N3P308details.php?movie_id=$pelicula&orden=reviewer_name&orden2=$descAsc">Reviewer</a></th>
     <th><a href="N3P308details.php?movie_id=$pelicula&orden=review_comment&orden2=$descAsc">Comments</a></th>
     <th style="width: 5em;"><a href="N3P308details.php?movie_id=$pelicula&orden=review_rating&orden2=$descAsc">Rating</a></th>
    </tr>
ENDHTML;

while ($row = mysqli_fetch_assoc($result)) {
    $date = $row['review_date'];
    $name = $row['reviewer_name'];
    $comment = $row['review_comment'];
    $rating = generate_ratings($row['review_rating']);
    

    echo <<<ENDHTML
    <tr>
      <td style="vertical-align:top; text-align: center;">$date</td>
      <td style="vertical-align:top;">$name</td>
      <td style="vertical-align:top;">$comment</td>
      <td style="vertical-align:top;">$rating</td>
    </tr>
ENDHTML;
}

echo <<<ENDHTML
  </div>
 </body>
</html>
ENDHTML;
?>
