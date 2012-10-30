<?php
/**
 * TentPHP
 *
 * LICENSE
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.txt.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to kontakt@beberlei.de so I can send you a copy immediately.
 */

namespace TentPHP\Util;

/**
 * Mentions Parser API
 *
 * Can parse a string for mentions based on the common "^name" syntax.
 */
class Mentions
{
    public function extractMentions($text, $contextEntity, $character = "^")
    {
        $mentions     = array();
        $contextParts = parse_url($contextEntity);
        $hostParts    = explode(".", $contextParts['host']);
        array_shift($hostParts);

        if (preg_match_all('(('.preg_quote($character). '([^\s]+)))', $text, $matches, PREG_OFFSET_CAPTURE)) {

            foreach ($matches[2] as $match) {
                list($entity, $pos) = $match;

                $entity = rtrim($entity, '.!?');

                if (strpos($entity, "http") === false) {
                    if (strpos($entity, implode(".", $hostParts)) === false) {
                        $entity = $contextParts['scheme'] . "://" . $entity . "." . implode(".", $hostParts);
                    } else {
                        $entity = $contextParts['scheme'] . "://" . $entity;
                    }
                }

                $mentions[] = array('entity' => $entity, 'pos' => $pos - 1, 'length' => strlen(rtrim($match[0], '.!?'))+1);
            }
        }

        return $mentions;
    }
}
