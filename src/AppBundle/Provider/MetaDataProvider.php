<?php

namespace AppBundle\Provider;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\UserService;
use Lolautruche\EzCoreExtraBundle\View\ConfigurableView;
use Lolautruche\EzCoreExtraBundle\View\ConfigurableViewParameterProvider;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MetaDataProvider extends ConfigurableViewParameterProvider
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
    public function doGetParameters(ConfigurableView $view, array $options = [])
    {
        $content = $view->getContent();

        if ($options['use_author_field']) {
            $authorName = (string)$content->getFieldValue($options['author_field_name']);
        }

        return [
            'author' => $authorName ?? $this->userService->loadUser($content->contentInfo->ownerId)->getName(),
            'contentType' => $this->contentTypeService->loadContentType($content->contentInfo->contentTypeId),
        ];
    }

    /**
     * Configures the OptionsResolver for the param provider.
     *
     * Example:
     * ```php
     * // type setting will be required
     * $resolver->setRequired('type');
     * // limit setting will be optional, and will have a default value of 10
     * $resolver->setDefault('limit', 10);
     * ```
     *
     * @param OptionsResolver $optionsResolver
     */
    protected function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver
            ->setDefaults([
                'use_author_field' => false,
                'author_field_name' => 'author',
            ]);
    }
}
