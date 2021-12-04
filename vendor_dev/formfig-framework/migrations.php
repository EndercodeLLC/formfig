<?php

namespace Formfig\Framework;

use DateTime;

class Migrations {

    public static function run() {

        $now = time();

        // Testing timezone. Make sure to subtract (24*60*60) from file's timezone offset.
        //$tz = timezone_open("America/Indianapolis");
        $date = new DateTime();
        $tz = $date->getTimezone();
        $dateTime = date_create("now",$tz);
        echo timezone_offset_get($tz,$dateTime) + (24*60*60);

        $file_paths = glob($_SERVER["DOCUMENT_ROOT"].'/../migrations/*.toml');

        $migration_paths = [];

        // Don't do any migrations that aren't formatted correctly... either as "first" or a proper date.

        foreach($file_paths as $file_path) {

            if( is_file($file_path) ) {

                $file_name = array_slice(explode("/", $file_path), -1)[0];

                // Only doing "first" if no full migration has been generated yet.

                if( strpos($file_name, "-first.toml") ) {

                    $table_name = str_replace("-first.toml", "", $file_name);

                    if( !is_file($_SERVER["DOCUMENT_ROOT"].'/../migrations/full/'.$table_name.'.toml') ) {

                        $migration_paths[] = $file_path;
                    }
                    continue;
                }

                // Add datetime-based migrations if their datetime is later than last migration

            }
        }

        echo '<pre>';print_r($migration_paths);echo '</pre>';
    }
}
