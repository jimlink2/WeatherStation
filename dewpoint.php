<?php
// Magnus formula constants
$a = 17.62;
$b = 243.12;

// ========================================================
// Calculator #1 variables
// Temperature + Humidity -> Dew Point
// ========================================================
$tempF1 = '';
$humidity1 = '';
$dewPointF1 = null;
$spread1 = null;
$error1 = '';

// ========================================================
// Calculator #2 variables
// Temperature + Dew Point -> Relative Humidity
// ========================================================
$tempF2 = '';
$dewPointF2 = '';
$humidity2 = null;
$spread2 = null;
$error2 = '';

// ========================================================
// Process Calculator #1
// Temperature + Humidity -> Dew Point
// ========================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['calculation'])
    && $_POST['calculation'] === 'dewpoint') {
    $tempF1 = filter_input(
        INPUT_POST,
        'tempF1',
        FILTER_VALIDATE_FLOAT
    );
    $humidity1 = filter_input(
        INPUT_POST,
        'humidity1',
        FILTER_VALIDATE_FLOAT
    );

    // Validate input
    if ($tempF1 === false || $tempF1 === null) {
        $error1 = 'Please enter a valid temperature.';
    } elseif (
        $humidity1 === false ||
        $humidity1 === null ||
        $humidity1 <= 0 ||
        $humidity1 > 100
    ) {
        $error1 =
            'Humidity must be greater than 0 and no more than 100%.';
    } else {
        // Convert Fahrenheit to Celsius
        $tempC = ($tempF1 - 32.0) * 5.0 / 9.0;

        // Calculate gamma using Magnus formula
        $gamma =
            log($humidity1 / 100.0)
            +
            (($a * $tempC) / ($b + $tempC));

        // Calculate dew point in Celsius
        $dewPointC =
            ($b * $gamma) / ($a - $gamma);

        // Convert dew point back to Fahrenheit
        $dewPointF1 =
            ($dewPointC * 9.0 / 5.0) + 32.0;

        // Dew point spread
        $spread1 =
            $tempF1 - $dewPointF1;
    }
}

// ========================================================
// Process Calculator #2
// Temperature + Dew Point -> Relative Humidity
// ========================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['calculation'])
    && $_POST['calculation'] === 'humidity') {
    $tempF2 = filter_input(
        INPUT_POST,
        'tempF2',
        FILTER_VALIDATE_FLOAT
    );
    $dewPointF2 = filter_input(
        INPUT_POST,
        'dewPointF2',
        FILTER_VALIDATE_FLOAT
    );

    // Validate input
    if ($tempF2 === false || $tempF2 === null) {
        $error2 =
            'Please enter a valid air temperature.';
    } elseif (
        $dewPointF2 === false ||
        $dewPointF2 === null
    ) {
        $error2 =
            'Please enter a valid dew point.';
    } elseif ($dewPointF2 > $tempF2) {
        $error2 =
            'Dew point cannot normally be higher than the air temperature.';
    } else {
        // Convert Fahrenheit to Celsius
        $tempC =
            ($tempF2 - 32.0) * 5.0 / 9.0;
        $dewPointC =
            ($dewPointF2 - 32.0) * 5.0 / 9.0;

        // Calculate Relative Humidity using
        // the Magnus saturation vapor pressure relationship.
        $humidity2 =
            100.0 *
            exp(
                (($a * $dewPointC) /
                ($b + $dewPointC))
                -
                (($a * $tempC) /
                ($b + $tempC))
            );

        // Dew point spread
        $spread2 =
            $tempF2 - $dewPointF2;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport"
      content="width=device-width, initial-scale=1.0">
<title>Dew Point & Humidity Calculator</title>

<style>
body {
    font-family: Arial, Helvetica, sans-serif;
    background-color: #f4f6f8;
    margin: 0;
    padding: 30px;
    color: #333;
}

h1 {
    text-align: center;
    margin-bottom: 5px;
}

.description {
    text-align: center;
    color: #666;
    margin-bottom: 30px;
}

.calculators {
    display: flex;
    gap: 30px;
    justify-content: center;
    align-items: flex-start;
    flex-wrap: wrap;
}

.calculator {
    width: 380px;
    background-color: white;
    padding: 25px;
    border-radius: 8px;
    box-shadow:
        0 2px 8px rgba(0, 0, 0, 0.15);
}

.calculator h2 {
    margin-top: 0;
    color: #222;
}

.subtitle {
    color: #666;
    margin-bottom: 20px;
}

label {
    display: block;
    margin-top: 15px;
    font-weight: bold;
}

input[type="number"] {
    width: 100%;
    box-sizing: border-box;
    padding: 9px;
    margin-top: 5px;
    font-size: 17px;
    border:
        1px solid #aaa;
    border-radius: 4px;
}

button {
    margin-top: 20px;
    padding: 11px 20px;
    font-size: 16px;
    background-color: #0078d4;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

button:hover {
    background-color: #005ea6;
}

.result {
    margin-top: 25px;
    padding: 15px;
    background-color: #e8f4ff;
    border-left:
        5px solid #0078d4;
    font-size: 18px;
    line-height: 1.6;
}

.big-result {
    font-size: 26px;
    font-weight: bold;
    color: #005ea6;
}

.error {
    margin-top: 20px;
    padding: 12px;
    background-color: #ffecec;
    border-left:
        5px solid #b00020;
    color: #b00020;
    font-weight: bold;
}

.experiment {
    max-width: 840px;
    margin:
        30px auto 0 auto;
    padding: 20px;
    background-color: #fffbe6;
    border-left:
        5px solid #e6b800;
    border-radius: 5px;
    line-height: 1.6;
}

.experiment h3 {
    margin-top: 0;
}

table {
    border-collapse: collapse;
    margin-top: 15px;
}

td,
th {
    padding:
        6px 15px 6px 0;
    text-align: right;
}

th {
    border-bottom:
        1px solid #999;
}

@media (max-width: 600px) {
    body {
        padding: 15px;
    }
    .calculator {
        width: 100%;
    }
}
</style>
</head>

<body>

<h1>Dew Point & Humidity Calculator</h1>

<div class="description">
    Experiment with the relationship between
    temperature, humidity and dew point.
</div>

<div class="calculators">

    <!-- ==================================================
         CALCULATOR #1
         Temperature + Humidity -> Dew Point
         ================================================== -->

    <div class="calculator">

        <h2>Calculate Dew Point</h2>

        <div class="subtitle">
            Enter the air temperature and
            relative humidity.
        </div>

        <form method="post">

            <input
                type="hidden"
                name="calculation"
                value="dewpoint"
            >

            <label for="tempF1">
                Air Temperature (&deg;F)
            </label>

            <input
                type="number"
                step="0.1"
                id="tempF1"
                name="tempF1"
                value="<?=
                    htmlspecialchars(
                        (string)$tempF1
                    )
                ?>"
                required
            >

            <label for="humidity1">
                Relative Humidity (%)
            </label>

            <input
                type="number"
                step="0.1"
                min="0.1"
                max="100"
                id="humidity1"
                name="humidity1"
                value="<?=
                    htmlspecialchars(
                        (string)$humidity1
                    )
                ?>"
                required
            >

            <button type="submit">
                Calculate Dew Point
            </button>

        </form>

        <?php if ($dewPointF1 !== null): ?>

            <div class="result">

                Dew Point
                <br>

                <span class="big-result">
                    <?=
                        number_format(
                            $dewPointF1,
                            1
                        )
                    ?> &deg;F
                </span>

                <br><br>

                Air Temperature:
                <strong>
                    <?=
                        number_format(
                            $tempF1,
                            1
                        )
                    ?> &deg;F
                </strong>

                <br>

                Relative Humidity:
                <strong>
                    <?=
                        number_format(
                            $humidity1,
                            1
                        )
                    ?>%
                </strong>

                <br>

                Dew Point Spread:
                <strong>
                    <?=
                        number_format(
                            $spread1,
                            1
                        )
                    ?> &deg;F
                </strong>

            </div>

        <?php endif; ?>

        <?php if ($error1): ?>

            <div class="error">
                <?=
                    htmlspecialchars(
                        $error1
                    )
                ?>
            </div>

        <?php endif; ?>

    </div>

    <!-- ==================================================
         CALCULATOR #2
         Temperature + Dew Point -> Relative Humidity
         ================================================== -->

    <div class="calculator">

        <h2>Calculate Humidity</h2>

        <div class="subtitle">
            Enter the air temperature
            and dew point.
        </div>

        <form method="post">

            <input
                type="hidden"
                name="calculation"
                value="humidity"
            >

            <label for="tempF2">
                Air Temperature (&deg;F)
            </label>

            <input
                type="number"
                step="0.1"
                id="tempF2"
                name="tempF2"
                value="<?=
                    htmlspecialchars(
                        (string)$tempF2
                    )
                ?>"
                required
            >

            <label for="dewPointF2">
                Dew Point (&deg;F)
            </label>

            <input
                type="number"
                step="0.1"
                id="dewPointF2"
                name="dewPointF2"
                value="<?=
                    htmlspecialchars(
                        (string)$dewPointF2
                    )
                ?>"
                required
            >

            <button type="submit">
                Calculate Humidity
            </button>

        </form>

        <?php if ($humidity2 !== null): ?>

            <div class="result">

                Relative Humidity
                <br>

                <span class="big-result">
                    <?=
                        number_format(
                            $humidity2,
                            1
                        )
                    ?>%
                </span>

                <br><br>

                Air Temperature:
                <strong>
                    <?=
                        number_format(
                            $tempF2,
                            1
                        )
                    ?> &deg;F
                </strong>

                <br>

                Dew Point:
                <strong>
                    <?=
                        number_format(
                            $dewPointF2,
                            1
                        )
                    ?> &deg;F
                </strong>

                <br>

                Dew Point Spread:
                <strong>
                    <?=
                        number_format(
                            $spread2,
                            1
                        )
                    ?> &deg;F
                </strong>

            </div>

        <?php endif; ?>

        <?php if ($error2): ?>

            <div class="error">
                <?=
                    htmlspecialchars(
                        $error2
                    )
                ?>
            </div>

        <?php endif; ?>

    </div>

</div>

<!-- ======================================================
     Experiment section
     ====================================================== -->

<div class="experiment">

    <h3>Try This Experiment</h3>

    <p>
        Use the <strong>Calculate Humidity</strong>
        calculator.
    </p>

    <p>
        Keep the dew point fixed at:
        <strong>60&deg;F</strong>
    </p>

    <p>
        Then change only the air temperature:
    </p>

    <table>
        <tr>
            <th>
                Temperature
            </th>
            <th>
                Dew Point
            </th>
        </tr>

        <tr>
            <td>80&deg;F</td>
            <td>60&deg;F</td>
        </tr>
        <tr>
            <td>75&deg;F</td>
            <td>60&deg;F</td>
        </tr>
        <tr>
            <td>70&deg;F</td>
            <td>60&deg;F</td>
        </tr>
        <tr>
            <td>65&deg;F</td>
            <td>60&deg;F</td>
        </tr>
        <tr>
            <td>62&deg;F</td>
            <td>60&deg;F</td>
        </tr>
        <tr>
            <td>61&deg;F</td>
            <td>60&deg;F</td>
        </tr>
        <tr>
            <td>60&deg;F</td>
            <td>60&deg;F</td>
        </tr>
    </table>

    <p>
        Watch the relative humidity increase
        as the air temperature approaches
        the dew point.
    </p>

    <p>
        When the air temperature equals
        the dew point, the calculated
        relative humidity reaches
        <strong>100%</strong>.
    </p>

</div>

</body>
</html>
