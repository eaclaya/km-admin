<?php

use App\Repositories\SpecialNegotiationsRepository;

test('negotiation_status', function () {
    $repository = new SpecialNegotiationsRepository();
    $result = $repository->calculateNegotiationStatus(1);
    // $this->assertEquals(1, $result);
});
