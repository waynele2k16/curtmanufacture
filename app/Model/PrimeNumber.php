<?php
/**
 * @author    Wayne Le
 */
class PrimeNumber extends DataObject {
    public $action = null;
    public $limit = 1000;
    
    /**
     * Generate first prime numbers starting from two.
     * I am using the algorithm PGsimple3 (link: https://en.wikibooks.org/wiki/Some_Basic_and_Inefficient_Prime_Number_Generating_Algorithms)
     * @return array
     */
    public function getPrimeNumbers()
    {
        $this->action = isset($_GET['action']) ? $_GET['action'] : '';
        $this->limit = isset($_GET['limit']) ? $_GET['limit'] : 1000;
        $primeNumber = 2;
        $primeNumbers = array();
        $primeNumbers[] = $primeNumber;
        if ($this->limit == 1) {
            return $primeNumbers;
        }
        $primeNumber += 1;
        $primeNumbers[] = $primeNumber;
        // Generate array of prime numbers until we reach the limitation (defined in variale 'limit')
        while (count($primeNumbers) < $this->limit) {
            $primeNumber += 2;
            $flag = true;
            $sqrtPrimeNumber = sqrt($primeNumber);
            foreach ($primeNumbers as $p) {
                if ($p > $sqrtPrimeNumber) {
                    break;
                }
                if ($primeNumber % $p == 0) {
                    $flag = false;
                    break;
                }
            }
            if ($flag) {
                $primeNumbers[] = $primeNumber;
            }
        }
        return $primeNumbers;
    }
}