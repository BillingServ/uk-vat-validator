<?php

use PHPUnit\Framework\TestCase;
use VatValidator\VatValidatorService;

class VatValidatorTest extends TestCase
{
    public function testVerifyVatNumber()
    {
        $service = new VatValidatorService();
        $result = $service->verifyVatNumber('948561936944');

        $this->assertArrayHasKey('target', $result);
    }

    public function testGetConsultationNumber()
    {
        $service = new VatValidatorService();
        $result = $service->getConsultationNumber('553557881', '961925638');

        $this->assertArrayHasKey('consultationNumber', $result);
    }
}
