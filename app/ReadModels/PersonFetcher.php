<?php

declare(strict_types=1);

namespace App\ReadModels;

use App\Models\Person;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class PersonFetcher
{
    public function get(int $companyId, int $limit, int $page, array $filter): LengthAwarePaginator
    {
        return $this->builder($filter)
            ->where('company_id', '=', $companyId)
            ->paginate($limit, ['*'], 'page', $page);
    }

    public function getOne(int $companyId, int $personId): Person
    {
        return Person::query()
            ->where('company_id', '=', $companyId)
            ->where('id', '=', $personId)
            ->firstOrFail();
    }

    public function getOneByInviteToken(string $token): Person
    {
        return Person::query()
            ->where('telegram_chat_invite_token', '=', $token)
            ->firstOrFail();
    }

    public function getOneByTelegramChaId(string $chatId): Person
    {
        return Person::query()
            ->where('telegram_chat_id', '=', $chatId)
            ->firstOrFail();
    }

    public function getOneByInsuredPersonId(int $insuredPersonId): Person
    {
        return Person::query()
            ->select('persons.*')
            ->join('insured_persons', 'insured_persons.person_id', '=', 'persons.id')
            ->where('insured_persons.id', '=', $insuredPersonId)
            ->firstOrFail();
    }

    private function builder(array $filter): Builder
    {
        $builder = Person::query();

        foreach (['last_name', 'first_name', 'middle_name'] as $field) {
            if (isset($filter[$field])) {
                $builder->where($field, 'like', $filter[$field] . '%');
            }
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
