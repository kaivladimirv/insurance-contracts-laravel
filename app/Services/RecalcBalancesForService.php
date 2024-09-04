<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\Balance\RecalculationOfBalance;
use Illuminate\Support\Facades\DB;

class RecalcBalancesForService
{
    /**
     * @throws RecalculationOfBalance
     */
    public function recalc(int $contractId, int $serviceId): void
    {
        $result = DB::statement(
            '
            WITH limits AS (
                SELECT contract_services.contract_id,
                       insured_persons.id AS insured_person_id,
                       contract_services.service_id,
                       contract_services.limit_type,
                       contract_services.limit_value
                FROM contract_services
                    INNER JOIN insured_persons on contract_services.contract_id = insured_persons.contract_id
                WHERE contract_services.contract_id = :contract_id
                    AND contract_services.service_id = :service_id
            ), expense AS (
                SELECT insured_person_id,
                       service_id,
                       CASE WHEN limit_type=0 THEN sum(provided_services.amount)
                            WHEN limit_type=1 THEN sum(provided_services.quantity)
                            ELSE 0
                       END AS value
                FROM provided_services
                WHERE contract_id = :contract_id
                    AND service_id = :service_id
                    AND deleted_at IS NULL
                GROUP BY insured_person_id,
                         service_id,
                         limit_type
            ), new_balances AS (
                SELECT limits.contract_id,
                       limits.insured_person_id,
                       limits.service_id,
                       limits.limit_type,
                       (limits.limit_value - COALESCE(expense.value, 0)) as balance
                FROM limits
                    LEFT JOIN expense on limits.insured_person_id = expense.insured_person_id
                        AND limits.service_id = expense.service_id
            )
            MERGE INTO balances
            USING new_balances
            ON new_balances.insured_person_id = balances.insured_person_id
                   AND new_balances.service_id = balances.service_id
            WHEN MATCHED THEN
                UPDATE SET balance = new_balances.balance
            WHEN NOT MATCHED THEN
                INSERT (contract_id, insured_person_id, service_id, limit_type, balance)
                VALUES (new_balances.contract_id, new_balances.insured_person_id, new_balances.service_id, new_balances.limit_type, new_balances.balance);',
            [
                'contract_id' => $contractId,
                'service_id' => $serviceId
            ]
        );

        if ($result === false) {
            throw new RecalculationOfBalance(__('Error during service balance recalculation'));
        }
    }
}
