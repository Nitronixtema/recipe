#!/bin/bash
docker build -t taldykin_hellofresh .
docker run -it -v /var/www/html/hellofresh/hf.json:/var/tmp/file.json --rm taldykin_hellofresh --words="Potato,Veggie,Mushroom"
