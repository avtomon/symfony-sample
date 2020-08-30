<?php

namespace AppBundle\Controller\Api;

use AppBundle\Exception\Http\ValidationHttpException;
use AppBundle\Exception\Managers\ManagerValidationException;
use AppBundle\Manager\InvoicesManager;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class InvoiceController
 *
 */
class InvoiceController extends Controller
{
    /**
     * @Route(
     *     "/api/v1/invoices_list",
     *     methods={"POST"},
     *     name="list_invoices",
     * )
     * @SWG\Post(
     *     path="/api/v1/invoices_list",
     *     tags={"Invoices"},
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     description="Get invoices list",
     *     @SWG\Parameter(
     *         in="body",
     *         name="data",
     *         type="object",
     *         description="Request for list",
     *         required=true,
     *         @SWG\Schema(ref="#/definitions/ApiInvoiceListSearchContext")
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="List of invoices",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref="#/definitions/InvoicesListDTO"),
     *         )
     *     ),
     *     @SWG\Response(
     *         response="422",
     *         description="Validation error",
     *         @SWG\Schema(ref="#/definitions/ErrorValidationDTO")
     *     ),
     * )
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function listAction(Request $request) : JsonResponse
    {
        $assembler = $this->get('app.assembler_invoices_list.invoice_list_search_assembler');

        try {
            $dto = $assembler->writeSearchContextFromArray(
                $this->get('serializer')->decode($request->getContent(), 'json')
            );
            /** @var InvoicesManager $invoicesManager */
            $invoicesManager = $this->get('app.manager.invoices_manager');
            $result = $invoicesManager->invoicesList($dto);
        } catch (ManagerValidationException $exception) {
            throw new ValidationHttpException($exception->getErrors(), $exception->getMessage(), $exception);
        }

        return (new JsonResponse())->setContent($this->get('serializer')->serialize($result, 'json'));
    }
}
