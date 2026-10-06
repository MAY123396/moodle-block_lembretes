<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Block lembretes.
 *
 * @package    block_lembretes
 * @copyright  2026 Maysa
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Block lembretes definition.
 */
class block_lembretes extends block_base {
    // Moodle's block_base declares no init() of its own — #[\Override] would fatal
    // (PHP 8.3+ requires a real parent method to override; there isn't one here).
    /**
     * Set the block title.
     */
    public function init(): void {
        $this->title = get_string('pluginname', 'block_lembretes');
    }

    #[\Override]
    public function get_content(): ?stdClass {
        if ($this->content !== null) {
            return $this->content;
        }
        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';
        return $this->content;
    }
}
