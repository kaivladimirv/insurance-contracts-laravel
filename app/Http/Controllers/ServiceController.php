<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\InUse;
use App\Facades\Auth;
use App\Http\Requests\Service\IndexServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use App\ReadModels\ServiceFetcher;
use App\Swagger\Responses\CollectionResponse;
use App\Swagger\Responses\Service\ServiceNameValidationErrorResponse;
use App\Swagger\Responses\InvalidTokenResponse;
use App\UseCases\Service\Add\AddCommand;
use App\UseCases\Service\Add\AddHandler;
use App\UseCases\Service\Delete\DeleteCommand;
use App\UseCases\Service\Delete\DeleteHandler;
use App\UseCases\Service\Update\UpdateCommand;
use App\UseCases\Service\Update\UpdateHandler;
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

class ServiceController extends Controller
{
    private const int LIMIT = 30;

    #[Get(
        path: '/services',
        summary: 'Get service list',
        security: [['bearerAuth' => []]],
        tags: ['Services'],
        parameters: [
            new QueryParameter(ref: '#components/parameters/pageNumber'),
            new QueryParameter(ref: '#components/parameters/serviceName')
        ]
    )]
    #[\OpenApi\Attributes\Response(
        response: 200,
        description: 'OK',
        content: new JsonContent(
            properties: [
                new Property(
                    property: 'data',
                    type: 'array',
                    items: new Items(ref: ServiceResource::class)
                )],
            allOf: [new Schema(ref: CollectionResponse::class)]
        )
    )]
    #[\OpenApi\Attributes\Response(
        ref: InvalidTokenResponse::class,
        response: 401
    )]
    public function index(IndexServiceRequest $request, ServiceFetcher $fetcher): AnonymousResourceCollection
    {
        $services = $fetcher->get(
            Auth::company()->id,
            self::LIMIT,
            (int)$request->validated('page'),
            $request->validated()
        );

        return ServiceResource::collection($services);
    }

    #[Post(
        path: '/services',
        summary: 'Add service',
        security: [['bearerAuth' => []]],
        requestBody: new RequestBody(
            content: new MediaType(
                mediaType: 'multipart/form-data',
                schema: new Schema(ref: AddCommand::class)
            )
        ),
        tags: ['Services']
    )]
    #[\OpenApi\Attributes\Response(
        response: 200,
        description: 'The service has been successfully added. Returns service id'
    )]
    #[\OpenApi\Attributes\Response(
        ref: InvalidTokenResponse::class,
        response: 401
    )]
    #[\OpenApi\Attributes\Response(
        response: 422,
        description: 'Validation error',
        content: new JsonContent(ref: ServiceNameValidationErrorResponse::class)
    )]
    public function store(AddCommand $command, AddHandler $handler): JsonResponse
    {
        $serviceId = $handler->handle($command);

        return response()->json(['id' => $serviceId]);
    }

    #[Get(
        path: '/services/{serviceId}',
        summary: 'Get service data',
        security: [['bearerAuth' => []]],
        tags: ['Services'],
        parameters: [
            new PathParameter(ref: '#components/parameters/serviceId')
        ]
    )]
    #[\OpenApi\Attributes\Response(
        response: 200,
        description: 'OK',
        content: new JsonContent(ref: ServiceResource::class)
    )]
    #[\OpenApi\Attributes\Response(
        ref: InvalidTokenResponse::class,
        response: 401
    )]
    #[\OpenApi\Attributes\Response(
        response: 404,
        description: 'Service not found'
    )]
    public function show(Service $service): ServiceResource
    {
        return new ServiceResource($service);
    }

    #[Post(
        path: '/services/{serviceId}/update',
        summary: 'Update service data',
        security: [['bearerAuth' => []]],
        requestBody: new RequestBody(
            content: new MediaType(
                mediaType: 'multipart/form-data',
                schema: new Schema(ref: UpdateCommand::class)
            )
        ),
        tags: ['Services'],
        parameters: [
            new PathParameter(ref: '#components/parameters/serviceId')
        ]
    )]
    #[\OpenApi\Attributes\Response(
        response: 204,
        description: 'The service has been successfully updated'
    )]
    #[\OpenApi\Attributes\Response(
        ref: InvalidTokenResponse::class,
        response: 401
    )]
    #[\OpenApi\Attributes\Response(
        response: 404,
        description: 'Service not found'
    )]
    #[\OpenApi\Attributes\Response(
        response: 422,
        description: 'Validation error',
        content: new JsonContent(ref: ServiceNameValidationErrorResponse::class)
    )]
    public function update(UpdateCommand $command, UpdateHandler $handler): Response
    {
        $handler->handle($command);

        return response()->noContent();
    }

    #[Delete(
        path: '/services/{serviceId}',
        summary: 'Delete service',
        security: [['bearerAuth' => []]],
        tags: ['Services'],
        parameters: [
            new PathParameter(ref: '#components/parameters/serviceId')
        ]
    )]
    #[\OpenApi\Attributes\Response(
        response: 204,
        description: 'The service has been successfully deleted'
    )]
    #[\OpenApi\Attributes\Response(
        ref: InvalidTokenResponse::class,
        response: 401
    )]
    #[\OpenApi\Attributes\Response(
        response: 404,
        description: 'Service not found'
    )]
    #[\OpenApi\Attributes\Response(
        response: 409,
        description: 'The service is used in contracts'
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
