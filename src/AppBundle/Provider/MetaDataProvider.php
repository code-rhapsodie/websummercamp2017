<?php

namespace AppBundle\Provider;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\UserService;
use Lolautruche\EzCoreExtraBundle\View\ConfigurableView;
use Lolautruche\EzCoreExtraBundle\View\ViewParameterProviderInterface;

class MetaDataProvider implements ViewParameterProviderInterface
{
    /**
     * @var ContentService
     */
    private $userService;

    /**
     * @var ContentTypeService
     */
    private $contentTypeService;

    public function __construct(UserService $userService, ContentTypeService $contentTypeService)
    {
        $this->userService = $userService;
        $this->contentTypeService = $contentTypeService;
    }

    /**
     * Returns a hash of parameters to inject into the matched view.
     * Key is the parameter name, value is the parameter value.
     *
     * Available view parameters (e.g. "content", "location"...) are accessible from $view.
     *
     * @param ConfigurableView $view Decorated matched view, containing initial parameters.
     * @param array $options
     *
     * @return array
     */
    public function getViewParameters(ConfigurableView $view, array $options = [])
    {
        $content = $view->getContent();

        return [
            'author' => $this->userService->loadUser($content->contentInfo->ownerId),
            'contentType' => $this->contentTypeService->loadContentType($content->contentInfo->contentTypeId),
        ];
    }
}
