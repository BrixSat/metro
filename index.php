<?php
	// load vendor autoload
	require_once __DIR__ . '/vendor/autoload.php';

	use JamesGordo\CSV\Parser;

    if (isset($_POST['type']))
    {
        if ($_POST['type'] == 1)
        {
            // Initalize the Parser
            $metro = new Parser('metro_povoa.csv', ',');
            $type1="checked";
        }
        elseif ($_POST['type'] == 2)
        {
            // Initalize the Parser
            $metro = new Parser('metro_porto.csv', ',');
            $type2="checked";
        }
    }else
    {
        if(isset($_COOKIE['type']))
        {
            if($_COOKIE['type'] == 1)
            {
                $metro = new Parser('metro_povoa.csv', ',');
                $type1 = "checked";
            }
            else
            {
                $metro = new Parser('metro_porto.csv', ',');
                $type2 = "checked";
            }
        }
        else
        {
            // Initalize the Parser
            $metro = new Parser('metro_povoa.csv', ',');
            $type1 = "checked";
        }
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' || (isset($_COOKIE['type']) && isset($_COOKIE['origin']) && isset($_COOKIE['destination'])) )
    {

        // loop through each user and echo the details
        $timeOrigin = array();
        $timeVerdes = array();
        $timeDestination = array();
        if (isset($_POST['origin']))
        {
            setcookie("origin", $_POST['origin']);
            $origin = $_POST['origin'];
        }
        else
        {
            $origin = $_COOKIE['origin'];
        }
        if (isset($_POST['destination']))
        {
            setcookie("destination", $_POST['destination']);
            $destination = $_POST['destination'];
        }
        else
        {
            $destination = $_COOKIE['destination'];
        }

        if (isset($_POST['type']))
        {
            setcookie("type", $_POST['type']);
            if($_POST['type'] == 1)
            {
                $metro = new Parser('metro_povoa.csv', ',');
                $type1 = "checked";
            }
            else
            {
                $metro = new Parser('metro_porto.csv', ',');
                $type2 = "checked";
            }
        }
        else
        {
            if($_COOKIE['type'] == 1)
            {
                $metro = new Parser('metro_povoa.csv', ',');
                $type1 = "checked";
            }
            else
            {
                $metro = new Parser('metro_porto.csv', ',');
                $type2 = "checked";
            }
        }

        $headers = $metro->getHeaders();
        if (array_search($origin, $metro->getHeaders(), true) < array_search($destination, $metro->getHeaders(), true))
        {
            foreach ($metro->all() as $schedule)
            {
                array_push($timeOrigin, $schedule->$origin);
                array_push($timeVerdes, $schedule->Verdes);
                array_push($timeDestination, $schedule->$destination);
            }

            $timestamp = strtotime(date("H:i:s"));
            $diff = null;
            $index = null;

            foreach ($timeOrigin as $key => $time)
            {
                $currDiff = abs($timestamp - strtotime($time));
                if (is_null($diff) || $currDiff < $diff)
                {
                    $index = $key;
                    $diff = $currDiff;
                }
            }
            $expresso = "";
            $expresso1 = "";
            if ($timeVerdes[$index] == "-")
            {
                $expresso = " (expresso)";
            }
            if ($timeVerdes[$index + 1] == "-")
            {
                $expresso1 = " (expresso)";
            }

            $partida = "Próximo metro a sair da estação " . str_replace("_", " ", $origin) . " com destino a " . str_replace("_", " ", $destination) . " é às: " . $timeOrigin[$index] . $expresso . " ou  " . $timeOrigin[$index + 1] . $expresso1 . ".<br>";

            $chegada = "<br/>Chegada a " . str_replace("_", " ", $destination) . " " . $timeDestination[$index] . $expresso . " ou  " . $timeDestination[$index + 1] . $expresso1 . ".";

        }else
        {
            $partida = "Seleção de estações errada, não pode selecionar um destino anterior à partida.";
        }
    }

    $cookieDestination="";
    $cookieOrigin="";
    if(isset($_COOKIE['origin']))
    {
        $cookieOrigin = $_COOKIE['origin'];
    }
    if(isset($_COOKIE['destination']))
    {
        $cookieDestination = $_COOKIE['destination'];
    }
    if(isset($_COOKIE['type']))
    {
        if($_COOKIE['type'] == 1)
        {
            $type1 = "checked";
        }
        else
        {
            $type2 = "checked";
        }
    }

    $stationsOrigin="";
    $stationsDestination="";
    // Stations
    foreach ($metro->getHeaders() as $station)
    {
        if ($cookieOrigin == $station)
        {
            $stationsOrigin .= '<option value="'. $station .'" selected>' .str_replace("_", " " ,$station) .'</option>';
        }
        else
        {
            $stationsOrigin .= '<option value="'. $station .'">' .str_replace("_", " " ,$station) .'</option>';
        }

        if ($cookieDestination == $station)
        {
            $stationsDestination .= '<option value="'. $station .'" selected>' .str_replace("_", " " ,$station) .'</option>';
        }
        else
        {
            $stationsDestination .= '<option value="'. $station .'">' .str_replace("_", " " ,$station) .'</option>';
        }
    }

?>
<!doctype html>
    <html>
    <head>
        <title>Metro!</title>
        <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css"> <!-- load bootstrap via CDN -->
        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script> <!-- load jquery via CDN -->
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <img src="MetroPRTLB.png"/>
                    <br/>
                    <br/>
                    <br/>
                </div>
            </div>
            <!-- OUR FORM -->
            <form autocomplete="off" action="index.php" method="POST" >
                <div class="row">
                    <div class="col-md-12">
                        <div id="type-group" class="form-group">
                            <label for="type">Destino:</label><br>
                            <label class="radio-inline"><input type="radio" name="type" value="1" <?php echo $type1;?> autocomplete="on">Porto -> Póvoa</label>
                            <label class="radio-inline"><input type="radio" name="type" value="2" <?php echo $type2;?> autocomplete="on">Póvoa -> Porto</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div id="origin-group" class="form-group">
                            <label for="origin">Origem:</label><br/>
                            <select name="origin" id="origin">
                                <?php echo $stationsOrigin; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div id="destination-group" class="form-group">
                            <label for="destination">Destino:</label><br/>
                            <select name="destination" id="destination">
                                <?php echo $stationsDestination; ?>
                            </select>
                        </div>
                    </div>
                    </div>
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-success">Submit <span class="fa fa-arrow-right"></span></button>
                    </div>
                </div>
            </form>

            <div class="row">
                <div class="col-md-12">
                    <div id="result"><h4>
                        <br/>
                        <br/>
                        <?php
                            echo $partida;
                        ?>
                        <br/>
                        <?php
                        echo $chegada;
                        ?>
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
