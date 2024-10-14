<?php

use siot\core\db\Migration;

/**
 * A copy of migration from m221013_132134_create_update_amount_trigger_on_article_transaction_action_table for removing warehouse.company_type from trigger
 * Class m240319_094255_alter_update_amount_trigger_in_article_transaction_action
 */
class m240319_094255_alter_update_amount_trigger_in_article_transaction_action extends Migration
{
    /**
     * @var string
     */
    private string $name = 'update_amount';

    /**
     * @var string
     */
    private string $table = 'article_transaction_action';

    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function safeUp()
    {
        $this->dropTrigger($this->getTriggerName(), $this->table);
        $this->createFunction($this->name, $this->getFunctionBody());
        $this->createTrigger(
            $this->getTriggerName(),
            $this->table,
            $this->name,
            'AFTER',
            'INSERT OR UPDATE'
        );
    }

    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function safeDown()
    {
        $this->dropTrigger($this->getTriggerName(), $this->table);
        $this->createFunction($this->name, $this->getOldFunctionBody());
        $this->createTrigger(
            $this->getTriggerName(),
            $this->table,
            $this->name,
            'AFTER',
            'INSERT OR UPDATE'
        );
    }

    /**
     * @return string
     */
    private function getFunctionBody(): string
    {
        return <<<SQL
DECLARE
    statusNew integer := 1;
    statusInProgress integer := 2;
    statusDone integer := 3;
    statusCancel integer := 4;
    statusPreOrder integer := 5;
    typeIn integer := 1;
    existItemAmount integer := 0;
    amountValue integer := 0;
    uniqueAmountValue integer := 0;
    reserveAmountValue integer := 0;
    reserveUniqueAmountValue integer := 0;
BEGIN
    IF (TG_OP = 'INSERT') THEN
        -- Check if exist record for this article_id and warehouse_id in itemAmount table
        existItemAmount = (
            SELECT COUNT(id) FROM article_amount
            WHERE article_id = NEW.article_id AND warehouse_id = NEW.warehouse_id
            LIMIT 1
        );
        -- Create record if not exist
        IF (existItemAmount = 0) THEN
            INSERT INTO article_amount
                (
                    article_id,
                    warehouse_id,
                    amount,
                    unique_amount,
                    reserve_amount,
                    reserve_unique_amount,
                    updated_at
                )
            VALUES
                (NEW.article_id, NEW.warehouse_id, 0, 0, 0, 0, extract(epoch from now()));
        END IF;

        IF (NEW.type = typeIn) THEN
            IF (NEW.status = statusDone) THEN
                amountValue = NEW.amount;

                IF (NEW.unique_article_id IS NOT NULL) THEN
                    uniqueAmountValue = NEW.amount;
                END IF;
            ELSEIF (NEW.status = statusNew OR NEW.status = statusInProgress) THEN
                reserveAmountValue = NEW.amount;
                IF (NEW.unique_article_id IS NOT NULL) THEN
                    reserveUniqueAmountValue = NEW.amount;
                END IF;
            END IF;
        ELSE
            IF (NEW.status = statusDone OR NEW.status = statusInProgress) THEN
                amountValue = - NEW.amount;
                IF (NEW.unique_article_id IS NOT NULL) THEN
                    uniqueAmountValue = - NEW.amount;
                END IF;
            ELSEIF (NEW.status = statusNew) THEN
                reserveAmountValue = - NEW.amount;
                IF (NEW.unique_article_id IS NOT NULL) THEN
                    reserveUniqueAmountValue = - NEW.amount;
                END IF;
            END IF;
        END IF;
    ELSE
        IF (NEW.type = typeIn) THEN
            IF (NEW.status = statusDone AND (OLD.status = statusNew OR OLD.status = statusInProgress)) THEN
                amountValue = NEW.amount;
                IF (NEW.amount <> OLD.amount) THEN
                    reserveAmountValue = - OLD.amount;
                ELSE
                    reserveAmountValue = - NEW.amount;
                END IF;
                IF (NEW.unique_article_id IS NOT NULL) THEN
                    uniqueAmountValue = NEW.amount;
                    reserveUniqueAmountValue = - NEW.amount;
                END IF;
            ELSEIF (
                (NEW.status = statusInProgress AND OLD.status = statusNew) OR
                (NEW.status = statusInProgress AND NEW.previous_status = statusNew)
            ) THEN
                IF (NEW.amount <> OLD.amount) THEN
                    reserveAmountValue = - (OLD.amount - NEW.amount);
                END IF;
                IF (NEW.unique_article_id IS NOT NULL) THEN
                    reserveUniqueAmountValue = - (OLD.amount - NEW.amount);
                END IF;
            ELSEIF (NEW.status = statusNew) THEN
                IF (NEW.amount <> OLD.amount) THEN
                    reserveAmountValue = (NEW.amount - OLD.amount);
                END IF;
            ELSEIF (NEW.status = statusCancel) THEN
                reserveAmountValue = - NEW.amount;
                IF (NEW.unique_article_id IS NOT NULL) THEN
                    reserveUniqueAmountValue = - NEW.amount;
                END IF;
            END IF;
        ELSE
            IF (NEW.status = statusInProgress OR (NEW.status = statusDone AND OLD.status = statusNew)) THEN
                amountValue = - NEW.amount;
                IF (NEW.amount <> OLD.amount) THEN
                    reserveAmountValue = OLD.amount;
                ELSE
                    reserveAmountValue = NEW.amount;
                END IF;

                IF (NEW.unique_article_id IS NOT NULL) THEN
                    uniqueAmountValue = - NEW.amount;
                    reserveUniqueAmountValue = NEW.amount;
                END IF;

            ELSEIF (NEW.status = statusDone AND OLD.status = statusInProgress) THEN
                IF (NEW.amount <> OLD.amount) THEN
                    amountValue = OLD.amount - NEW.amount;
                END IF;
            ELSEIF (NEW.status = statusNew AND OLD.status = statusPreOrder) THEN
                reserveAmountValue = - NEW.amount;
            ELSEIF (NEW.status = statusCancel) THEN
                IF (OLD.status = statusNew) THEN
                    reserveAmountValue = NEW.amount;
                    IF (NEW.unique_article_id IS NOT NULL) THEN
                        reserveUniqueAmountValue = NEW.amount;
                    END IF;
                ELSEIF (OLD.status = statusInProgress OR OLD.status = statusDone) THEN
                    amountValue = NEW.amount;
                    IF (NEW.unique_article_id IS NOT NULL) THEN
                        uniqueAmountValue = NEW.amount;
                    END IF;
                END IF;
            END IF;
        END IF;
    END IF;

    UPDATE article_amount
    SET
        amount = amount + amountValue,
        reserve_amount = reserve_amount + reserveAmountValue,
        unique_amount = unique_amount + uniqueAmountValue,
        reserve_unique_amount = reserve_unique_amount + reserveUniqueAmountValue
    WHERE article_id = NEW.article_id AND warehouse_id = NEW.warehouse_id;

    RETURN NEW;
END;
SQL;
    }

    /**
     * @return string
     */
    private function getOldFunctionBody(): string
    {
        return <<<SQL
DECLARE
    statusNew integer := 1;
    statusInProgress integer := 2;
    statusDone integer := 3;
    statusCancel integer := 4;
    statusPreOrder integer := 5;
    typeIn integer := 1;
    existItemAmount integer := 0;
    amountValue integer := 0;
    uniqueAmountValue integer := 0;
    reserveAmountValue integer := 0;
    reserveUniqueAmountValue integer := 0;
    companyType integer;
    companyTypeOwn integer := 1;
BEGIN
    -- Get company's type from warehouse
    companyType = (SELECT company_type FROM warehouse WHERE id = NEW.warehouse_id);

    -- Count amount of articles only for own companies
    IF (companyType != companyTypeOwn) THEN
        RETURN NEW;
    END IF;

    IF (TG_OP = 'INSERT') THEN
        -- Check if exist record for this article_id and warehouse_id in itemAmount table
        existItemAmount = (
            SELECT COUNT(id) FROM article_amount
            WHERE article_id = NEW.article_id AND warehouse_id = NEW.warehouse_id
            LIMIT 1
        );
        -- Create record if not exist
        IF (existItemAmount = 0) THEN
            INSERT INTO article_amount
                (
                    article_id,
                    warehouse_id,
                    amount,
                    unique_amount,
                    reserve_amount,
                    reserve_unique_amount,
                    updated_at
                )
            VALUES
                (NEW.article_id, NEW.warehouse_id, 0, 0, 0, 0, extract(epoch from now()));
        END IF;

        IF (NEW.type = typeIn) THEN
            IF (NEW.status = statusDone) THEN
                amountValue = NEW.amount;

                IF (NEW.unique_article_id IS NOT NULL) THEN
                    uniqueAmountValue = NEW.amount;
                END IF;
            ELSEIF (NEW.status = statusNew OR NEW.status = statusInProgress) THEN
                reserveAmountValue = NEW.amount;
                IF (NEW.unique_article_id IS NOT NULL) THEN
                    reserveUniqueAmountValue = NEW.amount;
                END IF;
            END IF;
        ELSE
            IF (NEW.status = statusDone OR NEW.status = statusInProgress) THEN
                amountValue = - NEW.amount;
                IF (NEW.unique_article_id IS NOT NULL) THEN
                    uniqueAmountValue = - NEW.amount;
                END IF;
            ELSEIF (NEW.status = statusNew) THEN
                reserveAmountValue = - NEW.amount;
                IF (NEW.unique_article_id IS NOT NULL) THEN
                    reserveUniqueAmountValue = - NEW.amount;
                END IF;
            END IF;
        END IF;
    ELSE
        IF (NEW.type = typeIn) THEN
            IF (NEW.status = statusDone AND (OLD.status = statusNew OR OLD.status = statusInProgress)) THEN
                amountValue = NEW.amount;
                IF (NEW.amount <> OLD.amount) THEN
                    reserveAmountValue = - OLD.amount;
                ELSE
                    reserveAmountValue = - NEW.amount;
                END IF;
                IF (NEW.unique_article_id IS NOT NULL) THEN
                    uniqueAmountValue = NEW.amount;
                    reserveUniqueAmountValue = - NEW.amount;
                END IF;
            ELSEIF (
                (NEW.status = statusInProgress AND OLD.status = statusNew) OR
                (NEW.status = statusInProgress AND NEW.previous_status = statusNew)
            ) THEN
                IF (NEW.amount <> OLD.amount) THEN
                    reserveAmountValue = - (OLD.amount - NEW.amount);
                END IF;
                IF (NEW.unique_article_id IS NOT NULL) THEN
                    reserveUniqueAmountValue = - (OLD.amount - NEW.amount);
                END IF;
            ELSEIF (NEW.status = statusNew) THEN
                IF (NEW.amount <> OLD.amount) THEN
                    reserveAmountValue = (NEW.amount - OLD.amount);
                END IF;
            ELSEIF (NEW.status = statusCancel) THEN
                reserveAmountValue = - NEW.amount;
                IF (NEW.unique_article_id IS NOT NULL) THEN
                    reserveUniqueAmountValue = - NEW.amount;
                END IF;
            END IF;
        ELSE
            IF (NEW.status = statusInProgress OR (NEW.status = statusDone AND OLD.status = statusNew)) THEN
                amountValue = - NEW.amount;
                IF (NEW.amount <> OLD.amount) THEN
                    reserveAmountValue = OLD.amount;
                ELSE
                    reserveAmountValue = NEW.amount;
                END IF;

                IF (NEW.unique_article_id IS NOT NULL) THEN
                    uniqueAmountValue = - NEW.amount;
                    reserveUniqueAmountValue = NEW.amount;
                END IF;

            ELSEIF (NEW.status = statusDone AND OLD.status = statusInProgress) THEN
                IF (NEW.amount <> OLD.amount) THEN
                    amountValue = OLD.amount - NEW.amount;
                END IF;
            ELSEIF (NEW.status = statusNew AND OLD.status = statusPreOrder) THEN
                reserveAmountValue = - NEW.amount;
            ELSEIF (NEW.status = statusCancel) THEN
                IF (OLD.status = statusNew) THEN
                    reserveAmountValue = NEW.amount;
                    IF (NEW.unique_article_id IS NOT NULL) THEN
                        reserveUniqueAmountValue = NEW.amount;
                    END IF;
                ELSEIF (OLD.status = statusInProgress OR OLD.status = statusDone) THEN
                    amountValue = NEW.amount;
                    IF (NEW.unique_article_id IS NOT NULL) THEN
                        uniqueAmountValue = NEW.amount;
                    END IF;
                END IF;
            END IF;
        END IF;
    END IF;

    UPDATE article_amount
    SET
        amount = amount + amountValue,
        reserve_amount = reserve_amount + reserveAmountValue,
        unique_amount = unique_amount + uniqueAmountValue,
        reserve_unique_amount = reserve_unique_amount + reserveUniqueAmountValue
    WHERE article_id = NEW.article_id AND warehouse_id = NEW.warehouse_id;

    RETURN NEW;
END;
SQL;
    }

    /**
     * @return string
     */
    private function getTriggerName(): string
    {
        return $this->name . '_trigger';
    }
}

