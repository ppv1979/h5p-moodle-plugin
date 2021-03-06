<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Defines backup_hvp_activity_task class
 *
 * @package     mod_hvp
 * @category    backup
 * @copyright   2016 Joubel AS <contact@joubel.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/hvp/backup/moodle2/backup_hvp_stepslib.php');

/**
 * Provides the steps to perform one complete backup of a H5P instance
 */
class backup_hvp_activity_task extends backup_activity_task {

    /**
     * No specific settings for this activity
     */
    protected function define_my_settings() {
    }

    /**
     * Defines a backup step to store the instance data in the hvp.xml file
     */
    protected function define_my_steps() {
        $this->add_step(new backup_hvp_activity_structure_step('hvp_structure', 'hvp.xml'));

        // Ideally this step would only run once per backup, unfortunately, the
        // nature of the backup system does not allow for activities to have
        // shared resources.
        $this->add_step(new backup_hvp_libraries_structure_step('hvp_libraries', 'hvp_libraries.xml'));

        // One suggestion for increasing performance would be to only add the
        // libraries to one activity, but then that would have to be restored
        // before the other activities.

        // Another suggestion is to reduce the is to reduce the size of the XML
        // by reading the data from JSON again after restoring.
    }

    /**
     * Encodes URLs to the index.php and view.php scripts
     *
     * @param string $content some HTML text that eventually contains URLs to the activity instance scripts
     * @return string the content with the URLs encoded
     */
    static public function encode_content_links($content) {
        global $CFG;

        $base = preg_quote($CFG->wwwroot,"/");

        // Link to the list of glossaries
        $search="/(".$base."\/mod\/hvp\/index.php\?id\=)([0-9]+)/";
        $content= preg_replace($search, '$@HVPINDEX*$2@$', $content);

        // Link to hvp view by moduleid
        $search="/(".$base."\/mod\/hvp\/view.php\?id\=)([0-9]+)/";
        $content= preg_replace($search, '$@HVPVIEWBYID*$2@$', $content);

        return $content;
    }
}
