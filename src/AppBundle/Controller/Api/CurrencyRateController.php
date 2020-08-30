<?php

namespace AppBundle\Controller\Api;

use AppBundle\Controller\AbstractApiController;
use AppBundle\Manager\CurrencyRateManager;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CurrencyRateController
 *
 * @package AppBundle\Controller\Api
 */
class CurrencyRateController extends AbstractApiController
{
    /**
     * @Route("/api/v1/currency_rate_import", methods={"POST"}, name="currency_rate_import")
     * @SWG\Post(
     *     path="/api/v1/currency_rate_import",
     *     tags={"Currency rate"},
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     description="Import actual currency rates",
     *     @SWG\Response(
     *         response="204",
     *         description="Actual curency rates was imported"
     *     )
     * )
     *
     * @return Response
     */
    public function getAllSettingsAction() : Response
    {
        /** @var CurrencyRateManager $currencyRateManager */
        $currencyRateManager = $this->get('app.currency_rate_manager');
        $currencyRateManager->importFromCurrencyApi();

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
