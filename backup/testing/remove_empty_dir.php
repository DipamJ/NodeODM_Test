<?php

    function removeEmptyDirs($path, $checkUpdated = false, $report = false) {
        $dirs = glob($path . "/*", GLOB_ONLYDIR);

        foreach($dirs as $dir) {
            $files = glob($dir . "/*");
            $innerDirs = glob($dir . "/*", GLOB_ONLYDIR);
            if(empty($files)) {
                if(!rmdir($dir))
                    echo "Err: " . $dir . "<br />";
               elseif($report)
                    echo $dir . " - removed!" . "<br />";
            } elseif(!empty($innerDirs)) {
                removeEmptyDirs($dir, $checkUpdated, $report);
                if($checkUpdated)
                    removeEmptyDirs($path, $checkUpdated, $report);
            }
        }

    }

removeEmptyDirs('./tt', true, false);

?>
