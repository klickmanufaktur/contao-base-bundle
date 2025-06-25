<?php

namespace Klickmanufaktur\ContaoBaseBundle\Service\EventListener;

use Contao\ContentModel;
use Contao\Controller;
use Contao\MemberGroupModel;
use Contao\StringUtil;

class ChildRecordCallbackListenerHelper {

    public function formatInfo(array $data): string
    {
        $parts = [];

        foreach ($data as $key => $value) {
            $parts[] = sprintf('<strong>%s</strong>: %s', $key, $value);
        }

        return implode("<span style='padding: 0 8px;'>|</span>", $parts);
    }

    public function addCteType(array $arrRow, string $info = ''): string {
        $category = $this->getContentElementGroupLabel($this->getContentElementGroup($arrRow['type']));
        $key = $arrRow['invisible'] ? 'unpublished' : 'published';
        $type = $GLOBALS['TL_LANG']['CTE'][$arrRow['type']][0] ?? $arrRow['type'];

        // Remove the class if it is a wrapper element
        if (in_array($arrRow['type'], $GLOBALS['TL_WRAPPERS']['start']) || in_array($arrRow['type'], $GLOBALS['TL_WRAPPERS']['separator']) || in_array($arrRow['type'], $GLOBALS['TL_WRAPPERS']['stop']))
        {
            if (($group = $this->getContentElementGroup($arrRow['type'])) !== null)
            {
                //$type = ($GLOBALS['TL_LANG']['CTE'][$group] ?? $group) . ' (' . $type . ')';
            }
        }

        // Add the group name if it is a single element (see #5814)
        elseif (in_array($arrRow['type'], $GLOBALS['TL_WRAPPERS']['single']))
        {
            if (($group = $this->getContentElementGroup($arrRow['type'])) !== null)
            {
                $type = ($GLOBALS['TL_LANG']['CTE'][$group] ?? $group) . ' (' . $type . ')';
            }
        }

        // Add the ID of the aliased element
        if ($arrRow['type'] == 'alias')
        {
            $type .= ' ID ' . $arrRow['cteAlias'];
        }

        // Add the protection status
        if ($arrRow['protected'] ?? null)
        {
            $groupIds = StringUtil::deserialize($arrRow['groups'], true);
            $groupNames = array();

            if (!empty($groupIds))
            {
                if (in_array(-1, array_map('intval', $groupIds), true))
                {
                    $groupNames[] = $GLOBALS['TL_LANG']['MSC']['guests'];
                }

                if (null !== ($groups = MemberGroupModel::findMultipleByIds($groupIds)))
                {
                    $groupNames += $groups->fetchEach('name');
                }
            }

            $key .= ' icon-protected';
            $type .= ' (' . $GLOBALS['TL_LANG']['MSC']['protected'] . ($groupNames ? ': ' . implode(', ', $groupNames) : '') . ')';
        }

        // Add the headline level (see #5858)
        if ($arrRow['type'] == 'headline' && is_array($headline = StringUtil::deserialize($arrRow['headline'])))
        {
            $type .= ' (' . $headline['unit'] . ')';
        }

        if ($arrRow['start'] && $arrRow['stop'])
        {
            $type .= ' <span class="visibility">(' . sprintf($GLOBALS['TL_LANG']['MSC']['showFromTo'], Date::parse(Config::get('datimFormat'), $arrRow['start']), Date::parse(Config::get('datimFormat'), $arrRow['stop'])) . ')</span>';
        }
        elseif ($arrRow['start'])
        {
            $type .= ' <span class="visibility">(' . sprintf($GLOBALS['TL_LANG']['MSC']['showFrom'], Date::parse(Config::get('datimFormat'), $arrRow['start'])) . ')</span>';
        }
        elseif ($arrRow['stop'])
        {
            $type .= ' <span class="visibility">(' . sprintf($GLOBALS['TL_LANG']['MSC']['showTo'], Date::parse(Config::get('datimFormat'), $arrRow['stop'])) . ')</span>';
        }

        $objModel = new ContentModel();
        $objModel->setRow($arrRow);

        $class = 'cte_preview';

        try
        {
            $preview = StringUtil::insertTagToSrc(Controller::getContentElement($objModel));
        }
        catch (Throwable $exception)
        {
            $preview = '<p class="tl_error">' . StringUtil::specialchars($exception->getMessage()) . '</p>';
        }

        if (!empty($arrRow['sectionHeadline']))
        {
            $sectionHeadline = StringUtil::deserialize($arrRow['sectionHeadline'], true);

            if (!empty($sectionHeadline['value']) && !empty($sectionHeadline['unit']))
            {
                $preview = '<' . $sectionHeadline['unit'] . '>' . $sectionHeadline['value'] . '</' . $sectionHeadline['unit'] . '>' . $preview;
            }
        }

        // Strip HTML comments to check if the preview is empty
        if (trim(preg_replace('/<!--(.|\s)*?-->/', '', $preview)) == '')
        {
            $class .= ' empty';
        }

        if($info != '') {
            return "
                <div class='cte_type $key'>$category <svg style='width: 8px; height: 8px;' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 320 512'><!--!Font Awesome Pro 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2025 Fonticons, Inc.--><path fill='#589b0e' d='M273 239c9.4 9.4 9.4 24.6 0 33.9L113 433c-9.4 9.4-24.6 9.4-33.9 0s-9.4-24.6 0-33.9l143-143L79 113c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0L273 239z'/></svg> $type</div>
                <div class='$class'>
                    <div style='background:var(--table-header); color: var(--gray); border-bottom: 1px solid var(--border); margin: -10px -10px 10px -10px; padding: 10px; font-size: .8rem;'>$info</div>
                    $preview
                </div>
            ";
        }

        if (in_array($arrRow['type'], $GLOBALS['TL_WRAPPERS']['start']) || in_array($arrRow['type'], $GLOBALS['TL_WRAPPERS']['separator']) || in_array($arrRow['type'], $GLOBALS['TL_WRAPPERS']['stop'])) {
            return "
            <div class='cte_type $key'>$category <svg style='width: 8px; height: 8px;' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 320 512'><!--!Font Awesome Pro 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2025 Fonticons, Inc.--><path fill='#589b0e' d='M273 239c9.4 9.4 9.4 24.6 0 33.9L113 433c-9.4 9.4-24.6 9.4-33.9 0s-9.4-24.6 0-33.9l143-143L79 113c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0L273 239z'/></svg> $type</div>
        ";
        }

        return "
            <div class='cte_type $key'>$category <svg style='width: 8px; height: 8px;' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 320 512'><!--!Font Awesome Pro 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2025 Fonticons, Inc.--><path fill='#589b0e' d='M273 239c9.4 9.4 9.4 24.6 0 33.9L113 433c-9.4 9.4-24.6 9.4-33.9 0s-9.4-24.6 0-33.9l143-143L79 113c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0L273 239z'/></svg> $type</div>
            <div class='$class'>$preview</div>
        ";
    }

    public function getContentElementGroup(string $elementType): int|string|null {
        foreach ($GLOBALS['TL_CTE'] as $group => $elements) {
            if (array_key_exists($elementType, $elements)) {
                return $group;
            }
        }

        return null;
    }

    public function getContentElementGroupLabel($group): string {
        return $GLOBALS['TL_LANG']['CTE'][$group];
    }

}