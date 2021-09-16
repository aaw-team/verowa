<?php
declare(strict_types=1);
namespace AawTeam\Verowa\ViewHelpers\Uri;
/*
 * Copyright by Agentur am Wasser | Maeder & Partner AG
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use AawTeam\Verowa\Domain\Model\Event;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * IcsDownloadViewHelper
 */
class IcsDownloadViewHelper extends AbstractViewHelper
{
    /**
     * {@inheritDoc}
     * @see \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper::initializeArguments()
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('event', Event::class, 'The event', true);
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return string
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        $eventUid = $arguments['event']->getUid();

        /** @var UriBuilder $uriBuilder */
        $uriBuilder = $renderingContext->getControllerContext()->getUriBuilder();
        $uri = $uriBuilder
            ->reset()
            ->setArguments(['tx_verowa' => ['action' => 'icsdownload', 'event' => $eventUid]])
            ->setCreateAbsoluteUri(false)
        ;

        if ($site = self::getSite()){
            $uri->setTargetPageUid($site->getRootPageId());
        }

        return $uri->build();
    }

    /**
     * @return Site|null
     */
    protected static function getSite(): ?Site
    {
        $site = self::getTypo3Request()->getAttribute('site', null);
        if (!($site instanceof Site)) {
            $site = null;
        }
        return $site;
    }

    /**
     * @return ServerRequest
     */
    protected static function getTypo3Request(): ServerRequest
    {
        return $GLOBALS['TYPO3_REQUEST'];
    }
}
