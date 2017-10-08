<?php
/**
 * Created by PhpStorm.
 * User: Quentin Schueller
 * Date: 10/06/2017
 * Time: 12:21
 */

// Let's define some constants that will be required

//define gravitational constant 9.81 m/s²
define("GRAVITATIONAL_CONSTANT", 9.81);

// define air density constant: 1225 g/m^3
// p / rho
define("AIR_DENSITY", 1225);

if (!empty($_POST)) {
    // errors array
    $errors = array();

    // check inputs
    if (empty($_POST['mass'])) {
        $errors[] = 'Entrez une masse pour votre fusée!';
    }

    //  no errors
    if (count($errors) == 0) {
        // form variables
        $descentRate = $_POST['descentRate'];
        $mass = $_POST['mass'];
        $cd = $_POST['cd'];
        $shape = $_POST['shape'];

        $surface = calculateChuteArea($mass, $cd, $descentRate);
        $diameter = chuteDiameter($surface);

        $result = round($diameter, 2) * 100;

        // Graph building
        // Create a range of mass and calculate respective diameters.

        // Mass range
        $massArray = array();
        switch ($mass) {
            case $mass >= 50:
                $massArray = range($mass - 50, $mass + 50, 10);
                break;
            case $mass < 50:
                $massArray = range($mass - 40, $mass + 40, 5);
                break;
            default:
                $massArray = $mass;
                break;
        }

        // Diameter range
        $diameterResults = array();

        $stats = array();

        // create associative array as $mass key => diameter value
        foreach ($massArray as &$massValue) {
            $diameterResults[$massValue] = round(chuteDiameter(calculateChuteArea($massValue, $cd, $descentRate)), 2);

            // create stats array for json
            $stats[] = ["factor" => $massValue, "cumulative_eigenvalue" => (round(chuteDiameter(calculateChuteArea($massValue, $cd, $descentRate)), 2)) * 100];
        }
    }
}

function calculateChuteArea($mass, $cd, $descentRate)
{
    // Surface (S)
    // S = ( 2 * 9.81 * mass ) / (p * Cd * V²)
    $surface = (2 * GRAVITATIONAL_CONSTANT * $mass) / (AIR_DENSITY * $cd * pow($descentRate, 2));

    // return surface in m²
    return $surface;
}

function chuteDiameter($surface)
{
    // Diameter (D)
    // D = square root (4 * S / pi)
    $diameter = sqrt(4 * $surface / pi());

    return $diameter;
}

?>

<!DOCTYPE html>
<html class="uk-height-1-1">
<head>
    <meta charset="utf-8"/>
    <link rel="stylesheet" href="assets/css/uikit.min.css"/>
    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <script src="assets/js/uikit.min.js"></script>
    <title> Calculateur de descentes des parachutes</title>

    <link rel="stylesheet" type="text/css" href="d3-screeplot/dist/ScreePlot.css"/>
    <script data-require="d3@3.5.3" data-semver="3.5.3" src="//cdnjs.cloudflare.com/ajax/libs/d3/3.5.3/d3.js"></script>
    <style>
        body {
            font: 10px sans-serif;
        }

        .axis path,
        .axis line {
            fill: none;
            stroke: #000;
            shape-rendering: crispEdges;
        }

        .x.axis path {
            display: none;
        }

        .line {
            fill: none;
            stroke: steelblue;
            stroke-width: 1.5px;
        }
    </style>
</head>


<body class="uk-height-1-1">
<div class="uk-vertical-align uk-text-center">
    <div class="uk-vertical-align-middle">

        <h1> Calculateur de parachute </h1>

        <p> Ce calculateur permet de calculer le diamètre du parachute nécessaire selon la masse de la fusée ainsi que
            de
            la vitesse de descente souhaitée. </p>
        <p> Limitation: les spill hole ne sont pas supportés.</p>

        <div class="uk-panel">

            <h2> Paramètres d'entrée </h2>

            <form class="uk-form uk-form-horizontal" action="chuteCalculator.php" method="POST">
                <fieldset class="data-uk-margin">
                    <!--  Chute descent rate -->
                    <div class="uk-form-row">
                        <label class="uk-form-label  " for="descent-rate-value"> Vitesse de descente (m/s) :</label>
                        <div class="uk-form-controls">
                            <select class=" uk-form-width-large" id="descent-rate-value" name="descentRate">
                                <option value="3.5" <?php if (isset($descentRate) && $descentRate == 3.5) echo "selected" ?> >
                                    3.5 m/s
                                </option>
                                <option value="4.0" <?php if (isset($descentRate) && $descentRate == 4.0) echo "selected" ?> >
                                    4.0 m/s
                                </option>
                                <option value="4.5" <?php if (isset($descentRate) && $descentRate == 4.5) echo "selected" ?> >
                                    4.5 m/s
                                </option>
                                <option value="5.0" <?php if (isset($descentRate) && $descentRate == 5.0) echo "selected" ?>>
                                    5.0 m/s
                                </option>
                                <option value="5.5" <?php if (isset($descentRate) && $descentRate == 5.5) echo "selected" ?> >
                                    5.5 m/s
                                </option>
                                <option value="6.0" <?php if (isset($descentRate) && $descentRate == 6.0) echo "selected" ?> >
                                    6.0 m/s
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Rocket mass -->
                    <div class="uk-form-row">
                        <label class="uk-form-label" for="mass-form"> Masse de la fusée (g) :</label>
                        <div class="uk-form-controls">
                            <input class=" uk-form-width-large" type="number" id="mass-form" name="mass" step="any"
                                   placeholder="Renseignez le poids de la fusée en grammes"
                                   value="<?php if (isset($mass)) echo $mass ?>"/>
                        </div>
                        <?php
                        if (isset($errors)) {
                            foreach ($errors as $value) {
                                echo '<span class="uk-text-danger">' . $value . '</span>';
                            }
                        }
                        ?>
                    </div>

                    <!-- Chute form -->
                    <div class="uk-form-row">
                        <label class="uk-form-label" for="chute-form"> Forme de parachute :</label>
                        <div class="uk-form-controls">
                            <select class=" uk-form-width-large" id="chute-form" name="shape">
                                <option value="round"> Ronde</option>
                            </select>
                        </div>
                    </div>

                    <!-- Coefficient Drag -->
                    <div class="uk-form-row">
                        <label class="uk-form-label" for="chute-cd"> Coefficient de traînée :</label>
                        <div class="uk-form-controls">
                            <select class=" uk-form-width-large" id="chute-cd" name="cd">
                                <option value="0.75" class="uk-icon-optin-monster"> 0.75</option>
                            </select>
                        </div>
                    </div>

                    <div class="uk-form-row">
                        <button type="submit" class="uk-button"> Calculer</button>
                    </div>

                </fieldset>
            </form>
        </div>

        <div class="uk-block">
            <?php
            if (isset($result)) {
                echo ' <h2> Résultats </h2>';

                echo "Votre fusée de <strong> $mass grammes </strong> a besoin : <br/> 
                        - d'un parachute de <strong> $result cm </strong> de diamètre pour obtenir une vitesse de chute de <strong> $descentRate m/s</strong>";

                echo "<div id=\"screePlot\"></div>";
            } else {
                echo 'Entrez une masse pour votre fusée et cliquez sur Calculer.';
            }
            ?>
        </div>

        <div class="uk-block">
            <h2> Références </h2>
            <ul class="uk-list uk-list-striped">
                <li> <a href="http://www.rocketmime.com/rockets/descent.html" alt="" target="_blank">Parachute Descent Calculations </a> </li>
                <li> <a href="https://apogeerockets.com/education/downloads/Newsletter149.pdf" alt="" target="_blank">Properly Sizing Parachutes for your Rockets (PDF) </a> </li>
                <li> <a href="https://www.apogeerockets.com/education/downloads/Newsletter361.pdf" alt="" target="_blank">Selecting the Proper Size Drogue Parachutes </a> </li>
            </ul>
        </div>


    </div>
</div>

<script src="d3-screeplot/dist/libs/d3v4.js"></script>
<script src="d3-screeplot/dist/libs/jquery-3.2.1.min.js"></script>
<script src="d3-screeplot/dist/ScreePlot.js"></script>

<script>
    var screePlotData = <?php if (!empty($stats)) {
            echo json_encode($stats);
        } else {
            echo '[ 
                       {  
                          "factor":350,
                          "cumulative_eigenvalue":88
                       },
                       {  
                          "factor":360,
                          "cumulative_eigenvalue":89
                       },
                       {  
                          "factor":370,
                          "cumulative_eigenvalue":91
                       },
                       {  
                          "factor":380,
                          "cumulative_eigenvalue":92
                       },
                       {  
                          "factor":390,
                          "cumulative_eigenvalue":93
                       },
                       {  
                          "factor":400,
                          "cumulative_eigenvalue":94
                       },
                       {  
                          "factor":410,
                          "cumulative_eigenvalue":95
                       },
                       {  
                          "factor":420,
                          "cumulative_eigenvalue":97
                       },
                       {  
                          "factor":430,
                          "cumulative_eigenvalue":98
                       },
                       {  
                          "factor":440,
                          "cumulative_eigenvalue":99
                       },
                       {  
                          "factor":450,
                          "cumulative_eigenvalue":100
                       }
                    ]';
        }
        ?> ;

    var screePlotCSSOptions = {
        domElement: "#screePlot",
        width: $('#screePlot').parent().width(),
        height: 550,
        margin: {top: 20, right: 20, bottom: 20, left: 35},
        showGridlines: true,
        noOfGridlines: 10,
        showAxes: false,
        svgBackground: '#FFFFFF',
        barFill: '#3498db',
        barStroke: '#FFFFFF',
        barStrokeWidth: 0,
        selBarFill: '#2ECC71',
        selBarStroke: '#FFFFFF',
        selBarStrokeWidth: 0,
        circleFill: '#3498db',
        circleStroke: '#FFFFFF',
        circleStrokeWidth: 1,
        selCircleFill: '#2ECC71',
        selCircleStroke: '#FFFFFF',
        selCircleStrokeWidth: 1,
        lineStrokeWidth: 2,
        filterLineStrokeWidth: 2,
        nodeTextColor: "#ffff00"
    };

    var screePlotDataOptions = {
        factorSelected: <?php if (isset($mass)) {
        echo $mass;
    } else {
        echo 5;
    } ?>
    };

    var screePlot = new ScreePlot(screePlotCSSOptions);
    screePlot.initialize(); // initializes the SVG and UI elements
    screePlot.render(screePlotData, screePlotDataOptions); // Use this to render as well as update with new data and configurations.
</script>
</body>
</html>