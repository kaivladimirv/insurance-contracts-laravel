<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\InUse;
use App\Http\Requests\ContractService\IndexContractServiceRequest;
use App\Http\Resources\ContractServiceResource;
use App\Models\ContractService;
use App\ReadModels\ContractServiceFetcher;
use App\Swagger\Responses\CollectionResponse;
use App\Swagger\Responses\ContractService\ServiceIdValidationErrorResponse;
use App\Swagger\Responses\ContractService\LimitValidationErrorResponse;
use App\Swagger\Responses\InvalidTokenResponse;
use App\UseCases\ContractService\Add\AddCommand;
use App\UseCases\ContractService\Add\AddHandler;
use App\UseCases\ContractService\Delete\DeleteCommand;
use App\UseCases\ContractService\Delete\DeleteHandler;
use App\UseCases\ContractService\Update\UpdateCommand;
use App\UseCases\ContractService\Update\UpdateHandler;
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

class ContractServiceController extends Controller
{
    private const int LIMIT = 30;

    #[Get(
        path: '/contracts/{contractId}/services',
        summary: 'Get contract services',
        security: [['bearerAuth' => []]],
        tags: ['Contracts/Services'],
        parameters: [
            new PathParameter(ref: '#components/parameters/contractId'),
            new QueryParameter(ref: '#components/parameters/pageNumber'),
            new QueryParameter(ref: '#components/parameters/contractServiceLimitType'),
            new QueryParameter(ref: '#components/parameters/limitValueFrom'),
            new QueryParameter(ref: '#components/parameters/limitValueTo'),
        ]
    )]
    #[\OpenApi\Attributes\Response(
        response: 200,
        description: 'OK',
        content: new JsonContent(
            properties: [
                new Property(property: 'data', type: 'array', items: new Items(ref: ContractServiceResource::class))
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
        description: 'Contract not found'
    )]
    public function index(IndexContractServiceRequest $request, int $contractId, ContractServiceFetcher $fetcher): AnonymousResourceCollection
    {
        $contractServices = $fetcher->get(
            $contractId,
            self::LIMIT,
            (int)$request->validated('page'),
            $request->validated()
        );

        return ContractServiceResource::collection($contractServices);
    }

    #[Post(
        path: '/contracts/{contractId}/services',
        summary: 'Add service to contract',
        security: [['bearerAuth' => []]],
        requestBody: new RequestBody(
            content: new MediaType(
                mediaType: 'multipart/form-data',
                schema: new Schema(ref: AddCommand::class)
            )
        ),
        tags: ['Contracts/Services'],
        parameters: [
            new PathParameter(ref: '#components/parameters/contractId')
        ]
    )]
    #[\OpenApi\Attributes\Response(
        response: 204,
        description: 'The service has been successfully added to the contract'
    )]
    #[\OpenApi\Attributes\Response(
        ref: InvalidTokenResponse::class,
        response: 401
    )]
    #[\OpenApi\Attributes\Response(
        response: 404,
        description: 'Contract not found'
    )]
    #[\OpenApi\Attributes\Response(
        response: 422,
        description: 'Validation error',
        content: new JsonContent(allOf: [
            new Schema(ref: ServiceIdValidationErrorResponse::class),
            new Schema(ref: LimitValidationErrorResponse::class),
        ])
    )]
    public function store(AddCommand $command, AddHandler $handler): Response
    {
        $handler->handle($command);

        return response()->noContent();
    }

    #[Get(
        path: '/contracts/{contractId}/services/{serviceId}',
        summary: 'Get service data',
        security: [['bearerAuth' => []]],
        tags: ['Contracts/Services'],
        parameters: [
            new PathParameter(ref: '#components/parameters/contractId'),
            new PathParameter(ref: '#components/parameters/serviceId')
        ]
    )]
    #[\OpenApi\Attributes\Response(
        response: 200,
        description: 'OK',
        content: new JsonContent(ref: ContractServiceResource::class)
    )]
    #[\OpenApi\Attributes\Response(
        ref: InvalidTokenResponse::class,
        response: 401
    )]
    #[\OpenApi\Attributes\Response(
        response: 404,
        description: 'Contract or service not found'
    )]
    public function show(int $_contractId, ContractService $contractService): ContractServiceResource
    {
        return new ContractServiceResource($contractService);
    }

    #[Post(
        path: '/contracts/{contractId}/services/{serviceId}/update',
        summary: 'Update service data',
        security: [['bearerAuth' => []]],
        requestBody: new RequestBody(
            content: new MediaType(
                mediaType: 'multipart/form-data',
                schema: new Schema(ref: UpdateCommand::class)
            )
        ),
        tags: ['Contracts/Services'],
        parameters: [
            new PathParameter(ref: '#components/parameters/contractId'),
            new PathParameter(ref: '#components/parameters/serviceId')
        ]
    )]
    #[\OpenApi\Attributes\Response(
        response: 204,
        description: 'The service has been successfully updated in the contract'
    )]
    #[\OpenApi\Attributes\Response(
        ref: InvalidTokenResponse::class,
        response: 401
    )]
    #[\OpenApi\Attributes\Response(
        response: 404,
        description: 'Contract or service not found'
    )]
    #[\OpenApi\Attributes\Response(
        response: 422,
        description: 'Validation error',
        content: new JsonContent(ref: LimitValidationErrorResponse::class)
    )]
    /**
     * @throws InUse
     */
    public function update(UpdateCommand $command, UpdateHandler $handler): Response
    {
        $handler->handle($command);

        return response()->noContent();
    }

    #[Delete(
        path: '/contracts/{contractId}/services/{serviceId}',
        summary: 'Remove a service from a contract',
        security: [['bearerAuth' => []]],
        tags: ['Contracts/Services'],
        parameters: [
            new PathParameter(ref: '#components/parameters/contractId'),
            new PathParameter(ref: '#components/parameters/serviceId')
        ]
    )]
    #[\OpenApi\Attributes\Response(
        response: 204,
        description: 'The service was successfully removed from the contract'
    )]
    #[\OpenApi\Attributes\Response(
        ref: InvalidTokenResponse::class,
        response: 401
    )]
    #[\OpenApi\Attributes\Response(
        response: 404,
        description: 'Contract or service not found'
    )]
    public function destroy(DeleteCommand $command, DeleteHandler $handler): Response
    {
        $handler->handle($command);

        return response()->noContent();
    }
}
