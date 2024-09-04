<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Request;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\CompanyTokenResource;
use App\Swagger\Responses\Company\EmailValidationErrorResponse;
use App\Swagger\Responses\Company\NameValidationErrorResponse;
use App\Swagger\Responses\Company\PasswordValidationErrorResponse;
use App\Swagger\Responses\InvalidTokenResponse;
use App\UseCases\Company\ChangeEmail\ChangeEmailCommand;
use App\UseCases\Company\ChangeEmail\ChangeEmailHandler;
use App\UseCases\Company\ChangePassword\ChangePasswordCommand;
use App\UseCases\Company\ChangePassword\ChangePasswordHandler;
use App\UseCases\Company\Confirm\ConfirmCommand;
use App\UseCases\Company\Confirm\ConfirmHandler;
use App\UseCases\Company\ConfirmEmailChange\ConfirmEmailChangeCommand;
use App\UseCases\Company\ConfirmEmailChange\ConfirmEmailChangeHandler;
use App\UseCases\Company\CreateToken\CreateTokenCommand;
use App\UseCases\Company\CreateToken\CreateTokenHandler;
use App\UseCases\Company\Register\RegisterCommand;
use App\UseCases\Company\Register\RegisterHandler;
use App\UseCases\Company\Update\UpdateCommand;
use App\UseCases\Company\Update\UpdateHandler;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\MediaType;
use OpenApi\Attributes\Patch;
use OpenApi\Attributes\PathParameter;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\RequestBody;
use OpenApi\Attributes\Schema;

class CompanyController extends Controller
{
    #[Post(
        path: '/company',
        summary: 'Registers a company',
        requestBody: new RequestBody(
            content: new MediaType(
                mediaType: 'multipart/form-data',
                schema: new Schema(ref: RegisterCommand::class)
            )
        ),
        tags: ['Company']
    )]
    #[\OpenApi\Attributes\Response(
        response: 204,
        description: 'Company successfully registered'
    )]
    #[\OpenApi\Attributes\Response(
        response: 422,
        description: 'Validation error',
        content: new JsonContent(
            allOf: [
                new Schema(ref: NameValidationErrorResponse::class),
                new Schema(ref: EmailValidationErrorResponse::class),
                new Schema(ref: PasswordValidationErrorResponse::class)
            ],
        )
    )]
    public function register(RegisterCommand $command, RegisterHandler $handler): Response
    {
        $handler->handle($command);

        return response()->noContent();
    }

    #[Patch(
        path: '/company/confirm/{emailConfirmToken}',
        summary: 'Company confirmation',
        tags: ['Company'],
        parameters: [
            new PathParameter(ref: '#components/parameters/emailConfirmToken')
        ]
    )]
    #[\OpenApi\Attributes\Response(
        response: 204,
        description: 'Company is confirmed'
    )]
    #[\OpenApi\Attributes\Response(
        response: 404,
        description: 'Token not found'
    )]
    public function confirm(ConfirmCommand $command, ConfirmHandler $handler): Response
    {
        $handler->handle($command);

        return response()->noContent();
    }

    #[Patch(
        path: '/company/token',
        summary: 'Get access token',
        security: [['basicAuth' => []]],
        tags: ['Company']
    )]
    #[\OpenApi\Attributes\Response(
        response: 200,
        description: 'OK',
        content: new JsonContent(ref: CompanyTokenResource::class)
    )]
    #[\OpenApi\Attributes\Response(
        response: 401,
        description: 'Invalid username or password'
    )]
    public function token(Request $request, CreateTokenHandler $handler): CompanyTokenResource
    {
        $command = new CreateTokenCommand($request->company()->id);

        $token = $handler->handle($command);

        return new CompanyTokenResource($token);
    }

    #[Get(
        path: '/company',
        summary: 'Get company data',
        security: [['bearerAuth' => []]],
        tags: ['Company']
    )]
    #[\OpenApi\Attributes\Response(
        response: 200,
        description: 'OK',
        content: new JsonContent(ref: CompanyResource::class)
    )]
    #[\OpenApi\Attributes\Response(ref: InvalidTokenResponse::class, response: 401)]
    public function show(Request $request): CompanyResource
    {
        return new CompanyResource($request->company());
    }

    #[Post(
        path: '/company/update',
        summary: 'Update company data',
        security: [['bearerAuth' => []]],
        requestBody: new RequestBody(
            content: new MediaType(
                mediaType: 'multipart/form-data',
                schema: new Schema(ref: UpdateCommand::class)
            )
        ),
        tags: ['Company']
    )]
    #[\OpenApi\Attributes\Response(
        response: 204,
        description: 'Company changed successfully'
    )]
    #[\OpenApi\Attributes\Response(ref: InvalidTokenResponse::class, response: 401)]
    #[\OpenApi\Attributes\Response(
        response: 422,
        description: 'Validation error',
        content: new JsonContent(ref: NameValidationErrorResponse::class)
    )]
    /**
     * @throws ValidationException
     */
    public function update(UpdateCommand $command, UpdateHandler $handler): Response
    {
        $handler->handle($command);

        return response()->noContent();
    }

    #[Post(
        path: '/company/password',
        summary: 'Change password',
        security: [['basicAuth' => []]],
        requestBody: new RequestBody(
            content: new MediaType(
                mediaType: 'multipart/form-data',
                schema: new Schema(ref: ChangePasswordCommand::class)
            )
        ),
        tags: ['Company']
    )]
    #[\OpenApi\Attributes\Response(
        response: 204,
        description: 'Password changed successfully'
    )]
    #[\OpenApi\Attributes\Response(
        response: 401,
        description: 'Invalid username or password'
    )]
    #[\OpenApi\Attributes\Response(
        response: 422,
        description: 'Validation error',
        content: new JsonContent(ref: PasswordValidationErrorResponse::class)
    )]
    /**
     * @throws ValidationException
     */
    public function changePassword(ChangePasswordCommand $command, ChangePasswordHandler $handler): Response
    {
        $handler->handle($command);

        return response()->noContent();
    }

    #[Post(
        path: '/company/email',
        summary: 'Change email',
        security: [['basicAuth' => []]],
        requestBody: new RequestBody(
            content: new MediaType(
                mediaType: 'multipart/form-data',
                schema: new Schema(ref: ChangeEmailCommand::class)
            )
        ),
        tags: ['Company']
    )]
    #[\OpenApi\Attributes\Response(
        response: 204,
        description: 'Email changed successfully'
    )]
    #[\OpenApi\Attributes\Response(
        response: 401,
        description: 'Invalid username or password'
    )]
    #[\OpenApi\Attributes\Response(
        response: 422,
        description: 'Validation error',
        content: new JsonContent(ref: EmailValidationErrorResponse::class)
    )]
    /**
     * @throws ValidationException
     */
    public function changeEmail(ChangeEmailCommand $command, ChangeEmailHandler $handler): Response
    {
        $handler->handle($command);

        return response()->noContent();
    }

    #[Patch(
        path: '/company/email/confirm/{newEmailConfirmToken}',
        summary: 'Confirm new email',
        security: [['basicAuth' => []]],
        tags: ['Company'],
        parameters: [
            new PathParameter(ref: '#components/parameters/newEmailConfirmToken')
        ]
    )]
    #[\OpenApi\Attributes\Response(
        response: 204,
        description: 'New email confirmed'
    )]
    #[\OpenApi\Attributes\Response(
        response: 401,
        description: 'Invalid username or password'
    )]
    #[\OpenApi\Attributes\Response(
        response: 404,
        description: 'New email confirm token not found'
    )]
    public function confirmEmailChange(ConfirmEmailChangeCommand $command, ConfirmEmailChangeHandler $handler): Response
    {
        $handler->handle($command);

        return response()->noContent();
    }
}
