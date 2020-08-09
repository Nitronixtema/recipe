<?php

use App\Conditions\{MostDeliveredPostcode, UniqueRecipes, RecipesContainWords, RecipesByPostcodeAndTime};

require __DIR__  . '/vendor/autoload.php';

ini_set('memory_limit', '1G');

$t = microtime(1);

$fixtures = '/var/tmp/file.json';

if (!file_exists($fixtures)) {
    throw new Exception('No fixtures file');
}

$getopt = new \GetOpt\GetOpt([
    \GetOpt\Option::create(null, 'interval', \GetOpt\GetOpt::OPTIONAL_ARGUMENT)
        ->setDescription('Time interval')
        ->setValidation(function ($interval) {
            $parts = explode(':', $interval);
            if (count($parts) !== 2) {
                return false;
            }

            foreach ($parts as $part) {
                $hour = (int)$part;
                $meridiem = substr($part, -2);

                if ($hour <= 0 || $hour > 12
                    || !in_array($meridiem, ['AM', 'PM'])
                    || $hour . $meridiem !== $part
                ) {
                    return false;
                }
            }

            return true;
        })
        ->setDefaultValue('10AM:3PM'),
    \GetOpt\Option::create(null, 'postcode', \GetOpt\GetOpt::OPTIONAL_ARGUMENT)
        ->setDescription('Postcode')
        ->setValidation('is_numeric')
        ->setDefaultValue(10120),
    \GetOpt\Option::create(null, 'words', \GetOpt\GetOpt::OPTIONAL_ARGUMENT)
        ->setDescription('Recipe words')
        ->setValidation('strlen')
        ->setDefaultValue('Potato,Veggie,Mushroom'),
]);

$getopt->process();

$interval = explode(':', $getopt->getOption('interval'));

$processor = new \App\Processor();
$processor->addCondition(new MostDeliveredPostcode());
$processor->addCondition(new UniqueRecipes());
$processor->addCondition(new RecipesContainWords(
    explode(',', $getopt->getOption('words'))
));
$processor->addCondition(new RecipesByPostcodeAndTime(
    $getopt->getOption('postcode'), $interval[0], $interval[1]
));


$reader = new \App\FileJsonReader($fixtures);

while (!$reader->isOver()) {
    if ($item = $reader->getItem()) {
        $processor->processData($item);
    }
}

echo json_encode($processor->getResult());

fwrite(STDERR, "\n" . (microtime(1) - $t) . "\n");
