<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

require_once LIBS_DIR . '/MediaList/MediaList.php';

/**
 * Media list widget
 * @title Media files
 */
class MediaListWidget extends Widget
{
    public function render()
    {
        $list = new MediaList;
        $list->setHidden('id');
        $list->setHidden('InUse');
        $list->render();
    }
}
