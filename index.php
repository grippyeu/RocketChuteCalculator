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

        $result = round($diameter, 2);
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
    <!--    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js"></script>-->

    <!--    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/uikit/2.27.4/css/uikit.min.css" />-->
    <!--    <script src="https://cdnjs.cloudflare.com/ajax/libs/uikit/2.27.4/js/uikit.min.js"></script>-->
    <title> Calculateur de descentes des parachutes</title>

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

        <p> Ce calculateur permet de calculer le diamètre du parachute nécessaire selon la masse de la fusée ainsi que de
            la vitesse de descente souhaitée. </p>
        <p> Limitation: les spill hole ne sont pas supportés.</p>

        <div class="uk-panel">

            <h2> Paramètres d'entrée </h2>

            <form class="uk-form uk-form-horizontal" action="index.php" method="POST">
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

        <div class="uk-panel">
            <h2> Résultats </h2>

            <?php
            if (isset($result)) {

                echo "Votre fusée de <strong> $mass grammes </strong> a besoin : <br/> 
                        - d'un parachute de <strong> $result m </strong> de diamètre pour obtenir une vitesse de chute de <strong> $descentRate m/s</strong>";

            }
            ?>
        </div>

        <div class="">
            <!--            <svg width="960" height="500"></svg>-->
        </div>
    </div>
</div>

<!--<script src="https://d3js.org/d3.v4.min.js"></script>-->

<script>
    var myData = "date	New York	San Francisco	Austin\n\
20111001	63.4	62.7	72.2\n\
20111002	58.0	59.9	67.7\n\
20111003	53.3	59.1	69.4\n\
20111004	55.7	58.8	68.0\n\
20111005	64.2	58.7	72.4\n\
20111006	58.8	57.0	77.0\n\
20111007	57.9	56.7	82.3\n\
20111008	61.8	56.8	78.9\n\
20111009	69.3	56.7	68.8\n\
20111010	71.2	60.1	68.7\n\
20111011	68.7	61.1	70.3\n\
20111012	61.8	61.5	75.3\n\
20111013	63.0	64.3	76.6\n\
20111014	66.9	67.1	66.6\n\
20111015	61.7	64.6	68.0\n\
20111016	61.8	61.6	70.6\n\
20111017	62.8	61.1	71.1\n\
20111018	60.8	59.2	70.0\n\
20111019	62.1	58.9	61.6\n\
20111020	65.1	57.2	57.4\n\
20111021	55.6	56.4	64.3\n\
20111022	54.4	60.7	72.4\n";


    d3.tsv("data.tsv", function (data) {
        data.forEach(function (d) {
            d.mass = +d.mass;
            d.D35 = +d.D35;
            d.D40 = +d.D40;
            d.D45 = +d.D45;
            d.D50 = +d.D50;
            d.D55 = +d.D55;
            d.D60 = +d.D60;
        });

        var margin = {
                top: 20,
                right: 80,
                bottom: 30,
                left: 50
            },
            width = 900 - margin.left - margin.right,
            height = 500 - margin.top - margin.bottom;

        //var parseDate = d3.time.format("%Y%m%d").parse;

        var x = d3.time.scale()
            .range([0, width]);

        var y = d3.scale.linear()
            .range([height, 0]);

        var color = d3.scale.category10();

        var xAxis = d3.svg.axis()
            .scale(x)
            .orient("bottom");

        var yAxis = d3.svg.axis()
            .scale(y)
            .orient("left");

        var line = d3.svg.line()
            .interpolate("basis")
            .x(function (d) {
                return x(d.mass);
            })
            .y(function (d) {
                return y(d.speed);
            });

        var svg = d3.select("body").append("svg")
            .attr("width", width + margin.left + margin.right)
            .attr("height", height + margin.top + margin.bottom)
            .append("g")
            .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

        // first domain: mass
        color.domain(d3.keys(data[0]).filter(function (key) {
            return key !== "mass";
        }));

//        data.forEach(function (d) {
////        d.mass = parseDate(d.mass);
//
//        });

        var descentRates = color.domain().map(function (name) {
            return {
                name: name,
                values: data.map(function (d) {
                    return {
                        mass: d.mass,
                        speed: +d[name]
                    };
                })
            };
        });

        x.domain(d3.extent(data, function (d) {
            return d.mass;
        }));

        y.domain([
            d3.min(descentRates, function (c) {
                return d3.min(c.values, function (v) {
                    return v.speed;
                });
            }),
            d3.max(descentRates, function (c) {
                return d3.max(c.values, function (v) {
                    return v.speed;
                });
            })
        ]);

        var legend = svg.selectAll('g')
            .data(descentRates)
            .enter()
            .append('g')
            .attr('class', 'legend');

        legend.append('rect')
            .attr('x', width - 20)
            .attr('y', function (d, i) {
                return i * 20;
            })
            .attr('width', 10)
            .attr('height', 10)
            .style('fill', function (d) {
                return color(d.name);
            });

        legend.append('text')
            .attr('x', width - 8)
            .attr('y', function (d, i) {
                return (i * 20) + 9;
            })
            .text(function (d) {
                return d.name;
            });

        svg.append("g")
            .attr("class", "x axis")
            .attr("transform", "translate(0," + height + ")")
            .call(xAxis);

        svg.append("g")
            .attr("class", "y axis")
            .call(yAxis)
            .append("text")
            .attr("transform", "rotate(-90)")
            .attr("y", 6)
            .attr("dy", ".71em")
            .style("text-anchor", "end")
            .text("Mass (grammes)");

        var city = svg.selectAll(".city")
            .data(descentRates)
            .enter().append("g")
            .attr("class", "city");

        city.append("path")
            .attr("class", "line")
            .attr("d", function (d) {
                return line(d.values);
            })
            .style("stroke", function (d) {
                return color(d.name);
            });

        city.append("text")
            .datum(function (d) {
                return {
                    name: d.name,
                    value: d.values[d.values.length - 1]
                };
            })
            .attr("transform", function (d) {
                return "translate(" + x(d.value.mass) + "," + y(d.value.speed) + ")";
            })
            .attr("x", 3)
            .attr("dy", ".35em")
            .text(function (d) {
                return d.name;
            });

        var mouseG = svg.append("g")
            .attr("class", "mouse-over-effects");

        mouseG.append("path") // this is the black vertical line to follow mouse
            .attr("class", "mouse-line")
            .style("stroke", "black")
            .style("stroke-width", "1px")
            .style("opacity", "0");

        var lines = document.getElementsByClassName('line');

        var mousePerLine = mouseG.selectAll('.mouse-per-line')
            .data(descentRates)
            .enter()
            .append("g")
            .attr("class", "mouse-per-line");

        mousePerLine.append("circle")
            .attr("r", 7)
            .style("stroke", function (d) {
                return color(d.name);
            })
            .style("fill", "none")
            .style("stroke-width", "1px")
            .style("opacity", "0");

        mousePerLine.append("text")
            .attr("transform", "translate(10,3)");

        mouseG.append('svg:rect') // append a rect to catch mouse movements on canvas
            .attr('width', width) // can't catch mouse events on a g element
            .attr('height', height)
            .attr('fill', 'none')
            .attr('pointer-events', 'all')
            .on('mouseout', function () { // on mouse out hide line, circles and text
                d3.select(".mouse-line")
                    .style("opacity", "0");
                d3.selectAll(".mouse-per-line circle")
                    .style("opacity", "0");
                d3.selectAll(".mouse-per-line text")
                    .style("opacity", "0");
            })
            .on('mouseover', function () { // on mouse in show line, circles and text
                d3.select(".mouse-line")
                    .style("opacity", "1");
                d3.selectAll(".mouse-per-line circle")
                    .style("opacity", "1");
                d3.selectAll(".mouse-per-line text")
                    .style("opacity", "1");
            })
            .on('mousemove', function () { // mouse moving over canvas
                var mouse = d3.mouse(this);
                d3.select(".mouse-line")
                    .attr("d", function () {
                        var d = "M" + mouse[0] + "," + height;
                        d += " " + mouse[0] + "," + 0;
                        return d;
                    });

                d3.selectAll(".mouse-per-line")
                    .attr("transform", function (d, i) {
                        console.log(width / mouse[0])
                        var xDate = x.invert(mouse[0]),
                            bisect = d3.bisector(function (d) {
                                return d.mass;
                            }).right;
                        idx = bisect(d.values, xDate);

                        var beginning = 0,
                            end = lines[i].getTotalLength(),
                            target = null;

                        while (true) {
                            target = Math.floor((beginning + end) / 2);
                            pos = lines[i].getPointAtLength(target);
                            if ((target === end || target === beginning) && pos.x !== mouse[0]) {
                                break;
                            }
                            if (pos.x > mouse[0]) end = target;
                            else if (pos.x < mouse[0]) beginning = target;
                            else break; //position found
                        }

                        d3.select(this).select('text')
                            .text(y.invert(pos.y).toFixed(2));

                        return "translate(" + mouse[0] + "," + pos.y + ")";
                    });
            });

    });
</script>


<script>


        var svg = d3.select("svg"),
            margin = {top: 20, right: 20, bottom: 30, left: 50},
            width = +svg.attr("width") - margin.left - margin.right,
            height = +svg.attr("height") - margin.top - margin.bottom,
            g = svg.append("g").attr("transform", "translate(" + margin.left + "," + margin.top + ")");

        var parseTime = d3.timeParse("%d-%b-%y");

        var x = d3.scaleTime()
            .rangeRound([0, width]);

        var y = d3.scaleLinear()
            .rangeRound([height, 0]);

        var line = d3.line()
            .x(function(d) { return x(d.mass); })
            .y(function(d) { return y(d.diameter); });

        d3.tsv("data3_5.tsv", function(d) {
            d.mass = +d.mass;
            d.diameter = +d.diameter;
            return d;
        }, function(error, data) {
            if (error) throw error;

            x.domain(d3.extent(data, function(d) { return d.mass; }));
            y.domain(d3.extent(data, function(d) { return d.diameter; }));

            g.append("g")
                .attr("transform", "translate(0," + height + ")")
                .call(d3.axisBottom(x))
                .select(".domain")
                .remove();

            g.append("g")
                .call(d3.axisLeft(y))
                .append("text")
                .attr("fill", "#000")
                .attr("transform", "rotate(-90)")
                .attr("y", 6)
                .attr("dy", "0.71em")
                .attr("text-anchor", "end")
                .text("Poids (grammes)");

            g.append("path")
                .datum(data)
                .attr("fill", "none")
                .attr("stroke", "steelblue")
                .attr("stroke-linejoin", "round")
                .attr("stroke-linecap", "round")
                .attr("stroke-width", 1.5)
                .attr("d", line)
            .text("Vitesse de descente");
        });

</script>
</body>
</html>