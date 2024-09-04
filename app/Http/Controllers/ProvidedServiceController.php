<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ProvidedService\IndexProvidedServiceRequest;
use App\Http\Resources\ProvidedServiceResource;
use App\Models\ProvidedService;
use App\ReadModels\ProvidedServiceFetcher;
use App\Swagger\Responses\CollectionResponse;
use App\Swagger\Responses\InvalidTokenResponse;
use App\Swagger\Responses\ProvidedService\ValidationErrorResponse;
use App\UseCases\ProvidedService\CancelRegistration\CancelRegistrationCommand;
use App\UseCases\ProvidedService\CancelRegistration\CancelRegistrationHandler;
use App\UseCases\ProvidedService\Registration\RegistrationCommand;
use App\UseCases\ProvidedService\Registration\RegistrationHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use OpenApi\Attributes\Delete;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\Items;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\MediaType;
use OpenApi\Attributes\PathParameter;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\QueryParameter;
use OpenApi\Attributes\RequestBody;
use OpenApi\Attributes\Schema;

class ProvidedServiceController extends Controller
{
    private const int LIMIT = 30;

    #[Get(
        path: '/insured_persons/{insuredPersonId}/provided_services',
        summary: 'Get provided services to insured person',
        security: [['bearerAuth' => []]],
        tags: ['Provided services'],
        parameters: [
            new PathParameter(ref: '#components/parameters/insuredPersonId'),
            new QueryParameter(ref: '#components/parameters/pageNumber'),
            new QueryParameter(ref: '#components/parameters/serviceIdInQuery'),
            new QueryParameter(ref: '#components/parameters/serviceName'),
            new QueryParameter(ref: '#components/parameters/dateOfServiceFrom'),
            new QueryParameter(ref: '#components/parameters/dateOfServiceTo'),
            new QueryParameter(ref: '#components/parameters/limitType'),
            new QueryParameter(ref: '#components/parameters/quantityFrom'),
            new QueryParameter(ref: '#components/parameters/quantityTo'),
            new QueryParameter(ref: '#components/parameters/priceFrom'),
            new QueryParameter(ref: '#components/parameters/priceTo'),
            new QueryParameter(ref: '#components/parameters/amountFrom'),
            new QueryParameter(ref: '#components/parameters/amountTo'),
        ]
    )]
    #[\OpenApi\Attributes\Response(
        response: 200,
        description: 'OK',
        content: new JsonContent(
            properties: [
                new Property(property: 'data', type: 'array', items: new Items(ref: ProvidedServiceResource::class))
            ],
            allOf: [new Schema(ref: CollectionResponse::class)]
        )
    )]
    #[\OpenApi\Attributes\Response(
        ref: InvalidTokenResponse::class,
        response: 401
    )]
    #[\OpenApi\Attributes\Response(
        response: 404,
        description: 'Insured person not found'
    )]
    public function index(IndexProvidedServiceRequest $request, int $insuredPersonId, ProvidedServiceFetcher $fetcher): AnonymousResourceCollection
    {
        $providedServices = $fetcher->getByInsuredPerson(
            $insuredPersonId,
            self::LIMIT,
            (int)$request->validated('page'),
            $request->validated()
        );

        return ProvidedServiceResource::collection($providedServices);
    }

    #[Post(
        path: '/insured_persons/{insuredPersonId}/provided_services',
        summary: 'Registration of the provided service under contract',
        security: [['bearerAuth' => []]],
        requestBody: new RequestBody(
            content: new MediaType(
                mediaType: 'multipart/form-data',
                schema: new Schema(ref: RegistrationCommand::class)
            )
        ),
        tags: ['Provided services'],
        parameters: [
            new PathParameter(ref: '#components/parameters/insuredPersonId')
        ]
    )]
    #[\OpenApi\Attributes\Response(
        response: 200,
        description: 'The provided service has been successfully registered',
        content: new JsonContent(properties: [new Property(property: 'id', description: 'Provided service id', type: 'integer', example: 2102)])
    )]
    #[\OpenApi\Attributes\Response(
        response: 400,
        description: 'Error',
        content: new JsonContent(oneOf: [
            new Schema(properties: [
                new Property(
                    property: 'message',
                    type: 'string',
                    anyOf: [
                        new Schema(example: 'The contract has expired'),
                        new Schema(example: 'The date of service provision is not included in the contract period'),
                        new Schema(example: 'The number of services provided exceeds the limit under the contract. Contract limit 200. Balance 186.'),
                    ]
                )
            ])
        ])
    )]
    #[\OpenApi\Attributes\Response(
        ref: InvalidTokenResponse::class,
        response: 401
    )]
    #[\OpenApi\Attributes\Response(
        response: 404,
        description: 'Insured person or service not found'
    )]
    #[\OpenApi\Attributes\Response(
        response: 422,
        description: 'Validation error',
        content: new JsonContent(ref: ValidationErrorResponse::class)
    )]
    public function store(RegistrationCommand $command, RegistrationHandler $handler): JsonResponse
    {
        $id = $handler->handle($command);

        return response()->json(['id' => $id]);
    }

    #[Get(
        path: '/insured_persons/{insuredPersonId}/provided_services/{providedServiceId}',
        summary: 'Get data about provided service',
        security: [['bearerAuth' => []]],
        tags: ['Provided services'],
        parameters: [
            new PathParameter(ref: '#components/parameters/insuredPersonId'),
            new PathParameter(ref: '#components/parameters/providedServiceId'),
        ]
    )]
    #[\OpenApi\Attributes\Response(
        response: 200,
        description: 'OK',
        content: new JsonContent(ref: ProvidedServiceResource::class)
    )]
    #[\OpenApi\Attributes\Response(
        ref: InvalidTokenResponse::class,
        response: 401
    )]
    #[\OpenApi\Attributes\Response(
        response: 404,
        description: 'Insured person or provided service not found'
    )]
    public function show(int $_insuredPersonId, ProvidedService $providedService): ProvidedServiceResource
    {
        return new ProvidedServiceResource($providedService);
    }

    #[Delete(
        path: '/insured_persons/{insuredPersonId}/provided_services/{providedServiceId}',
        summary: 'Cancel the registration of the provided service',
        security: [['bearerAuth' => []]],
        tags: ['Provided services'],
        parameters: [
            new PathParameter(ref: '#components/parameters/insuredPersonId'),
            new PathParameter(ref: '#components/parameters/providedServiceId'),
        ]
    )]
    #[\OpenApi\Attributes\Response(
        response: 204,
        description: 'Cancellation of the registration of the provided service is successfully completed'
    )]
    #[\OpenApi\Attributes\Response(
        ref: InvalidTokenResponse::class,
        response: 401
    )]
    #[\OpenApi\Attributes\Response(
        response: 404,
        description: 'Insured person or provided service not found'
    )]
    public function destroy(CancelRegistrationCommand $command, CancelRegistrationHandler $handler): Response
    {
        $handler->handle($command);

        return response()->noContent();
    }
}
