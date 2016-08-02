<?php
/**
 * @author    Wayne Le
 */
class PrimeNumbersControllerTest extends PHPUnit_Framework_TestCase
{
    public function testPrimeNumbers()
    {
        $primeNumber = new PrimeNumber();
        $primeNumbers = $primeNumber->getPrimeNumbers();
        $this->assertEquals(1000, count($primeNumbers));
    }
}