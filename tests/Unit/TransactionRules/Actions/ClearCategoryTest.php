<?php
/**
 * ClearCategoryTest.php
 * Copyright (c) 2019 james@firefly-iii.org
 *
 * This file is part of Firefly III (https://github.com/firefly-iii).
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */
declare(strict_types=1);

namespace Tests\Unit\TransactionRules\Actions;

use FireflyIII\Models\RuleAction;
use FireflyIII\TransactionRules\Actions\ClearCategory;
use Log;
use Tests\TestCase;

/**
 * Class ClearCategoryTest
 */
class ClearCategoryTest extends TestCase
{
    /**
     *
     */
    public function setUp(): void
    {
        parent::setUp();
        Log::info(sprintf('Now in %s.', get_class($this)));
    }

    /**
     * @covers \FireflyIII\TransactionRules\Actions\ClearCategory
     */
    public function testAct(): void
    {
        // associate budget with journal:
        $journal  = $this->user()->transactionJournals()->where('description', 'Rule action test transaction.')->first();
        $category = $this->user()->categories()->inRandomOrder()->first();

        // link a budget.
        $journal->categories()->save($category);
        $this->assertGreaterThan(0, $journal->categories()->count());

        $array = [
            'transaction_journal_id' => $journal->id,
        ];

        // fire the action:
        $ruleAction               = new RuleAction;
        $ruleAction->action_value = null;
        $action                   = new ClearCategory($ruleAction);
        $result                   = $action->actOnArray($array);
        $this->assertTrue($result);

        // assert result
        $this->assertEquals(0, $journal->categories()->count());
    }
}
