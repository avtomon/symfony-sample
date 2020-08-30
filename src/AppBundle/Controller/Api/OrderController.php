<?php

namespace AppBundle\Controller\Api;

use AppBundle\Controller\AbstractApiController;
use AppBundle\DTO\OrderDTO;
use AppBundle\DTO\OrderPaidDTO;
use AppBundle\DTO\TransactionTokenDTO;
use AppBundle\Exception\Http\ValidationHttpException;
use AppBundle\Exception\Managers\ManagerException;
use AppBundle\Exception\Managers\ManagerForbiddenException;
use AppBundle\Exception\Managers\ManagerNotFoundException;
use AppBundle\Exception\Managers\ManagerValidationException;
use AppBundle\Exception\StoredProcedure\CannotLoadSettingsException;
use AppBundle\Exception\StoredProcedure\InvalidOrderException;
use AppBundle\Exception\StoredProcedure\TransactionTokenCanceledException;
use AppBundle\Exception\StoredProcedure\TransactionTokenCompletedException;
use AppBundle\Exception\StoredProcedure\TransactionTokenExistsException;
use AppBundle\Exception\StoredProcedure\TransactionTokenIsNotExistsException;
use AppBundle\Manager\OrderManager;
use PgFunc\Exception\Usage;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class OrderController
 *
 * @package AppBundle\Controller\Api
 *
 * @Route("/api/v1/")
 */
class OrderController extends AbstractApiController
{
    /**
     * @Route("order_create", methods={"POST"}, name="order_create")
     * @SWG\Post(
     *     path="/api/v1/order_create",
     *     tags={"Orders"},
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     description="Create new order",
     *     @SWG\Parameter(
     *         in="body",
     *         name="request",
     *         type="object",
     *         description="data of order",
     *         required=true,
     *         @SWG\Schema(ref="#/definitions/OrderDTO")
     *     ),
     *     @SWG\Response(
     *         response="204",
     *         description="Order has been added."
     *      ),
     *     @SWG\Response(
     *         response="404",
     *         description="ObjectData not found",
     *         @SWG\Schema(ref="#/definitions/ErrorDTO")
     *     ),
     *     @SWG\Response(
     *         response="422",
     *         description="Validation error.|Unprocessable currency.",
     *         @SWG\Schema(ref="#/definitions/ErrorValidationDTO")
     *     )
     * )
     * @param Request $request
     *
     * @return Response
     * @throws Usage
     */
    public function orderCreateAction(Request $request) : Response
    {
        $serializer = $this->get('serializer');

        /** @var OrderDTO $orderDTO */
        $orderDTO = $serializer->deserialize($request->getContent(), OrderDTO::class, 'json');

        /** @var OrderManager $orderManager */
        $orderManager = $this->get('app.order_manager');

        try {
            $orderManager->orderCreate($orderDTO);
        } catch (ManagerNotFoundException $exception) {
            throw new NotFoundHttpException($exception->getMessage());
        } catch (ManagerValidationException $exception) {
            throw new ValidationHttpException($exception->getErrors(), $exception->getMessage());
        } catch (InvalidOrderException $exception) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        } catch (CannotLoadSettingsException $exception) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        } catch (ManagerException $exception) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage(), $exception);
        }

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("order_complete", methods={"POST"}, name="api_order_complete")
     * @SWG\Post(
     *     path="/api/v1/order_complete",
     *     tags={"Orders"},
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     description="Order is completed",
     *     @SWG\Parameter(
     *         in="body",
     *         name="order",
     *         type="object",
     *         description="order data",
     *         required=true,
     *         @SWG\Schema(ref="#/definitions/TransactionTokenDTO")
     *     ),
     *     @SWG\Response(
     *         response="204",
     *         description="Order has completed."
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Not found.",
     *         @SWG\Schema(ref="#/definitions/ErrorDTO")
     *     ),
     *     @SWG\Response(
     *         response="422",
     *         description="Validation error.",
     *         @SWG\Schema(ref="#/definitions/ErrorValidationDTO")
     *     )
     * )
     * @param Request $request
     *
     * @return JsonResponse
     * @throws Usage
     */
    public function orderCompletedAction(Request $request) : JsonResponse
    {
        $serializer = $this->get('serializer');

        /** @var TransactionTokenDTO $dto */
        $dto = $serializer->deserialize($request->getContent(), TransactionTokenDTO::class, 'json');

        try {
            /** @var OrderManager $orderManager */
            $orderManager = $this->get('app.order_manager');
            $orderManager->orderCompleted($dto);
        } catch (TransactionTokenIsNotExistsException | ManagerNotFoundException $exception) {
            throw new NotFoundHttpException($exception->getMessage());
        } catch (ManagerValidationException $exception) {
            throw new ValidationHttpException($exception->getErrors(), $exception->getMessage());
        } catch (ManagerException $exception) {
            throw new HttpException(JsonResponse::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * @Route(
     *     "order_cancel",
     *     methods={"POST"},
     *     name="order_cancel"
     * )
     * @SWG\Post(
     *     path="/api/v1/order_cancel",
     *     tags={"Orders"},
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     description="Cancel order",
     *     @SWG\Parameter(
     *         name="request",
     *         in="body",
     *         type="object",
     *         description="Order params",
     *         required=true,
     *         @SWG\Schema(ref="#/definitions/TransactionTokenDTO")
     *     ),
     *     @SWG\Response(
     *         response="204",
     *         description="Order cancel successfully"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Not found.",
     *         @SWG\Schema(ref="#/definitions/ErrorDTO")
     *     ),
     *     @SWG\Response(
     *         response="422",
     *         description="Validation error.",
     *         @SWG\Schema(ref="#/definitions/ErrorValidationDTO")
     *     )
     * )
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws Usage
     */
    public function orderCancelAction(Request $request) : JsonResponse
    {
        $serializer = $this->get('serializer');

        /** @var TransactionTokenDTO $dto */
        $dto = $serializer->deserialize($request->getContent(), TransactionTokenDTO::class, 'json');

        try {
            /** @var OrderManager $orderManager */
            $orderManager = $this->get('app.order_manager');
            $orderManager->orderCancel($dto);
        } catch (ManagerValidationException $exception) {
            throw new ValidationHttpException($exception->getErrors(), $exception->getMessage());
        } catch (TransactionTokenIsNotExistsException | ManagerNotFoundException  $exception) {
            throw new NotFoundHttpException($exception->getMessage(), $exception);
        } catch (ManagerForbiddenException $exception) {
            throw new AccessDeniedHttpException('Method is not allowed', $exception);
        } catch (ManagerException $exception) {
            throw new HttpException(JsonResponse::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage(), $exception);
        }

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * @Route(
     *     "order_paid",
     *     methods={"POST"},
     *     name="order_paid"
     * )
     * @SWG\Post(
     *     path="/api/v1/order_paid",
     *     tags={"Orders"},
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     description="Paid order",
     *     @SWG\Parameter(
     *         name="request",
     *         in="body",
     *         type="object",
     *         description="Order params",
     *         required=true,
     *         @SWG\Schema(ref="#/definitions/OrderPaidDTO")
     *     ),
     *     @SWG\Response(
     *         response="204",
     *         description="Order paid successfully"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Not found.",
     *         @SWG\Schema(ref="#/definitions/ErrorDTO")
     *     ),
     *     @SWG\Response(
     *         response="422",
     *         description="Validation error.",
     *         @SWG\Schema(ref="#/definitions/ErrorValidationDTO")
     *     )
     * )
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws Usage
     */
    public function orderPaidAction(Request $request) : JsonResponse
    {
        $serializer = $this->get('serializer');

        /** @var OrderPaidDTO $orderPaidDTO */
        $orderPaidDTO = $serializer->deserialize($request->getContent(), OrderPaidDTO::class, 'json');

        try {
            /** @var OrderManager $orderManager */
            $orderManager = $this->get('app.order_manager');
            $orderManager->orderPaid($orderPaidDTO);
        } catch (ManagerValidationException $exception) {
            throw new ValidationHttpException($exception->getErrors(), $exception->getMessage(), $exception);
        } catch (TransactionTokenIsNotExistsException | ManagerNotFoundException $exception) {
            throw new NotFoundHttpException($exception->getMessage(), $exception);
        } catch (TransactionTokenExistsException
            | TransactionTokenCompletedException
            | TransactionTokenCanceledException $exception) {

            throw new ConflictHttpException('Conflict. ' . $exception->getMessage(), $exception);
        } catch (ManagerException $exception) {
            throw new BadRequestHttpException('Unexpected error. ' . $exception->getMessage(), $exception);
        }

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
