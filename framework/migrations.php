<?php

namespace Formfig\Framework;

use DateTime;
use DateTimeZone;
use Yosymfony\Toml\Toml;

class Migrations {

    public static function make($table_to_make):string {

        //TASK: Make sure this checks for any migrations with that table name existing. If there are none, just use "first" instead of datetime in filename.

        $file_paths = glob(__DIR__.'/../migrations/*.toml');

        $table_names = [];

        foreach($file_paths as $file_path) {

            $table_name = "";

            if( !is_file($file_path) ) { continue; } // Is not a valid file page, so skip

            $file_name = array_slice(explode("/", $file_path), -1)[0];

            if( strpos($file_name, "-first.toml") ) {
                $table_name = str_replace("-first.toml", "", $file_name);
            } else {

                $parts = explode("-", $file_name);
                $l = count($parts) - 1; // Last index

                if( count($parts) < 8) { continue; } // Not a valid date-based migration

                $table_name = str_replace("-".$parts[$l-6]."-".$parts[$l-5]."-".$parts[$l-4]."-".$parts[$l-3]."-".$parts[$l-2]."-".$parts[$l-1]."-".$parts[$l], "", $file_name);
            }

            $table_names[] = $table_name;
        }

        if(in_array($table_to_make, $table_names)) {

            $date = new DateTime();
            $tz = $date->getTimezone();
            $dateTime = date_create("now",$tz);
            $tz_offset =  timezone_offset_get($tz,$dateTime) + (24*60*60);

            $file_name = $table_to_make.$dateTime->format("-Y-m-d-H-i-s-").$tz_offset.".toml";

        } else {
            $file_name = $table_to_make."-first.toml";
        }

        // Create file with only an id field

        $content =

"[id]
shortcut = \"id\"
";

        $file_loc = __DIR__."/../migrations/".$file_name;

        if( file_put_contents($file_loc, $content) === false ) {
            return "Error writing migration file.";
        }

        return "Successfully wrote migration file.";
    }

    public static function run():string {

        $now = time();

        $file_paths = glob(__DIR__.'/../migrations/*.toml');

        $migration_paths = [];

        // Don't do any migrations that aren't formatted correctly... either as "first" or a proper date.

        foreach($file_paths as $file_path) {

            $table_name = "";
            $ts = 0;

            if( !is_file($file_path) ) { continue; } // Is not a valid file page, so skip

            $file_name = array_slice(explode("/", $file_path), -1)[0];

            // Only doing "first" if no full migration has been generated yet.

            if( strpos($file_name, "-first.toml") ) {

                $table_name = str_replace("-first.toml", "", $file_name);

                if( is_file(__DIR__.'/../local-data/migrations.toml') ) {

                    // Read from local-data/migrations.toml to see if there's an entry for this table_name
                    $md = Toml::ParseFile(__DIR__.'/../local-data/migrations.toml');

                    if( isset($md[$table_name]) && !empty($md[$table_name]) ) {
                        continue; // Migration was run at least once on this table, so don't add 'first'
                    }
                }

            } else {

                $parts = explode("-", $file_name);

                if( count($parts) < 8) { continue; } // Not a valid date-based migration

                // Get timestamp from migration datetime string
                $l = count($parts) - 1; // Last index
                $date_str = $parts[$l-6]."-".$parts[$l-5]."-".$parts[$l-4];
                $time_str = $parts[$l-3].":".$parts[$l-2].":".$parts[$l-1];
                $tz_with_day = str_replace(".toml", "", $parts[$l]);
                $tz_offset = (int)$tz_with_day - (24*60*60);
                $dt = new DateTime($date_str." ".$time_str, new DateTimeZone('GMT'));
                $ts = (int)$dt->getTimestamp() + $tz_offset;

                $table_name = str_replace("-".$parts[$l-6]."-".$parts[$l-5]."-".$parts[$l-4]."-".$parts[$l-3]."-".$parts[$l-2]."-".$parts[$l-1]."-".$parts[$l], "", $file_name);

                // Read from local-data/migrations.toml to see if there's an entry for this table_name
                $md = Toml::ParseFile(__DIR__.'/../local-data/migrations.toml');
                $last_migration_ts = 0;
                if( isset($md[$table_name]) && !empty($md[$table_name]) ) {
                    $last_migration_ts = $md[$table_name];
                }

                if( $last_migration_ts >= $ts ) { continue; } // Migration already happened so skip.
            }

            $migration_paths[$table_name][] = [
                "file_path" => $file_path,
                "table_name" => $table_name,
                "timestamp" => $ts,
            ];
        }

        //TASK: Now that we know what migrations we should run, sort them by timestamp, then run them in order from oldest to newest.

        return print_r($migration_paths, true);
    }
}
