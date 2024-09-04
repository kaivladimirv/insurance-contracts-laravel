<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\InUse;
use App\Facades\Auth;
use App\Http\Requests\Contract\IndexContractRequest;
use App\Http\Requests\Contract\ShowProvidedServicesRequest;
use App\Http\Resources\ContractResource;
use App\Http\Resources\ProvidedServiceResource;
use App\Models\Contract;
use App\ReadModels\ContractFetcher;
use App\ReadModels\ProvidedServiceFetcher;
use App\Swagger\Responses\CollectionResponse;
use App\Swagger\Responses\Contract\ValidationErrorResponse;
use App\Swagger\Responses\InvalidTokenResponse;
use App\UseCases\Contract\Add\AddCommand;
use App\UseCases\Contract\Add\AddHandler;
use App\UseCases\Contract\Delete\DeleteCommand;
use App\UseCases\Contract\Delete\DeleteHandler;
use App\UseCases\Contract\Update\UpdateCommand;
use App\UseCases\Contract\Update\UpdateHandler;
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

class ContractController extends Controller
{
    private const int LIMIT = 30;

    #[Get(
        path: '/contracts',
        summary: 'Get contract list',
        security: [['bearerAuth' => []]],
        tags: ['Contracts'],
        parameters: [
            new QueryParameter(ref: '#components/parameters/pageNumber'),
            new QueryParameter(ref: '#components/parameters/contractNumber'),
            new QueryParameter(ref: '#components/parameters/periodFrom'),
            new QueryParameter(ref: '#components/parameters/periodTo'),
            new QueryParameter(ref: '#components/parameters/maxAmountFrom'),
            new QueryParameter(ref: '#components/parameters/maxAmountTo')
        ]
    )]
    #[\OpenApi\Attributes\Response(
        response: 200,
        description: 'OK',
        content: new JsonContent(
            properties: [
                new Property(property: 'data', type: 'array', items: new Items(ref: ContractResource::class))
            ],
            allOf: [new Schema(ref: CollectionResponse::class)]
        )
    )]
    #[\OpenApi\Attributes\Response(
        ref: InvalidTokenResponse::class,
        response: 401
    )]
    public function index(IndexContractRequest $request, ContractFetcher $fetcher): AnonymousResourceCollection
    {
        $contracts = $fetcher->get(
            Auth::company()->id,
            self::LIMIT,
            (int)$request->validated('page'),
            $request->validated()
        );

        return ContractResource::collection($contracts);
    }

    #[Post(
        path: '/contracts',
        summary: 'Add contract',
        security: [['bearerAuth' => []]],
        requestBody: new RequestBody(
            content: new MediaType(
                mediaType: 'multipart/form-data',
                schema: new Schema(ref: AddCommand::class)
            )
        ),
        tags: ['Contracts']
    )]
    #[\OpenApi\Attributes\Response(
        response: 200,
        description: 'The contract has been successfully created. Returns contract id',
        content: new JsonContent(properties: [new Property(property: 'id', description: 'Contract id', type: 'integer', example: 256)])
    )]
    #[\OpenApi\Attributes\Response(
        ref: InvalidTokenResponse::class,
        response: 401
    )]
    #[\OpenApi\Attributes\Response(
        response: 422,
        description: 'Validation error',
        content: new JsonContent(ref: ValidationErrorResponse::class)
    )]
    public function store(AddCommand $command, AddHandler $handler): JsonResponse
    {
        $contractId = $handler->handle($command);

        return response()->json(['id' => $contractId]);
    }


    #[Get(
        path: '/contracts/{contractId}',
        summary: 'Get contract data',
        security: [['bearerAuth' => []]],
        tags: ['Contracts'],
        parameters: [
            new PathParameter(ref: '#components/parameters/contractId')
        ]
    )]
    #[\OpenApi\Attributes\Response(
        response: 200,
        description: 'OK',
        content: new JsonContent(ref: ContractResource::class)
    )]
    #[\OpenApi\Attributes\Response(
        ref: InvalidTokenResponse::class,
        response: 401
    )]
    #[\OpenApi\Attributes\Response(
        response: 404,
        description: 'Contract not found'
    )]
    public function show(Contract $contract): ContractResource
    {
        return new ContractResource($contract);
    }

    #[Post(
        path: '/contracts/{contractId}/update',
        summary: 'Update contract data',
        security: [['bearerAuth' => []]],
        requestBody: new RequestBody(
            content: new MediaType(
                mediaType: 'multipart/form-data',
                schema: new Schema(ref: UpdateCommand::class)
            )
        ),
        tags: ['Contracts'],
        parameters: [
            new PathParameter(ref: '#components/parameters/contractId')
        ]
    )]
    #[\OpenApi\Attributes\Response(
        response: 204,
        description: 'The contract has been successfully updated'
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
        content: new JsonContent(ref: ValidationErrorResponse::class)
    )]
    public function update(UpdateCommand $command, UpdateHandler $handler): Response
    {
        $handler->handle($command);

        return response()->noContent();
    }

    #[Delete(
        path: '/contracts/{contractId}',
        summary: 'Delete contract',
        security: [['bearerAuth' => []]],
        tags: ['Contracts'],
        parameters: [
            new PathParameter(ref: '#components/parameters/contractId')
        ]
    )]
    #[\OpenApi\Attributes\Response(
        response: 204,
        description: 'The contract has been successfully deleted'
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
        response: 409,
        description: 'Services were provided under the contract'
    )]
    /**
     * @throws InUse
     */
    public function destroy(DeleteCommand $command, DeleteHandler $handler): Response
    {
        $handler->handle($command);

        return response()->noContent();
    }

    #[Get(
        path: '/contracts/{contractId}/provided_services',
        summary: 'Get a list of services that were provided under the contract',
        security: [['bearerAuth' => []]],
        tags: ['Contracts'],
        parameters: [
            new PathParameter(ref: '#components/parameters/contractId'),
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
            new QueryParameter(ref: '#components/parameters/maxAmountFrom'),
            new QueryParameter(ref: '#components/parameters/maxAmountTo')
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
        description: 'Contract not found'
    )]
    public function showProvidedServices(ShowProvidedServicesRequest $request, Contract $contract, ProvidedServiceFetcher $fetcher): AnonymousResourceCollection
    {
        $providedServices = $fetcher->getByContract(
            $contract->id,
            self::LIMIT,
            (int)$request->validated('page'),
            $request->validated()
        );

        return ProvidedServiceResource::collection($providedServices);
    }
}
