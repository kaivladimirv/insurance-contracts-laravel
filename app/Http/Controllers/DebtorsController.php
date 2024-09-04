<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\DebtorsRequest;
use App\Http\Resources\DebtorResource;
use App\Models\Contract;
use App\ReadModels\DebtorFetcher;
use App\Swagger\Responses\CollectionResponse;
use App\Swagger\Responses\Debtors\ValidationErrorResponse;
use App\Swagger\Responses\InvalidTokenResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\Items;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\QueryParameter;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Response;
use OpenApi\Attributes\Schema;

class DebtorsController extends Controller
{
    private const int LIMIT = 30;

    #[Get(
        path: '/debtors',
        summary: 'Get debtors by company',
        security: [['bearerAuth' => []]],
        tags: ['Debtors'],
        parameters: [
            new QueryParameter(ref: '#components/parameters/pageNumber'),
            new QueryParameter(ref: '#components/parameters/serviceIdInQuery'),
            new QueryParameter(ref: '#components/parameters/debtFrom'),
            new QueryParameter(ref: '#components/parameters/debtTo')
        ]
    )]
    #[Response(
        response: 200,
        description: 'OK',
        content: new JsonContent(
            properties: [
                new Property(
                    property: 'data',
                    type: 'array',
                    items: new Items(ref: DebtorResource::class)
                )],
            allOf: [new Schema(ref: CollectionResponse::class)]
        )
    )]
    #[Response(
        ref: InvalidTokenResponse::class,
        response: 401
    )]
    #[Response(
        response: 422,
        description: 'Validation error',
        content: new JsonContent(ref: ValidationErrorResponse::class)
    )]
    public function index(DebtorsRequest $request, DebtorFetcher $fetcher): AnonymousResourceCollection
    {
        $data = $fetcher->getAllByCompanyId(
            $request->company()->id,
            self::LIMIT,
            (int)$request->validated('page'),
            $request->validated()
        );

        return DebtorResource::collection($data);
    }

    #[Get(
        path: '/debtors/{contractId}',
        summary: 'Get debtors by contract',
        security: [['bearerAuth' => []]],
        tags: ['Debtors'],
        parameters: [
            new QueryParameter(ref: '#components/parameters/contractId'),
            new QueryParameter(ref: '#components/parameters/pageNumber'),
            new QueryParameter(ref: '#components/parameters/serviceIdInQuery'),
            new QueryParameter(ref: '#components/parameters/debtFrom'),
            new QueryParameter(ref: '#components/parameters/debtTo')
        ]
    )]
    #[Response(
        response: 200,
        description: 'OK',
        content: new JsonContent(
            properties: [
                new Property(
                    property: 'data',
                    type: 'array',
                    items: new Items(ref: DebtorResource::class)
                )],
            allOf: [new Schema(ref: CollectionResponse::class)]
        )
    )]
    #[Response(
        ref: InvalidTokenResponse::class,
        response: 401
    )]
    #[Response(
        response: 404,
        description: 'Contract not found',
    )]
    #[Response(
        response: 422,
        description: 'Validation error',
        content: new JsonContent(ref: ValidationErrorResponse::class)
    )]
    public function indexByContract(DebtorsRequest $request, Contract $contract, DebtorFetcher $fetcher): AnonymousResourceCollection
    {
        $data = $fetcher->getAllByContractId(
            $contract->id,
            self::LIMIT,
            (int)$request->validated('page'),
            $request->validated()
        );

        return DebtorResource::collection($data);
    }
}
