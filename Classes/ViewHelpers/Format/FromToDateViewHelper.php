<?php
declare(strict_types=1);
namespace AawTeam\Verowa\ViewHelpers\Format;
/*
 * Copyright by Agentur am Wasser | Maeder & Partner AG
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use AawTeam\Verowa\Utility\DateTimeUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * FromToDateViewHelper
 */
class FromToDateViewHelper extends AbstractViewHelper
{
    /**
     * {@inheritDoc}
     * @see \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper::initializeArguments()
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('from', \DateTimeInterface::class, 'The "from" datetime', true);
        $this->registerArgument('to', \DateTimeInterface::class, 'The "to" datetime', true);
        $this->registerArgument('hideTime', 'bool', 'Hide the time part', false, false);
        $this->registerArgument('timeSeparator', 'string', 'The separator', false, ' | ');
        $this->registerArgument('partsSeparator', 'string', 'The separator', false, ' - ');
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return string
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        $start = $arguments['from'];
        $end = $arguments['to'];
        $hideTime = $arguments['hideTime'];
        $timeSeparator = $arguments['timeSeparator'];
        $partsSeparator = $arguments['partsSeparator'];

        if ($hideTime) {
            return DateTimeUtility::renderFromToDate($start, $end, $partsSeparator);
        }
        return DateTimeUtility::renderFromToDateAndTime($start, $end, $timeSeparator, $partsSeparator);
    }
}
