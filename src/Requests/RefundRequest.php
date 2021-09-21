<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Requests;

use Devpark\Transfers24\Contracts\IResponse;
use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Factories\ActionFactory;
use Devpark\Transfers24\Factories\ForResponses\PaymentMethodsResponseFactory;
use Devpark\Transfers24\Factories\ForTranslators\PaymentMethodsTranslatorFactory;
use Devpark\Transfers24\Factories\ForTranslators\RefundTranslatorFactory;
use Devpark\Transfers24\Language;
use Devpark\Transfers24\Responses\InvalidResponse;
use Devpark\Transfers24\Responses\PaymentMethods;
use Devpark\Transfers24\Responses\TestConnection;

class RefundRequest
{
    use RequestCredentialsKeeperTrait;

    /**
     * @var PaymentMethodsTranslatorFactory
     */
    private $translator_factory;
    /**
     * @var ActionFactory
     */
    private $action_factory;
    /**
     * @var PaymentMethodsResponseFactory
     */
    private $response_factory;

    /**
     * @var string
     */
    protected $language = Language::POLISH;

    public function __construct(
        RefundTranslatorFactory $translator_factory, Credentials $credentials_keeper,
        ActionFactory $action_factory, RefundResponseFactory $response_factory
    )
    {
        $this->credentials_keeper = $credentials_keeper;
        $this->translator_factory = $translator_factory;
        $this->action_factory = $action_factory;
        $this->response_factory = $response_factory;
    }

    /**
     * @return PaymentMethods|InvalidResponse
     */
    public function execute():IResponse
    {
        $translator = $this->translator_factory->create($this->credentials_keeper, $this);
        $action = $this->action_factory->create($this->response_factory, $translator);
        return $action->execute();
    }

    /**
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * Set language interface.
     *
     * @param $language
     *
     * @return $this
     */
    public function setLanguage($language)
    {
        $this->language = Language::get($language);

        return $this;
    }

}
