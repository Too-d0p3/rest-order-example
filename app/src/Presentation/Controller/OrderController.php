<?php

namespace App\Presentation\Controller;

use App\Domain\Order\Exception\InvalidDeliveryDateException;
use App\Shared\Exception\ValidationProblemException;
use RuntimeException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use App\Domain\Order\Command\CreateOrderHandler;
use App\Domain\Order\DTO\CreateOrderRequest;
use App\Domain\Order\Exception\OrderAlreadyExistsException;
use App\Shared\DTO\RequestDtoResolver;
use App\Shared\Validation\RequestDtoValidator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[Route('/orders')]
class OrderController extends AbstractApiController
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    #[Route('', name: 'order_create', methods: ['POST'])]
    public function create(
        Request             $request,
        RequestDtoResolver  $dtoResolver,
        RequestDtoValidator $validator,
        CreateOrderHandler  $handler
    ): JsonResponse
    {
        try {
            $dto = $dtoResolver->resolve(CreateOrderRequest::class, $request);
            $validator->validate($dto);
        } catch (ValidationProblemException $e) {
            return $this->createProblemJsonResponse(
                'Validation Failed',
                $e->getMessage(),
                Response::HTTP_UNPROCESSABLE_ENTITY,
                '/errors/validation-failed',
                ['invalid-params' => $this->formatValidationErrors($e->getValidationErrors())]
            );
        }

        try {
            $order = $handler->handle($dto);
        } catch (OrderAlreadyExistsException $e) {
            return $this->createProblemJsonResponse(
                title: 'Order Already Exists',
                detail: $e->getMessage(),
                status: Response::HTTP_CONFLICT,
                type: '/errors/order-exists'
            );
        }catch (InvalidDeliveryDateException $e) {
            return $this->createProblemJsonResponse(
                title: 'Invalid Input',
                detail: $e->getMessage(),
                status: Response::HTTP_BAD_REQUEST,
                type: '/errors/invalid-input'
            );
        } catch (RuntimeException $e) {
            return $this->createProblemJsonResponse('Bad Request', $e->getMessage(), Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $e) {
            return $this->createInternalServerErrorResponse('An unexpected error occurred during delivery date update.');
        }

        $jsonOrder = $this->serializer->serialize($order, 'json', ['groups' => 'order:read']);

        return new JsonResponse($jsonOrder, Response::HTTP_CREATED, [], true);
    }
}