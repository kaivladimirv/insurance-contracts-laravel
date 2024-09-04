<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\InUse;
use App\Http\Requests\InsuredPerson\IndexInsuredPersonRequest;
use App\Http\Resources\BalanceResource;
use App\Http\Resources\InsuredPersonResource;
use App\Models\InsuredPerson;
use App\ReadModels\BalanceFetcher;
use App\ReadModels\InsuredPersonFetcher;
use App\Swagger\Responses\CollectionResponse;
use App\Swagger\Responses\InsuredPerson\PersonIdValidationErrorResponse;
use App\Swagger\Responses\InsuredPerson\ValidationErrorResponse;
use App\Swagger\Responses\InvalidTokenResponse;
use App\UseCases\InsuredPerson\Add\AddCommand;
use App\UseCases\InsuredPerson\Add\AddHandler;
use App\UseCases\InsuredPerson\Delete\DeleteCommand;
use App\UseCases\InsuredPerson\Delete\DeleteHandler;
use App\UseCases\InsuredPerson\Update\UpdateCommand;
use App\UseCases\InsuredPerson\Update\UpdateHandler;
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

class InsuredPersonController extends Controller
{
    private const int LIMIT = 30;

    #[Get(
        path: '/contracts/{contractId}/insured_persons',
        summary: 'Get insured persons',
        security: [['bearerAuth' => []]],
        tags: ['Contracts/Insured persons'],
        parameters: [
            new PathParameter(ref: '#components/parameters/contractId'),
            new QueryParameter(ref: '#components/parameters/pageNumber'),
            new QueryParameter(ref: '#components/parameters/policyNumber'),
            new QueryParameter(ref: '#components/parameters/isAllowedToExceedLimit')
        ]
    )]
    #[\OpenApi\Attributes\Response(
        response: 200,
        description: 'OK',
        content: new JsonContent(
            properties: [
                new Property(property: 'data', type: 'array', items: new Items(ref: InsuredPersonResource::class))
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
    public function index(IndexInsuredPersonRequest $request, int $contractId, InsuredPersonFetcher $fetcher): AnonymousResourceCollection
    {
        $insuredPersons = $fetcher->get(
            $contractId,
            self::LIMIT,
            (int)$request->validated('page'),
            $request->validated()
        );

        return InsuredPersonResource::collection($insuredPersons);
    }

    #[Post(
        path: '/contracts/{contractId}/insured_persons',
        summary: 'Add insured person to contract',
        security: [['bearerAuth' => []]],
        requestBody: new RequestBody(
            content: new MediaType(
                mediaType: 'multipart/form-data',
                schema: new Schema(ref: AddCommand::class)
            )
        ),
        tags: ['Contracts/Insured persons'],
        parameters: [
            new PathParameter(ref: '#components/parameters/contractId')
        ]
    )]
    #[\OpenApi\Attributes\Response(
        response: 200,
        description: 'The insured person has been successfully added to the contract. Returns insured person id',
        content: new JsonContent(properties: [new Property(property: 'id', description: 'Insured person id', type: 'integer', example: 102)])
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
            new Schema(ref: PersonIdValidationErrorResponse::class),
            new Schema(ref: ValidationErrorResponse::class),
        ])
    )]
    public function store(AddCommand $command, AddHandler $handler): JsonResponse
    {
        $insuredPersonId = $handler->handle($command);

        return response()->json(['id' => $insuredPersonId]);
    }

    #[Get(
        path: '/contracts/{contractId}/insured_persons/{insuredPersonId}',
        summary: 'Get insured person data',
        security: [['bearerAuth' => []]],
        tags: ['Contracts/Insured persons'],
        parameters: [
            new PathParameter(ref: '#components/parameters/contractId'),
            new PathParameter(ref: '#components/parameters/insuredPersonId')
        ]
    )]
    #[\OpenApi\Attributes\Response(
        response: 200,
        description: 'OK',
        content: new JsonContent(ref: InsuredPersonResource::class)
    )]
    #[\OpenApi\Attributes\Response(
        ref: InvalidTokenResponse::class,
        response: 401
    )]
    #[\OpenApi\Attributes\Response(
        response: 404,
        description: 'Contract or insured person not found'
    )]
    public function show(int $_contractId, InsuredPerson $insuredPerson): InsuredPersonResource
    {
        return new InsuredPersonResource($insuredPerson);
    }

    #[Post(
        path: '/contracts/{contractId}/insured_persons/{insuredPersonId}/update',
        summary: 'Update insured person data',
        security: [['bearerAuth' => []]],
        requestBody: new RequestBody(
            content: new MediaType(
                mediaType: 'multipart/form-data',
                schema: new Schema(ref: UpdateCommand::class)
            )
        ),
        tags: ['Contracts/Insured persons'],
        parameters: [
            new PathParameter(ref: '#components/parameters/contractId'),
            new PathParameter(ref: '#components/parameters/insuredPersonId')
        ]
    )]
    #[\OpenApi\Attributes\Response(
        response: 204,
        description: 'The insured person has been successfully updated in the contract'
    )]
    #[\OpenApi\Attributes\Response(
        ref: InvalidTokenResponse::class,
        response: 401
    )]
    #[\OpenApi\Attributes\Response(
        response: 404,
        description: 'Contract or insured person not found'
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
        path: '/contracts/{contractId}/insured_persons/{insuredPersonId}',
        summary: 'Remove a insured person from a contract',
        security: [['bearerAuth' => []]],
        tags: ['Contracts/Insured persons'],
        parameters: [
            new PathParameter(ref: '#components/parameters/contractId'),
            new PathParameter(ref: '#components/parameters/insuredPersonId')
        ]
    )]
    #[\OpenApi\Attributes\Response(
        response: 204,
        description: 'The insured person was successfully removed from the contract'
    )]
    #[\OpenApi\Attributes\Response(
        ref: InvalidTokenResponse::class,
        response: 401
    )]
    #[\OpenApi\Attributes\Response(
        response: 404,
        description: 'Contract or insured person not found'
    )]
    #[\OpenApi\Attributes\Response(
        response: 409,
        description: 'Services have already been provided to the insured person'
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
        path: '/contracts/{contractId}/insured_persons/{insuredPersonId}/balance',
        summary: 'Get balance for the insured person',
        security: [['bearerAuth' => []]],
        tags: ['Contracts/Insured persons'],
        parameters: [
            new PathParameter(ref: '#components/parameters/contractId'),
            new PathParameter(ref: '#components/parameters/insuredPersonId')
        ]
    )]
    #[\OpenApi\Attributes\Response(
        response: 200,
        description: 'OK',
        content: new JsonContent(ref: BalanceResource::class)
    )]
    #[\OpenApi\Attributes\Response(
        ref: InvalidTokenResponse::class,
        response: 401
    )]
    #[\OpenApi\Attributes\Response(
        response: 404,
        description: 'Contract or insured person not found'
    )]
    public function showBalance(int $_contractId, InsuredPerson $insuredPerson, BalanceFetcher $fetcher): AnonymousResourceCollection
    {
        $balances = $fetcher->getByInsuredPerson($insuredPerson->id);

        return BalanceResource::collection($balances);
    }
}
