<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\InUse;
use App\Facades\Auth;
use App\Http\Requests\Person\IndexPersonRequest;
use App\Http\Resources\PersonResource;
use App\Models\Person;
use App\ReadModels\PersonFetcher;
use App\Swagger\Responses\CollectionResponse;
use App\Swagger\Responses\InvalidTokenResponse;
use App\Swagger\Responses\Person\ValidationErrorResponse;
use App\UseCases\Person\Add\AddCommand;
use App\UseCases\Person\Add\AddHandler;
use App\UseCases\Person\Delete\DeleteCommand;
use App\UseCases\Person\Delete\DeleteHandler;
use App\UseCases\Person\Update\UpdateCommand;
use App\UseCases\Person\Update\UpdateHandler;
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

class PersonController extends Controller
{
    private const int LIMIT = 30;

    #[Get(
        path: '/persons',
        summary: 'Get person list',
        security: [['bearerAuth' => []]],
        tags: ['Persons'],
        parameters: [
            new QueryParameter(ref: '#components/parameters/pageNumber'),
            new QueryParameter(ref: '#components/parameters/lastName'),
            new QueryParameter(ref: '#components/parameters/firstName'),
            new QueryParameter(ref: '#components/parameters/middleName'),
            new QueryParameter(ref: '#components/parameters/email'),
            new QueryParameter(ref: '#components/parameters/phoneNumber'),
            new QueryParameter(ref: '#components/parameters/notifierType')
        ]
    )]
    #[\OpenApi\Attributes\Response(
        response: 200,
        description: 'OK',
        content: new JsonContent(
            properties: [
                new Property(property: 'data', type: 'array', items: new Items(ref: PersonResource::class))
            ],
            allOf: [new Schema(ref: CollectionResponse::class)]
        )
    )]
    #[\OpenApi\Attributes\Response(
        ref: InvalidTokenResponse::class,
        response: 401
    )]
    public function index(IndexPersonRequest $request, PersonFetcher $fetcher): AnonymousResourceCollection
    {
        $persons = $fetcher->get(
            Auth::company()->id,
            self::LIMIT,
            (int)$request->validated('page'),
            $request->validated()
        );

        return PersonResource::collection($persons);
    }

    #[Post(
        path: '/persons',
        summary: 'Add person',
        security: [['bearerAuth' => []]],
        requestBody: new RequestBody(
            content: new MediaType(
                mediaType: 'multipart/form-data',
                schema: new Schema(ref: AddCommand::class)
            )
        ),
        tags: ['Persons']
    )]
    #[\OpenApi\Attributes\Response(
        response: 200,
        description: 'The person has been successfully added. Returns person id',
        content: new JsonContent(properties: [new Property(property: 'id', description: 'Person id', type: 'integer', example: 345)])
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
        $personId = $handler->handle($command);

        return response()->json(['id' => $personId]);
    }

    #[Get(
        path: '/persons/{personId}',
        summary: 'Get service data',
        security: [['bearerAuth' => []]],
        tags: ['Persons'],
        parameters: [
            new PathParameter(ref: '#components/parameters/personId')
        ]
    )]
    #[\OpenApi\Attributes\Response(
        response: 200,
        description: 'OK',
        content: new JsonContent(ref: PersonResource::class)
    )]
    #[\OpenApi\Attributes\Response(
        ref: InvalidTokenResponse::class,
        response: 401
    )]
    #[\OpenApi\Attributes\Response(
        response: 404,
        description: 'Person not found'
    )]
    public function show(Person $person): PersonResource
    {
        return new PersonResource($person);
    }

    #[Post(
        path: '/persons/{personId}/update',
        summary: 'Update person data',
        security: [['bearerAuth' => []]],
        requestBody: new RequestBody(
            content: new MediaType(
                mediaType: 'multipart/form-data',
                schema: new Schema(ref: UpdateCommand::class)
            )
        ),
        tags: ['Persons'],
        parameters: [
            new PathParameter(ref: '#components/parameters/personId')
        ]
    )]
    #[\OpenApi\Attributes\Response(
        response: 204,
        description: 'The person has been successfully updated'
    )]
    #[\OpenApi\Attributes\Response(
        ref: InvalidTokenResponse::class,
        response: 401
    )]
    #[\OpenApi\Attributes\Response(
        response: 404,
        description: 'Person not found'
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
        path: '/persons/{personId}',
        summary: 'Delete person',
        security: [['bearerAuth' => []]],
        tags: ['Persons'],
        parameters: [
            new PathParameter(ref: '#components/parameters/personId')
        ]
    )]
    #[\OpenApi\Attributes\Response(
        response: 204,
        description: 'The person has been successfully deleted'
    )]
    #[\OpenApi\Attributes\Response(
        ref: InvalidTokenResponse::class,
        response: 401
    )]
    #[\OpenApi\Attributes\Response(
        response: 404,
        description: 'Person not found'
    )]
    #[\OpenApi\Attributes\Response(
        response: 409,
        description: 'The person is the insured person'
    )]
    /**
     * @throws InUse
     */
    public function destroy(DeleteCommand $command, DeleteHandler $handler): Response
    {
        $handler->handle($command);

        return response()->noContent();
    }
}
