<?php

use AlexaRank\AlexaRank;

require("src/AlexaRank.php");
$alexa = new AlexaRank();
$data = $alexa->getRank("https://www.pornobene.com/");


echo $data->getGlobalRank()."\n";
echo $data->getAlexaCountry()."\n";
echo $data->getAlexaCountryRank()."\n";
