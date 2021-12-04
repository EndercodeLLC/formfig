<?php

namespace Formfig\Framework;

class Migrations {

    public static function run() {

        $now = time();

        $file_paths = glob($_SERVER["DOCUMENT_ROOT"].'/../migrations/*.toml');

        $migration_paths = [];

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
