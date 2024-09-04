<?php

declare(strict_types=1);

namespace App\ReadModels;

use App\Models\Person;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PersonFetcher
{
    public function get(int $companyId, int $limit, int $page, array $filter): LengthAwarePaginator
    {
        return $this->builder($filter)
            ->where('company_id', '=', $companyId)
            ->paginate($limit, ['*'], 'page', $page);
    }

    public function getOne(int $companyId, int $personId): Model|Builder|Person
    {
        return Person::query()
            ->where('company_id', '=', $companyId)
            ->where('id', '=', $personId)
            ->firstOrFail();
    }

    public function getOneByInviteToken(string $token): Model|Builder|Person
    {
        return Person::query()
            ->where('telegram_chat_invite_token', '=', $token)
            ->firstOrFail();
    }

    public function getOneByTelegramChaId(string $chatId): Model|Builder|Person
    {
        return Person::query()
            ->where('telegram_chat_id', '=', $chatId)
            ->firstOrFail();
    }

    private function builder(array $filter): Builder
    {
        $builder = Person::query();

        if (isset($filter['last_name'])) {
            $builder->where('last_name', 'LIKE', $filter['last_name'] . '%');
        }

        if (isset($filter['first_name'])) {
            $builder->where('first_name', 'LIKE', $filter['first_name'] . '%');
        }

        if (isset($filter['middle_name'])) {
            $builder->where('middle_name', 'LIKE', $filter['middle_name'] . '%');
        }

        if (isset($filter['email'])) {
            $builder->where('email', '=', mb_strtolower((string)$filter['email']));
        }

        if (isset($filter['phone_number'])) {
            $builder->where('phone_number', '=', $filter['phone_number']);
        }

        if (array_key_exists('notifier_type', $filter)) {
            if (is_null($filter['notifier_type'])) {
                $builder->whereNull('notifier_type');
            } else {
                $builder->where('notifier_type', '=', $filter['notifier_type']);
            }
        }

        return $builder
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->orderBy('middle_name');
    }
}
