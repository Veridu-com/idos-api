<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Event\Company;

use App\Entity\Company;
use App\Event\AbstractEvent;
use Test\Unit\AbstractUnit;
use App\Event\Company\Created;

class CreatedTest extends AbstractUnit {

	public function testConstruct() {
		$company = new Company([]);

		$created = new Created($company);

		$this->assertInstanceOf(Company::class, $created->company);
	}
}
