<?php

namespace AppBundle\Controller\Api;

use AppBundle\Assembler\BalancesListRequest\BalanceRequestAssembler;
use AppBundle\Assembler\PagesDTOAssembler;
use AppBundle\DTO\BalanceRefillOrChargeOffRequestDTO;
use AppBundle\DTO\BalanceRequestDTO;
use AppBundle\Exception\Http\ValidationHttpException;
use AppBundle\Exception\Managers\ManagerException;
use AppBundle\Exception\Managers\ManagerValidationException;
use AppBundle\Exception\StoredProcedure\TransactionTokenExistsException;
use AppBundle\Manager\BalanceManager;
use PgFunc\Exception\Usage;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class BalanceController
 *
 * @package AppBundle\Controller
 */
class BalanceController extends Controller
{
    /**
     * @Route(
     *     "api/v1/balances_get",
     *     methods={"POST"},
     *     name="get_balances_by_object",
     * )
     * @SWG\Post(
     *     path="/api/v1/balances_get",
     *     tags={"Balances"},
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     description="Get balances data by object",
     *     @SWG\Parameter(
     *         in="body",
     *         name="data",
     *         type="object",
     *         description="object_type(string), object_id(int)",
     *         required=true,
     *         @SWG\Schema(ref="#/definitions/BalanceRequestDTO")
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="List of balances",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref="#/definitions/BalanceResponseDTO"),
     *         )
     *     ),
     *     @SWG\Response(
     *         response="422",
     *         description="Validation error",
     *         @SWG\Schema(ref="#/definitions/ErrorValidationDTO")
     *     ),
     * )
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return JsonResponse
     * @throws \AppBundle\Exception\Http\ValidationHttpException
     */
    public function getBalanceByObjectAction(Request $request) : JsonResponse
    {
        $serializer = $this->get('serializer');
        /** @var BalanceManager $balanceManager */
        $balanceManager = $this->get('app.balance_manager');

        /** @var BalanceRequestDTO $balanceRequestDTO */
        $balanceRequestDTO = $serializer->deserialize(
            $request->getContent(),
            BalanceRequestDTO::class,
            'json'
        );

        try {
            $balances = $balanceManager->getBalancesByObject($balanceRequestDTO);
        } catch (\InvalidArgumentException | ManagerValidationException $exception) {
            throw new ValidationHttpException($exception->getErrors(), $exception->getMessage(), $exception);
        }

        return (new JsonResponse(null, JsonResponse::HTTP_OK))
            ->setContent($serializer->serialize($balances, 'json'));
    }

    /**
     * @Route(
     *     "api/v1/balances_list",
     *     methods={"POST"},
     *     name="get_balances_list",
     * )
     * @SWG\Post(
     *     path="/api/v1/balances_list",
     *     tags={"Balances"},
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     description="Get balances list by filters",
     *     @SWG\Parameter(
     *         in="body",
     *         name="data",
     *         type="object",
     *         description="Balances filters",
     *         required=true,
     *         @SWG\Schema(ref="#/definitions/BalanceRequest")
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="List of balances",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref="#/definitions/BalanceListItemResponseDTO"),
     *         )
     *     ),
     *     @SWG\Response(
     *         response="422",
     *         description="Validation error",
     *         @SWG\Schema(ref="#/definitions/ErrorValidationDTO")
     *     ),
     * )
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getBalancesList(Request $request) : JsonResponse
    {
        $serializer = $this->get('serializer');
        /** @var BalanceManager $balanceManager */
        $balanceManager = $this->get('app.balance_manager');
        /** @var BalanceRequestAssembler $balanceRequestAssembler */
        $balanceRequestAssembler = $this->get('app.assembler.balance_list_request_assembler');

        $balanceRequestArray = $serializer->decode($request->getContent(), 'json');

        $balanceRequest = $balanceRequestAssembler->writeObject($balanceRequestArray);

        try {
            $balancesCount = $balanceManager->getBalancesListCount($balanceRequest);
            $balances = $balanceManager->getBalancesList($balanceRequest);
        } catch (ManagerValidationException $exception) {
            throw new ValidationHttpException($exception->getErrors(), $exception->getMessage(), $exception);
        } catch (\InvalidArgumentException $exception) {
            throw new ValidationHttpException([], $exception->getMessage(), $exception);
        }

        /** @var PagesDTOAssembler $pagesDTOAssembler */
        $pagesDTOAssembler = $this->get('app.assembler.pages_assembler');
        $pagesDTO = $pagesDTOAssembler->writeDTO(
            $balancesCount,
            $balanceRequest->getLimit(),
            $balanceRequest->getPage()
        );

        return (new JsonResponse(null, JsonResponse::HTTP_OK))
            ->setContent($serializer->serialize(['result' => $balances, 'pages' => $pagesDTO], 'json'));
    }

    /**
     * @Route(
     *     "api/v1/balances_refill",
     *     methods={"POST"},
     *     name="balances_refill",
     * )
     * @SWG\Post(
     *     path="/api/v1/balances_refill",
     *     tags={"Balances"},
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     description="Refill current balance",
     *     @SWG\Parameter(
     *         in="body",
     *         name="data",
     *         type="object",
     *         description="Balance refill",
     *         required=true,
     *         @SWG\Schema(ref="#/definitions/BalanceRefillOrChargeOffRequestDTO")
     *     ),
     *     @SWG\Response(
     *         response="204",
     *         description="Balance refill"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Transaction token already exists.",
     *         @SWG\Schema(ref="#/definitions/ErrorDTO")
     *     ),
     *     @SWG\Response(
     *         response="422",
     *         description="Validation error",
     *         @SWG\Schema(ref="#/definitions/ErrorValidationDTO")
     *     ),
     * )
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws Usage
     */
    public function balanceRefillAction(Request $request) : Response
    {
        $serializer = $this->get('serializer');

        /** @var BalanceRefillOrChargeOffRequestDTO $dto */
        $dto = $serializer->deserialize($request->getContent(), BalanceRefillOrChargeOffRequestDTO::class, 'json');

        try {
            /** @var BalanceManager $balanceManager */
            $balanceManager = $this->get('app.balance_manager');
            $balanceManager->balanceRefill($dto);
        } catch (ManagerValidationException $exception) {
            throw new ValidationHttpException($exception->getErrors(), $exception->getMessage(), $exception);
        } catch (TransactionTokenExistsException $exception) {
            throw new HttpException(JsonResponse::HTTP_FORBIDDEN, $exception->getMessage());
        } catch (ManagerException $exception) {
            throw new HttpException(JsonResponse::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route(
     *     "api/v1/balances_charge_off",
     *     methods={"POST"},
     *     name="balances_charge_off",
     * )
     * @SWG\Post(
     *     path="/api/v1/balances_charge_off",
     *     tags={"Balances"},
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     description="Charge off current balance",
     *     @SWG\Parameter(
     *         in="body",
     *         name="data",
     *         type="object",
     *         description="Balance charge off",
     *         required=true,
     *         @SWG\Schema(ref="#/definitions/BalanceRefillOrChargeOffRequestDTO")
     *     ),
     *     @SWG\Response(
     *         response="204",
     *         description="Balance charge off"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Transaction token already exists.",
     *         @SWG\Schema(ref="#/definitions/ErrorDTO")
     *     ),
     *     @SWG\Response(
     *         response="422",
     *         description="Validation error",
     *         @SWG\Schema(ref="#/definitions/ErrorValidationDTO")
     *     ),
     * )
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws Usage
     */
    public function balanceChargeOffAction(Request $request) : Response
    {
        $serializer = $this->get('serializer');

        /** @var BalanceRefillOrChargeOffRequestDTO $dto */
        $dto = $serializer->deserialize($request->getContent(), BalanceRefillOrChargeOffRequestDTO::class, 'json');

        try {
            /** @var BalanceManager $balanceManager */
            $balanceManager = $this->get('app.balance_manager');
            $balanceManager->balanceChargeOff($dto);
        } catch (ManagerValidationException $exception) {
            throw new ValidationHttpException($exception->getErrors(), $exception->getMessage(), $exception);
        } catch (TransactionTokenExistsException $exception) {
            throw new HttpException(JsonResponse::HTTP_FORBIDDEN, $exception->getMessage());
        } catch (ManagerException $exception) {
            throw new HttpException(JsonResponse::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
