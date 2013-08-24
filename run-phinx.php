<?php
foreach(glob("../website/var/website_configs/*") as $file) {
    if(preg_match('/yml/', $file)) {
        $id = str_replace('.phinx.yml', '', basename($file));
        $command = "vendor/bin/phinx migrate -e \"production\" -c \"/var/www/website/var/website_configs/$id.phinx.yml\"";
        exec($command, $output);
        echo implode("\n", $output);
    }
}